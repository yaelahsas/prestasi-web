<?php
$page_title   = 'Laporan';
$active_menu  = 'laporan';
$body_class   = 'bg-gradient-to-br from-school-light-green to-white';
$topbar_title   = 'Laporan';
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

<?php $this->load->view('templates/footer'); ?>
