<style>
    .detail-wrapper {
        max-width: 1200px;
        margin: auto;
    }

    .detail-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
        margin-bottom: 25px;
        overflow: hidden;
    }

    .detail-header {
        padding: 15px 20px;
        font-size: 18px;
        font-weight: 600;
        border-bottom: 1px solid #eee;
        background: #fafafa;
    }

    .detail-body {
        padding: 28px;
    }

    /* ===========================
   INFORMASI BARANG
=========================== */

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 80px;
    }

    .info-column {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-title {
        color: #9ca3af;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 5px;
    }

    .info-value {
        color: #222;
        font-size: 18px;
        font-weight: 600;
    }

    .badge-status {
        display: inline-block;
        width: fit-content;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .aktif {
        background: #dcfce7;
        color: #15803d;
    }

    .nonaktif {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* ===========================
   RIWAYAT
=========================== */

    .table-riwayat {
        width: 100%;
        border-collapse: collapse;
    }

    .table-riwayat thead {
        background: #f5f6fa;
    }

    .table-riwayat th,
    .table-riwayat td {
        padding: 14px;
        border-bottom: 1px solid #eee;
    }

    .baik {
        background: #dcfce7;
        color: #15803d;
    }

    .rusak {
        background: #fee2e2;
        color: #b91c1c;
    }

    .hilang {
        background: #e5e7eb;
        color: #374151;
    }
</style>

<div class="detail-wrapper">

    <div class="detail-card">

        <div class="detail-header">
            Informasi Barang
        </div>

        <div class="detail-body">

            <div class="info-grid">

                {{-- KOLOM KIRI --}}
                <div class="info-column">

                    <div class="info-item">
                        <span class="info-title">Nama Barang</span>
                        <span class="info-value">{{ $barang->nama_barang }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-title">Kategori</span>
                        <span class="info-value">{{ ucfirst($barang->Kategori) }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-title">Satuan</span>
                        <span class="info-value">{{ $barang->satuan }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-title">Jumlah Total</span>
                        <span class="info-value">{{ $barang->jumlah_total }}</span>
                    </div>

                </div>

                {{-- KOLOM KANAN --}}
                <div class="info-column">

                    <div class="info-item">
                        <span class="info-title">Stok Hari Ini</span>
                        <span class="info-value">{{ $barang->stok_hari_ini }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-title">Dipakai Hari Ini</span>
                        <span class="info-value">{{ $barang->dipakai_hari_ini }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-title">Status</span>

                        <span class="badge-status {{ $barang->status }}">
                            {{ ucfirst($barang->status) }}
                        </span>

                    </div>

                    <div class="info-item">
                        <span class="info-title">Keterangan</span>
                        <span class="info-value">
                            {{ $barang->keterangan ?: '-' }}
                        </span>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="detail-card">

        <div class="detail-header">
            Riwayat Kondisi Barang
        </div>

        <div class="detail-body">

            <table class="table-riwayat">

                <thead>

                    <tr>

                        <th>Tanggal</th>

                        <th>Penyewa</th>

                        <th>Qty</th>

                        <th>Sebelum</th>

                        <th>Sesudah</th>

                        <th>Catatan</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($barang->kondisiBarang as $item)
                        <tr>

                            <td>

                                @if ($item->penugasan && $item->penugasan->penyewaan)
                                    {{ \Carbon\Carbon::parse($item->penugasan->penyewaan->tanggal_mulai)->format('d-m-Y') }}
                                @else
                                    -
                                @endif

                            </td>

                            <td>

                                {{ optional(optional($item->penugasan)->penyewaan)->nama_penyewa ?? '-' }}

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
