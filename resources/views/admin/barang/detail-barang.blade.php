<div class="detail-wrapper">

    {{-- =======================
        INFORMASI BARANG
    ======================== --}}
    <div class="detail-card">

        <div class="detail-header">
            Informasi Barang
        </div>

        <div class="detail-body">

            <div class="barang-info-grid">

                {{-- KOLOM KIRI --}}
                <div class="barang-info-column">

                    <div class="barang-info-item">
                        <div class="barang-info-title">
                            Nama Barang
                        </div>

                        <div class="barang-info-value">
                            {{ $barang->nama_barang }}
                        </div>
                    </div>

                    <div class="barang-info-item">
                        <div class="barang-info-title">
                            Kategori
                        </div>

                        <div class="barang-info-value">
                            {{ ucfirst($barang->Kategori) }}
                        </div>
                    </div>

                    <div class="barang-info-item">
                        <div class="barang-info-title">
                            Satuan
                        </div>

                        <div class="barang-info-value">
                            {{ ucfirst($barang->satuan) }}
                        </div>
                    </div>

                    <div class="barang-info-item">
                        <div class="barang-info-title">
                            Jumlah Total
                        </div>

                        <div class="barang-info-value">
                            {{ $barang->jumlah_total }}
                        </div>
                    </div>

                </div>

                {{-- KOLOM KANAN --}}
                <div class="barang-info-column">

                    <div class="barang-info-item">

                        <div class="barang-info-title">
                            Stok Hari Ini
                        </div>

                        <div class="barang-info-value">
                            {{ $barang->stok_hari_ini }}
                        </div>

                    </div>

                    <div class="barang-info-item">

                        <div class="barang-info-title">
                            Dipakai Hari Ini
                        </div>

                        <div class="barang-info-value">
                            {{ $barang->dipakai_hari_ini }}
                        </div>

                    </div>

                    <div class="barang-info-item">

                        <div class="barang-info-title">
                            Status
                        </div>

                        <span class="badge-status {{ $barang->status }}">
                            {{ ucfirst($barang->status) }}
                        </span>

                    </div>

                    <div class="barang-info-item">

                        <div class="barang-info-title">
                            Keterangan
                        </div>

                        <div class="barang-info-value">
                            {{ $barang->keterangan ?: '-' }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- RIWAYAT KONDISI --}}


    <div class="detail-card">

        <div class="detail-header">
            Riwayat Kondisi
        </div>

        <div class="detail-body">

            <div class="table-responsive">

                <table class="table-riwayat">

                    <thead>

                        <tr>

                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Sebelum</th>
                            <th>Sesudah</th>
                            <th>Catatan</th>
                            <th>Pegawai</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($barang->kondisiBarang as $item)
                            <tr>

                                <td>
                                    @if ($item->penugasan && $item->penugasan->penyewaan)
                                        {{ \Carbon\Carbon::parse($item->penugasan->penyewaan->tanggal_mulai)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    {{ $item->jumlah_barang }}
                                </td>

                                <td>
                                    <span class="badge-status {{ $item->kondisi_sebelum }}">
                                        {{ ucfirst($item->kondisi_sebelum) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge-status {{ $item->kondisi_sesudah }}">
                                        {{ ucfirst($item->kondisi_sesudah) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $item->catatan ?: '-' }}
                                </td>

                                <td>
                                    @if ($item->penugasan && $item->penugasan->pegawai->count())
                                        {{ $item->penugasan->pegawai->pluck('name')->join(', ') }}
                                    @else
                                        -
                                    @endif
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="empty">

                                    Belum ada riwayat kondisi barang.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>
