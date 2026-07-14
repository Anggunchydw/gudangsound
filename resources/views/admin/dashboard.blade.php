<div class="dashboard-toolbar">

    <div>

        <a href="{{ admin_url('penyewaan/create') }}" class="btn btn-primary">
            <i class="feather icon-plus-circle"></i>
            Tambah Penyewaan
        </a>

        <a href="{{ admin_url('paket/create') }}" class="btn btn-default">
            <i class="feather icon-package"></i>
            Tambah Paket
        </a>

        <a href="{{ admin_url('barang/create') }}" class="btn btn-default">
            <i class="feather icon-box"></i>
            Tambah Barang
        </a>

    </div>

    <div class="dashboard-update">
        Update terakhir :
        {{ now()->translatedFormat('d M Y, H:i') }} WIB
    </div>

</div>


<div class="row">

    <div class="col-md-4">

        <div class="dashboard-card">

            <div>

                <div class="dashboard-label">

                    Total Penyewaan

                </div>

                <div class="dashboard-number">

                    {{ $totalPenyewaan }}

                </div>

                <div class="dashboard-desc">

                    Bulan ini

                </div>

            </div>

            <div class="dashboard-icon purple">

                <i class="feather icon-calendar"></i>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="dashboard-card">

            <div>

                <div class="dashboard-label">

                    Stok Barang

                </div>

                <div class="dashboard-number">

                    {{ $stokBarang }}

                </div>

                <div class="dashboard-desc">

                    Siap digunakan

                </div>

            </div>

            <div class="dashboard-icon blue">

                <i class="feather icon-box"></i>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="dashboard-card">

            <div>

                <div class="dashboard-label">

                    Pendapatan Bulan Ini

                </div>

                <div class="dashboard-money">

                    Rp {{ number_format($pendapatan, 0, ',', '.') }}

                </div>

                <div class="dashboard-desc">

                    Total pemasukan

                </div>

            </div>

            <div class="dashboard-icon green">

                <i class="feather icon-credit-card"></i>

            </div>

        </div>

    </div>

</div>


<div class="box">

    <div class="box-header with-border">

        <h3 class="box-title">

            Transaksi Terbaru

        </h3>

    </div>

    <div class="box-body table-responsive">

        <table class="table dashboard-table">

            <thead>

                <tr>

                    <th>Nama Penyewa</th>

                    <th>Tanggal Acara</th>

                    <th>Paket / Barang</th>

                    <th>Total Biaya</th>

                    <th>Status</th>

                    <th width="60">Aksi</th>

                </tr>

            </thead>

            <tbody>

                @forelse($transaksiTerbaru as $item)
                    <tr>

                        <td>

                            {{ $item->nama_penyewa }}

                        </td>

                        <td>

                            {{ date('d M Y', strtotime($item->tanggal_mulai)) }}

                        </td>

                        <td>

                            @php
                                $items = [];

                                // Paket
                                foreach ($item->detailPaket as $detail) {
                                    if ($detail->paket) {
                                        $items[] = $detail->paket->nama_paket;
                                    }
                                }

                                // Barang Custom
                                foreach ($item->detailBarang as $detail) {
                                    if ($detail->barang) {
                                        $items[] = $detail->barang->nama_barang;
                                    }
                                }

                                // Ambil maksimal 2 item pertama
                                $preview = array_slice($items, 0, 2);
                            @endphp

                            @if (count($items))
                                {{ implode(', ', $preview) }}

                                @if (count($items) > 2)
                                    <small class="text-muted">
                                        +{{ count($items) - 2 }} lainnya
                                    </small>
                                @endif
                            @else
                                -
                            @endif

                        </td>

                        <td>

                            Rp {{ number_format($item->total_harga, 0, ',', '.') }}

                        </td>

                        <td>

                            @if ($item->status_pembayaran == 'DP')
                                <span class="status-dp">

                                    DP

                                </span>
                            @else
                                <span class="status-lunas">

                                    Lunas

                                </span>
                            @endif

                        </td>

                        <td>

                            <a href="{{ admin_url('penyewaan/' . $item->id) }}">

                                <i class="feather icon-eye"></i>

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6" class="text-center">

                            Belum ada transaksi

                        </td>

                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>
