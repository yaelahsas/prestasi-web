<?php
$page_title   = 'Data Jurnal';
$active_menu  = 'jurnal';
$body_class   = 'bg-gradient-to-br from-school-light-green to-white';
$extra_css    = ['https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css'];
$extra_js_head = ['https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js'];
$extra_style  = <<<'CSS'

        .image-modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.8); animation: fadeIn 0.3s; }
        .image-modal.show { display: flex; align-items: center; justify-content: center; }
        .image-modal img { max-width: 90%; max-height: 90%; border-radius: 8px; }
        .close-image { position: absolute; top: 20px; right: 40px; color: white; font-size: 40px; font-weight: bold; cursor: pointer; }
        .close-image:hover { color: #ccc; }
        .modal-body { max-height: 70vh; overflow-y: auto; }
CSS;
$topbar_title   = 'Data Jurnal';
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
                    <i class="fas fa-book text-school-green"></i>
                    Data Jurnal
                </h1>
                <p class="text-gray-600 mt-1">Kelola data jurnal kegiatan bimbingan belajar</p>
            </div>

            <!-- STAT CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

                <!-- Total Jurnal -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                <i class="fas fa-book text-school-green"></i>
                                Total Jurnal
                            </p>
                            <h2 class="text-3xl font-bold text-gray-800 mt-2" id="totalJurnal">
                                <?= $total_jurnal; ?>
                            </h2>
                            <div class="mt-2 flex items-center text-xs text-green-600">
                                <i class="fas fa-book mr-1"></i>
                                <span>Semua periode</span>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center shadow-lg">
                            <i class="fas fa-book text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Jurnal Hari Ini -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green" style="animation-delay: 0.1s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                <i class="fas fa-calendar-day text-school-green"></i>
                                Jurnal Hari Ini
                            </p>
                            <h2 class="text-3xl font-bold text-gray-800 mt-2" id="jurnalHariIni">
                                 <?= $total_hari; ?>
                            </h2>
                            <div class="mt-2 flex items-center text-xs text-green-600">
                                <i class="fas fa-clock mr-1"></i>
                                <span><?= date('d/m/Y'); ?></span>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center shadow-lg">
                            <i class="fas fa-calendar-day text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Jurnal Bulan Ini -->
                <div class="stat-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-yellow" style="animation-delay: 0.2s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-school-yellow"></i>
                                Jurnal Bulan Ini
                            </p>
                            <h2 class="text-3xl font-bold text-gray-800 mt-2" id="jurnalBulanIni">
                                 <?= $total_bulan; ?>
                            </h2>
                            <div class="mt-2 flex items-center text-xs text-yellow-600">
                                <i class="fas fa-calendar mr-1"></i>
                                <span><?= date('F Y'); ?></span>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-school-light-yellow to-school-yellow flex items-center justify-center shadow-lg">
                            <i class="fas fa-calendar-alt text-white text-2xl"></i>
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
                                <input type="text" id="searchInput" placeholder="Cari tanggal, guru, atau materi..." 
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
                    <span>Tambah Jurnal</span>
                </button>
                <button onclick="refreshTable()" class="btn btn-success">
                    <i class="fas fa-sync-alt"></i>
                    <span>Refresh Data</span>
                </button>
                <button onclick="openFilterModal()" class="btn btn-warning">
                    <i class="fas fa-filter"></i>
                    <span>Filter Tanggal</span>
                </button>
            </div>

            <!-- DATA TABLE -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden animate-slide-up" style="animation-delay: 0.5s">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-table text-school-green"></i>
                        Tabel Data Jurnal
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table id="jurnalTable" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tanggal</th>
                                <th>Guru</th>
                                <th>Kelas</th>
                                <th>Mapel</th>
                                <th>Materi</th>
                                <th>Jumlah Siswa</th>
                                <th>Foto Bukti</th>
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
    <div id="jurnalModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-xl font-bold" id="modalTitle">Tambah Jurnal</h3>
                <button type="button" class="text-white hover:text-gray-200" onclick="closeModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="jurnalForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="id_jurnal" name="id_jurnal">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="tanggal">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" id="tanggal" name="tanggal" class="form-control" required>
                            <div class="error-message" id="tanggal_error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="id_guru">Guru <span class="text-red-500">*</span></label>
                            <select id="id_guru" name="id_guru" class="form-control" required>
                                <option value="">-- Pilih Guru --</option>
                            </select>
                            <div class="error-message" id="id_guru_error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="id_kelas">Kelas <span class="text-red-500">*</span></label>
                            <select id="id_kelas" name="id_kelas" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                            </select>
                            <div class="error-message" id="id_kelas_error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="id_mapel">Mata Pelajaran <span class="text-red-500">*</span></label>
                            <select id="id_mapel" name="id_mapel" class="form-control" required>
                                <option value="">-- Pilih Mata Pelajaran --</option>
                            </select>
                            <div class="error-message" id="id_mapel_error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="jumlah_siswa">Jumlah Siswa <span class="text-red-500">*</span></label>
                            <input type="number" id="jumlah_siswa" name="jumlah_siswa" class="form-control" min="1" required>
                            <div class="error-message" id="jumlah_siswa_error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="foto_bukti">Foto Bukti</label>
                            <input type="file" id="foto_bukti" name="foto_bukti" class="form-control" accept="image/*">
                            <small class="text-gray-500">Maksimal 2MB, format: JPG, PNG, GIF</small>
                            <div class="error-message" id="foto_bukti_error"></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="materi">Materi <span class="text-red-500">*</span></label>
                        <textarea id="materi" name="materi" class="form-control" rows="3" required></textarea>
                        <div class="error-message" id="materi_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" class="form-control" rows="2"></textarea>
                        <div class="error-message" id="keterangan_error"></div>
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

    <!-- Modal View Jurnal -->
    <div id="viewJurnalModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-xl font-bold">Detail Jurnal</h3>
                <button type="button" class="text-white hover:text-gray-200" onclick="closeViewModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="modal-body" id="viewJurnalContent">
                <!-- Content akan dimuat via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeViewModal()">
                    <i class="fas fa-times"></i>
                    <span>Tutup</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Filter Tanggal -->
    <div id="filterModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-xl font-bold">Filter Berdasarkan Tanggal</h3>
                <button type="button" class="text-white hover:text-gray-200" onclick="closeFilterModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="tanggal_awal">Tanggal Awal <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal_awal" name="tanggal_awal" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="tanggal_akhir">Tanggal Akhir <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal_akhir" name="tanggal_akhir" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeFilterModal()">
                    <i class="fas fa-times"></i>
                    <span>Batal</span>
                </button>
                <button type="button" class="btn btn-primary" onclick="applyFilter()">
                    <i class="fas fa-filter"></i>
                    <span>Terapkan Filter</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div id="imageModal" class="image-modal">
        <span class="close-image" onclick="closeImageModal()">&times;</span>
        <img id="previewImage" src="" alt="Preview">
    </div>

    <script src="<?= base_url('assets/js/jurnal/jurnal.js'); ?>"></script>

<?php $this->load->view('templates/footer'); ?>
