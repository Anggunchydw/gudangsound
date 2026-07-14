<div class="box box-primary">

    <div class="box-header with-border">
        <h3 class="box-title">
            Rekap Keuangan
        </h3>
    </div>

    <div class="box-body">

        <form method="GET">

            <div class="row" style="margin-bottom:20px">

                <div class="col-md-3">
                    <input type="date" name="mulai" value="{{ $mulai }}" class="form-control">
                </div>

                <div class="col-md-3">
                    <input type="date" name="sampai" value="{{ $sampai }}" class="form-control">
                </div>

                <div class="col-md-2">

                    <button class="btn btn-primary">
                        <i class="feather icon-filter"></i>
                        Lihat Periode
                    </button>

                </div>

            </div>

        </form>

        <div class="table-responsive">

             <table class="table table-hover" style="border:none;">
                <thead style="background:#f5f5f5">

                    <tr>

                        <th width="150">Tanggal</th>
                        <th width="150">Tipe</th>
                        <th>Keterangan</th>
                        <th width="180">Masuk</th>
                        <th width="180">Keluar</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($data as $item)
                        <tr>

                            <td>
                                {{ date('d-m-Y', strtotime($item['tanggal'])) }}
                            </td>

                            <td>

                                @if ($item['tipe'] == 'Pemasukan')
                                    <span
                                        style="
                display:inline-block;
                padding:4px 10px;
                background:#d4edda;
                color:#155724;
                border-radius:15px;
                font-weight:600;
                font-size:12px;
            ">
                                        Pemasukan
                                    </span>
                                @else
                                    <span
                                        style="
                display:inline-block;
                padding:4px 10px;
                background:#f8d7da;
                color:#721c24;
                border-radius:15px;
                font-weight:600;
                font-size:12px;
            ">
                                        Pengeluaran
                                    </span>
                                @endif

                            </td>

                            <td>
                                {{ $item['keterangan'] }}
                            </td>

                            <td>

                                @if ($item['masuk'] > 0)
                                    <strong style="color:#28a745">
                                        Rp {{ number_format($item['masuk'], 0, ',', '.') }}
                                    </strong>
                                @else
                                    -
                                @endif

                            </td>

                            <td>

                                @if ($item['keluar'] > 0)
                                    <strong style="color:#dc3545">
                                        Rp {{ number_format($item['keluar'], 0, ',', '.') }}
                                    </strong>
                                @else
                                    -
                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="text-center">
                                Tidak ada data
                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div class="box-footer">

        <table class="table">

            <tr>

                <td width="250">
                    Total Pemasukan
                </td>

                <td>

                    <strong style="color:green">

                        Rp {{ number_format($totalMasuk, 0, ',', '.') }}

                    </strong>

                </td>

            </tr>

            <tr>

                <td>

                    Total Pengeluaran

                </td>

                <td>

                    <strong style="color:red">

                        Rp {{ number_format($totalKeluar, 0, ',', '.') }}

                    </strong>

                </td>

            </tr>

            <tr>

                <td>

                    <strong>Saldo Bersih</strong>

                </td>

                <td>

                    <strong style="font-size:18px;color:#0b5394">

                        Rp {{ number_format($totalMasuk - $totalKeluar, 0, ',', '.') }}

                    </strong>

                </td>

            </tr>

        </table>

    </div>

</div>
