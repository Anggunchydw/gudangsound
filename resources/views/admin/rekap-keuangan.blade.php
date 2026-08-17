<div class="box box-primary">

    <div class="rekap-keuangan">

        {{-- HEADER LAPORAN --}}
        <div class="rekap-header">



            <h2>Laporan Rekap Keuangan</h2>

        </div>


        {{-- FILTER --}}
        <div class="rekap-filter-card">

            <form method="GET">

                <div class="filter-row">

                    <div class="filter-group">

                        <label>Mulai</label>

                        <input type="date" name="mulai" value="{{ $mulai }}" class="form-control">

                    </div>


                    <div class="filter-group">

                        <label>Sampai</label>

                        <input type="date" name="sampai" value="{{ $sampai }}" class="form-control">

                    </div>


                    <div class="filter-action">

                        <button type="submit" class="btn-filter">

                            <i class="feather icon-filter"></i>

                            Lihat Periode

                        </button>

                    </div>


                    <div class="filter-print">

                        <a href="{{ url('admin/rekap-keuangan/cetak?mulai=' . $mulai . '&sampai=' . $sampai) }}"
                            target="_blank" class="btn-print">

                            <i class="feather icon-printer"></i>

                            Cetak Laporan

                        </a>

                    </div>

                </div>

            </form>

        </div>


        {{-- PERIODE --}}
        @if ($mulai || $sampai)
            <div class="periode-info">

                <strong>Periode:</strong>

                {{ $mulai ? date('d-m-Y', strtotime($mulai)) : '-' }}

                <span>s/d</span>

                {{ $sampai ? date('d-m-Y', strtotime($sampai)) : '-' }}

            </div>
        @endif


        {{-- TABEL LAPORAN --}}
        <div class="rekap-table-wrapper">

            <table class="rekap-table">

                <thead>

                    <tr>

                        <th width="15%">Tanggal</th>

                        <th width="15%">Tipe</th>

                        <th>Keterangan</th>

                        <th width="20%" class="text-right">
                            Masuk
                        </th>

                        <th width="20%" class="text-right">
                            Keluar
                        </th>

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


                            <td class="text-right">

                                @if ($item['masuk'] > 0)
                                    <span class="nominal-masuk">
                                        Rp {{ number_format($item['masuk'], 0, ',', '.') }}
                                    </span>
                                @else
                                    -
                                @endif

                            </td>


                            <td class="text-right">

                                @if ($item['keluar'] > 0)
                                    <span class="nominal-keluar">
                                        Rp {{ number_format($item['keluar'], 0, ',', '.') }}
                                    </span>
                                @else
                                    -
                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="empty-data">

                                Tidak ada data pada periode yang dipilih.

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- RINGKASAN --}}
        <div class="summary-wrapper">

            <table class="summary-table">

                <tr>

                    <td>Total Pemasukan</td>

                    <td class="text-right nominal-masuk">

                        Rp {{ number_format($totalMasuk, 0, ',', '.') }}

                    </td>

                </tr>


                <tr>

                    <td>Total Pengeluaran</td>

                    <td class="text-right nominal-keluar">

                        Rp {{ number_format($totalKeluar, 0, ',', '.') }}

                    </td>

                </tr>


                <tr class="saldo-row">

                    <td>
                        Saldo Bersih
                    </td>

                    <td class="text-right">

                        Rp {{ number_format($totalMasuk - $totalKeluar, 0, ',', '.') }}

                    </td>

                </tr>

            </table>

        </div>

    </div>

</div>
