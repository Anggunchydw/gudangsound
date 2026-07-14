<div id="calendar"></div>
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

            height: 700,
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

                $('#m-paket').html(
                    data.extendedProps.paket ?? '-'
                );

                $('#m-pegawai').html('-');

                $('#eventModal').modal('show');

            }

        });

        calendar.render();

    });
</script>
