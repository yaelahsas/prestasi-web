<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Guru - Sistem Prestasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = { theme: { extend: { colors: {
            'school-green': '#22c55e', 'school-dark-green': '#16a34a',
            'school-yellow': '#facc15',
        }}}}
    </script>
    <style>
        .nav-active { background-color: #dcfce7; color: #16a34a; font-weight: 600; }
        .stat-card { transition: transform 0.2s ease; }
        .stat-card:hover { transform: translateY(-3px); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- SIDEBAR (sama dengan dashboard) -->
<div id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl transform -translate-x-full md:translate-x-0 transition-all duration-300 z-50 flex flex-col">
    <div class="bg-gradient-to-r from-school-green to-school-dark-green p-5 flex items-center gap-3">
        <i class="fas fa-graduation-cap text-3xl text-white"></i>
        <div>
            <span class="font-bold text-xl text-white block">Sistem Prestasi</span>
            <span class="text-green-100 text-xs">Bimbingan Belajar</span>
        </div>
    </div>
    <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Menu Utama</p>
        <a href="<?= base_url('dashboard') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-home w-5 text-center"></i> Dashboard
        </a>
        <a href="<?= base_url('jurnal') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-book w-5 text-center"></i> Jurnal
        </a>
        <a href="<?= base_url('absensi') ?>" class="nav-item nav-active flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all">
            <i class="fas fa-calendar-check w-5 text-center"></i> Absensi
        </a>
        <a href="<?= base_url('guru') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-chalkboard-teacher w-5 text-center"></i> Guru
        </a>
        <a href="<?= base_url('kelas') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-school w-5 text-center"></i> Kelas
        </a>
        <a href="<?= base_url('mapel') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-book-open w-5 text-center"></i> Mapel
        </a>
        <a href="<?= base_url('laporan') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-file-alt w-5 text-center"></i> Laporan
        </a>
        <?php if ($user['role'] === 'admin'): ?>
        <a href="<?= base_url('users') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-users w-5 text-center"></i> Users
        </a>
        <?php endif; ?>
    </nav>
    <div class="p-4 border-t border-gray-100">
        <a href="<?= base_url('auth/logout') ?>" class="flex items-center gap-2 text-sm text-red-500 hover:text-red-700 transition-colors px-2 py-1 rounded-lg hover:bg-red-50">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</div>
<div id="overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

<!-- MAIN -->
<div class="md:ml-64 min-h-screen flex flex-col">
    <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between sticky top-0 z-30">
        <div class="flex items-center gap-3">
            <button id="btnSidebar" class="md:hidden text-gray-500"><i class="fas fa-bars text-xl"></i></button>
            <h1 class="text-lg font-bold text-gray-800">Absensi Guru</h1>
        </div>
        <button onclick="openModal()" class="flex items-center gap-2 bg-school-green text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-school-dark-green transition-colors">
            <i class="fas fa-plus"></i> Tambah Absensi
        </button>
    </header>

    <main class="flex-1 p-6 space-y-6">

        <!-- FILTER BULAN/TAHUN -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
                    <select id="filterBulan" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-school-green">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT); ?>" <?= $bulan == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : ''; ?>>
                            <?= date('F', mktime(0,0,0,$m,1)); ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun</label>
                    <select id="filterTahun" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-school-green">
                        <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                        <option value="<?= $y; ?>" <?= $tahun == $y ? 'selected' : ''; ?>><?= $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button onclick="applyFilter()" class="px-4 py-2 bg-school-green text-white rounded-xl text-sm font-medium hover:bg-school-dark-green transition-colors">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
            </div>
        </div>

        <!-- REKAP STAT CARDS -->
        <?php
        $total_abs = $total_absensi;
        $stat_items = [
            ['label'=>'Total Absensi', 'val'=>$total_abs->total ?? 0, 'icon'=>'fa-list', 'color'=>'bg-gray-100 text-gray-600'],
            ['label'=>'Hadir',         'val'=>$total_abs->hadir ?? 0, 'icon'=>'fa-check-circle', 'color'=>'bg-green-100 text-green-600'],
            ['label'=>'Izin',          'val'=>$total_abs->izin  ?? 0, 'icon'=>'fa-info-circle',  'color'=>'bg-blue-100 text-blue-600'],
            ['label'=>'Sakit',         'val'=>$total_abs->sakit ?? 0, 'icon'=>'fa-heartbeat',    'color'=>'bg-yellow-100 text-yellow-600'],
            ['label'=>'Alpha',         'val'=>$total_abs->alpha ?? 0, 'icon'=>'fa-times-circle', 'color'=>'bg-red-100 text-red-600'],
        ];
        ?>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
            <?php foreach ($stat_items as $s): ?>
            <div class="stat-card bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
                <div class="w-10 h-10 rounded-xl <?= $s['color']; ?> flex items-center justify-center mx-auto mb-2">
                    <i class="fas <?= $s['icon']; ?>"></i>
                </div>
                <p class="text-2xl font-bold text-gray-800"><?= $s['val']; ?></p>
                <p class="text-xs text-gray-500 mt-0.5"><?= $s['label']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- TABEL ABSENSI -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800">Data Absensi</h3>
                <span class="text-xs text-gray-500"><?= date('F', mktime(0,0,0,$bulan,1)); ?> <?= $tahun; ?></span>
            </div>
            <div class="p-5 overflow-x-auto">
                <table id="absensiTable" class="w-full text-sm" style="width:100%">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Guru</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kelas</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mapel</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <!-- REKAP PER GURU -->
        <?php if (!empty($rekap)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">Rekap Kehadiran Per Guru</h3>
                <p class="text-xs text-gray-500 mt-0.5"><?= date('F', mktime(0,0,0,$bulan,1)); ?> <?= $tahun; ?></p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-y border-gray-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Guru</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-green-600 uppercase">Hadir</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-blue-600 uppercase">Izin</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-yellow-600 uppercase">Sakit</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-red-600 uppercase">Alpha</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">% Hadir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach ($rekap as $r): ?>
                        <?php $pct = $r->total > 0 ? round(($r->hadir / $r->total) * 100) : 0; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <p class="font-semibold text-gray-800 text-sm"><?= $r->nama_guru; ?></p>
                                <p class="text-xs text-gray-400"><?= $r->nip ?: '-'; ?></p>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-gray-700"><?= $r->total; ?></td>
                            <td class="px-4 py-3 text-center text-green-600 font-semibold"><?= $r->hadir; ?></td>
                            <td class="px-4 py-3 text-center text-blue-600 font-semibold"><?= $r->izin; ?></td>
                            <td class="px-4 py-3 text-center text-yellow-600 font-semibold"><?= $r->sakit; ?></td>
                            <td class="px-4 py-3 text-center text-red-600 font-semibold"><?= $r->alpha; ?></td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $pct >= 80 ? 'bg-green-100 text-green-700' : ($pct >= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'); ?>">
                                    <?= $pct; ?>%
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </main>
</div>

<!-- MODAL TAMBAH/EDIT ABSENSI -->
<div id="modalAbsensi" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800" id="modalTitle">Tambah Absensi</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
        </div>
        <form id="formAbsensi" class="p-6 space-y-4">
            <input type="hidden" id="id_absensi" name="id_absensi">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" id="tanggal" name="tanggal" max="<?= date('Y-m-d'); ?>"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Guru <span class="text-red-500">*</span></label>
                <select id="id_guru" name="id_guru" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green">
                    <option value="">-- Pilih Guru --</option>
                    <?php foreach ($guru as $g): ?>
                    <option value="<?= $g->id_guru; ?>"><?= $g->nama_guru; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select id="status" name="status" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green">
                    <option value="hadir">Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alpha">Alpha</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="2"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green resize-none"
                    placeholder="Keterangan (opsional)"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-school-green text-white rounded-xl text-sm font-semibold hover:bg-school-dark-green">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const base_url = '<?= base_url(); ?>';
let currentBulan = '<?= $bulan; ?>';
let currentTahun = '<?= $tahun; ?>';
let absensiTable;

$(document).ready(function() {
    $('#btnSidebar').on('click', function() { $('#sidebar').removeClass('-translate-x-full'); $('#overlay').removeClass('hidden'); });
    $('#overlay').on('click', function() { $('#sidebar').addClass('-translate-x-full'); $('#overlay').addClass('hidden'); });

    absensiTable = $('#absensiTable').DataTable({
        ajax: {
            url: base_url + 'absensi/get_absensi_data',
            type: 'GET',
            data: function(d) {
                d.bulan = currentBulan;
                d.tahun = currentTahun;
            }
        },
        columns: [
            { data: 0, width: '50px' },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5, className: 'text-center' },
            { data: 6 },
            { data: 7, className: 'text-center', orderable: false },
        ],
        order: [[1, 'desc']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
        pageLength: 25,
    });

    $('#formAbsensi').on('submit', function(e) {
        e.preventDefault();
        $.post(base_url + 'absensi/save_absensi', $(this).serialize(), function(res) {
            if (res.status === 'success') {
                closeModal();
                absensiTable.ajax.reload();
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 2000, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
            }
        });
    });
});

function applyFilter() {
    currentBulan = $('#filterBulan').val();
    currentTahun = $('#filterTahun').val();
    absensiTable.ajax.reload();
}

function openModal() {
    $('#modalTitle').text('Tambah Absensi');
    $('#formAbsensi')[0].reset();
    $('#id_absensi').val('');
    $('#tanggal').val('<?= date('Y-m-d'); ?>');
    $('#modalAbsensi').removeClass('hidden');
}

function closeModal() {
    $('#modalAbsensi').addClass('hidden');
}

function editAbsensi(id) {
    $.get(base_url + 'absensi/get_absensi_by_id/' + id, function(res) {
        if (res.status === 'success') {
            const a = res.data;
            $('#modalTitle').text('Edit Absensi');
            $('#id_absensi').val(a.id_absensi);
            $('#tanggal').val(a.tanggal);
            $('#id_guru').val(a.id_guru);
            $('#status').val(a.status);
            $('#keterangan').val(a.keterangan);
            $('#modalAbsensi').removeClass('hidden');
        }
    });
}

function deleteAbsensi(id) {
    Swal.fire({
        title: 'Hapus Absensi?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.get(base_url + 'absensi/delete_absensi/' + id, function(res) {
                if (res.status === 'success') {
                    absensiTable.ajax.reload();
                    Swal.fire({ icon: 'success', title: 'Dihapus!', timer: 2000, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            });
        }
    });
}
</script>
</body>
</html>
