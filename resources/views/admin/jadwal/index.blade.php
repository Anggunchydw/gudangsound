<div class="jadwal-header">

    @php
        $user = auth('admin')->user();
    @endphp

    @if ($user->isRole('administrator') || $user->isRole('pemilik'))
        <a href="{{ admin_url('penyewaan/create') }}" class="btn btn-primary">

            <i class="feather icon-plus"></i>

            Tambah Penyewaan

        </a>
    @endif

</div>

<div class="jadwal-wrapper">

    {{-- =======================
        KALENDER
    ======================== --}}
    <div class="calendar-section">

        <div id="calendar"></div>

    </div>

    {{-- =======================
        SIDEBAR
    ======================== --}}
    <div class="sidebar-section">

        {{-- Ringkasan --}}
        <div class="sidebar-card">

            <h5>Ringkasan Bulan Ini</h5>

            <div class="summary-grid">

                <div>

                    <h2>{{ $totalAcara }}</h2>

                    <span>Total Acara</span>

                </div>

                <div>

                    <h2>{{ $belumLunas }}</h2>

                    <span>Belum Lunas</span>

                </div>

            </div>

        </div>

        {{-- Agenda Hari Ini --}}
        <div class="sidebar-card">

            <h5>Agenda Hari Ini</h5>

            <div class="agenda-list">

                @forelse($agendaHariIni as $item)
                    <div class="agenda-card">

                        <div class="agenda-date-box">

                            <div class="day">
                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d') }}
                            </div>

                            <div class="month">
                                {{ strtoupper(\Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('M')) }}
                            </div>

                        </div>

                        <div class="agenda-info">

                            <div class="agenda-title">
                                {{ $item->nama_penyewa }}
                            </div>

                            <div class="agenda-detail">

                                <i class="feather icon-map-pin"></i>

                                {{ $item->lokasi }}

                            </div>

                            <div class="agenda-detail">

                                <i class="feather icon-package"></i>

                                {{ $item->paket_barang }}

                            </div>

                            <span class="badge badge-{{ $item->status_pembayaran == 'Lunas' ? 'success' : 'warning' }}">

                                {{ $item->status_pembayaran }}

                            </span>

                        </div>

                    </div>

                @empty

                    <p class="text-muted">

                        Tidak ada agenda hari ini.

                    </p>
                @endforelse

            </div>

        </div>

        {{-- Akan Datang --}}
        <div class="sidebar-card">

            <h5>Akan Datang</h5>

            <div class="agenda-list">

                @forelse($akanDatang as $item)
                    <div class="agenda-card">

                        <div class="agenda-date-box">

                            <div class="day">
                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d') }}
                            </div>

                            <div class="month">
                                {{ strtoupper(\Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('M')) }}
                            </div>

                        </div>

                        <div class="agenda-info">

                            <div class="agenda-title">
                                {{ $item->nama_penyewa }}
                            </div>

                            <div class="agenda-detail">

                                <i class="feather icon-map-pin"></i>

                                {{ $item->lokasi }}

                            </div>

                            <div class="agenda-detail">

                                <i class="feather icon-package"></i>

                                {{ $item->paket_barang }}

                            </div>

                            <span
                                class="badge badge-{{ $item->status_pembayaran == 'Lunas' ? 'success' : 'warning' }}">

                                {{ $item->status_pembayaran }}

                            </span>

                        </div>

                    </div>

                @empty

                    <p class="text-muted">

                        Tidak ada agenda mendatang.

                    </p>
                @endforelse

            </div>

        </div>

    </div>

</div>

{{-- =======================
    MODAL DETAIL
======================== --}}
<div class="modal fade" id="eventModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-body">

                <div class="event-header">

                    <div class="event-title">

                        <h4 id="m-penyewa"></h4>

                        <p id="m-tanggal"></p>

                    </div>

                    <button type="button" class="event-close" data-dismiss="modal">

                        <i class="feather icon-x"></i>

                    </button>

                </div>

                <hr>

                <div class="detail-item">

                    <div class="detail-icon">

                        <i class="feather icon-check-circle"></i>

                    </div>

                    <div class="detail-content">

                        <small>STATUS PEMBAYARAN</small>

                        <div id="m-status"></div>

                    </div>

                </div>

                <div class="detail-item">

                    <div class="detail-icon">

                        <i class="feather icon-package"></i>

                    </div>

                    <div class="detail-content">

                        <small>PAKET / BARANG DISEWA</small>

                        <div id="m-paket"></div>

                    </div>

                </div>

                <div class="detail-item">

                    <div class="detail-icon">

                        <i class="feather icon-map-pin"></i>

                    </div>

                    <div class="detail-content">

                        <small>LOKASI</small>

                        <div id="m-lokasi"></div>

                    </div>

                </div>

                <div class="detail-item">

                    <div class="detail-icon">

                        <i class="feather icon-users"></i>

                    </div>
                    <div class="detail-content">

                        <small>KETERANGAN</small>

                        <div id="m-keterangan"></div>

                    </div>

                </div>

                <div class="detail-item">

                    <div class="detail-icon">

                        <i class="feather icon-file-text"></i>

                    </div>

                    <div class="detail-content">

                        <small>PEGAWAI BERTUGAS</small>

                        <div id="m-pegawai">-</div>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-primary" data-dismiss="modal">

                    Tutup

                </button>

            </div>

        </div>

    </div>

</div>


<script>
    Dcat.ready(function() {

        let calendarEl = document.getElementById('calendar');

        let calendar = new FullCalendar.Calendar(calendarEl, {

            locale: 'id',
            initialView: 'dayGridMonth',
            contentHeight: 'auto',
            expandRows: true,
            handleWindowResize: true,
            windowResize: function() {
                calendar.updateSize();
            },
            events: "{{ admin_url('Jadwal-Acara/events') }}",

            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },

            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                week: 'Minggu',
                day: 'Hari'
            },

            eventClick: function(info) {

                let data = info.event;

                $('#m-penyewa').text(data.title);

                $('#m-tanggal').text(
                    data.extendedProps.mulai +
                    " s/d " +
                    data.extendedProps.selesai
                );

                if (data.extendedProps.status == "Lunas") {

                    $('#m-status').html(
                        '<span class="status-badge status-lunas">Lunas</span>'
                    );

                } else {

                    $('#m-status').html(
                        '<span class="status-badge status-dp">DP</span>'
                    );

                }

                $('#m-lokasi').text(data.extendedProps.lokasi);
                $('#m-keterangan').text(
                    data.extendedProps.keterangan || '-'
                );
                $('#m-paket').html(
                    data.extendedProps.paket ?? '-'
                );
                $('#m-pegawai').html(data.extendedProps.pegawai || '-');
                $('#eventModal').modal('show');

            }

        });

        calendar.render();

    });
</script>
