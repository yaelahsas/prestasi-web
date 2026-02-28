<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Saya - Portal Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = { theme: { extend: { colors: {
            'school-green': '#22c55e', 'school-dark-green': '#16a34a',
        }}}}
    </script>
    <style>
        .nav-active { background-color: #dcfce7; color: #16a34a; font-weight: 600; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- SIDEBAR -->
<div id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl transform -translate-x-full md:translate-x-0 transition-all duration-300 z-50 flex flex-col">
    <div class="bg-gradient-to-r from-school-green to-school-dark-green p-5 flex items-center gap-3">
        <i class="fas fa-chalkboard-teacher text-3xl text-white"></i>
        <div>
            <span class="font-bold text-xl text-white block">Portal Guru</span>
            <span class="text-green-100 text-xs">Sistem Prestasi</span>
        </div>
    </div>
    <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
        <a href="<?= base_url('guru_portal') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-home w-5 text-center"></i> Dashboard
        </a>
        <a href="<?= base_url('guru_portal/jurnal') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-book w-5 text-center"></i> Jurnal Saya
        </a>
        <a href="<?= base_url('guru_portal/absensi') ?>" class="nav-item nav-active flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all">
            <i class="fas fa-calendar-check w-5 text-center"></i> Absensi Saya
        </a>
        <a href="<?= base_url('guru_portal/profil') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-user w-5 text-center"></i> Profil
        </a>
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
    <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center gap-3 sticky top-0 z-30">
        <button id="btnSidebar" class="md:hidden text-gray-500"><i class="fas fa-bars text-xl"></i></button>
        <h1 class="text-lg font-bold text-gray-800">Absensi Saya</h1>
    </header>

    <main class="flex-1 p-6 space-y-6">

        <!-- Filter Bulan/Tahun -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
                    <select name="bulan" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-school-green">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT); ?>" <?= $bulan == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : ''; ?>>
                            <?= date('F', mktime(0,0,0,$m,1)); ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun</label>
                    <select name="tahun" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-school-green">
                        <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                        <option value="<?= $y; ?>" <?= $tahun == $y ? 'selected' : ''; ?>><?= $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-school-green text-white rounded-xl text-sm font-medium hover:bg-school-dark-green transition-colors">
                    <i class="fas fa-filter mr-1"></i> Tampilkan
                </button>
            </form>
        </div>

        <!-- Rekap Absensi -->
        <?php if ($rekap): ?>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <?php
            $items = [
                ['label'=>'Hadir', 'val'=>$rekap->hadir ?? 0, 'icon'=>'fa-check-circle', 'color'=>'bg-green-100 text-green-600'],
                ['label'=>'Izin',  'val'=>$rekap->izin  ?? 0, 'icon'=>'fa-info-circle',  'color'=>'bg-blue-100 text-blue-600'],
                ['label'=>'Sakit', 'val'=>$rekap->sakit ?? 0, 'icon'=>'fa-heartbeat',    'color'=>'bg-yellow-100 text-yellow-600'],
                ['label'=>'Alpha', 'val'=>$rekap->alpha ?? 0, 'icon'=>'fa-times-circle', 'color'=>'bg-red-100 text-red-600'],
            ];
            foreach ($items as $s):
            ?>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
                <div class="w-10 h-10 rounded-xl <?= $s['color']; ?> flex items-center justify-center mx-auto mb-2">
                    <i class="fas <?= $s['icon']; ?>"></i>
                </div>
                <p class="text-2xl font-bold text-gray-800"><?= $s['val']; ?></p>
                <p class="text-xs text-gray-500 mt-0.5"><?= $s['label']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Daftar Absensi -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">Riwayat Absensi</h3>
                <p class="text-xs text-gray-500 mt-0.5"><?= date('F', mktime(0,0,0,$bulan,1)); ?> <?= $tahun; ?></p>
            </div>
            <?php if (!empty($absensi_list)): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-y border-gray-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Kelas</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Mapel</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php
                        $status_colors = [
                            'hadir' => 'bg-green-100 text-green-700',
                            'izin'  => 'bg-blue-100 text-blue-700',
                            'sakit' => 'bg-yellow-100 text-yellow-700',
                            'alpha' => 'bg-red-100 text-red-700',
                        ];
                        foreach ($absensi_list as $a):
                            $color = $status_colors[$a->status] ?? 'bg-gray-100 text-gray-700';
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="text-xs bg-gray-50 text-gray-700 px-2 py-1 rounded-lg font-medium border border-gray-100">
                                    <?= date('d M Y', strtotime($a->tanggal)); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell text-xs text-gray-600"><?= $a->nama_kelas ?? '-'; ?></td>
                            <td class="px-4 py-3 hidden md:table-cell text-xs text-gray-600"><?= $a->nama_mapel ?? '-'; ?></td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $color; ?>">
                                    <?= ucfirst($a->status); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell text-xs text-gray-500"><?= $a->keterangan ?: '-'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                <i class="fas fa-calendar-times text-4xl mb-3 text-gray-200"></i>
                <p class="text-sm font-medium text-gray-500">Belum ada data absensi</p>
                <p class="text-xs text-gray-400 mt-1">untuk <?= date('F', mktime(0,0,0,$bulan,1)); ?> <?= $tahun; ?></p>
            </div>
            <?php endif; ?>
        </div>

    </main>
</div>

<script>
$(document).ready(function() {
    $('#btnSidebar').on('click', function() { $('#sidebar').removeClass('-translate-x-full'); $('#overlay').removeClass('hidden'); });
    $('#overlay').on('click', function() { $('#sidebar').addClass('-translate-x-full'); $('#overlay').addClass('hidden'); });
});
</script>
</body>
</html>
