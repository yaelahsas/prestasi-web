<?php
/**
 * Sidebar partial
 *
 * Variables expected:
 *   $user         - array with 'nama' and 'role' keys
 *   $active_menu  - string: 'dashboard'|'jurnal'|'guru'|'kelas'|'mapel'|'laporan'|'users'|'sekolah'|'whatsapp'
 */

$menus = [
    ['key' => 'dashboard', 'url' => 'dashboard',  'icon' => 'fas fa-home',                'label' => 'Dashboard'],
    ['key' => 'jurnal',    'url' => 'jurnal',      'icon' => 'fas fa-book',                'label' => 'Jurnal'],
    ['key' => 'guru',      'url' => 'guru',        'icon' => 'fas fa-chalkboard-teacher',  'label' => 'Guru'],
    ['key' => 'kelas',     'url' => 'kelas',       'icon' => 'fas fa-school',              'label' => 'Kelas'],
    ['key' => 'mapel',     'url' => 'mapel',       'icon' => 'fas fa-book-open',           'label' => 'Mata Pelajaran'],
    ['key' => 'laporan',   'url' => 'laporan',     'icon' => 'fas fa-file-alt',            'label' => 'Laporan'],
    ['key' => 'users',     'url' => 'users',       'icon' => 'fas fa-users',               'label' => 'Users'],
    ['key' => 'sekolah',   'url' => 'sekolah',     'icon' => 'fas fa-building',            'label' => 'Data Sekolah'],
    ['key' => 'whatsapp',  'url' => 'whatsapp',    'icon' => 'fab fa-whatsapp',            'label' => 'WhatsApp Bot'],
];

$active = isset($active_menu) ? $active_menu : '';
?>

<!-- ===== SIDEBAR ===== -->
<div id="sidebar"
    class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl transform -translate-x-full md:translate-x-0 transition-all duration-300 ease-in-out z-50 flex flex-col">

    <!-- Brand -->
    <div class="bg-gradient-to-r from-school-green to-school-dark-green p-5 flex items-center gap-3 shadow-md flex-shrink-0">
        <i class="fas fa-graduation-cap text-3xl text-white"></i>
        <div>
            <span class="font-bold text-xl text-white block">Sistem Prestasi</span>
            <span class="text-green-100 text-xs">Bimbingan Belajar</span>
        </div>
    </div>

    <!-- Nav -->
    <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Menu Utama</p>

        <?php foreach ($menus as $menu): ?>
        <a href="<?= base_url($menu['url']); ?>"
            class="nav-item flex items-center gap-3 p-3 rounded-lg transition-all duration-200 group
                   <?= $active === $menu['key']
                       ? 'nav-active'
                       : 'text-gray-700 hover:bg-school-light-green hover:text-school-dark-green'; ?>">
            <i class="<?= $menu['icon']; ?> w-5 text-center group-hover:scale-110 transition-transform"></i>
            <span><?= $menu['label']; ?></span>
        </a>
        <?php endforeach; ?>

        <div class="pt-4 mt-4 border-t border-gray-200">
            <a href="<?= base_url('auth/logout'); ?>"
                class="flex items-center gap-3 p-3 rounded-lg text-red-600 hover:bg-red-50 transition-all duration-200 group">
                <i class="fas fa-sign-out-alt w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Logout</span>
            </a>
        </div>
    </nav>

    <!-- User info footer -->
    <div class="p-4 border-t border-gray-100 bg-gray-50 flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-school-green to-school-dark-green flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                <?= strtoupper(substr($user['nama'], 0, 1)); ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800 truncate"><?= $user['nama']; ?></p>
                <p class="text-xs text-gray-500 capitalize"><?= $user['role']; ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Mobile overlay -->
<div id="overlay" class="fixed inset-0 bg-black/40 hidden z-40"></div>
