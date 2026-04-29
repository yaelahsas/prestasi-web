<!-- Page Title -->
<div class="mb-6 animate-fade-in">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
        <i class="fas fa-star text-school-green"></i>
        Data Ekstrakurikuler
    </h1>
    <p class="text-gray-600 mt-1">Kelola data kegiatan ekstrakurikuler</p>
</div>

<!-- STAT CARDS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

    <!-- Total Ekstra -->
    <div class="stat-card bg-white rounded-xl shadow-md p-4 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs text-gray-600 font-medium flex items-center gap-2 mb-1">
                    <i class="fas fa-star text-school-green text-sm"></i>
                    Total Ekstra
                </p>
                <h2 class="text-2xl font-bold text-gray-800" id="totalEkstra">
                    <?= $total_ekstra; ?>
                </h2>
                <div class="mt-1 flex items-center text-xs text-green-600">
                    <i class="fas fa-star mr-1"></i>
                    <span>Semua status</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center shadow-md ml-3 flex-shrink-0">
                <i class="fas fa-star text-white text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Ekstra Aktif -->
    <div class="stat-card bg-white rounded-xl shadow-md p-4 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green" style="animation-delay: 0.1s">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs text-gray-600 font-medium flex items-center gap-2 mb-1">
                    <i class="fas fa-check-circle text-school-green text-sm"></i>
                    Ekstra Aktif
                </p>
                <h2 class="text-2xl font-bold text-gray-800" id="ekstraAktif">
                    0
                </h2>
                <div class="mt-1 flex items-center text-xs text-green-600">
                    <i class="fas fa-check-circle mr-1"></i>
                    <span>Sedang berjalan</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center shadow-md ml-3 flex-shrink-0">
                <i class="fas fa-check-circle text-white text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Ekstra Nonaktif -->
    <div class="stat-card bg-white rounded-xl shadow-md p-4 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-yellow" style="animation-delay: 0.2s">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs text-gray-600 font-medium flex items-center gap-2 mb-1">
                    <i class="fas fa-pause-circle text-school-yellow text-sm"></i>
                    Ekstra Nonaktif
                </p>
                <h2 class="text-2xl font-bold text-gray-800" id="ekstraNonaktif">
                    0
                </h2>
                <div class="mt-1 flex items-center text-xs text-yellow-600">
                    <i class="fas fa-pause-circle mr-1"></i>
                    <span>Tidak berjalan</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-school-light-yellow to-school-yellow flex items-center justify-center shadow-md ml-3 flex-shrink-0">
                <i class="fas fa-pause-circle text-white text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Search Box -->
    <div class="stat-card bg-white rounded-xl shadow-md p-4 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green" style="animation-delay: 0.3s">
        <div class="flex items-center justify-between">
            <div class="w-full">
                <p class="text-xs text-gray-600 font-medium flex items-center gap-2 mb-2">
                    <i class="fas fa-search text-school-green text-sm"></i>
                    Pencarian
                </p>
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari ekstrakurikuler..."
                        class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:border-school-green text-sm">
                    <button id="searchBtn" class="absolute right-2 top-2 text-school-green hover:text-school-dark-green">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ACTION BUTTONS -->
<div class="flex flex-wrap gap-2 mb-6 animate-slide-up" style="animation-delay: 0.4s">
    <button onclick="openModal()" class="btn btn-primary text-sm sm:text-base">
        <i class="fas fa-plus"></i>
        <span class="hidden sm:inline">Tambah Ekstrakurikuler</span>
        <span class="sm:hidden">Tambah</span>
    </button>
    <button onclick="refreshTable()" class="btn btn-success text-sm sm:text-base">
        <i class="fas fa-sync-alt"></i>
        <span class="hidden sm:inline">Refresh Data</span>
        <span class="sm:hidden">Refresh</span>
    </button>
</div>

<!-- DATA TABLE -->
<div class="bg-white rounded-xl shadow-md overflow-hidden animate-slide-up" style="animation-delay: 0.5s">
    <div class="p-3 sm:p-4 border-b border-gray-200">
        <h3 class="text-base sm:text-lg font-semibold text-gray-800 flex items-center gap-2">
            <i class="fas fa-table text-school-green text-sm sm:text-base"></i>
            <span class="hidden sm:inline">Tabel Data Ekstrakurikuler</span>
            <span class="sm:hidden">Data Ekstra</span>
        </h3>
    </div>
    <div class="table-responsive overflow-x-auto">
        <table id="ekstraTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th class="text-xs sm:text-sm text-center" style="width: 50px;">No</th>
                    <th class="text-xs sm:text-sm">Nama Ekstrakurikuler</th>
                    <th class="text-xs sm:text-sm text-center" style="width: 120px;">Jml Pembina</th>
                    <th class="text-xs sm:text-sm text-center" style="width: 100px;">Status</th>
                    <th class="text-xs sm:text-sm text-center" style="width: 150px;">Aksi</th>
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
<div id="ekstraModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="text-lg sm:text-xl font-bold" id="modalTitle">Tambah Ekstrakurikuler</h3>
            <button type="button" class="text-white hover:text-gray-200" onclick="closeModal()">
                <i class="fas fa-times text-lg sm:text-xl"></i>
            </button>
        </div>
        <form id="ekstraForm">
            <div class="modal-body">
                <input type="hidden" id="id_ekstra" name="id_ekstra">

                <div class="form-group">
                    <label for="nama_ekstra" class="text-sm sm:text-base">Nama Ekstrakurikuler <span class="text-red-500">*</span></label>
                    <input type="text" id="nama_ekstra" name="nama_ekstra" class="form-control text-sm sm:text-base" required>
                    <div class="error-message" id="nama_ekstra_error"></div>
                </div>

                <div class="form-group">
                    <label for="guru_ids" class="text-sm sm:text-base">Pembina <span class="text-red-500">*</span></label>
                    <select id="guru_ids" name="guru_ids[]" class="form-control text-sm sm:text-base" multiple>
                    </select>
                    <small class="text-gray-500 text-xs">Pilih satu atau lebih guru</small>
                    <div class="error-message" id="guru_ids_error"></div>
                </div>

                <div class="form-group">
                    <label for="deskripsi" class="text-sm sm:text-base">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control text-sm sm:text-base" rows="3"></textarea>
                    <div class="error-message" id="deskripsi_error"></div>
                </div>

                <div class="form-group">
                    <label for="status" class="text-sm sm:text-base">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" class="form-control text-sm sm:text-base" required>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                    <div class="error-message" id="status_error"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary text-sm sm:text-base" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                    <span>Batal</span>
                </button>
                <button type="submit" class="btn btn-primary text-sm sm:text-base" id="saveBtn">
                    <div class="spinner"></div>
                    <span class="btn-text">Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Guru Details -->
<div id="guruDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="text-lg sm:text-xl font-bold" id="guruModalTitle">Detail Pembina</h3>
            <button type="button" class="text-white hover:text-gray-200" onclick="closeGuruModal()">
                <i class="fas fa-times text-lg sm:text-xl"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="guruDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary text-sm sm:text-base" onclick="closeGuruModal()">
                <i class="fas fa-times"></i>
                <span>Tutup</span>
            </button>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/ekstra/ekstra.js'); ?>"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>