<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
            color: #333;
            margin: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h2 {
            margin: 0;
            color: #27426e;
            font-size: 22px;
        }

        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 13px;
        }

        .periode {
            margin-bottom: 20px;
            padding: 10px;
            background: #f7f9fc;
            border-left: 4px solid #27426e;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .laporan th {
            background: #27426e;
            color: white;
            padding: 10px;
            font-size: 12px;
            text-align: left;
        }

        .laporan td {
            padding: 9px;
            border-bottom: 1px solid #dddddd;
        }

        .text-right {
            text-align: right;
        }

        .masuk {
            color: #198754;
            font-weight: bold;
        }

        .keluar {
            color: #dc3545;
            font-weight: bold;
        }

        .summary {
            margin-top: 30px;
            width: 45%;
            float: right;
            border: 1px solid #ddd;
        }

        .summary td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .summary tr:last-child {
            background: #27426e;
            color: white;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #888;
        }
    </style>

</head>

<body>

    <div class="header">

        <h2>HSB AUDIO SOUND SYSTEM</h2>

        <p>Laporan Rekap Keuangan</p>

    </div>

    @if ($mulai || $sampai)
        <div class="periode">

            <strong>Periode :</strong>

            {{ $mulai ?: '-' }}

            s/d

            {{ $sampai ?: '-' }}

        </div>
    @endif

    <table class="laporan">

        <thead>

            <tr>

                <th width="18%">Tanggal</th>
                <th width="18%">Tipe</th>
                <th>Keterangan</th>
                <th width="18%" class="text-right">Masuk</th>
                <th width="18%" class="text-right">Keluar</th>

            </tr>

        </thead>

        <tbody>

            @foreach ($data as $item)
                <tr>

                    <td>{{ date('d-m-Y', strtotime($item['tanggal'])) }}</td>

                    <td>{{ $item['tipe'] }}</td>

                    <td>{{ $item['keterangan'] }}</td>

                    <td class="text-right">

                        @if ($item['masuk'])
                            <span class="masuk">

                                Rp {{ number_format($item['masuk'], 0, ',', '.') }}

                            </span>
                        @else
                            -
                        @endif

                    </td>

                    <td class="text-right">

                        @if ($item['keluar'])
                            <span class="keluar">

                                Rp {{ number_format($item['keluar'], 0, ',', '.') }}

                            </span>
                        @else
                            -
                        @endif

                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>

    <table class="summary">

        <tr>

            <td>Total Pemasukan</td>

            <td class="text-right">

                Rp {{ number_format($totalMasuk, 0, ',', '.') }}

            </td>

        </tr>

        <tr>

            <td>Total Pengeluaran</td>

            <td class="text-right">

                Rp {{ number_format($totalKeluar, 0, ',', '.') }}

            </td>

        </tr>

        <tr>

            <td>Saldo Bersih</td>

            <td class="text-right">

                Rp {{ number_format($totalMasuk - $totalKeluar, 0, ',', '.') }}

            </td>

        </tr>

    </table>

    <div class="footer">

        Dicetak pada {{ date('d-m-Y H:i') }}

    </div>

</body>

</html>
