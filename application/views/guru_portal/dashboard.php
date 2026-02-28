<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Guru - <?= $guru ? $guru->nama_guru : $user['nama']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                'school-green': '#22c55e', 'school-dark-green': '#16a34a',
                'school-yellow': '#facc15', 'school-blue': '#3b82f6',
            }}}
        }
    </script>
    <style>
        .stat-card { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.10); }
        .nav-active { background-color: #dcfce7; color: #16a34a; font-weight: 600; }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .fade-in-up { animation: fadeInUp 0.5s ease both; }
        .table-row:hover { background-color: #f0fdf4; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- SIDEBAR -->
<div id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl transform -translate-x-full md:translate-x-0 transition-all duration-300 z-50 flex flex-col">
    <div class="bg-gradient-to-r from-school-green to-school-dark-green p-5 flex items-center gap-3">
        <i class="fas fa-chalkboard-teacher text-3xl text-white"></i>
        <div>
            <span class="font-bold text-xl text-white block">Portal Guru</span>
            <span class="text-green-100 text-xs">Sistem Prestasi</span>
        </div>
    </div>
    <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Menu</p>
        <a href="<?= base_url('guru_portal') ?>" class="nav-item nav-active flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all">
            <i class="fas fa-home w-5 text-center"></i> Dashboard
        </a>
        <a href="<?= base_url('guru_portal/jurnal') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-book w-5 text-center"></i> Jurnal Saya
        </a>
        <a href="<?= base_url('guru_portal/absensi') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-calendar-check w-5 text-center"></i> Absensi Saya
        </a>
        <a href="<?= base_url('guru_portal/profil') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-user w-5 text-center"></i> Profil
        </a>
    </nav>
    <div class="p-4 border-t border-gray-100">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-9 h-9 rounded-full bg-school-green flex items-center justify-center text-white font-bold text-sm">
                <?= strtoupper(substr($user['nama'], 0, 1)); ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800 truncate"><?= $user['nama']; ?></p>
                <p class="text-xs text-gray-500">Guru</p>
            </div>
        </div>
        <a href="<?= base_url('auth/logout') ?>" class="flex items-center gap-2 text-sm text-red-500 hover:text-red-700 transition-colors px-2 py-1 rounded-lg hover:bg-red-50">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</div>

<!-- OVERLAY MOBILE -->
<div id="overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

<!-- MAIN CONTENT -->
<div class="md:ml-64 min-h-screen flex flex-col">
    <!-- TOPBAR -->
    <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between sticky top-0 z-30">
        <div class="flex items-center gap-3">
            <button id="btnSidebar" class="md:hidden text-gray-500 hover:text-gray-700">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div>
                <h1 class="text-lg font-bold text-gray-800">Dashboard Guru</h1>
                <p class="text-xs text-gray-500" id="currentDateTime"></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="tambahJurnal()" class="flex items-center gap-2 bg-school-green text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-school-dark-green transition-colors">
                <i class="fas fa-plus"></i> Tambah Jurnal
            </button>
        </div>
    </header>

    <main class="flex-1 p-6 space-y-6">

        <!-- GREETING -->
        <div class="bg-gradient-to-r from-school-green to-school-dark-green rounded-2xl p-6 text-white fade-in-up">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Selamat datang,</p>
                    <h2 class="text-2xl font-bold mt-1"><?= $guru ? $guru->nama_guru : $user['nama']; ?></h2>
                    <?php if ($guru): ?>
                    <p class="text-green-100 text-sm mt-1">
                        <i class="fas fa-school mr-1"></i><?= $guru->nama_kelas; ?> &bull;
                        <i class="fas fa-book ml-2 mr-1"></i><?= $guru->nama_mapel; ?>
                    </p>
                    <?php endif; ?>
                </div>
                <i class="fas fa-chalkboard-teacher text-5xl text-white/30"></i>
            </div>
        </div>

        <!-- STAT CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 fade-in-up" style="animation-delay:0.1s">
            <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs text-gray-500">Total Jurnal</p>
                        <p class="text-2xl font-bold text-gray-800 counter" data-target="<?= $total_jurnal; ?>"><?= $total_jurnal; ?></p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                        <i class="fas fa-book text-school-green"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-400">Semua waktu</p>
            </div>
            <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs text-gray-500">Jurnal Bulan Ini</p>
                        <p class="text-2xl font-bold text-gray-800 counter" data-target="<?= $total_jurnal_bulan; ?>"><?= $total_jurnal_bulan; ?></p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-blue-500"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-400"><?= date('F Y'); ?></p>
            </div>
            <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs text-gray-500">Jurnal Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-800 counter" data-target="<?= $total_jurnal_hari; ?>"><?= $total_jurnal_hari; ?></p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center">
                        <i class="fas fa-calendar-day text-yellow-500"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-400"><?= date('d F Y'); ?></p>
            </div>
        </div>

        <!-- CHART + ABSENSI -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 fade-in-up" style="animation-delay:0.2s">
            <!-- Chart Tren Jurnal -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-base font-bold text-gray-800 mb-4">Tren Jurnal 6 Bulan Terakhir</h3>
                <div style="height:200px">
                    <canvas id="chartJurnalGuru"></canvas>
                </div>
            </div>
            <!-- Rekap Absensi -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-base font-bold text-gray-800 mb-1">Absensi Bulan Ini</h3>
                <p class="text-xs text-gray-500 mb-4"><?= date('F Y'); ?></p>
                <?php if ($rekap_absensi): ?>
                <div class="space-y-3">
                    <?php
                    $items = [
                        ['label'=>'Hadir', 'val'=>$rekap_absensi->hadir ?? 0, 'color'=>'bg-green-500'],
                        ['label'=>'Izin',  'val'=>$rekap_absensi->izin  ?? 0, 'color'=>'bg-blue-500'],
                        ['label'=>'Sakit', 'val'=>$rekap_absensi->sakit ?? 0, 'color'=>'bg-yellow-500'],
                        ['label'=>'Alpha', 'val'=>$rekap_absensi->alpha ?? 0, 'color'=>'bg-red-500'],
                    ];
                    $total_abs = $rekap_absensi->total ?? 0;
                    foreach ($items as $item):
                        $pct = $total_abs > 0 ? round(($item['val'] / $total_abs) * 100) : 0;
                    ?>
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-600"><?= $item['label']; ?></span>
                            <span class="font-semibold"><?= $item['val']; ?> (<?= $pct; ?>%)</span>
                        </div>
                        <div class="bg-gray-100 rounded-full h-2">
                            <div class="<?= $item['color']; ?> h-2 rounded-full" style="width:<?= $pct; ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="flex flex-col items-center justify-center h-32 text-gray-400">
                    <i class="fas fa-calendar-times text-3xl mb-2 text-gray-200"></i>
                    <p class="text-sm">Belum ada data absensi</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- JURNAL TERBARU -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 fade-in-up overflow-hidden" style="animation-delay:0.3s">
            <div class="flex items-center justify-between p-6 pb-4">
                <h3 class="text-base font-bold text-gray-800">Jurnal Terbaru Saya</h3>
                <a href="<?= base_url('guru_portal/jurnal') ?>" class="text-xs text-school-green hover:text-school-dark-green font-semibold flex items-center gap-1">
                    Lihat Semua <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
            <?php if (!empty($jurnal_terbaru)): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-y border-gray-100">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Kelas</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Mapel</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Materi</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Siswa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach ($jurnal_terbaru as $j): ?>
                        <tr class="table-row">
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded-lg font-medium">
                                    <?= date('d M Y', strtotime($j->tanggal)); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs font-semibold text-gray-700"><?= $j->nama_kelas; ?></td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-md"><?= $j->nama_mapel; ?></span>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell text-xs text-gray-600 truncate max-w-xs"><?= $j->materi; ?></td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-700 text-xs font-bold">
                                    <?= $j->jumlah_siswa ?? '-'; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="flex flex-col items-center justify-center h-40 text-gray-400 px-6 pb-6">
                <i class="fas fa-book-open text-4xl mb-3 text-gray-200"></i>
                <p class="text-sm font-medium text-gray-500">Belum ada jurnal</p>
                <button onclick="tambahJurnal()" class="mt-3 px-4 py-2 bg-school-green text-white text-xs rounded-lg hover:bg-school-dark-green transition-colors font-medium">
                    <i class="fas fa-plus mr-1"></i> Tambah Jurnal Pertama
                </button>
            </div>
            <?php endif; ?>
        </div>

    </main>

    <footer class="text-center py-4 text-xs text-gray-400 border-t border-gray-100">
        &copy; <?= date('Y'); ?> Portal Guru &mdash; Sistem Prestasi
    </footer>
</div>

<!-- DATA CHART -->
<script>
    const chartGuruLabels = <?= json_encode(array_column($jurnal_per_bulan, 'label')); ?>;
    const chartGuruData   = <?= json_encode(array_column($jurnal_per_bulan, 'count')); ?>;
</script>
<script>
$(document).ready(function() {
    // DateTime
    function updateTime() {
        const now = new Date();
        $('#currentDateTime').text(now.toLocaleDateString('id-ID', {
            weekday:'long', year:'numeric', month:'long', day:'numeric',
            hour:'2-digit', minute:'2-digit'
        }));
    }
    updateTime(); setInterval(updateTime, 1000);

    // Chart
    const ctx = document.getElementById('chartJurnalGuru');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartGuruLabels,
                datasets: [{
                    label: 'Jurnal',
                    data: chartGuruData,
                    backgroundColor: 'rgba(34,197,94,0.7)',
                    borderColor: '#16a34a',
                    borderWidth: 1.5,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#9ca3af' } },
                    y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0, font: { size: 10 }, color: '#9ca3af' } }
                }
            }
        });
    }

    // Sidebar mobile
    $('#btnSidebar').on('click', function() {
        $('#sidebar').removeClass('-translate-x-full');
        $('#overlay').removeClass('hidden');
    });
    $('#overlay').on('click', function() {
        $('#sidebar').addClass('-translate-x-full');
        $('#overlay').addClass('hidden');
    });
});

function tambahJurnal() {
    window.location.href = '<?= base_url('guru_portal/jurnal') ?>';
}
</script>
</body>
</html>
