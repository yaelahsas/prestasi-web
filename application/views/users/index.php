<?php
$page_title   = 'Pengguna';
$active_menu  = 'users';
$body_class   = 'bg-gradient-to-br from-school-light-green to-white';
$extra_css    = ['https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css'];
$extra_js_head = ['https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js'];
$topbar_title   = 'Pengguna';
$topbar_actions = <<<'HTML'

    <button id="refreshBtn" class="p-2 rounded-lg bg-green-50 hover:bg-school-green text-school-dark-green hover:text-white transition-all duration-200 group">
        <i class="fas fa-sync-alt group-hover:rotate-180 transition-transform duration-500 text-sm"></i>
    </button>
HTML;
$this->load->view('templates/header');
$this->load->view('templates/sidebar');
?>

<!-- ===== MAIN ===== -->
<div class="md:ml-64 min-h-screen">

    <?php $this->load->view('templates/topbar'); ?>

        <!-- CONTENT -->
        <main class="p-4">

            <!-- Page Title -->
            <div class="mb-6 animate-fade-in">
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <i class="fas fa-users text-school-green"></i>
                    Data Pengguna
                </h1>
                <p class="text-gray-600 mt-1">Kelola data pengguna sistem</p>
            </div>

            <!-- STAT CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

                <!-- Total Users -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                <i class="fas fa-users text-school-green"></i>
                                Total Pengguna
                            </p>
                            <h2 class="text-3xl font-bold text-gray-800 mt-2" id="totalUsers">
                                <?= $total_users; ?>
                            </h2>
                            <div class="mt-2 flex items-center text-xs text-green-600">
                                <i class="fas fa-users mr-1"></i>
                                <span>Semua status</span>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center shadow-lg">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Users Aktif -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green" style="animation-delay: 0.1s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                <i class="fas fa-user-check text-school-green"></i>
                                Pengguna Aktif
                            </p>
                            <h2 class="text-3xl font-bold text-gray-800 mt-2" id="usersAktif">
                                0
                            </h2>
                            <div class="mt-2 flex items-center text-xs text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>
                                <span>Sedang aktif</span>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center shadow-lg">
                            <i class="fas fa-user-check text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Users Nonaktif -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-yellow" style="animation-delay: 0.2s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                <i class="fas fa-user-times text-school-yellow"></i>
                                Pengguna Nonaktif
                            </p>
                            <h2 class="text-3xl font-bold text-gray-800 mt-2" id="usersNonaktif">
                                0
                            </h2>
                            <div class="mt-2 flex items-center text-xs text-yellow-600">
                                <i class="fas fa-pause-circle mr-1"></i>
                                <span>Tidak aktif</span>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-school-light-yellow to-school-yellow flex items-center justify-center shadow-lg">
                            <i class="fas fa-user-times text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Search Box -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green" style="animation-delay: 0.3s">
                    <div class="flex items-center justify-between">
                        <div class="w-full">
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2 mb-3">
                                <i class="fas fa-search text-school-green"></i>
                                Pencarian
                            </p>
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="Cari nama atau username..." 
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
            <div class="flex flex-wrap gap-3 mb-6 animate-slide-up" style="animation-delay: 0.4s">
                <button onclick="openModal()" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Pengguna</span>
                </button>
                <button onclick="refreshTable()" class="btn btn-success">
                    <i class="fas fa-sync-alt"></i>
                    <span>Refresh Data</span>
                </button>
            </div>

            <!-- DATA TABLE -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden animate-slide-up" style="animation-delay: 0.5s">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-table text-school-green"></i>
                        Tabel Data Pengguna
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table id="usersTable" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Dibuat</th>
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
    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-xl font-bold" id="modalTitle">Tambah Pengguna</h3>
                <button type="button" class="text-white hover:text-gray-200" onclick="closeModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="id_user" name="id_user">
                    
                    <div class="form-group">
                        <label for="nama">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" id="nama" name="nama" class="form-control" required>
                        <div class="error-message" id="nama_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username <span class="text-red-500">*</span></label>
                        <input type="text" id="username" name="username" class="form-control" required>
                        <div class="error-message" id="username_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password <span id="passwordRequired" class="text-red-500">*</span></label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                        <div class="error-message" id="password_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Role <span class="text-red-500">*</span></label>
                        <select id="role" name="role" class="form-control" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="tim">Tim</option>
                        </select>
                        <div class="error-message" id="role_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status <span class="text-red-500">*</span></label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                        <div class="error-message" id="status_error"></div>
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

    <script src="<?= base_url('assets/js/users/users.js'); ?>"></script>

<?php $this->load->view('templates/footer'); ?>
