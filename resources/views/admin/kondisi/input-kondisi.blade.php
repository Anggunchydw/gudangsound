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

                <td>

                    {{ $penugasan->penyewaan->lokasi }}

                </td>

            </tr>

        </table>

        <br>

        <form method="POST" action="{{ admin_url('kondisi-barang/' . $penugasan->id . '/simpan') }}">

            @csrf

            <table class="table table-striped table-bordered">

                <thead>

                    <tr>

                        <th>Barang</th>

                        <th width="70">Qty</th>

                        <th width="240">Kondisi Sebelum</th>

                        <th width="240">Kondisi Sesudah</th>

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

                            <td>

                                @foreach (['baik', 'rusak', 'hilang'] as $k)
                                    <label>

                                        <input type="radio" name="barang[{{ $i }}][kondisi_sesudah]"
                                            value="{{ $k }}"
                                            {{ $item['kondisi_sesudah'] == $k ? 'checked' : '' }}>

                                        {{ ucfirst($k) }}

                                    </label>

                                    <br>
                                @endforeach

                            </td>

                            <td>

                                <textarea class="form-control" name="barang[{{ $i }}][catatan]" rows="2">{{ $item['catatan'] }}</textarea>

                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>

            <button class="btn btn-primary">

                Simpan

            </button>

        </form>

    </div>

</div>
