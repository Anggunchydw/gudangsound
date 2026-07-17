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

                            <a href="{{ admin_url('kondisi-barang/' . $item->id . '/input') }}"
                                class="btn btn-primary btn-sm">

                                Input Kondisi

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="4" align="center">
                            Tidak ada penugasan.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>
