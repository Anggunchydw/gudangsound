<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <style>
        body {

            font-family: DejaVu Sans;

            font-size: 12px;

        }

        table {

            width: 100%;

            border-collapse: collapse;

        }

        th,
        td {

            padding: 8px;

        }

        .border {

            border: 1px solid #ddd;

        }

        .title {

            font-size: 24px;

            font-weight: bold;

            text-align: center;

        }

        .text-right {

            text-align: right;

        }

        .text-center {

            text-align: center;

        }

        hr {

            margin: 20px 0;

        }

        .badge {

            display: inline-block;

            padding: 6px 12px;

            border: 1px solid #ff9800;

            color: #ff9800;

            font-weight: bold;

        }

        .green {

            color: green;

        }

        .red {

            color: red;

        }
    </style>

</head>

<body>

    <table>

        <tr>

            <td>

                <h2>HSB Audio</h2>

                Jl. Sumber Beras, Banyuwangi

            </td>

            <td class="text-right">

                <div class="title">

                    BUKTI PENYEWAAN

                </div>

                No Invoice :

                <b>

                    INV-{{ str_pad($penyewaan->id, 5, '0', STR_PAD_LEFT) }}

                </b>

                <br>

                Tanggal :

                {{ date('d M Y') }}

            </td>

        </tr>

    </table>

    <hr>

    <table>

        <tr>

            <td width="50%">

                <b>Informasi Penyewa</b>

                <br><br>

                Nama :

                {{ $penyewaan->nama_penyewa }}

                <br>

                No HP :

                {{ $penyewaan->no_tlp }}

            </td>

            <td>

                <b>Detail Acara</b>

                <br><br>

                Lokasi :

                {{ $penyewaan->lokasi }}

                <br>

                Periode :

                {{ $penyewaan->tanggal_mulai }}

                s/d

                {{ $penyewaan->tanggal_selesai }}

            </td>

        </tr>

    </table>

    <br>

    <table border="1">

        <thead>

            <tr>

                <th width="5%">No</th>

                <th>Barang/Paket</th>

                <th width="15%">Jumlah</th>

                <th width="25%">Harga</th>

            </tr>

        </thead>

        <tbody>

            @php $no=1; @endphp

            @foreach ($penyewaan->detailPaket as $paket)
                <tr>

                    <td class="text-center">

                        {{ $no++ }}

                    </td>

                    <td>

                        {{ $paket->paket->nama_paket }}

                    </td>

                    <td class="text-center">

                        {{ $paket->jumlah_paket }}

                    </td>

                    <td class="text-right">

                        Rp {{ number_format($paket->paket->harga_paket, 0, ',', '.') }}

                    </td>

                </tr>
            @endforeach

            @foreach ($penyewaan->detailBarang as $barang)
                <tr>

                    <td class="text-center">

                        {{ $no++ }}

                    </td>

                    <td>

                        {{ $barang->barang->nama_barang }}

                    </td>

                    <td class="text-center">

                        {{ $barang->jumlah_barang }}

                    </td>

                    <td class="text-right">

                        Rp {{ number_format($barang->barang->harga_sewa, 0, ',', '.') }}

                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>

    <br><br>

    <table>

        <tr>

            <td width="50%">

                @if ($penyewaan->status_pembayaran == 'DP')
                    <span class="badge">

                        DP (BELUM LUNAS)

                    </span>
                @else
                    <span class="badge">

                        LUNAS

                    </span>
                @endif

            </td>

            <td>

                <table>

                    <tr>

                        <td>Total Harga</td>

                        <td class="text-right">

                            Rp {{ number_format($penyewaan->total_harga, 0, ',', '.') }}

                        </td>

                    </tr>

                    <tr>

                        <td>Uang DP</td>

                        <td class="text-right green">

                            Rp {{ number_format($penyewaan->uang_muka, 0, ',', '.') }}

                        </td>

                    </tr>

                    <tr>

                        <td>Sisa Pembayaran</td>

                        <td class="text-right red">

                            Rp {{ number_format($penyewaan->total_harga - $penyewaan->uang_muka, 0, ',', '.') }}

                        </td>

                    </tr>

                </table>

            </td>

        </tr>

    </table>

    <br><br><br><br>

    <table>

        <tr>

            <td class="text-center">

                Penyewa

                <br><br><br><br>

                ____________________

                <br>

                {{ $penyewaan->nama_penyewa }}

            </td>

            <td class="text-center">

                HSB Audio

                <br><br><br><br>

                ____________________

                <br>

                Administrator

            </td>

        </tr>

    </table>

</body>

</html>
