@php

    \Dcat\Admin\Admin::css(asset('css/penyewaan.css'));

    $user = auth('admin')->user();
    $canManage = $user->isRole('administrator') || $user->isRole('pemilik');

    $sisa = $penyewaan->total_harga - $penyewaan->uang_muka;
@endphp

<div class="mb-3">

    <a href="{{ admin_url('penyewaan') }}" class="btn btn-secondary">
        <i class="feather icon-arrow-left"></i> Kembali
    </a>

    <a href="{{ admin_url('penyewaan/' . $penyewaan->id . '/cetak') }}" target="_blank" class="btn btn-primary">
        <i class="feather icon-printer"></i> Cetak Bukti
    </a>

</div>

<div class="penyewaan-detail">
    <div class="row">

        {{-- ================= KIRI ================== --}}
        <div class="col-lg-8">

            <div class="card mb-3">

                <div class="card-header">
                    Informasi Penyewaan
                </div>

                <div class="card-body">

                    <div class="info-grid">

                        <div class="info-item">
                            <div class="info-label">Nama Penyewa</div>
                            <div class="info-value">{{ $penyewaan->nama_penyewa }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Tanggal Mulai</div>
                            <div class="info-value">
                                {{ \Carbon\Carbon::parse($penyewaan->tanggal_mulai)->format('d M Y') }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">No. Telepon</div>
                            <div class="info-value">{{ $penyewaan->no_tlp }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Tanggal Selesai</div>
                            <div class="info-value">
                                {{ \Carbon\Carbon::parse($penyewaan->tanggal_selesai)->format('d M Y') }}
                            </div>
                        </div>

                        <div class="info-item info-lokasi">
                            <div class="info-label">Lokasi</div>
                            <div class="info-value">
                                {{ $penyewaan->lokasi }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Keterangan</div>
                            <div class="info-value">{{ $penyewaan->keterangan }}
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <div class="card">

                <div class="card-header">
                    Detail Paket & Barang
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table ">

                            <thead>

                                <tr>

                                    <th width="60">No</th>

                                    <th>Nama Barang</th>

                                    <th width="180">Asal</th>

                                    <th width="100">Jumlah</th>

                                </tr>

                            </thead>

                            <tbody>

                                @php
                                    $no = 1;
                                @endphp

                                @foreach ($penyewaan->detailBarang as $barang)
                                    <tr>

                                        <td>{{ $no++ }}</td>

                                        <td>{{ $barang->barang->nama_barang }}</td>

                                        <td>Barang Satuan</td>

                                        <td>{{ $barang->jumlah_barang }}</td>

                                    </tr>
                                @endforeach


                                @foreach ($penyewaan->detailPaket as $paket)
                                    @foreach ($paket->paket->detail as $detail)
                                        <tr>

                                            <td>{{ $no++ }}</td>

                                            <td>{{ $detail->barang->nama_barang }}</td>

                                            <td>{{ $paket->paket->nama_paket }}</td>

                                            <td>{{ $detail->jumlah * $paket->jumlah_paket }}</td>

                                        </tr>
                                    @endforeach
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>
        </div>


        {{-- ================= KANAN ================== --}}
        <div class="col-lg-4">

            <div class="card mb-3">

                <div class="card-header">
                    Tagihan & Pembayaran
                </div>

                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">

                        <strong>Total Harga</strong>

                        <strong>

                            Rp {{ number_format($penyewaan->total_harga, 0, ',', '.') }}

                        </strong>

                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2 text-success">

                        <span>Sudah Dibayar</span>

                        <strong>

                            Rp {{ number_format($penyewaan->uang_muka, 0, ',', '.') }}

                        </strong>

                    </div>

                    <div class="d-flex justify-content-between text-danger">

                        <span>Sisa Tagihan</span>

                        <strong>

                            Rp {{ number_format($sisa, 0, ',', '.') }}

                        </strong>

                    </div>

                    @if ($canManage && $penyewaan->status_pembayaran != 'Lunas')
                        <hr>

                        <form method="POST" action="{{ admin_url('penyewaan/' . $penyewaan->id . '/pembayaran') }}">

                            @csrf

                            <div class="form-group">

                                <label>Nominal Pembayaran</label>

                                <input type="number" class="form-control" name="nominal" min="1"
                                    max="{{ $sisa }}" required>

                            </div>

                            <button class="btn btn-primary btn-block">

                                <i class="feather icon-plus-circle"></i>

                                Tambah Pembayaran

                            </button>

                        </form>
                    @endif

                </div>

            </div>


            <div class="card">

                <div class="card-header">
                    Riwayat Pembayaran
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">

                            <thead>

                                <tr>

                                    <th>Tanggal</th>

                                    <th>Status</th>

                                    <th class="text-right">Nominal</th>

                                </tr>

                            </thead>

                            <tbody>

                                <tr>

                                    <td>{{ $penyewaan->created_at->format('d M Y') }}</td>

                                    <td>{{ $penyewaan->status_pembayaran }}</td>

                                    <td class="text-right">

                                        Rp {{ number_format($penyewaan->uang_muka, 0, ',', '.') }}

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                        @if ($penyewaan->status_pembayaran != 'Lunas')
                            <small class="text-muted">

                                Belum ada pelunasan

                            </small>
                        @endif

                    </div>

                </div>

            </div>

        </div>
    </div>
