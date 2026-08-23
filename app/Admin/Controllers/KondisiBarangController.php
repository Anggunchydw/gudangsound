<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Penugasan;
use Dcat\Admin\Layout\Content;
use App\Models\KondisiBarang;
use App\Models\Barang;
use Dcat\Admin\Admin;
use Dcat\Admin\Http\Controllers\AdminController;

class KondisiBarangController extends AdminController
{
    public function index(Content $content)
    {
        $pegawai = auth('admin')->user();

        $penugasan = Penugasan::with([
            'penyewaan.detailBarang',
            'penyewaan.detailPaket.paket.detail',
            'kondisiBarang'
        ])
            ->whereHas('pegawai', function ($q) use ($pegawai) {
                $q->where('admin_users.id', $pegawai->id);
            })
            ->get();

        foreach ($penugasan as $item) {

            // Hitung jumlah jenis barang yang seharusnya diinput
            $barang = [];

            foreach ($item->penyewaan->detailBarang as $detail) {

                $barang[$detail->barang_id] = true;
            }

            foreach ($item->penyewaan->detailPaket as $paket) {

                if (!$paket->paket) {
                    continue;
                }

                foreach ($paket->paket->detail as $detail) {

                    $barang[$detail->barang_id] = true;
                }
            }

            $totalBarang = count($barang);

            $totalInput = $item->kondisiBarang->count();

            $belumLengkap = $item->kondisiBarang
                ->filter(function ($k) {
                    return empty($k->kondisi_sebelum)
                        || empty($k->kondisi_sesudah);
                })
                ->count();

            if ($totalInput == 0) {

                $item->status_input = 'belum';
            } elseif (
                $totalInput < $totalBarang ||
                $belumLengkap > 0
            ) {

                $item->status_input = 'belum_lengkap';
            } else {

                $item->status_input = 'lengkap';
            }
        }

        return $content
            ->title('Kondisi Barang')
            ->body(view(
                'admin.kondisi.daftar-penugasan',
                compact('penugasan')
            ));
    }

    public function input(Penugasan $penugasan, Content $content)
    {
        $user = Admin::user();

        // Pegawai hanya boleh mengakses penugasan yang menjadi tanggung jawabnya
        if (
            $user->isRole('pegawai') &&
            !$penugasan->pegawai()
                ->where('admin_users.id', $user->id)
                ->exists()
        ) {
            abort(403);
        }

        Admin::css(asset('css/kondisi-barang.css'));

        $penugasan->load([
            'penyewaan.detailBarang.barang',
            'penyewaan.detailPaket.paket.detail.barang',
            'kondisiBarang'
        ]);

        $barang = [];

        // Barang yang dipilih langsung
        foreach ($penugasan->penyewaan->detailBarang as $detail) {

            if (!$detail->barang) {
                continue;
            }

            $id = $detail->barang->id;

            if (!isset($barang[$id])) {
                $barang[$id] = [
                    'id' => $id,
                    'nama' => $detail->barang->nama_barang,
                    'jumlah' => 0,
                ];
            }

            $barang[$id]['jumlah'] +=
                (int) $detail->jumlah_barang;
        }

        // Barang yang berasal dari paket
        foreach ($penugasan->penyewaan->detailPaket as $paket) {

            if (!$paket->paket) {
                continue;
            }

            foreach ($paket->paket->detail as $detail) {

                if (!$detail->barang) {
                    continue;
                }

                $id = $detail->barang->id;

                if (!isset($barang[$id])) {
                    $barang[$id] = [
                        'id' => $id,
                        'nama' => $detail->barang->nama_barang,
                        'jumlah' => 0,
                    ];
                }

                $barang[$id]['jumlah'] +=
                    (int) $detail->jumlah *
                    (int) $paket->jumlah_paket;
            }
        }

        $kondisi = $penugasan->kondisiBarang
            ->keyBy('barang_id');

        foreach ($barang as &$item) {

            $data = $kondisi->get($item['id']);

            $item['kondisi_sebelum'] =
                $data->kondisi_sebelum ?? null;

            $item['kondisi_sesudah'] =
                $data->kondisi_sesudah ?? null;

            $item['jumlah_bermasalah'] =
                $data->jumlah_bermasalah ?? 0;

            $item['catatan'] =
                $data->catatan ?? null;
        }

        unset($item);

        return $content
            ->title('Input Kondisi Barang')
            ->body(view(
                'admin.kondisi.input-kondisi',
                [
                    'penugasan' => $penugasan,
                    'barang' => collect($barang)->values(),
                ]
            ));
    }

    public function simpan(Request $request, Penugasan $penugasan)
    {
        $user = Admin::user();

        if (
            $user->isRole('pegawai') &&
            !$penugasan->pegawai()
                ->where('admin_users.id', $user->id)
                ->exists()
        ) {
            abort(403);
        }

        $penugasan->load([
            'penyewaan.detailBarang',
            'penyewaan.detailPaket.paket.detail',
        ]);

        $barangPenugasan = [];

        // Barang yang dipilih langsung
        foreach ($penugasan->penyewaan->detailBarang as $detail) {

            $barangId = (int) $detail->barang_id;

            $barangPenugasan[$barangId] =
                ($barangPenugasan[$barangId] ?? 0)
                + (int) $detail->jumlah_barang;
        }

        // Barang yang berasal dari paket
        foreach ($penugasan->penyewaan->detailPaket as $paket) {

            if (!$paket->paket) {
                continue;
            }

            foreach ($paket->paket->detail as $detail) {

                $barangId = (int) $detail->barang_id;

                $jumlah =
                    (int) $detail->jumlah *
                    (int) $paket->jumlah_paket;

                $barangPenugasan[$barangId] =
                    ($barangPenugasan[$barangId] ?? 0)
                    + $jumlah;
            }
        }

        $validator = Validator::make(
            $request->all(),
            [
                'barang' => [
                    'required',
                    'array',
                ],

                'barang.*.barang_id' => [
                    'required',
                    'integer',
                    'exists:barang,id',
                    'distinct',
                ],

                'barang.*.kondisi_sebelum' => [
                    'nullable',
                    'in:baik,rusak,hilang',
                ],

                'barang.*.kondisi_sesudah' => [
                    'required',
                    'in:baik,rusak,hilang',
                ],

                'barang.*.jumlah_bermasalah' => [
                    'required',
                    'integer',
                    'min:0',
                ],

                'barang.*.catatan' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],
            ],
            [
                'barang.required' =>
                'Data kondisi barang wajib diisi.',

                'barang.array' =>
                'Format data barang tidak valid.',

                'barang.*.barang_id.required' =>
                'ID barang wajib diisi.',

                'barang.*.barang_id.integer' =>
                'ID barang tidak valid.',

                'barang.*.barang_id.exists' =>
                'Barang tidak ditemukan.',

                'barang.*.barang_id.distinct' =>
                'Barang tidak boleh diproses lebih dari satu kali.',

                'barang.*.kondisi_sebelum.in' =>
                'Kondisi sebelum tidak valid.',

                'barang.*.kondisi_sesudah.required' =>
                'Kondisi sesudah wajib dipilih.',

                'barang.*.kondisi_sesudah.in' =>
                'Kondisi barang tidak valid.',

                'barang.*.jumlah_bermasalah.required' =>
                'Jumlah barang bermasalah wajib diisi.',

                'barang.*.jumlah_bermasalah.integer' =>
                'Jumlah barang bermasalah harus berupa angka.',

                'barang.*.jumlah_bermasalah.min' =>
                'Jumlah barang bermasalah tidak boleh kurang dari 0.',

                'barang.*.catatan.string' =>
                'Catatan harus berupa teks.',

                'barang.*.catatan.max' =>
                'Catatan terlalu panjang.',
            ]
        );

        if ($validator->fails()) {

            admin_error(
                'Gagal',
                $validator->errors()->first()
            );

            return redirect()->to(
                admin_url(
                    'kondisi-barang/' .
                        $penugasan->id .
                        '/input'
                )
            )->withInput();
        }

        $validated = $validator->validated();

        // Validasi barang terhadap penugasan
        foreach ($validated['barang'] as $item) {

            $barangId = (int) $item['barang_id'];

            if (!isset($barangPenugasan[$barangId])) {

                abort(
                    422,
                    'Barang tidak termasuk dalam penugasan.'
                );
            }
        }

        try {

            DB::transaction(function () use (
                $validated,
                $barangPenugasan,
                $penugasan
            ) {

                foreach ($validated['barang'] as $item) {

                    $barangId = (int) $item['barang_id'];

                    $jumlahBarang =
                        (int) $barangPenugasan[$barangId];

                    $jumlahBermasalah =
                        (int) $item['jumlah_bermasalah'];

                    $kondisiBaru =
                        $item['kondisi_sesudah'];


                    if ($kondisiBaru === 'baik') {

                        $jumlahBermasalah = 0;
                    }

                    if (
                        in_array(
                            $kondisiBaru,
                            ['rusak', 'hilang']
                        ) &&
                        $jumlahBermasalah <= 0
                    ) {

                        throw new \RuntimeException(
                            'Masukkan jumlah barang yang rusak atau hilang.'
                        );
                    }

                    //Jumlah bermasalah tidak boleh melebihi
                    // jumlah barang yang sebenarnya dibawa
                    if ($jumlahBermasalah > $jumlahBarang) {

                        throw new \RuntimeException(
                            'Jumlah barang rusak/hilang tidak boleh melebihi jumlah barang yang dibawa.'
                        );
                    }

                    //Kunci row barang sebelum perubahan stok
                    $barang = Barang::where(
                        'id',
                        $barangId
                    )
                        ->lockForUpdate()
                        ->first();

                    if (!$barang) {

                        throw new \RuntimeException(
                            'Barang tidak ditemukan.'
                        );
                    }

                    //Ambil kondisi lama dan kunci row
                    $dataLama = KondisiBarang::where(
                        'penugasan_id',
                        $penugasan->id
                    )
                        ->where(
                            'barang_id',
                            $barangId
                        )
                        ->lockForUpdate()
                        ->first();

                    $kondisiLama =
                        $dataLama->kondisi_sesudah ?? null;

                    $jumlahLama =
                        (int) (
                            $dataLama->jumlah_bermasalah ?? 0
                        );

                    //Update stok
                    // Kondisi berubah menjadi baik
                    if ($kondisiBaru === 'baik') {

                        if (
                            in_array(
                                $kondisiLama,
                                ['rusak', 'hilang']
                            )
                        ) {

                            $barang->increment(
                                'jumlah_total',
                                $jumlahLama
                            );
                        }
                    }

                    // Kondisi rusak atau hilang
                    else {

                        $selisih =
                            $jumlahBermasalah -
                            $jumlahLama;

                        // Barang bermasalah bertambah
                        if ($selisih > 0) {

                            //Cek stok setelah row dikunci
                            if (
                                $barang->jumlah_total <
                                $selisih
                            ) {

                                throw new \RuntimeException(
                                    'Stok barang tidak mencukupi.'
                                );
                            }

                            $barang->decrement(
                                'jumlah_total',
                                $selisih
                            );
                        }

                        // Barang bermasalah berkurang
                        elseif ($selisih < 0) {

                            $barang->increment(
                                'jumlah_total',
                                abs($selisih)
                            );
                        }
                    }

                    //Simpan kondisi barang
                    KondisiBarang::updateOrCreate(
                        [
                            'penugasan_id' =>
                            $penugasan->id,

                            'barang_id' =>
                            $barangId,
                        ],
                        [
                            //Nilai jumlah_barang berasal dari database
                            'jumlah_barang' =>
                            $jumlahBarang,

                            'jumlah_bermasalah' =>
                            $jumlahBermasalah,

                            'kondisi_sebelum' =>
                            $item['kondisi_sebelum'] ?? null,

                            'kondisi_sesudah' =>
                            $kondisiBaru,

                            'catatan' =>
                            $item['catatan'] ?? null,
                        ]
                    );
                }
            });
        } catch (\RuntimeException $e) {
            //Error validasi/proses
            admin_error(
                'Gagal',
                $e->getMessage()
            );

            return redirect()->to(
                admin_url(
                    'kondisi-barang/' .
                        $penugasan->id .
                        '/input'
                )
            )->withInput();
        } catch (\Throwable $e) {

            report($e);

            admin_error(
                'Gagal',
                'Terjadi kesalahan saat menyimpan data kondisi barang.'
            );

            return redirect()->to(
                admin_url(
                    'kondisi-barang/' .
                        $penugasan->id .
                        '/input'
                )
            )->withInput();
        }

        admin_success(
            'Berhasil',
            'Data kondisi barang berhasil disimpan.'
        );

        return redirect(
            admin_url('kondisi-barang')
        );
    }
}
