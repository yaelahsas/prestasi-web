<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Jurnal - Sistem Prestasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = { theme: { extend: { colors: {
            'school-green': '#22c55e', 'school-dark-green': '#16a34a',
        }}}}
    </script>
    <style>
        .nav-active { background-color: #dcfce7; color: #16a34a; font-weight: 600; }
        .fc .fc-toolbar-title { font-size: 1.1rem; font-weight: 700; color: #1f2937; }
        .fc .fc-button-primary { background-color: #22c55e !important; border-color: #16a34a !important; }
        .fc .fc-button-primary:hover { background-color: #16a34a !important; }
        .fc .fc-button-primary:not(:disabled).fc-button-active { background-color: #15803d !important; }
        .fc .fc-daygrid-day-number { font-size: 0.8rem; color: #374151; }
        .fc .fc-day-today { background-color: #f0fdf4 !important; }
        .fc .fc-event { cursor: pointer; font-size: 0.75rem; border-radius: 6px; padding: 2px 6px; }
        .fc .fc-col-header-cell-cushion { font-size: 0.8rem; font-weight: 600; color: #6b7280; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- SIDEBAR -->
<div id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl transform -translate-x-full md:translate-x-0 transition-all duration-300 z-50 flex flex-col">
    <div class="bg-gradient-to-r from-school-green to-school-dark-green p-5 flex items-center gap-3">
        <i class="fas fa-graduation-cap text-3xl text-white"></i>
        <div>
            <span class="font-bold text-xl text-white block">Sistem Prestasi</span>
            <span class="text-green-100 text-xs">Bimbingan Belajar</span>
        </div>
    </div>
    <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Menu Utama</p>
        <a href="<?= base_url('dashboard') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-home w-5 text-center"></i> Dashboard
        </a>
        <a href="<?= base_url('jurnal') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-book w-5 text-center"></i> Jurnal
        </a>
        <a href="<?= base_url('kalender') ?>" class="nav-item nav-active flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all">
            <i class="fas fa-calendar-alt w-5 text-center"></i> Kalender
        </a>
        <a href="<?= base_url('absensi') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-calendar-check w-5 text-center"></i> Absensi
        </a>
        <a href="<?= base_url('guru') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-chalkboard-teacher w-5 text-center"></i> Guru
        </a>
        <a href="<?= base_url('laporan') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-file-alt w-5 text-center"></i> Laporan
        </a>
    </nav>
    <div class="p-4 border-t border-gray-100">
        <a href="<?= base_url('auth/logout') ?>" class="flex items-center gap-2 text-sm text-red-500 hover:text-red-700 transition-colors px-2 py-1 rounded-lg hover:bg-red-50">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</div>
<div id="overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

<!-- MAIN -->
<div class="md:ml-64 min-h-screen flex flex-col">
    <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center gap-3 sticky top-0 z-30">
        <button id="btnSidebar" class="md:hidden text-gray-500"><i class="fas fa-bars text-xl"></i></button>
        <div>
            <h1 class="text-lg font-bold text-gray-800">Kalender Jurnal</h1>
            <p class="text-xs text-gray-500">Visualisasi jurnal per tanggal</p>
        </div>
    </header>

    <main class="flex-1 p-6 space-y-4">

        <!-- LEGEND -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <div class="flex flex-wrap gap-4 items-center">
                <span class="text-sm font-semibold text-gray-700">Keterangan:</span>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded" style="background:#86efac"></div>
                    <span class="text-xs text-gray-600">1-2 Jurnal</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded" style="background:#22c55e"></div>
                    <span class="text-xs text-gray-600">3-4 Jurnal</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded" style="background:#16a34a"></div>
                    <span class="text-xs text-gray-600">5+ Jurnal</span>
                </div>
                <div class="ml-auto text-xs text-gray-400">Klik tanggal untuk melihat detail</div>
            </div>
        </div>

        <!-- CALENDAR -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div id="calendar"></div>
        </div>

    </main>
</div>

<!-- MODAL DETAIL JURNAL -->
<div id="modalDetail" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-gray-800" id="detailTitle">Detail Jurnal</h3>
                <p class="text-xs text-gray-500" id="detailDate"></p>
            </div>
            <button onclick="closeDetail()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
        </div>
        <div id="detailContent" class="p-6 space-y-3"></div>
    </div>
</div>

<script>
const base_url = '<?= base_url(); ?>';

document.addEventListener('DOMContentLoaded', function() {
    // Sidebar
    document.getElementById('btnSidebar').addEventListener('click', function() {
        document.getElementById('sidebar').classList.remove('-translate-x-full');
        document.getElementById('overlay').classList.remove('hidden');
    });
    document.getElementById('overlay').addEventListener('click', function() {
        document.getElementById('sidebar').classList.add('-translate-x-full');
        document.getElementById('overlay').classList.add('hidden');
    });

    // FullCalendar
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek'
        },
        events: {
            url: base_url + 'kalender/get_events',
            method: 'GET',
            failure: function() {
                console.error('Gagal memuat events kalender');
            }
        },
        eventClick: function(info) {
            const date = info.event.startStr;
            loadDetail(date);
        },
        dateClick: function(info) {
            loadDetail(info.dateStr);
        },
        eventDidMount: function(info) {
            const props = info.event.extendedProps;
            info.el.setAttribute('title', props.count + ' jurnal:\n' + props.details);
        },
        height: 'auto',
        aspectRatio: 1.8,
    });
    calendar.render();
});

function loadDetail(date) {
    fetch(base_url + 'kalender/get_detail_by_date?tanggal=' + date)
        .then(r => r.json())
        .then(function(res) {
            if (res.status === 'success') {
                const d = new Date(date);
                const opts = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                document.getElementById('detailTitle').textContent = res.data.length + ' Jurnal';
                document.getElementById('detailDate').textContent = d.toLocaleDateString('id-ID', opts);

                let html = '';
                if (res.data.length === 0) {
                    html = '<div class="text-center text-gray-400 py-8"><i class="fas fa-calendar-times text-4xl mb-3 text-gray-200"></i><p>Tidak ada jurnal pada tanggal ini</p></div>';
                } else {
                    res.data.forEach(function(j, i) {
                        html += '<div class="flex gap-3 p-3 bg-gray-50 rounded-xl">';
                        html += '<div class="w-8 h-8 rounded-full bg-school-green flex items-center justify-center text-white text-xs font-bold flex-shrink-0">' + (i+1) + '</div>';
                        html += '<div class="flex-1 min-w-0">';
                        html += '<p class="font-semibold text-gray-800 text-sm">' + j.nama_guru + '</p>';
                        html += '<div class="flex gap-2 mt-1">';
                        html += '<span class="text-xs bg-yellow-50 text-yellow-700 px-2 py-0.5 rounded-md">' + j.nama_kelas + '</span>';
                        html += '<span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-md">' + j.nama_mapel + '</span>';
                        html += '</div>';
                        html += '<p class="text-xs text-gray-600 mt-1 truncate">' + j.materi + '</p>';
                        if (j.jumlah_siswa) {
                            html += '<p class="text-xs text-gray-400 mt-0.5"><i class="fas fa-users mr-1"></i>' + j.jumlah_siswa + ' siswa</p>';
                        }
                        html += '</div></div>';
                    });
                }

                document.getElementById('detailContent').innerHTML = html;
                document.getElementById('modalDetail').classList.remove('hidden');
            }
        });
}

function closeDetail() {
    document.getElementById('modalDetail').classList.add('hidden');
}
</script>
</body>
</html>
