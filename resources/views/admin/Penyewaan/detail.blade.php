@php
    $user = auth('admin')->user();
    $canManage = $user->isRole('administrator') || $user->isRole('pemilik');
@endphp
<div class="mb-3">

    <a href="{{ admin_url('penyewaan') }}" class="btn btn-secondary">
        <i class="feather icon-arrow-left"></i>
        Kembali
    </a>

    <a href="{{ admin_url('penyewaan/' . $penyewaan->id . '/cetak') }}" target="_blank" class="btn btn-primary">

        <i class="feather icon-printer"></i>
        Cetak Bukti

    </a>

    @if ($canManage)
        <a href="{{ admin_url('penyewaan/' . $penyewaan->id . '/edit') }}" class="btn btn-warning">

            <i class="feather icon-edit"></i>
            Edit

        </a>
    @endif

</div>
<div class="card">
    <div class="card-header">
        Informasi Penyewaan
    </div>

    <div class="card-body">

        <table class="table table-borderless">

            <tr>
                <th>Nama Penyewa</th>
                <td>{{ $penyewaan->nama_penyewa }}</td>
            </tr>

            <tr>
                <th>No Telepon</th>
                <td>{{ $penyewaan->no_tlp }}</td>
            </tr>

            <tr>
                <th>Tanggal Mulai</th>
                <td>{{ $penyewaan->tanggal_mulai }}</td>
            </tr>

            <tr>
                <th>Tanggal Selesai</th>
                <td>{{ $penyewaan->tanggal_selesai }}</td>
            </tr>

            <tr>
                <th>Lokasi</th>
                <td>{{ $penyewaan->lokasi }}</td>
            </tr>

        </table>

    </div>
</div>
<div class="card">

    <div class="card-header">
        Tagihan & Pembayaran
    </div>

    <div class="card-body">

        <p>
            <b>Total Harga</b>

            <span class="float-right">

                Rp {{ number_format($penyewaan->total_harga, 0, ',', '.') }}

            </span>
        </p>

        <hr>

        <p style="color:green">

            Sudah Dibayar

            <span class="float-right">

                Rp {{ number_format($penyewaan->uang_muka, 0, ',', '.') }}

            </span>

        </p>

        <p style="color:red">

            Sisa Tagihan

            <span class="float-right">

                Rp {{ number_format($penyewaan->total_harga - $penyewaan->uang_muka, 0, ',', '.') }}

            </span>

        </p>

        @if ($canManage && $penyewaan->status_pembayaran != 'Lunas')
            <hr>

            <form method="POST" action="{{ admin_url('penyewaan/' . $penyewaan->id . '/pembayaran') }}">

                @csrf

                <div class="form-group">

                    <label>

                        Nominal Pembayaran

                    </label>

                    <input type="number" name="nominal" class="form-control" min="1"
                        max="{{ $penyewaan->total_harga - $penyewaan->uang_muka }}" required>

                </div>

                @error('nominal')
                    <small class="text-danger">

                        {{ $message }}

                    </small>
                @enderror

                <button class="btn btn-success mt-2">

                    <i class="feather icon-dollar-sign"></i>

                    Simpan Pembayaran

                </button>

            </form>
        @endif

    </div>

</div>
<div class="card">

    <div class="card-header">

        Detail Barang

    </div>

    <div class="card-body">

        <table class="table">

            <thead>

                <tr>

                    <th>No</th>

                    <th>Barang</th>

                    <th>Jumlah</th>

                </tr>

            </thead>

            <tbody>

                @foreach ($penyewaan->detailBarang as $i => $barang)
                    <tr>

                        <td>{{ $i + 1 }}</td>

                        <td>{{ $barang->barang->nama_barang }}</td>

                        <td>{{ $barang->jumlah_barang }}</td>

                    </tr>
                @endforeach

            </tbody>

        </table>

    </div>

</div>
<div class="card">

    <div class="card-header">

        Detail Paket

    </div>

    <div class="card-body">

        <table class="table">

            <thead>

                <tr>

                    <th>Paket</th>

                    <th>Jumlah</th>

                </tr>

            </thead>

            <tbody>

                @foreach ($penyewaan->detailPaket as $paket)
                    <tr>

                        <td>{{ $paket->paket->nama_paket }}</td>

                        <td>{{ $paket->jumlah_paket }}</td>

                    </tr>
                @endforeach

            </tbody>

        </table>

    </div>

</div>
