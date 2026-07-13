<div class="mb-3">

    <a href="{{ admin_url('penyewaan/' . $penyewaan->id) }}" class="btn btn-secondary">

        <i class="feather icon-arrow-left"></i>

        Kembali

    </a>

</div>

<div class="card">

    <div class="card-header">

        Informasi Tagihan

    </div>

    <div class="card-body">

        <table class="table table-borderless">

            <tr>
                <th width="250">Nama Penyewa</th>
                <td>{{ $penyewaan->nama_penyewa }}</td>
            </tr>

            <tr>
                <th>Total Tagihan</th>
                <td>
                    Rp {{ number_format($penyewaan->total_harga, 0, ',', '.') }}
                </td>
            </tr>

            <tr>
                <th>Sudah Dibayar</th>
                <td style="color:green">
                    Rp {{ number_format($penyewaan->uang_muka, 0, ',', '.') }}
                </td>
            </tr>

            <tr>
                <th>Sisa Tagihan</th>
                <td style="color:red">
                    Rp {{ number_format($penyewaan->total_harga - $penyewaan->uang_muka, 0, ',', '.') }}
                </td>
            </tr>

        </table>

    </div>

</div>

<div class="card mt-3">

    <div class="card-header">

        Tambah Pembayaran

    </div>

    <div class="card-body">

        <form method="POST" action="{{ admin_url('penyewaan/' . $penyewaan->id . '/pembayaran') }}">

            @csrf

            <div class="form-group">

                <label>

                    Nominal Pembayaran

                </label>

                <input type="number" class="form-control" name="nominal" min="1"
                    max="{{ $penyewaan->total_harga - $penyewaan->uang_muka }}" required>

                @error('nominal')
                    <small class="text-danger">

                        {{ $message }}

                    </small>
                @enderror

            </div>

            <button class="btn btn-success">

                <i class="feather icon-dollar-sign"></i>

                Simpan Pembayaran

            </button>

        </form>

    </div>

</div>
