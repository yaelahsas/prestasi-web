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