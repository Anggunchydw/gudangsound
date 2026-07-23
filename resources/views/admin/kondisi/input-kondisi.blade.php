<div class="kondisi-wrapper box">

    <div class="box-header">
        <h3>Input Kondisi Barang</h3>
    </div>

    <div class="box-body">

        {{-- Informasi Penyewaan --}}
        <div class=" info-table">
            <table class="table ">
                <tbody>
                    <tr>
                        <th width="180">Penyewa</th>
                        <td>{{ $penugasan->penyewaan->nama_penyewa }}</td>
                    </tr>

                    <tr>
                        <th>Tanggal</th>
                        <td>
                            {{ \Carbon\Carbon::parse($penugasan->penyewaan->tanggal_mulai)->format('d-m-Y') }}
                            s/d
                            {{ \Carbon\Carbon::parse($penugasan->penyewaan->tanggal_selesai)->format('d-m-Y') }}
                        </td>
                    </tr>

                    <tr>
                        <th>Lokasi</th>
                        <td>{{ $penugasan->penyewaan->lokasi }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <form method="POST" action="{{ admin_url('kondisi-barang/' . $penugasan->id . '/simpan') }}">

            @csrf

            <div class=" kondisi-table">

                <table class="table table-bordered table-hover">

                    <thead>

                        <tr>
                            <th class="barang-col">Barang</th>
                            <th class="qty-col">Qty</th>
                            <th class="kondisi-col">Kondisi Sebelum</th>
                            <th class="kondisi-col">Kondisi Sesudah</th>
                            <th class="jumlah-col">Jumlah Rusak / Hilang</th>
                            <th class="catatan-col">Catatan</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($barang as $i => $item)
                            <tr>

                                <td>

                                    {{ $item['nama'] }}

                                    <input type="hidden" name="barang[{{ $i }}][barang_id]"
                                        value="{{ $item['id'] }}">

                                </td>

                                <td>

                                    {{ $item['jumlah'] }}

                                    <input type="hidden" name="barang[{{ $i }}][jumlah_barang]"
                                        value="{{ $item['jumlah'] }}">

                                </td>

                                <td>

                                    <div class="radio-group">

                                        @foreach (['baik', 'rusak', 'hilang'] as $k)
                                            <label>

                                                <input type="radio"
                                                    name="barang[{{ $i }}][kondisi_sebelum]"
                                                    value="{{ $k }}"
                                                    {{ $item['kondisi_sebelum'] == $k ? 'checked' : '' }}>

                                                {{ ucfirst($k) }}

                                            </label>
                                        @endforeach

                                    </div>

                                </td>

                                <td>

                                    <div class="radio-group">

                                        @foreach (['baik', 'rusak', 'hilang'] as $k)
                                            <label>

                                                <input type="radio" class="kondisi-radio"
                                                    data-row="{{ $i }}"
                                                    name="barang[{{ $i }}][kondisi_sesudah]"
                                                    value="{{ $k }}"
                                                    {{ $item['kondisi_sesudah'] == $k ? 'checked' : '' }}>

                                                {{ ucfirst($k) }}

                                            </label>
                                        @endforeach

                                    </div>

                                </td>

                                <td>

                                    <input type="number" id="jumlah-{{ $i }}" class="form-control"
                                        name="barang[{{ $i }}][jumlah_bermasalah]" min="0"
                                        max="{{ $item['jumlah'] }}" value="{{ $item['jumlah_bermasalah'] }}">

                                    <small class="text-muted">
                                        Isi 0 jika semua barang baik.
                                    </small>

                                </td>

                                <td>

                                    <textarea rows="2" class="form-control" name="barang[{{ $i }}][catatan]">{{ $item['catatan'] }}</textarea>

                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>

            </div>

            <div class="btn-area">

                <button class="btn btn-primary">

                    <i class="feather icon-save"></i>

                    Simpan

                </button>

            </div>

        </form>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        function toggleJumlah(row, kondisi) {

            let input = document.getElementById('jumlah-' + row);

            if (kondisi === 'baik') {

                input.value = 0;
                input.readOnly = true;

            } else {

                input.readOnly = false;

            }
        }

        document.querySelectorAll('.kondisi-radio').forEach(function(r) {

            r.addEventListener('change', function() {

                toggleJumlah(
                    this.dataset.row,
                    this.value
                );

            });

        });

        document.querySelectorAll('.kondisi-radio:checked').forEach(function(r) {

            toggleJumlah(
                r.dataset.row,
                r.value
            );

        });

    });
</script>
