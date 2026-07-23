<style>
    .kondisi-barang-page .label-success {
        background: #28a745;
        color: #fff;
        padding: 5px 10px;
        border-radius: 20px;
    }

    .kondisi-barang-page .label-warning {
        background: #ffc107;
        color: #212529;
        padding: 5px 10px;
        border-radius: 20px;
    }

    .kondisi-barang-page .label-danger {
        background: #dc3545;
        color: #fff;
        padding: 5px 10px;
        border-radius: 20px;
    }

    /* Responsive Table */
    .kondisi-barang-page .table-responsive-custom {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .kondisi-barang-page .penugasan-table {
        width: 100%;
        min-width: 700px;
    }

    .kondisi-barang-page .penugasan-table th,
    .kondisi-barang-page .penugasan-table td {
        vertical-align: middle;
        white-space: nowrap;
    }

    @media (max-width:768px) {

        .kondisi-barang-page .box {
            border-radius: 10px;
        }

        .kondisi-barang-page .box-header {
            padding: 15px;
        }

        .kondisi-barang-page .box-header h3 {
            font-size: 18px;
        }

        .kondisi-barang-page .box-body {
            padding: 12px;
        }

        .kondisi-barang-page .penugasan-table {
            min-width: 650px;
            font-size: 13px;
        }

        .kondisi-barang-page .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .kondisi-barang-page .label-success,
        .kondisi-barang-page .label-warning,
        .kondisi-barang-page .label-danger {
            display: inline-block;
            white-space: nowrap;
            font-size: 12px;
            padding: 5px 10px;
        }
    }
</style>

<div class="kondisi-barang-page">

    <div class="box">

        <div class="box-header">
            <h3>Daftar Penugasan Saya</h3>
        </div>

        <div class="box-body">

            <div class="table-responsive-custom">

                <table class="table table-bordered penugasan-table">

                    <thead>
                        <tr>
                            <th>Penyewa</th>
                            <th>Tanggal</th>
                            <th>Tim</th>
                            <th>Status</th>
                            <th width="160">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($penugasan as $item)

                            <tr>

                                <td>
                                    {{ $item->penyewaan->nama_penyewa }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($item->penyewaan->tanggal_mulai)->format('d-m-Y') }}
                                </td>

                                <td>
                                    {{ $item->tim }}
                                </td>

                                <td>

                                    @if($item->status_input == 'belum')

                                        <span class="label label-warning">
                                            Belum Diinput
                                        </span>

                                    @elseif($item->status_input == 'belum_lengkap')

                                        <span class="label label-danger">
                                            Input Belum Lengkap
                                        </span>

                                    @else

                                        <span class="label label-success">
                                            Sudah Lengkap
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    <a href="{{ admin_url('kondisi-barang/'.$item->id.'/input') }}"
                                        class="btn btn-primary btn-sm">

                                        @if($item->status_input == 'belum')

                                            Input Kondisi

                                        @elseif($item->status_input == 'belum_lengkap')

                                            Lanjutkan Input

                                        @else

                                            Lihat / Edit

                                        @endif

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="text-center">
                                    Tidak ada penugasan.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>
