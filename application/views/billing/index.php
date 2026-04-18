<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Billing - Sistem Prestasi</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        const base_url = '<?= base_url(); ?>';
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    
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
            50% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
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

        /* DataTable Custom Styling */
        .dataTables_wrapper {
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dataTables_filter input {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px 12px;
            margin-left: 8px;
        }

        .dataTables_filter input:focus {
            border-color: #22c55e;
            outline: none;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
        }

        .dataTables_length select {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 6px 10px;
        }

        .dataTables_paginate .paginate_button {
            border-radius: 6px;
            margin: 0 2px;
        }

        .dataTables_paginate .paginate_button.current {
            background: #22c55e !important;
            color: white !important;
            border: 1px solid #22c55e !important;
        }

        .dataTables_paginate .paginate_button:hover {
            background: #86efac !important;
            color: #16a34a !important;
            border: 1px solid #86efac !important;
        }

        /* Modal Custom Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            margin: auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            background: linear-gradient(to right, #22c55e, #16a34a);
            color: white;
            padding: 20px;
            border-radius: 16px 16px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 24px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            border-radius: 0 0 16px 16px;
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

        .error-message {
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
            display: none;
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

        /* Tab Styling */
        .tab-container {
            display: flex;
            gap: 4px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }

        .tab-btn {
            padding: 12px 24px;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-weight: 600;
            color: #6b7280;
            transition: all 0.3s;
        }

        .tab-btn:hover {
            color: #22c55e;
            background: #f0fdf4;
        }

        .tab-btn.active {
            color: #22c55e;
            border-bottom-color: #22c55e;
            background: #f0fdf4;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            padding-left: 14px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }

        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #22c55e;
            outline: none;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #22c55e;
        }

        .select2-container--default .select2-dropdown {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px 12px;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #22c55e;
            outline: none;
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
        class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl transform -translate-x-full md:translate-x-0 transition-all duration-300 ease-in-out z-50 md:shadow-2xl flex flex-col">

        <div class="bg-gradient-to-r from-school-green to-school-dark-green p-5 flex items-center gap-3 shadow-md">
            <i class="fas fa-graduation-cap text-3xl text-white animate-pulse-slow"></i>
            <span class="font-bold text-xl text-white">Sistem Prestasi</span>
        </div>

        <nav class="p-4 space-y-2 flex-1 overflow-y-auto">
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

            <a href="<?= base_url('ekstra') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-star w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Ekstrakurikuler</span>
            </a>

            <a href="<?= base_url('billing') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg bg-school-light-green text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-file-invoice-dollar w-5 text-center group-hover:scale-110 transition-transform"></i> 
                <span class="font-medium">Billing</span>
            </a>

            <a href="<?= base_url('users') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-users w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Pengguna</span>
            </a>

            <a href="<?= base_url('sekolah') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-school w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Sekolah</span>
            </a>

            <a href="<?= base_url('laporan') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-file-alt w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Laporan</span>
            </a>

            <a href="<?= base_url('whatsapp') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fab fa-whatsapp w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">WhatsApp Bot</span>
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
        <header class="bg-white shadow-md flex items-center justify-between px-4 h-16 sticky top-0 z-40 backdrop-blur-lg bg-opacity-95">

            <div class="flex items-center gap-4">
                <button id="btnSidebar" class="md:hidden text-2xl text-school-green hover:text-school-dark-green transition-colors">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 class="text-xl font-semibold text-gray-800 hidden sm:block">Sistem Billing Guru</h2>
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
                    <i class="fas fa-file-invoice-dollar text-school-green"></i>
                    Billing Jurnal Guru
                </h1>
                <p class="text-gray-600 mt-1">Kelola billing honor guru berdasarkan jurnal harian</p>
            </div>

            <!-- TABS -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 animate-slide-up">
                <div class="tab-container">
                    <button class="tab-btn active" onclick="switchTab('periode')">
                        <i class="fas fa-calendar-alt mr-2"></i>Periode
                    </button>
                    <button class="tab-btn" onclick="switchTab('billing')">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>Billing
                    </button>
                    <button class="tab-btn" onclick="switchTab('tarif')">
                        <i class="fas fa-tags mr-2"></i>Tarif
                    </button>
                </div>

                <!-- TAB CONTENT: PERIODE -->
                <div id="tab-periode" class="tab-content active">
                    <!-- ACTION BUTTONS -->
                    <div class="flex flex-wrap gap-3 mb-6">
                        <button onclick="openPeriodModal()" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Periode</span>
                        </button>
                        <button onclick="refreshPeriodTable()" class="btn btn-success">
                            <i class="fas fa-sync-alt"></i>
                            <span>Refresh Data</span>
                        </button>
                    </div>

                    <!-- DATA TABLE -->
                    <div class="overflow-x-auto">
                        <table id="periodTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Periode</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan dimuat via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB CONTENT: BILLING -->
                <div id="tab-billing" class="tab-content">
                    <!-- ACTION BUTTONS -->
                    <div class="flex flex-wrap gap-3 mb-6">
                        <button onclick="openGenerateModal()" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            <span>Generate Billing</span>
                        </button>
                        <button onclick="refreshBillingTable()" class="btn btn-success">
                            <i class="fas fa-sync-alt"></i>
                            <span>Refresh Data</span>
                        </button>
                    </div>

                    <!-- DATA TABLE -->
                    <div class="overflow-x-auto">
                        <table id="billingTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Kode Billing</th>
                                    <th>Guru</th>
                                    <th>NIP</th>
                                    <th>Periode</th>
                                    <th>Total Jurnal</th>
                                    <th>Total Honor</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan dimuat via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB CONTENT: TARIF -->
                <div id="tab-tarif" class="tab-content">
                    <div class="flex flex-wrap gap-3 mb-6">
                        <button onclick="openTarifModal()" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Tarif</span>
                        </button>
                        <button onclick="refreshTarifTable()" class="btn btn-success">
                            <i class="fas fa-sync-alt"></i>
                            <span>Refresh Data</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="tarifTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Jenis Kegiatan</th>
                                    <th>Tarif</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan dimuat via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>

    </div>

    <!-- Modal Periode -->
    <div id="periodModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-xl font-bold" id="periodModalTitle">Tambah Periode</h3>
                <button type="button" class="text-white hover:text-gray-200" onclick="closePeriodModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="periodForm">
                <div class="modal-body">
                    <input type="hidden" id="id_period" name="id_period">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="bulan">Bulan <span class="text-red-500">*</span></label>
                            <select id="bulan" name="bulan" class="form-control" required>
                                <option value="">-- Pilih Bulan --</option>
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="tahun">Tahun <span class="text-red-500">*</span></label>
                            <select id="tahun" name="tahun" class="form-control" required>
                                <option value="">-- Pilih Tahun --</option>
                                <option value="2024">2024</option>
                                <option value="2025">2025</option>
                                <option value="2026">2026</option>
                                <option value="2027">2027</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="tanggal_mulai">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="tanggal_selesai">Tanggal Selesai <span class="text-red-500">*</span></label>
                            <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status <span class="text-red-500">*</span></label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="draft">Draft</option>
                                <option value="aktif">Aktif</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closePeriodModal()">
                        <i class="fas fa-times"></i>
                        <span>Batal</span>
                    </button>
                    <button type="submit" class="btn btn-primary" id="savePeriodBtn">
                        <div class="spinner"></div>
                        <span class="btn-text">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Generate Billing -->
    <div id="generateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-xl font-bold">Generate Billing</h3>
                <button type="button" class="text-white hover:text-gray-200" onclick="closeGenerateModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="generateForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="generate_id_period">Periode <span class="text-red-500">*</span></label>
                        <select id="generate_id_period" name="id_period" class="form-control select2-period" required>
                            <option value="">-- Pilih Periode --</option>
                        </select>
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-semibold mb-2">
                            <i class="fas fa-info-circle mr-2"></i>Informasi
                        </h4>
                        <p class="text-sm text-gray-600">
                            Generate billing akan membuat billing untuk semua guru aktif berdasarkan jurnal yang diinput dalam periode yang dipilih.
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeGenerateModal()">
                        <i class="fas fa-times"></i>
                        <span>Batal</span>
                    </button>
                    <button type="submit" class="btn btn-primary" id="generateBtn">
                        <div class="spinner"></div>
                        <span class="btn-text">Generate</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal View Billing -->
    <div id="viewBillingModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-xl font-bold">Detail Billing</h3>
                <button type="button" class="text-white hover:text-gray-200" onclick="closeViewBillingModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="modal-body" id="viewBillingContent">
                <!-- Content akan dimuat via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeViewBillingModal()">
                    <i class="fas fa-times"></i>
                    <span>Tutup</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Tarif -->
    <div id="tarifModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-xl font-bold" id="tarifModalTitle">Tambah Tarif</h3>
                <button type="button" class="text-white hover:text-gray-200" onclick="closeTarifModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="tarifForm">
                <div class="modal-body">
                    <input type="hidden" id="id_tarif" name="id_tarif">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="jenis_kegiatan">Jenis Kegiatan <span class="text-red-500">*</span></label>
                            <select id="jenis_kegiatan" name="jenis_kegiatan" class="form-control" required>
                                <option value="">-- Pilih Jenis Kegiatan --</option>
                                <option value="reguler">Reguler</option>
                                <option value="olimpiade">Olimpiade</option>
                                <option value="luring">Luring</option>
                                <option value="daring">Daring</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="tarif">Tarif per Jurnal <span class="text-red-500">*</span></label>
                            <input type="number" id="tarif" name="tarif" class="form-control" min="0" step="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status <span class="text-red-500">*</span></label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeTarifModal()">
                        <i class="fas fa-times"></i>
                        <span>Batal</span>
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveTarifBtn">
                        <div class="spinner"></div>
                        <span class="btn-text">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?= base_url('assets/js/billing/billing.js'); ?>"></script>

</body>

</html>
