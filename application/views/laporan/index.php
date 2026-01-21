<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Sistem Prestasi</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        const base_url = '<?= base_url(); ?>';
        const currentMonth = '<?= date('m'); ?>';
        const currentYear = '<?= date('Y'); ?>';
    </script>

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

        /* Form Styling */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #22c55e;
            outline: none;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
        }

        /* Button Styling */
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(to right, #22c55e, #16a34a);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #16a34a, #15803d);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .btn-info {
            background: #3b82f6;
            color: white;
        }

        .btn-info:hover {
            background: #2563eb;
        }

        /* Loading Spinner */
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #22c55e;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: none;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading .spinner {
            display: inline-block;
        }

        .loading .btn-text {
            display: none;
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

            <a href="<?= base_url('users') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-users w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Pengguna</span>
            </a>

            <a href="<?= base_url('sekolah') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-school w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Sekolah</span>
            </a>

            <a href="<?= base_url('laporan') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg bg-school-light-green text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-file-alt w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Laporan</span>
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
                <h2 class="text-xl font-semibold text-gray-800 hidden sm:block">Laporan</h2>
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
                    <i class="fas fa-file-alt text-school-green"></i>
                    Laporan
                </h1>
                <p class="text-gray-600 mt-1">Cetak berbagai laporan kegiatan bimbingan belajar</p>
            </div>

            <!-- STAT CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

                <!-- Informasi Sekolah -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                <i class="fas fa-school text-school-green"></i>
                                Informasi Sekolah
                            </p>
                            <div id="infoSekolah" class="mt-2">
                                <div class="text-center">
                                    <i class="fas fa-spinner fa-spin"></i> Memuat data sekolah...
                                </div>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center shadow-lg">
                            <i class="fas fa-school text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Jurnal Bulan Ini -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green" style="animation-delay: 0.1s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                <i class="fas fa-book text-school-green"></i>
                                Jurnal Bulan Ini
                            </p>
                            <h2 class="text-3xl font-bold text-gray-800 mt-2" id="totalJurnalBulanIni">
                                0
                            </h2>
                            <div class="mt-2 flex items-center text-xs text-green-600">
                                <i class="fas fa-calendar mr-1"></i>
                                <span><?= date('F Y'); ?></span>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center shadow-lg">
                            <i class="fas fa-book text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Guru Aktif -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green" style="animation-delay: 0.2s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                <i class="fas fa-chalkboard-teacher text-school-green"></i>
                                Guru Aktif
                            </p>
                            <h2 class="text-3xl font-bold text-gray-800 mt-2" id="totalGuruAktif">
                                0
                            </h2>
                            <div class="mt-2 flex items-center text-xs text-green-600">
                                <i class="fas fa-users mr-1"></i>
                                <span>Semua kelas</span>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center shadow-lg">
                            <i class="fas fa-chalkboard-teacher text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Kelas Aktif -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-yellow" style="animation-delay: 0.3s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                <i class="fas fa-school text-school-yellow"></i>
                                Kelas Aktif
                            </p>
                            <h2 class="text-3xl font-bold text-gray-800 mt-2" id="totalKelasAktif">
                                0
                            </h2>
                            <div class="mt-2 flex items-center text-xs text-yellow-600">
                                <i class="fas fa-door-open mr-1"></i>
                                <span>Semua tingkat</span>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-school-light-yellow to-school-yellow flex items-center justify-center shadow-lg">
                            <i class="fas fa-school text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

            </div>

            <!-- LAPORAN SECTION -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

                <!-- Laporan Bulanan -->
                <div class="bg-white rounded-2xl shadow-lg p-6 animate-slide-up" style="animation-delay: 0.4s">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-calendar-alt text-school-green text-xl"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Laporan Jurnal Bulanan</h3>
                    </div>
                    <form id="formLaporanBulanan">
                        <div class="form-group">
                            <label for="bulan">Pilih Bulan</label>
                            <select name="bulan" id="bulan" class="form-control">
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tahun">Pilih Tahun</label>
                            <select name="tahun" id="tahun" class="form-control">
                                <?php 
                                $tahun_sekarang = date('Y');
                                for($tahun = $tahun_sekarang; $tahun >= 2020; $tahun--) {
                                    echo '<option value="' . $tahun . '">' . $tahun . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-full">
                            <i class="fas fa-file-pdf"></i>
                            <span>Cetak Laporan Bulanan</span>
                        </button>
                    </form>
                </div>

                <!-- Laporan Per Guru -->
                <div class="bg-white rounded-2xl shadow-lg p-6 animate-slide-up" style="animation-delay: 0.5s">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-chalkboard-teacher text-school-green text-xl"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Laporan Per Guru</h3>
                    </div>
                    <form id="formLaporanGuru">
                        <div class="form-group">
                            <label for="id_guru">Pilih Guru</label>
                            <select name="id_guru" id="id_guru" class="form-control">
                                <option value="">-- Pilih Guru --</option>
                                <?php foreach($guru as $g): ?>
                                <option value="<?= $g->id_guru ?>"><?= $g->nama_guru ?> (<?= $g->nip  ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bulan_guru">Pilih Bulan</label>
                            <select name="bulan_guru" id="bulan_guru" class="form-control">
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tahun_guru">Pilih Tahun</label>
                            <select name="tahun_guru" id="tahun_guru" class="form-control">
                                <?php 
                                $tahun_sekarang = date('Y');
                                for($tahun = $tahun_sekarang; $tahun >= 2020; $tahun--) {
                                    echo '<option value="' . $tahun . '">' . $tahun . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-full">
                            <i class="fas fa-file-pdf"></i>
                            <span>Cetak Laporan Guru</span>
                        </button>
                    </form>
                </div>

                <!-- Laporan Per Kelas -->
                <div class="bg-white rounded-2xl shadow-lg p-6 animate-slide-up" style="animation-delay: 0.6s">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-school text-school-green text-xl"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Laporan Per Kelas</h3>
                    </div>
                    <form id="formLaporanKelas">
                        <div class="form-group">
                            <label for="id_kelas">Pilih Kelas</label>
                            <select name="id_kelas" id="id_kelas" class="form-control">
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach($kelas as $k): ?>
                                <option value="<?= $k->id_kelas ?>"><?= $k->nama_kelas ?> (Tingkat <?= $k->tingkat ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bulan_kelas">Pilih Bulan</label>
                            <select name="bulan_kelas" id="bulan_kelas" class="form-control">
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tahun_kelas">Pilih Tahun</label>
                            <select name="tahun_kelas" id="tahun_kelas" class="form-control">
                                <?php 
                                $tahun_sekarang = date('Y');
                                for($tahun = $tahun_sekarang; $tahun >= 2020; $tahun--) {
                                    echo '<option value="' . $tahun . '">' . $tahun . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-full">
                            <i class="fas fa-file-pdf"></i>
                            <span>Cetak Laporan Kelas</span>
                        </button>
                    </form>
                </div>

                <!-- Rekap Kehadiran -->
                <div class="bg-white rounded-2xl shadow-lg p-6 animate-slide-up" style="animation-delay: 0.7s">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-users text-school-green text-xl"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Rekap Kehadiran Guru</h3>
                    </div>
                    <form id="formRekapKehadiran">
                        <div class="form-group">
                            <label for="bulan_rekap">Pilih Bulan</label>
                            <select name="bulan_rekap" id="bulan_rekap" class="form-control">
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tahun_rekap">Pilih Tahun</label>
                            <select name="tahun_rekap" id="tahun_rekap" class="form-control">
                                <?php 
                                $tahun_sekarang = date('Y');
                                for($tahun = $tahun_sekarang; $tahun >= 2020; $tahun--) {
                                    echo '<option value="' . $tahun . '">' . $tahun . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info w-full">
                            <i class="fas fa-file-pdf"></i>
                            <span>Cetak Rekap Kehadiran</span>
                        </button>
                    </form>
                </div>

            </div>

            <!-- INFO SECTION -->
            <div class="bg-white rounded-2xl shadow-lg p-6 animate-slide-up" style="animation-delay: 0.8s">
                <div class="flex items-center gap-3 mb-4">
                    <i class="fas fa-info-circle text-school-green text-xl"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Informasi Penting</h3>
                </div>
                <div class="alert alert-info bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Pastikan data sekolah sudah diisi dengan lengkap untuk kop surat dan tanda tangan pada laporan PDF.
                                <a href="<?= base_url('sekolah') ?>" class="btn btn-sm btn-primary ml-2">
                                    <i class="fas fa-school"></i> Data Sekolah
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </main>

    </div>

    <script src="<?= base_url('assets/js/laporan/laporan.js'); ?>"></script>
</body>

</html>