<?php
$page_title   = 'Data Sekolah';
$active_menu  = 'sekolah';
$body_class   = 'bg-gradient-to-br from-school-light-green to-white';
$topbar_title   = 'Data Sekolah';
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

    <!-- Debug Modal -->
    <div id="debugModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-xl font-bold">Debug Information</h3>
                <button type="button" class="text-white hover:text-gray-200" onclick="closeDebugModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="modal-body">
                <pre id="debugContent"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDebugModal()">
                    <i class="fas fa-times"></i>
                    <span>Tutup</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Rename original functions to avoid conflicts
        let openModalOriginal, closeModalOriginal;
        
        function closeDebugModal() {
            $('#debugModal').removeClass('show');
        }
        
        function showDebug(content) {
            $('#debugContent').text(JSON.stringify(content, null, 2));
            $('#debugModal').addClass('show');
        }
        
        $(document).ready(function() {
            // Store original functions
            openModalOriginal = openModal;
            closeModalOriginal = closeModal;
            
            // Override with new functions that include auto-refresh logic
            window.openModal = function(id = null) {
                console.log('openModal called with ID:', id);
                stopAutoRefresh();
                openModalOriginal(id);
            };
            
            window.closeModal = function() {
                console.log('closeModal called');
                closeModalOriginal();
                setTimeout(startAutoRefresh, 1000);
            };
            
            // Debug: Check if modal exists
            console.log('Modal element:', $('#sekolahModal').length);
            console.log('Base URL:', base_url);
        });
    </script>
    <script src="<?= base_url('assets/js/sekolah/sekolah.js'); ?>"></script>

<?php $this->load->view('templates/footer'); ?>
