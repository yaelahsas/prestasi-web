<?php
$page_title   = 'Data Kelas';
$active_menu  = 'kelas';
$body_class   = 'bg-gradient-to-br from-school-light-green to-white';
$extra_css    = ['https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css'];
$extra_js_head = ['https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js'];
$topbar_title   = 'Data Kelas';
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
                    <i class="fas fa-school text-school-green"></i>
                    Data Kelas
                </h1>
                <p class="text-gray-600 mt-1">Kelola data kelas bimbingan belajar</p>
            </div>

            <!-- STAT CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

                <!-- Total Kelas -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                <i class="fas fa-school text-school-green"></i>
                                Total Kelas
                            </p>
                            <h2 class="text-3xl font-bold text-gray-800 mt-2" id="totalKelas">
                                <?= $total_kelas; ?>
                            </h2>
                            <div class="mt-2 flex items-center text-xs text-green-600">
                                <i class="fas fa-users mr-1"></i>
                                <span>Semua status</span>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center shadow-lg">
                            <i class="fas fa-school text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Kelas Aktif -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green" style="animation-delay: 0.1s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                <i class="fas fa-check-circle text-school-green"></i>
                                Kelas Aktif
                            </p>
                            <h2 class="text-3xl font-bold text-gray-800 mt-2" id="kelasAktif">
                                0
                            </h2>
                            <div class="mt-2 flex items-center text-xs text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>
                                <span>Sedang digunakan</span>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center shadow-lg">
                            <i class="fas fa-check-circle text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Kelas Nonaktif -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-yellow" style="animation-delay: 0.2s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                <i class="fas fa-pause-circle text-school-yellow"></i>
                                Kelas Nonaktif
                            </p>
                            <h2 class="text-3xl font-bold text-gray-800 mt-2" id="kelasNonaktif">
                                0
                            </h2>
                            <div class="mt-2 flex items-center text-xs text-yellow-600">
                                <i class="fas fa-pause-circle mr-1"></i>
                                <span>Tidak digunakan</span>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-school-light-yellow to-school-yellow flex items-center justify-center shadow-lg">
                            <i class="fas fa-pause-circle text-white text-2xl"></i>
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
                                <input type="text" id="searchInput" placeholder="Cari nama atau tingkat..." 
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
                    <span>Tambah Kelas</span>
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
                        Tabel Data Kelas
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table id="kelasTable" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Kelas</th>
                                <th>Tingkat</th>
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

        </main>

    </div>

    <!-- Modal Form -->
    <div id="kelasModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-xl font-bold" id="modalTitle">Tambah Kelas</h3>
                <button type="button" class="text-white hover:text-gray-200" onclick="closeModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="kelasForm">
                <div class="modal-body">
                    <input type="hidden" id="id_kelas" name="id_kelas">
                    
                    <div class="form-group">
                        <label for="nama_kelas">Nama Kelas <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_kelas" name="nama_kelas" class="form-control" required>
                        <div class="error-message" id="nama_kelas_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="tingkat">Tingkat <span class="text-red-500">*</span></label>
                        <input type="text" id="tingkat" name="tingkat" class="form-control" required>
                        <div class="error-message" id="tingkat_error"></div>
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

    <script src="<?= base_url('assets/js/kelas/kelas.js'); ?>"></script>

<?php $this->load->view('templates/footer'); ?>
