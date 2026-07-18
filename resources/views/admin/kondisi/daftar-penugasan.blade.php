<style>
    .label-success {
        background: #28a745;
        color: #fff;
        padding: 5px 10px;
        border-radius: 20px;
    }

    .label-warning {
        background: #ffc107;
        color: #212529;
        padding: 5px 10px;
        border-radius: 20px;
    }

    .label-danger {
        background: #dc3545;
        color: #fff;
        padding: 5px 10px;
        border-radius: 20px;
    }
</style>

<div class="box">

    <div class="box-header">
        <h3>Daftar Penugasan Saya</h3>
    </div>

    <div class="box-body">

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th>Penyewa</th>
                    <th>Tanggal</th>
                    <th>Tim</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                @forelse($penugasan as $item)
                    <tr>

                        <td>{{ $item->penyewaan->nama_penyewa }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($item->penyewaan->tanggal_mulai)->format('d-m-Y') }}
                        </td>

                        <td>{{ $item->tim }}</td>

                        <td>

                            @if ($item->status_input == 'belum')
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

                            <a href="{{ admin_url('kondisi-barang/' . $item->id . '/input') }}"
                                class="btn btn-primary btn-sm">

                                @if ($item->status_input == 'belum')
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

                        <td colspan="5" align="center">
                            Tidak ada penugasan.
                        </td>

                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>
