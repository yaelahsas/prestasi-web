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
        /* Ripple effect */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
            pointer-events: none;
        }
        
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        /* Loading skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Smooth transitions */
        * {
            transition: all 0.3s ease;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #22c55e;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #16a34a;
        }
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
                        'school-light-yellow': '#fef3c7'
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-slow': 'bounce 2s infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gradient-to-br from-school-light-green to-white min-h-screen">

    <!-- ===== SIDEBAR ===== -->
    <div id="sidebar"
        class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl transform -translate-x-full md:translate-x-0 transition-all duration-300 ease-in-out z-50 md:shadow-2xl">

        <div class="bg-gradient-to-r from-school-green to-school-dark-green p-5 flex items-center gap-3 shadow-md">
            <i class="fas fa-graduation-cap text-3xl text-white animate-pulse-slow"></i>
            <span class="font-bold text-xl text-white">Sistem Prestasi</span>
        </div>

        <nav class="p-4 space-y-2">
            <a href="<?= base_url('dashboard') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-home w-5 text-center group-hover:scale-110 transition-transform"></i> 
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="<?= base_url('jurnal') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-book w-5 text-center group-hover:scale-110 transition-transform"></i> 
                <span class="font-medium">Jurnal</span>
            </a>

            <a href="<?= base_url('guru') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-chalkboard-teacher w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Guru</span>
            </a>

            <a href="<?= base_url('kelas') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-school w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Kelas</span>
            </a>

            <a href="<?= base_url('mapel') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-book-open w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Mata Pelajaran</span>
            </a>

            <a href="<?= base_url('laporan') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-file-alt w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Laporan</span>
            </a>

            <a href="<?= base_url('users') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-users w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Users</span>
            </a>

            <a href="<?= base_url('sekolah') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-school w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Data Sekolah</span>
            </a>

            <div class="pt-4 mt-4 border-t border-gray-200">
                <a href="<?= base_url('auth/logout') ?>" class="flex items-center gap-3 p-3 rounded-lg text-red-600 hover:bg-red-50 transition-all duration-200 group">
                    <i class="fas fa-sign-out-alt w-5 text-center group-hover:scale-110 transition-transform"></i> 
                    <span class="font-medium">Logout</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- overlay mobile -->
    <div id="overlay" class="fixed inset-0 bg-black/40 hidden z-40"></div>

    <!-- ===== MAIN ===== -->
    <div class="md:ml-64 min-h-screen">

        <!-- TOPBAR -->
        <header class="bg-white shadow-md flex items-center justify-between px-4 h-16 sticky top-0 z-40 backdrop-blur-lg bg-opacity-95">

            <div class="flex items-center gap-4">
                <button id="btnSidebar" class="md:hidden text-2xl text-school-green hover:text-school-dark-green transition-colors">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 class="text-xl font-semibold text-gray-800 hidden sm:block">Dashboard</h2>
            </div>

            <div class="flex items-center gap-4">
                <button id="refreshBtn" class="p-2 rounded-full bg-school-light-green hover:bg-school-green text-school-dark-green hover:text-white transition-all duration-200 group">
                    <i class="fas fa-sync-alt group-hover:rotate-180 transition-transform duration-500"></i>
                </button>
                <div class="text-sm text-gray-700 bg-school-light-yellow px-3 py-1 rounded-full">
                    <i class="fas fa-user-circle text-school-green mr-1"></i>
                    <span class="font-medium"><?= $user['nama']; ?></span>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="p-4">

            <!-- Page Title -->
            <div class="mb-6 animate-fade-in">
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <i class="fas fa-chart-line text-school-green"></i>
                    Dashboard
                </h1>
                <p class="text-gray-600 mt-1">Ringkasan aktivitas bimbingan belajar</p>
            </div>

            <!-- STAT CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8" id="statsContainer">

                <!-- Guru -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                <i class="fas fa-chalkboard-teacher text-school-green"></i>
                                Total Guru
                            </p>
                            <h2 class="text-3xl font-bold text-gray-800 mt-2 counter" data-target="<?= $ringkasan['total_guru']; ?>">
                                0
                            </h2>
                            <div class="mt-2 flex items-center text-xs text-green-600">
                                <i class="fas fa-arrow-up mr-1"></i>
                                <span>Aktif mengajar</span>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center shadow-lg">
                            <i class="fas fa-chalkboard-teacher text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Kelas -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-yellow" style="animation-delay: 0.1s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                <i class="fas fa-school text-school-yellow"></i>
                                Total Kelas
                            </p>
                            <h2 class="text-3xl font-bold text-gray-800 mt-2 counter" data-target="<?= $ringkasan['total_kelas']; ?>">
                                0
                            </h2>
                            <div class="mt-2 flex items-center text-xs text-yellow-600">
                                <i class="fas fa-users mr-1"></i>
                                <span>Sedang berjalan</span>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-school-light-yellow to-school-yellow flex items-center justify-center shadow-lg">
                            <i class="fas fa-school text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Bulanan -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green" style="animation-delay: 0.2s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-school-green"></i>
                                Jurnal Bulan Ini
                            </p>
                            <h2 class="text-3xl font-bold text-gray-800 mt-2 counter" data-target="<?= $ringkasan['total_jurnal_bulan_ini']; ?>">
                                0
                            </h2>
                            <div class="mt-2 flex items-center text-xs text-green-600">
                                <i class="fas fa-chart-line mr-1"></i>
                                <span><?= date('F Y'); ?></span>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center shadow-lg">
                            <i class="fas fa-calendar-alt text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Harian -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-yellow" style="animation-delay: 0.3s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                <i class="fas fa-calendar-day text-school-yellow"></i>
                                Jurnal Hari Ini
                            </p>
                            <h2 class="text-3xl font-bold text-gray-800 mt-2 counter" data-target="<?= $ringkasan['total_jurnal_hari_ini']; ?>">
                                0
                            </h2>
                            <div class="mt-2 flex items-center text-xs text-yellow-600">
                                <i class="fas fa-clock mr-1"></i>
                                <span><?= date('d M Y'); ?></span>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-school-light-yellow to-school-yellow flex items-center justify-center shadow-lg">
                            <i class="fas fa-calendar-day text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

            </div>

            <!-- QUICK ACTION -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10 animate-slide-up" style="animation-delay: 0.4s">

                <button onclick="tambahJurnal()"
                    class="group flex items-center justify-between bg-gradient-to-r from-school-green to-school-dark-green text-white p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden relative">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <h3 class="text-xl font-bold mb-1">Tambah Jurnal</h3>
                        <p class="text-sm opacity-90">Input kegiatan bimbingan</p>
                    </div>
                    <i class="fas fa-plus-circle text-4xl relative z-10 group-hover:rotate-90 transition-transform duration-300"></i>
                </button>

                <button onclick="cetakLaporan()"
                    class="group flex items-center justify-between bg-gradient-to-r from-school-yellow to-yellow-500 text-white p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden relative">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10">
                        <h3 class="text-xl font-bold mb-1">Cetak Laporan</h3>
                        <p class="text-sm opacity-90">Rekap PDF bulanan</p>
                    </div>
                    <i class="fas fa-file-pdf text-4xl relative z-10 group-hover:scale-110 transition-transform duration-300"></i>
                </button>

            </div>

            <!-- MENU GRID -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5 animate-slide-up" style="animation-delay: 0.5s">

                <a href="<?= base_url('jurnal') ?>"
                    class="group bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 text-center border-t-4 border-school-green">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-book text-white text-2xl"></i>
                    </div>
                    <p class="font-semibold text-gray-800 group-hover:text-school-green transition-colors">Jurnal</p>
                    <p class="text-xs text-gray-500 mt-1">Kelola jurnal</p>
                </a>

                <a href="<?= base_url('guru') ?>"
                    class="group bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 text-center border-t-4 border-school-yellow">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-school-light-yellow to-school-yellow flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-chalkboard-teacher text-white text-2xl"></i>
                    </div>
                    <p class="font-semibold text-gray-800 group-hover:text-school-yellow transition-colors">Guru</p>
                    <p class="text-xs text-gray-500 mt-1">Data pengajar</p>
                </a>

                <a href="<?= base_url('mapel') ?>"
                    class="group bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 text-center border-t-4 border-school-green">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-book-open text-white text-2xl"></i>
                    </div>
                    <p class="font-semibold text-gray-800 group-hover:text-school-green transition-colors">Mapel</p>
                    <p class="text-xs text-gray-500 mt-1">Mata pelajaran</p>
                </a>

                <a href="<?= base_url('kelas') ?>"
                    class="group bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 text-center border-t-4 border-school-yellow">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-school-light-yellow to-school-yellow flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <p class="font-semibold text-gray-800 group-hover:text-school-yellow transition-colors">Kelas</p>
                    <p class="text-xs text-gray-500 mt-1">Daftar kelas</p>
                </a>

                <a href="<?= base_url('laporan') ?>"
                    class="group bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 text-center border-t-4 border-school-yellow">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-school-light-yellow to-school-yellow flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-file-alt text-white text-2xl"></i>
                    </div>
                    <p class="font-semibold text-gray-800 group-hover:text-school-yellow transition-colors">Laporan</p>
                    <p class="text-xs text-gray-500 mt-1">Rekap data</p>
                </a>

                <a href="<?= base_url('sekolah') ?>"
                    class="group bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 text-center border-t-4 border-school-green">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-school text-white text-2xl"></i>
                    </div>
                    <p class="font-semibold text-gray-800 group-hover:text-school-green transition-colors">Sekolah</p>
                    <p class="text-xs text-gray-500 mt-1">Data sekolah</p>
                </a>

            </div>

        </main>

    </div>

    <script src="<?= base_url('assets/js/dashboard/dashboard.js'); ?>"></script>

</body>

</html>