    <!-- ===== SIDEBAR ===== -->
    <div id="sidebar"
        class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl transform -translate-x-full md:translate-x-0 transition-all duration-300 ease-in-out z-50 md:shadow-2xl flex flex-col">

        <div class="bg-gradient-to-r from-school-green to-school-dark-green p-5 flex items-center gap-3 shadow-md">
            <i class="fas fa-graduation-cap text-3xl text-white animate-pulse-slow"></i>
            <div>
                <span class="font-bold text-xl text-white block">Sistem Prestasi</span>
                <span class="text-green-100 text-xs">Bimbingan Belajar</span>
            </div>
        </div>

        <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Menu Utama</p>

            <a href="<?= base_url('dashboard') ?>" class="nav-item <?= $active_menu === 'dashboard' ? 'nav-active' : 'flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green' ?> transition-all duration-200 group">
                <i class="fas fa-home w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= base_url('jurnal') ?>" class="nav-item <?= $active_menu === 'jurnal' ? 'bg-school-light-green text-school-dark-green' : 'flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green' ?> transition-all duration-200 group">
                <i class="fas fa-book w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Jurnal</span>
            </a>
            <a href="<?= base_url('guru') ?>" class="nav-item <?= $active_menu === 'guru' ? 'bg-school-light-green text-school-dark-green' : 'flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green' ?> transition-all duration-200 group">
                <i class="fas fa-chalkboard-teacher w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Guru</span>
            </a>
            <a href="<?= base_url('kelas') ?>" class="nav-item <?= $active_menu === 'kelas' ? 'bg-school-light-green text-school-dark-green' : 'flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green' ?> transition-all duration-200 group">
                <i class="fas fa-school w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Kelas</span>
            </a>
            <a href="<?= base_url('mapel') ?>" class="nav-item <?= $active_menu === 'mapel' ? 'bg-school-light-green text-school-dark-green' : 'flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green' ?> transition-all duration-200 group">
                <i class="fas fa-book-open w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Mata Pelajaran</span>
            </a>
            <a href="<?= base_url('ekstra') ?>" class="nav-item <?= $active_menu === 'ekstra' ? 'bg-school-light-green text-school-dark-green' : 'flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green' ?> transition-all duration-200 group">
                <i class="fas fa-star w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Ekstrakurikuler</span>
            </a>
            <a href="<?= base_url('laporan') ?>" class="nav-item <?= $active_menu === 'laporan' ? 'bg-school-light-green text-school-dark-green' : 'flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green' ?> transition-all duration-200 group">
                <i class="fas fa-file-alt w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Laporan</span>
            </a>
            <a href="<?= base_url('users') ?>" class="nav-item <?= $active_menu === 'users' ? 'bg-school-light-green text-school-dark-green' : 'flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green' ?> transition-all duration-200 group">
                <i class="fas fa-users w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Users</span>
            </a>
            <a href="<?= base_url('sekolah') ?>" class="nav-item <?= $active_menu === 'sekolah' ? 'bg-school-light-green text-school-dark-green' : 'flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green' ?> transition-all duration-200 group">
                <i class="fas fa-building w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Data Sekolah</span>
            </a>
            <a href="<?= base_url('whatsapp') ?>" class="nav-item <?= $active_menu === 'whatsapp' ? 'bg-school-light-green text-school-dark-green' : 'flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green' ?> transition-all duration-200 group">
                <i class="fab fa-whatsapp w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>WhatsApp Bot</span>
            </a>
            <a href="<?= base_url('billing') ?>" class="nav-item <?= $active_menu === 'billing' ? 'bg-school-light-green text-school-dark-green' : 'flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green' ?> transition-all duration-200 group">
                <i class="fas fa-file-invoice-dollar w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Billing</span>
            </a>

            <div class="pt-4 mt-4 border-t border-gray-200">
                <a href="<?= base_url('auth/logout') ?>" class="flex items-center gap-3 p-3 rounded-lg text-red-600 hover:bg-red-50 transition-all duration-200 group">
                    <i class="fas fa-sign-out-alt w-5 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium">Logout</span>
                </a>
            </div>
        </nav>

        <!-- Sidebar footer -->
        <div class="p-4 border-t border-gray-100 bg-gray-50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-school-green to-school-dark-green flex items-center justify-center text-white font-bold text-sm">
                    <?= strtoupper(substr($user['nama'], 0, 1)); ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate"><?= $user['nama']; ?></p>
                    <p class="text-xs text-gray-500 capitalize"><?= $user['role']; ?></p>
                </div>
            </div>
        </div>
    </div>
