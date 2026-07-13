<?php

namespace App\Admin\Controllers;

use App\Services\PemasukanService;
use Illuminate\Http\Request;
use Dcat\Admin\Admin;
use App\Admin\Repositories\Penyewaan as PenyewaanRepository;
use App\Models\Penyewaan;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use App\Models\Barang;
use App\Models\Paket;
use Carbon\Carbon;
use App\Services\InventoryService;
use Dcat\Admin\Http\Controllers\AdminController;
use Barryvdh\DomPDF\Facade\Pdf;

class PenyewaanController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new PenyewaanRepository(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('nama_penyewa');
            $grid->column('no_tlp');
            $grid->column('tanggal_mulai');
            $grid->column('tanggal_selesai');
            $grid->column('lokasi');
            $grid->column('total_harga')
                ->display(function ($value) {
                    return 'Rp ' . number_format($value, 0, ',', '.');
                });
            $grid->column('status_pembayaran', 'Status')
                ->display(function ($value) {

                    $class = $value == 'DP'
                        ? 'status-dp'
                        : 'status-lunas';

                    return "<span class='{$class}'>{$value}</span>";
                });
            $grid->column('status_badge', 'Status')
                ->display(function ($value) {
                    return $value;
                });

            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('nama_penyewa');
            });
            $grid->actions(function (Grid\Displayers\Actions $actions) {

                $status = $actions->row->status_sekarang;

                if (in_array($status, ['booking', 'berlangsung'])) {

                    $url = admin_url("penyewaan/{$actions->getKey()}/cancel");

                    $actions->append(
                        "<a class='btn btn-sm btn-danger'
                            href='{$url}'
                            onclick=\"return confirm('Batalkan penyewaan ini?')\">
                            <i class='feather icon-x-circle'></i> Batalkan
                        </a>"
                    );
                }
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $penyewaan = Penyewaan::with([
            'detailBarang.barang',
            'detailPaket.paket',
        ])->findOrFail($id);

        return view(
            'admin.penyewaan.detail',
            compact('penyewaan')
        );
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        Admin::script(<<<JS

        function formatRupiah(angka){

            if (angka < 0) {
                return '-' + new Intl.NumberFormat('id-ID').format(Math.abs(angka));
            }

            return new Intl.NumberFormat('id-ID').format(angka);
        }

        function getCurrency(value){

            value = String(value);

            value = value.replace(/,/g,'');

            return parseFloat(value) || 0;
        }

        function hitungPelunasan(){

            let total = getCurrency(
                $('input[name="total_harga"]').val()
            );

            let dp = getCurrency(
                $('input[name="uang_muka"]').val()
            );

            let sisa = total - dp;

            $('#sisa-pelunasan').text(
                'Rp ' + formatRupiah(sisa)
            );
        }


        $(document).on(
            'keyup change',
            'input[name="total_harga"], input[name="uang_muka"]',
            hitungPelunasan
        );

        hitungPelunasan();


        JS);
        return Form::make(PenyewaanRepository::with([
            'detailPaket',
            'detailBarang'
        ]), function (Form $form) {
            $form->display('id');
            $form->text('nama_penyewa')
                ->required();

            $form->mobile('no_tlp')
                ->required();

            $form->date('tanggal_mulai')
                ->required();

            $form->date('tanggal_selesai')
                ->required();

            $form->text('lokasi')
                ->required();

            //DETAIL PAKET
            $form->divider('Paket');

            $form->hasMany('detailPaket', 'Paket yang Disewa', function (Form\NestedForm $form) {

                $form->select('paket_id', 'Pilih Paket')
                    ->options(
                        Paket::getPaketAktif()
                    )
                    ->required();

                $form->number('jumlah_paket', 'Jumlah')
                    ->default(1)
                    ->min(1);
            })->useTable();

            //DETAIL BARANG
            $form->divider('Barang Satuan');

            $form->hasMany('detailBarang', 'Barang yang Disewa', function (Form\NestedForm $form) {

                $form->select('barang_id', 'Barang')
                    ->options(
                        Barang::where('status', 'aktif')
                            ->pluck('nama_barang', 'id')
                    )
                    ->required();

                $form->number('jumlah_barang', 'Jumlah')
                    ->default(1)
                    ->min(1);
            })->useTable();
            $form->divider('Pembayaran');
            $form->currency('total_harga', 'Total Harga')
                ->symbol('Rp')
                ->required();
            $form->currency('uang_muka', 'Uang Muka (DP)')
                ->symbol('Rp')
                ->default(0)
                ->required();
            $form->html('
            <div class="form-group">
                <label class="control-label">Sisa Pelunasan</label>
                <div>
                    <strong id="sisa-pelunasan" style="color:#dc3545;">
                        Rp 0
                    </strong>
                </div>
            </div>
            ');
            $form->radio('status_pembayaran', 'Status Pembayaran')
                ->options([
                    'DP'    => '<span class="status-dp">DP</span>',
                    'Lunas' => '<span class="status-lunas">Lunas</span>',
                ])
                ->default('DP')
                ->attribute(['class' => 'status-radio'])
                ->required();

            $form->hidden('status_penyewaan')->default('booking');


            $form->saving(function (Form $form) {
                $totalHarga = (float) str_replace(',', '', $form->total_harga);
                $uangMuka   = (float) str_replace(',', '', request('uang_muka'));

                $form->uang_muka = $uangMuka;

                if ($uangMuka < 0) {

                    return $form->response()->error(
                        'Uang muka (DP) tidak boleh bernilai negatif.'
                    );
                }
                if ($uangMuka > $totalHarga) {

                    return $form->response()->error(
                        'Uang muka (DP) tidak boleh melebihi total harga.'
                    );
                }
                $form->status_pembayaran =
                    $uangMuka >= $totalHarga
                    ? 'Lunas'
                    : 'DP';
                try {

                    InventoryService::checkAvailability(

                        $form->tanggal_mulai,
                        $form->tanggal_selesai,


                        request()->input('detailBarang', []),

                        request()->input('detailPaket', []),

                        $form->model()->id // null saat tambah

                    );
                } catch (\Exception $e) {

                    return $form->response()->error($e->getMessage());
                }
            });
            $form->saved(function (Form $form) {

                $penyewaan = Penyewaan::find($form->getKey());

                if ($penyewaan->uang_muka > 0) {

                    PemasukanService::simpan(
                        $penyewaan,
                        $penyewaan->uang_muka,
                        $penyewaan->status_pembayaran == 'Lunas'
                            ? 'Lunas'
                            : 'DP',
                        'Pembayaran awal'
                    );
                }

                return $form->response()
                    ->success('Penyewaan berhasil disimpan.')
                    ->redirect(admin_url('penyewaan/' . $form->getKey()));
            });
        });
    }

    public function simpanPembayaran(Request $request, $id)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:1'
        ]);

        $penyewaan = Penyewaan::findOrFail($id);

        $sisa =
            $penyewaan->total_harga
            -
            $penyewaan->uang_muka;

        if ($request->nominal > $sisa) {

            return back()
                ->withErrors([
                    'nominal' => 'Nominal melebihi sisa tagihan.'
                ]);
        }

        $penyewaan->uang_muka += $request->nominal;

        if (
            $penyewaan->uang_muka >=
            $penyewaan->total_harga
        ) {

            $penyewaan->status_pembayaran = 'Lunas';
        } else {

            $penyewaan->status_pembayaran = 'DP';
        }

        $penyewaan->save();
        PemasukanService::simpan(
            $penyewaan,
            $request->nominal,
            $penyewaan->status_pembayaran == 'Lunas'
                ? 'Lunas'
                : 'DP',
            'Pembayaran lanjutan'
        );

        admin_success(
            'Berhasil',
            'Pembayaran berhasil ditambahkan.'
        );

        return redirect(
            admin_url("penyewaan/{$penyewaan->id}")
        );
    }
    public function cetak($id)
    {
        $penyewaan = \App\Models\Penyewaan::with([
            'detailBarang.barang',
            'detailPaket.paket.detail.barang',
        ])->findOrFail($id);

        $pdf = Pdf::loadView(
            'admin.penyewaan.invoice',
            compact('penyewaan')
        );

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream(
            'Bukti-Penyewaan-' . $penyewaan->id . '.pdf'
        );
    }
    public function cancel($id)
    {
        $penyewaan = \App\Models\Penyewaan::findOrFail($id);

        $penyewaan->update([
            'status_penyewaan' => 'dibatalkan'
        ]);

        admin_success('Berhasil', 'Penyewaan berhasil dibatalkan.');

        return redirect(admin_url('penyewaan'));
    }
}
