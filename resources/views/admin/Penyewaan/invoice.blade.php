<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 11px;
            line-height: 1.6;
            color: #333;
            position: relative;

        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            vertical-align: top;
        }

        th {
            background: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }

        .border {
            border: 1px solid #ddd;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 1px;
            color: #222;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        hr {
            margin: 18px 0;
            border: 0;
            border-top: 1px solid #bbb;
        }

        h2 {
            margin: 0;
            font-size: 24px;
            color: #222;
        }

        .badge {
            display: inline-block;
            padding: 6px 14px;
            border: 1px solid #ff9800;
            color: #ff9800;
            font-weight: bold;
            border-radius: 3px;
        }

        .badge-success {
            display: inline-block;
            padding: 6px 14px;
            border: 1px solid #28a745;
            color: #28a745;
            font-weight: bold;
            border-radius: 3px;
        }

        .green {
            color: #28a745;
            font-weight: bold;
        }

        .red {
            color: #dc3545;
            font-weight: bold;
        }

        .company-info {
            font-size: 11px;
            color: #444;
        }

        .invoice-info {
            font-size: 11px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 6px;
            color: #222;
        }

        .total-table td {
            padding: 6px;
        }

        .signature {
            text-align: center;
            line-height: 1.8;
        }

        .watermark {
            position: fixed;
            z-index: -1000;
            opacity: 0.05;
        }

        .watermark img {
            width: 220px;
            transform: rotate(-35deg);
        }
    </style>
</head>

<body>
    <div class="watermark" style="top:3%; left:-5%;">
    <img src="{{ public_path('images/logo pdf.png') }}">
</div>

<div class="watermark" style="top:3%; left:55%;">
    <img src="{{ public_path('images/logo pdf.png') }}">
</div>

<div class="watermark" style="top:33%; left:20%;">
    <img src="{{ public_path('images/logo pdf.png') }}">
</div>

<div class="watermark" style="top:60%; left:-5%;">
    <img src="{{ public_path('images/logo pdf.png') }}">
</div>

<div class="watermark" style="top:60%; left:55%;">
    <img src="{{ public_path('images/logo pdf.png') }}">
</div>

<div class="watermark" style="top:87%; left:20%;">
    <img src="{{ public_path('images/logo pdf.png') }}">
</div>
    {{-- HEADER --}}
    <table>
        <tr>
            <td width="55%">
                <h2>HSB Audio Sound System</h2>

                <div class="company-info" style="margin-top:8px;">
                    RT.01/RW.03, Dusun Sidomulyo<br>
                    Desa Sumberberas, Kec. Muncar<br>
                    Kabupaten Banyuwangi, Jawa Timur 68472
                </div>

                <div class="company-info" style="margin-top:6px;">
                    <strong>Tlp :</strong> 0813-3684-2333
                </div>
            </td>

            <td width="45%" class="text-right invoice-info">
                <div class="title">
                    BUKTI PENYEWAAN
                </div>

                <br>

                No Invoice :
                <strong>
                    INV-{{ str_pad($penyewaan->id, 5, '0', STR_PAD_LEFT) }}
                </strong>

                <br>

                Tanggal :
                {{ date('d M Y') }}
            </td>
        </tr>
    </table>

    <hr>

    {{-- INFORMASI --}}
    <table>
        <tr>

            <td width="50%">
                <div class="section-title">
                    Informasi Penyewa
                </div>

                Nama :
                {{ $penyewaan->nama_penyewa }}

                <br>

                No HP :
                {{ $penyewaan->no_tlp }}
            </td>

            <td width="50%">
                <div class="section-title">
                    Detail Acara
                </div>

                Lokasi :
                {{ $penyewaan->lokasi }}

                <br>

                Periode :
                {{ date('d-m-Y', strtotime($penyewaan->tanggal_mulai)) }}
                s/d
                {{ date('d-m-Y', strtotime($penyewaan->tanggal_selesai)) }}
            </td>

        </tr>
    </table>

    <br>

    {{-- DAFTAR BARANG --}}
    <table border="1">
        <thead>
            <tr>
                <th width="8%">No</th>
                <th>Barang / Paket</th>
                <th width="20%">Jumlah</th>
            </tr>
        </thead>

        <tbody>

            @php $no = 1; @endphp

            {{-- Paket --}}
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
                </tr>
            @endforeach

            {{-- Barang --}}
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
                </tr>
            @endforeach

        </tbody>
    </table>

    <br><br>

    {{-- STATUS & TOTAL --}}
    <table>

        <tr>

            <td width="50%" style="vertical-align:top;">

                @if ($penyewaan->status_pembayaran == 'DP')
                    <span class="badge">
                        DP (BELUM LUNAS)
                    </span>
                @else
                    <span class="badge-success">
                        LUNAS
                    </span>
                @endif

            </td>

            <td width="50%">

                <table class="total-table">

                    <tr>
                        <td>Total Harga</td>

                        <td class="text-right">
                            <strong>
                                Rp {{ number_format($penyewaan->total_harga, 0, ',', '.') }}
                            </strong>
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

    {{-- TANDA TANGAN --}}
    <table width="100%">
        <tr>

            <td width="50%"></td>

            <td width="50%" class="signature">

                Banyuwangi,
                {{ date('d F Y') }}

                <br><br>

                HSB Audio Sound System

                <br><br><br><br><br>

                _______________________

                <br>

                <strong>EDI SISWANTO</strong>

            </td>

        </tr>
    </table>

</body>

</html>
