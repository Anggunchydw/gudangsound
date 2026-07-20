<div class="box">

    <div class="box-header">
        <h3>Input Kondisi Barang</h3>
    </div>

    <div class="box-body">

        <table class="table table-bordered">

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

        </table>

        <br>

        <form method="POST" action="{{ admin_url('kondisi-barang/' . $penugasan->id . '/simpan') }}">

            @csrf

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>Barang</th>
                        <th width="80">Qty</th>
                        <th width="180">Kondisi Sebelum</th>
                        <th width="180">Kondisi Sesudah</th>
                        <th width="170">Jumlah Rusak / Hilang</th>
                        <th>Catatan</th>
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

                            {{-- Kondisi Sebelum --}}

                            <td>

                                @foreach (['baik', 'rusak', 'hilang'] as $k)
                                    <label>

                                        <input type="radio" name="barang[{{ $i }}][kondisi_sebelum]"
                                            value="{{ $k }}"
                                            {{ $item['kondisi_sebelum'] == $k ? 'checked' : '' }}>

                                        {{ ucfirst($k) }}

                                    </label>

                                    <br>
                                @endforeach

                            </td>

                            {{-- Kondisi Sesudah --}}

                            <td>

                                @foreach (['baik', 'rusak', 'hilang'] as $k)
                                    <label>

                                        <input type="radio" class="kondisi-radio" data-row="{{ $i }}"
                                            name="barang[{{ $i }}][kondisi_sesudah]"
                                            value="{{ $k }}"
                                            {{ $item['kondisi_sesudah'] == $k ? 'checked' : '' }}>

                                        {{ ucfirst($k) }}

                                    </label>

                                    <br>
                                @endforeach

                            </td>

                            {{-- Jumlah Bermasalah --}}

                            <td>

                                <input type="number" id="jumlah-{{ $i }}" class="form-control"
                                    name="barang[{{ $i }}][jumlah_bermasalah]" min="0"
                                    max="{{ $item['jumlah'] }}" value="{{ $item['jumlah_bermasalah'] }}">

                                <small class="text-muted">
                                    Isi 0 jika semua barang baik.
                                </small>

                            </td>

                            {{-- Catatan --}}

                            <td>

                                <textarea class="form-control" rows="2" name="barang[{{ $i }}][catatan]">{{ $item['catatan'] }}</textarea>

                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>

            <button class="btn btn-primary">

                <i class="feather icon-save"></i>

                Simpan

            </button>

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

        document.querySelectorAll('.kondisi-radio').forEach(function(radio) {

            radio.addEventListener('change', function() {

                toggleJumlah(
                    this.dataset.row,
                    this.value
                );

            });

        });

        document.querySelectorAll('.kondisi-radio:checked').forEach(function(radio) {

            toggleJumlah(
                radio.dataset.row,
                radio.value
            );

        });

    });
</script>
