<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 11px;
            line-height: 1.6;
            color: #374151;
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
            background: #EDF7EE;
            color: #2E7D32;
            font-weight: bold;
            text-align: center;
            border: 1px solid #D7EAD9;
        }

        td {
            border-color: #D7EAD9;
        }

        .border {
            border: 1px solid #D7EAD9;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 1px;
            color: #2E7D32;
        }

        h2 {
            margin: 0;
            font-size: 24px;
            color: #2E7D32;
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
            border-top: 2px solid #D7EAD9;
        }

        /* ======================
       STATUS
    ======================= */

        .badge {
            display: inline-block;
            padding: 6px 14px;
            background: #FFF8E6;
            border: 1px solid #F59E0B;
            color: #B45309;
            font-weight: bold;
            border-radius: 4px;
        }

        .badge-success {
            display: inline-block;
            padding: 6px 14px;
            background: #EDF7EE;
            border: 1px solid #2E7D32;
            color: #2E7D32;
            font-weight: bold;
            border-radius: 4px;
        }

        /* ======================
       WARNA NOMINAL
    ======================= */

        .green {
            color: #2E7D32;
            font-weight: bold;
        }

        .red {
            color: #DC2626;
            font-weight: bold;
        }

        /* ======================
       INFORMASI
    ======================= */

        .company-info {
            font-size: 11px;
            color: #4B5563;
        }

        .invoice-info {
            font-size: 11px;
            color: #4B5563;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 6px;
            color: #2E7D32;
            border-left: 4px solid #2E7D32;
            padding-left: 8px;
        }

        .total-table {
            border: 1px solid #D7EAD9;
        }

        .total-table td {
            padding: 8px;
            border-bottom: 1px solid #E5F1E6;
        }

        .total-table tr:last-child td {
            border-bottom: none;
        }

        .signature {
            text-align: center;
            line-height: 1.8;
        }

        /* ======================
       TABEL BARANG
    ======================= */

        table[border="1"] {
            border: 1px solid #D7EAD9;
        }

        table[border="1"] td,
        table[border="1"] th {
            border: 1px solid #D7EAD9;
        }

        table[border="1"] tbody tr:nth-child(even) {
            background: #FAFCFA;
        }

        .watermark {
            position: fixed;
            z-index: -1000;
            opacity: .05;
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
