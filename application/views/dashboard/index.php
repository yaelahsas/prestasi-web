<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Prestasi</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
            pointer-events: none;
        }
        @keyframes ripple-animation {
            to { transform: scale(4); opacity: 0; }
        }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #22c55e; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #16a34a; }
        .stat-card { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.10); }
        .nav-active { background-color: #dcfce7; color: #16a34a; font-weight: 600; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up { animation: fadeInUp 0.5s ease both; }
        .progress-bar { transition: width 1.2s ease; }
        .table-row:hover { background-color: #f0fdf4; }
        .greeting-card { background: linear-gradient(135deg, #16a34a 0%, #22c55e 60%, #4ade80 100%); }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'school-green': '#22c55e',
                        'school-yellow': '#facc15',
                        'school-dark-green': '#16a34a',
                        'school-light-green': '#86efac',
                        'school-light-yellow': '#fef3c7',
                        'school-blue': '#3b82f6',
                        'school-purple': '#8b5cf6',
                        'school-orange': '#f97316',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 min-h-screen">

    <!-- ===== SIDEBAR ===== -->
    <div id="sidebar"
        class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl transform -translate-x-full md:translate-x-0 transition-all duration-300 ease-in-out z-50 flex flex-col">

        <div class="bg-gradient-to-r from-school-green to-school-dark-green p-5 flex items-center gap-3 shadow-md">
            <i class="fas fa-graduation-cap text-3xl text-white"></i>
            <div>
                <span class="font-bold text-xl text-white block">Sistem Prestasi</span>
                <span class="text-green-100 text-xs">Bimbingan Belajar</span>
            </div>
        </div>

        <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Menu Utama</p>

            <a href="<?= base_url('dashboard') ?>" class="nav-item nav-active flex items-center gap-3 p-3 rounded-lg transition-all duration-200 group">
                <i class="fas fa-home w-5 text-center"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= base_url('jurnal') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-book w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Jurnal</span>
            </a>
            <a href="<?= base_url('guru') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-chalkboard-teacher w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Guru</span>
            </a>
            <a href="<?= base_url('kelas') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-school w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Kelas</span>
            </a>
            <a href="<?= base_url('mapel') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-book-open w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Mata Pelajaran</span>
            </a>
            <a href="<?= base_url('laporan') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-file-alt w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Laporan</span>
            </a>
            <a href="<?= base_url('users') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-users w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Users</span>
            </a>
            <a href="<?= base_url('sekolah') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-building w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Data Sekolah</span>
            </a>

            <div class="pt-4 mt-4 border-t border-gray-200">
                <a href="<?= base_url('auth/logout') ?>" class="flex items-center gap-3 p-3 rounded-lg text-red-600 hover:bg-red-50 transition-all duration-200 group">
                    <i class="fas fa-sign-out-alt w-5 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium">Logout</span>
                </a>
            </div>
        </nav>

        <!-- Sidebar footer -->
        <div class="p-4 border-t border-gray-100 bg-gray-50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-school-green to-school-dark-green flex items-center justify-center text-white font-bold text-sm">
                    <?= strtoupper(substr($user['nama'], 0, 1)); ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate"><?= $user['nama']; ?></p>
                    <p class="text-xs text-gray-500 capitalize"><?= $user['role']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- overlay mobile -->
    <div id="overlay" class="fixed inset-0 bg-black/40 hidden z-40"></div>

    <!-- ===== MAIN ===== -->
    <div class="md:ml-64 min-h-screen">

        <!-- TOPBAR -->
        <header class="bg-white shadow-sm flex items-center justify-between px-4 h-16 sticky top-0 z-40 border-b border-gray-100">
            <div class="flex items-center gap-4">
                <button id="btnSidebar" class="md:hidden text-2xl text-school-green hover:text-school-dark-green transition-colors">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h2 class="text-lg font-bold text-gray-800 hidden sm:block">Dashboard</h2>
                    <p class="text-xs text-gray-500 hidden sm:block" id="currentDateTime"></p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button id="refreshBtn" title="Refresh Data (F5)"
                    class="p-2 rounded-lg bg-green-50 hover:bg-school-green text-school-dark-green hover:text-white transition-all duration-200 group">
                    <i class="fas fa-sync-alt group-hover:rotate-180 transition-transform duration-500 text-sm"></i>
                </button>
                <button onclick="tambahJurnal()" title="Tambah Jurnal (Ctrl+N)"
                    class="hidden sm:flex items-center gap-2 px-3 py-2 rounded-lg bg-school-green text-white hover:bg-school-dark-green transition-all duration-200 text-sm font-medium">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Tambah Jurnal</span>
                </button>
                <div class="flex items-center gap-2 bg-green-50 px-3 py-2 rounded-lg">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-school-green to-school-dark-green flex items-center justify-center text-white font-bold text-xs">
                        <?= strtoupper(substr($user['nama'], 0, 1)); ?>
                    </div>
                    <span class="text-sm font-medium text-gray-700 hidden sm:block"><?= $user['nama']; ?></span>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="p-4 md:p-6 space-y-6">

            <!-- ===== GREETING CARD ===== -->
            <div class="greeting-card rounded-2xl p-6 text-white shadow-lg fade-in-up" style="animation-delay:0s">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold mb-1">
                            <?php
                                $jam = (int)date('H');
                                if ($jam < 12) echo 'Selamat Pagi';
                                elseif ($jam < 15) echo 'Selamat Siang';
                                elseif ($jam < 18) echo 'Selamat Sore';
                                else echo 'Selamat Malam';
                            ?>, <?= $user['nama']; ?>! &#128075;
                        </h1>
                        <p class="text-green-100 text-sm md:text-base">
                            Hari ini <strong><?= date('l, d F Y'); ?></strong> &mdash;
                            <?php if ($ringkasan['total_jurnal_hari_ini'] > 0): ?>
                                Ada <strong><?= $ringkasan['total_jurnal_hari_ini']; ?></strong> jurnal yang sudah diinput hari ini.
                            <?php else: ?>
                                Belum ada jurnal yang diinput hari ini.
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="flex gap-3 flex-shrink-0">
                        <a href="<?= base_url('jurnal') ?>"
                            class="flex items-center gap-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-2 rounded-xl text-white text-sm font-medium transition-all duration-200 border border-white/30">
                            <i class="fas fa-book text-xs"></i>
                            <span>Lihat Jurnal</span>
                        </a>
                        <a href="<?= base_url('laporan') ?>"
                            class="flex items-center gap-2 bg-white text-school-dark-green hover:bg-green-50 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 shadow-sm">
                            <i class="fas fa-file-pdf text-xs"></i>
                            <span>Laporan</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ===== STAT CARDS ===== -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4" id="statsContainer">

                <!-- Total Guru -->
                <div class="stat-card bg-white rounded-2xl shadow-sm p-5 border border-gray-100 fade-in-up" style="animation-delay:0.05s">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Guru</p>
                            <h2 class="text-3xl font-bold text-gray-800 counter" data-target="<?= $ringkasan['total_guru']; ?>">0</h2>
                            <p class="text-xs text-green-600 mt-1 flex items-center gap-1">
                                <i class="fas fa-circle text-[6px]"></i> Guru aktif
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-chalkboard-teacher text-school-green text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Kelas -->
                <div class="stat-card bg-white rounded-2xl shadow-sm p-5 border border-gray-100 fade-in-up" style="animation-delay:0.1s">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Kelas</p>
                            <h2 class="text-3xl font-bold text-gray-800 counter" data-target="<?= $ringkasan['total_kelas']; ?>">0</h2>
                            <p class="text-xs text-yellow-600 mt-1 flex items-center gap-1">
                                <i class="fas fa-circle text-[6px]"></i> Kelas aktif
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-school text-yellow-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Mapel -->
                <div class="stat-card bg-white rounded-2xl shadow-sm p-5 border border-gray-100 fade-in-up" style="animation-delay:0.15s">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Mata Pelajaran</p>
                            <h2 class="text-3xl font-bold text-gray-800 counter" data-target="<?= $ringkasan['total_mapel']; ?>">0</h2>
                            <p class="text-xs text-blue-600 mt-1 flex items-center gap-1">
                                <i class="fas fa-circle text-[6px]"></i> Mapel aktif
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-book-open text-blue-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Users -->
                <div class="stat-card bg-white rounded-2xl shadow-sm p-5 border border-gray-100 fade-in-up" style="animation-delay:0.2s">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pengguna</p>
                            <h2 class="text-3xl font-bold text-gray-800 counter" data-target="<?= $ringkasan['total_users']; ?>">0</h2>
                            <p class="text-xs text-purple-600 mt-1 flex items-center gap-1">
                                <i class="fas fa-circle text-[6px]"></i> User aktif
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-users text-purple-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Jurnal -->
                <div class="stat-card bg-white rounded-2xl shadow-sm p-5 border border-gray-100 fade-in-up" style="animation-delay:0.25s">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Jurnal</p>
                            <h2 class="text-3xl font-bold text-gray-800 counter" data-target="<?= $ringkasan['total_jurnal']; ?>">0</h2>
                            <p class="text-xs text-green-600 mt-1 flex items-center gap-1">
                                <i class="fas fa-circle text-[6px]"></i> Semua waktu
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clipboard-list text-school-green text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Jurnal Bulan Ini -->
                <div class="stat-card bg-white rounded-2xl shadow-sm p-5 border border-gray-100 fade-in-up" style="animation-delay:0.3s">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jurnal Bulan Ini</p>
                            <h2 class="text-3xl font-bold text-gray-800 counter" data-target="<?= $ringkasan['total_jurnal_bulan_ini']; ?>">0</h2>
                            <p class="text-xs text-orange-600 mt-1 flex items-center gap-1">
                                <i class="fas fa-circle text-[6px]"></i> <?= date('F Y'); ?>
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calendar-alt text-orange-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Jurnal Hari Ini -->
                <div class="stat-card bg-white rounded-2xl shadow-sm p-5 border border-gray-100 fade-in-up" style="animation-delay:0.35s">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jurnal Hari Ini</p>
                            <h2 class="text-3xl font-bold text-gray-800 counter" data-target="<?= $ringkasan['total_jurnal_hari_ini']; ?>">0</h2>
                            <p class="text-xs text-yellow-600 mt-1 flex items-center gap-1">
                                <i class="fas fa-circle text-[6px]"></i> <?= date('d M Y'); ?>
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calendar-day text-yellow-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Siswa Bulan Ini -->
                <div class="stat-card bg-white rounded-2xl shadow-sm p-5 border border-gray-100 fade-in-up" style="animation-delay:0.4s">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Siswa Dibimbing</p>
                            <h2 class="text-3xl font-bold text-gray-800 counter" data-target="<?= $ringkasan['total_siswa_bulan_ini']; ?>">0</h2>
                            <p class="text-xs text-blue-600 mt-1 flex items-center gap-1">
                                <i class="fas fa-circle text-[6px]"></i> Bulan ini
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-graduate text-blue-500 text-xl"></i>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ===== CHART TREN + GURU TERAKTIF ===== -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Chart Jurnal Per Bulan -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6 border border-gray-100 fade-in-up" style="animation-delay:0.45s">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-bold text-gray-800">Tren Jurnal 12 Bulan Terakhir</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Jumlah jurnal yang diinput per bulan</p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center">
                            <i class="fas fa-chart-line text-school-green text-sm"></i>
                        </div>
                    </div>
                    <div class="relative" style="height:220px">
                        <canvas id="chartJurnalBulan"></canvas>
                    </div>
                </div>

                <!-- Guru Teraktif -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 fade-in-up" style="animation-delay:0.5s">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-bold text-gray-800">Guru Teraktif</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Bulan <?= date('F Y'); ?></p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-yellow-100 flex items-center justify-center">
                            <i class="fas fa-trophy text-yellow-500 text-sm"></i>
                        </div>
                    </div>

                    <?php if (!empty($guru_teraktif)): ?>
                        <?php
                            $max_jurnal = 1;
                            foreach ($guru_teraktif as $g) {
                                if ((int)$g->total_jurnal > $max_jurnal) $max_jurnal = (int)$g->total_jurnal;
                            }
                            $rank_colors = ['text-yellow-500','text-gray-400','text-orange-400','text-gray-400','text-gray-400'];
                            $rank_icons  = ['fa-trophy','fa-medal','fa-award','fa-star','fa-star'];
                        ?>
                        <div class="space-y-3">
                            <?php foreach ($guru_teraktif as $i => $guru): ?>
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 flex items-center justify-center flex-shrink-0">
                                    <i class="fas <?= isset($rank_icons[$i]) ? $rank_icons[$i] : 'fa-star'; ?> <?= isset($rank_colors[$i]) ? $rank_colors[$i] : 'text-gray-400'; ?> text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate"><?= $guru->nama_guru; ?></p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                            <div class="bg-school-green h-1.5 rounded-full progress-bar"
                                                style="width: <?= round(($guru->total_jurnal / $max_jurnal) * 100); ?>%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500 flex-shrink-0"><?= $guru->total_jurnal; ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center h-32 text-gray-400">
                            <i class="fas fa-inbox text-3xl mb-2 text-gray-200"></i>
                            <p class="text-sm">Belum ada data bulan ini</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- ===== DISTRIBUSI MAPEL + JURNAL TERBARU ===== -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Distribusi per Mapel -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 fade-in-up" style="animation-delay:0.55s">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-bold text-gray-800">Distribusi Jurnal</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Per mata pelajaran</p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-chart-pie text-blue-500 text-sm"></i>
                        </div>
                    </div>
                    <?php if (!empty($jurnal_per_mapel)): ?>
                    <div class="relative" style="height:180px">
                        <canvas id="chartMapel"></canvas>
                    </div>
                    <?php else: ?>
                    <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                        <i class="fas fa-chart-pie text-4xl mb-2 text-gray-200"></i>
                        <p class="text-sm">Belum ada data</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Jurnal Terbaru -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 fade-in-up overflow-hidden" style="animation-delay:0.6s">
                    <div class="flex items-center justify-between p-6 pb-4">
                        <div>
                            <h3 class="text-base font-bold text-gray-800">Jurnal Terbaru</h3>
                            <p class="text-xs text-gray-500 mt-0.5">5 jurnal yang baru diinput</p>
                        </div>
                        <a href="<?= base_url('jurnal') ?>"
                            class="text-xs text-school-green hover:text-school-dark-green font-semibold flex items-center gap-1 transition-colors">
                            Lihat Semua <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>

                    <?php if (!empty($jurnal_terbaru)): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-y border-gray-100">
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Guru</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Kelas / Mapel</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Materi</th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Siswa</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php foreach ($jurnal_terbaru as $j): ?>
                                <tr class="table-row transition-colors">
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 text-xs bg-green-50 text-green-700 px-2 py-1 rounded-lg font-medium">
                                            <i class="fas fa-calendar-day text-[10px]"></i>
                                            <?= date('d M Y', strtotime($j->tanggal)); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-gray-800 text-xs"><?= $j->nama_guru; ?></p>
                                    </td>
                                    <td class="px-4 py-3 hidden md:table-cell">
                                        <span class="inline-block bg-yellow-50 text-yellow-700 text-xs px-2 py-0.5 rounded-md font-medium"><?= $j->nama_kelas; ?></span>
                                        <span class="inline-block bg-blue-50 text-blue-700 text-xs px-2 py-0.5 rounded-md font-medium ml-1"><?= $j->nama_mapel; ?></span>
                                    </td>
                                    <td class="px-4 py-3 hidden lg:table-cell">
                                        <p class="text-gray-600 text-xs truncate max-w-xs" title="<?= $j->materi; ?>"><?= $j->materi; ?></p>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-700 text-xs font-bold">
                                            <?= $j->jumlah_siswa; ?>
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
                        <p class="text-xs text-gray-400 mt-1">Klik "Tambah Jurnal" untuk mulai mencatat</p>
                        <button onclick="tambahJurnal()"
                            class="mt-3 px-4 py-2 bg-school-green text-white text-xs rounded-lg hover:bg-school-dark-green transition-colors font-medium">
                            <i class="fas fa-plus mr-1"></i> Tambah Jurnal
                        </button>
                    </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- ===== QUICK ACTIONS ===== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 fade-in-up" style="animation-delay:0.65s">

                <button onclick="tambahJurnal()"
                    class="group flex items-center gap-4 bg-gradient-to-r from-school-green to-school-dark-green text-white p-5 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 overflow-hidden relative">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-plus text-white"></i>
                    </div>
                    <div class="text-left relative z-10">
                        <p class="font-bold text-sm">Tambah Jurnal</p>
                        <p class="text-xs text-green-100">Input kegiatan bimbel</p>
                    </div>
                </button>

                <button onclick="cetakLaporan()"
                    class="group flex items-center gap-4 bg-gradient-to-r from-yellow-400 to-yellow-500 text-white p-5 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 overflow-hidden relative">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file-pdf text-white"></i>
                    </div>
                    <div class="text-left relative z-10">
                        <p class="font-bold text-sm">Cetak Laporan</p>
                        <p class="text-xs text-yellow-100">Rekap PDF bulanan</p>
                    </div>
                </button>

                <a href="<?= base_url('guru') ?>"
                    class="group flex items-center gap-4 bg-white border border-gray-100 text-gray-700 p-5 rounded-2xl shadow-sm hover:shadow-lg hover:border-school-green transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0 group-hover:bg-school-green transition-colors">
                        <i class="fas fa-chalkboard-teacher text-school-green group-hover:text-white transition-colors"></i>
                    </div>
                    <div>
                        <p class="font-bold text-sm">Data Guru</p>
                        <p class="text-xs text-gray-500"><?= $ringkasan['total_guru']; ?> guru aktif</p>
                    </div>
                </a>

                <a href="<?= base_url('kelas') ?>"
                    class="group flex items-center gap-4 bg-white border border-gray-100 text-gray-700 p-5 rounded-2xl shadow-sm hover:shadow-lg hover:border-school-yellow transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center flex-shrink-0 group-hover:bg-yellow-400 transition-colors">
                        <i class="fas fa-school text-yellow-500 group-hover:text-white transition-colors"></i>
                    </div>
                    <div>
                        <p class="font-bold text-sm">Data Kelas</p>
                        <p class="text-xs text-gray-500"><?= $ringkasan['total_kelas']; ?> kelas aktif</p>
                    </div>
                </a>

            </div>

        </main>

        <!-- Footer -->
        <footer class="text-center py-4 text-xs text-gray-400 border-t border-gray-100 mt-4">
            &copy; <?= date('Y'); ?> Sistem Prestasi &mdash; Bimbingan Belajar
        </footer>

    </div>

    <!-- ===== DATA UNTUK CHART (PHP -> JS) ===== -->
    <script>
        // Data chart jurnal per bulan
        const chartBulanLabels = <?= json_encode(array_column($jurnal_per_bulan, 'label')); ?>;
        const chartBulanData   = <?= json_encode(array_column($jurnal_per_bulan, 'count')); ?>;

        // Data chart distribusi mapel
        const chartMapelLabels = <?= json_encode(array_column($jurnal_per_mapel, 'nama_mapel')); ?>;
        const chartMapelData   = <?= json_encode(array_column($jurnal_per_mapel, 'total')); ?>;
    </script>

    <script src="<?= base_url('assets/js/dashboard/dashboard.js'); ?>"></script>

</body>

</html>
