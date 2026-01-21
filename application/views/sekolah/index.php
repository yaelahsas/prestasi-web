<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Sekolah - Sistem Prestasi</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    
    <script>
        const base_url = '<?= base_url(); ?>';
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    
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
            max-width: 600px;
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

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
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

        /* File upload styling */
        .file-upload {
            position: relative;
            display: inline-block;
            cursor: pointer;
            width: 100%;
        }

        .file-upload input[type=file] {
            position: absolute;
            left: -9999px;
        }

        .file-upload-label {
            display: block;
            padding: 10px 14px;
            border: 2px dashed #e5e7eb;
            border-radius: 8px;
            background: #f9fafb;
            text-align: center;
            transition: all 0.3s;
        }

        .file-upload-label:hover {
            border-color: #22c55e;
            background: #f0fdf4;
        }

        .current-logo {
            margin-top: 10px;
            text-align: center;
        }

        .current-logo img {
            max-width: 100px;
            max-height: 100px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

            <a href="<?= base_url('sekolah') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg bg-school-light-green text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-school w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Sekolah</span>
            </a>

            <a href="<?= base_url('laporan') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
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
                <h2 class="text-xl font-semibold text-gray-800 hidden sm:block">Data Sekolah</h2>
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
                    <i class="fas fa-school text-school-green"></i>
                    Data Sekolah
                </h1>
                <p class="text-gray-600 mt-1">Kelola data sekolah untuk keperluan laporan</p>
            </div>

            <!-- STAT CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

                <!-- Total Sekolah -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                <i class="fas fa-school text-school-green"></i>
                                Total Sekolah
                            </p>
                            <h2 class="text-3xl font-bold text-gray-800 mt-2" id="totalSekolah">
                                <?= $total_sekolah; ?>
                            </h2>
                            <div class="mt-2 flex items-center text-xs text-green-600">
                                <i class="fas fa-school mr-1"></i>
                                <span>Semua data sekolah</span>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center shadow-lg">
                            <i class="fas fa-school text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Search Box -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green" style="animation-delay: 0.1s">
                    <div class="flex items-center justify-between">
                        <div class="w-full">
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2 mb-3">
                                <i class="fas fa-search text-school-green"></i>
                                Pencarian
                            </p>
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="Cari nama sekolah atau alamat..." 
                                    class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:border-school-green">
                                <button id="searchBtn" class="absolute right-2 top-2 text-school-green hover:text-school-dark-green">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ACTION BUTTONS -->
            <div class="flex flex-wrap gap-3 mb-6 animate-slide-up" style="animation-delay: 0.2s">
                <button onclick="openModal()" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Sekolah</span>
                </button>
                <button onclick="refreshTable()" class="btn btn-success">
                    <i class="fas fa-sync-alt"></i>
                    <span>Refresh Data</span>
                </button>
            </div>

            <!-- DATA TABLE -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden animate-slide-up" style="animation-delay: 0.3s">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-table text-school-green"></i>
                        Tabel Data Sekolah
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table id="sekolahTable" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Sekolah</th>
                                <th>Alamat</th>
                                <th>Kepala Sekolah</th>
                                <th>NIP Kepala Sekolah</th>
                                <th>Logo</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan dimuat via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

        </main>

    </div>

    <!-- Modal Form -->
    <div id="sekolahModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-xl font-bold" id="modalTitle">Tambah Sekolah</h3>
                <button type="button" class="text-white hover:text-gray-200" onclick="closeModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="sekolahForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="id_sekolah" name="id_sekolah">
                    
                    <div class="form-group">
                        <label for="nama_sekolah">Nama Sekolah <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_sekolah" name="nama_sekolah" class="form-control" required>
                        <div class="error-message" id="nama_sekolah_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat" class="form-control" rows="3"></textarea>
                        <div class="error-message" id="alamat_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="kepala_sekolah">Kepala Sekolah</label>
                        <input type="text" id="kepala_sekolah" name="kepala_sekolah" class="form-control">
                        <div class="error-message" id="kepala_sekolah_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="nip_kepsek">NIP Kepala Sekolah</label>
                        <input type="text" id="nip_kepsek" name="nip_kepsek" class="form-control" placeholder="Opsional, 18 digit angka">
                        <div class="error-message" id="nip_kepsek_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="logo">Logo Sekolah</label>
                        <div class="file-upload">
                            <input type="file" id="logo" name="logo" class="form-control" accept="image/*">
                            <label for="logo" class="file-upload-label">
                                <i class="fas fa-cloud-upload-alt mr-2"></i>
                                <span id="fileLabel">Pilih file logo (jpg, png, gif - max 2MB)</span>
                            </label>
                        </div>
                        <div class="current-logo" id="currentLogo"></div>
                        <div class="error-message" id="logo_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">
                        <i class="fas fa-times"></i>
                        <span>Batal</span>
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        <div class="spinner"></div>
                        <span class="btn-text">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?= base_url('assets/js/sekolah/sekolah.js'); ?>"></script>

</body>

</html>