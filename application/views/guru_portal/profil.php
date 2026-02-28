<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Portal Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <a href="<?= base_url('guru_portal/absensi') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-calendar-check w-5 text-center"></i> Absensi Saya
        </a>
        <a href="<?= base_url('guru_portal/profil') ?>" class="nav-item nav-active flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all">
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
        <h1 class="text-lg font-bold text-gray-800">Profil Saya</h1>
    </header>

    <main class="flex-1 p-6 max-w-2xl mx-auto w-full space-y-6">

        <!-- Info Guru -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 rounded-full bg-school-green flex items-center justify-center text-white text-2xl font-bold">
                    <?= strtoupper(substr($user['nama'], 0, 1)); ?>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800"><?= $user['nama']; ?></h2>
                    <p class="text-sm text-gray-500">@<?= $user['username']; ?> &bull; Guru</p>
                </div>
            </div>

            <?php if ($guru): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-1">Nama Lengkap</p>
                    <p class="font-semibold text-gray-800"><?= $guru->nama_guru; ?></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-1">NIP</p>
                    <p class="font-semibold text-gray-800"><?= $guru->nip ?: '-'; ?></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-1">Kelas Utama</p>
                    <p class="font-semibold text-gray-800"><?= $guru->nama_kelas; ?></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-1">Mata Pelajaran</p>
                    <p class="font-semibold text-gray-800"><?= $guru->nama_mapel; ?></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-1">Status</p>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $guru->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                        <?= ucfirst($guru->status); ?>
                    </span>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Ganti Password -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-bold text-gray-800 mb-4">Ganti Password</h3>
            <form id="formPassword" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama <span class="text-red-500">*</span></label>
                    <input type="password" id="password_lama" name="password_lama"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green"
                        placeholder="Masukkan password lama">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru <span class="text-red-500">*</span></label>
                    <input type="password" id="password_baru" name="password_baru"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green"
                        placeholder="Minimal 6 karakter">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                    <input type="password" id="konfirmasi" name="konfirmasi"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green"
                        placeholder="Ulangi password baru">
                </div>
                <button type="submit" class="w-full px-4 py-2.5 bg-school-green text-white rounded-xl text-sm font-semibold hover:bg-school-dark-green transition-colors">
                    <i class="fas fa-key mr-1"></i> Ganti Password
                </button>
            </form>
        </div>

    </main>
</div>

<script>
const base_url = '<?= base_url(); ?>';

$(document).ready(function() {
    $('#btnSidebar').on('click', function() { $('#sidebar').removeClass('-translate-x-full'); $('#overlay').removeClass('hidden'); });
    $('#overlay').on('click', function() { $('#sidebar').addClass('-translate-x-full'); $('#overlay').addClass('hidden'); });

    $('#formPassword').on('submit', function(e) {
        e.preventDefault();
        const baru = $('#password_baru').val();
        const konfirmasi = $('#konfirmasi').val();

        if (baru !== konfirmasi) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Konfirmasi password tidak sesuai' });
            return;
        }

        $.post(base_url + 'guru_portal/ganti_password', $(this).serialize(), function(res) {
            if (res.status === 'success') {
                $('#formPassword')[0].reset();
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 2000, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
            }
        });
    });
});
</script>
</body>
</html>
