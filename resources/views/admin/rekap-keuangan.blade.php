<div class="box box-primary">

    <div class="box-body">

        <form method="GET">

            <div class="row rekap-filter">

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

                <div class="col-md-4 text-right">

                    <a href="{{ url('admin/rekap-keuangan/cetak?mulai=' . $mulai . '&sampai=' . $sampai) }}" target="_blank"
                        class="btn btn-primary">

                        <i class="feather icon-printer"></i>

                        Cetak Laporan

                    </a>

                </div>

            </div>

        </form>


        <div class="rekap-table-wrapper">

            <table class="table table-hover rekap-table">

                <thead>

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
                                    <span class="badge-pemasukan">

                                        Pemasukan

                                    </span>
                                @else
                                    <span class="badge-pengeluaran">

                                        Pengeluaran

                                    </span>
                                @endif

                            </td>

                            <td>

                                {{ $item['keterangan'] }}

                            </td>

                            <td>

                                @if ($item['masuk'] > 0)
                                    <span class="text-masuk">

                                        Rp {{ number_format($item['masuk'], 0, ',', '.') }}

                                    </span>
                                @else
                                    -
                                @endif

                            </td>

                            <td>

                                @if ($item['keluar'] > 0)
                                    <span class="text-keluar">

                                        Rp {{ number_format($item['keluar'], 0, ',', '.') }}

                                    </span>
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

        <table class="table rekap-footer">

            <tr>

                <td width="250">

                    Total Pemasukan

                </td>

                <td>

                    <span class="text-masuk">

                        Rp {{ number_format($totalMasuk, 0, ',', '.') }}

                    </span>

                </td>

            </tr>

            <tr>

                <td>

                    Total Pengeluaran

                </td>

                <td>

                    <span class="text-keluar">

                        Rp {{ number_format($totalKeluar, 0, ',', '.') }}

                    </span>

                </td>

            </tr>

            <tr>

                <td>

                    <strong>Saldo Bersih</strong>

                </td>

                <td>

                    <span class="text-saldo">

                        Rp {{ number_format($totalMasuk - $totalKeluar, 0, ',', '.') }}

                    </span>

                </td>

            </tr>

        </table>

    </div>

</div>
