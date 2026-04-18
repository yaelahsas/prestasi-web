<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Bot - Sistem Prestasi</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const base_url = '<?= base_url(); ?>';
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
            pointer-events: none;
        }
        @keyframes ripple-animation {
            to { transform: scale(4); opacity: 0; }
        }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #22c55e; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #16a34a; }
        .nav-active { background-color: #dcfce7; color: #16a34a; font-weight: 600; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up { animation: fadeInUp 0.5s ease both; }
        .session-card { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .session-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,0.10); }
        .status-connected { background-color: #dcfce7; color: #16a34a; }
        .status-disconnected { background-color: #fee2e2; color: #dc2626; }
        .status-connecting { background-color: #fef3c7; color: #d97706; }
        .qr-container { background: white; padding: 16px; border-radius: 12px; display: inline-block; }
        .pulse-dot {
            width: 10px; height: 10px; border-radius: 50%;
            animation: pulse 2s infinite;
        }
        .pulse-dot.connected { background-color: #22c55e; }
        .pulse-dot.disconnected { background-color: #ef4444; }
        .pulse-dot.connecting { background-color: #f59e0b; }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.3); }
        }
        .tab-active { border-bottom: 3px solid #22c55e; color: #16a34a; font-weight: 600; }
        .log-row:nth-child(even) { background-color: #f9fafb; }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'school-green': '#22c55e',
                        'school-yellow': '#facc15',
                        'school-dark-green': '#16a34a',
                        'school-light-green': '#86efac',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 min-h-screen">

    <!-- ===== SIDEBAR ===== -->
    <div id="sidebar"
        class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl transform -translate-x-full md:translate-x-0 transition-all duration-300 ease-in-out z-50 flex flex-col">

        <div class="bg-gradient-to-r from-school-green to-school-dark-green p-5 flex items-center gap-3 shadow-md">
            <i class="fas fa-graduation-cap text-3xl text-white"></i>
            <div>
                <span class="font-bold text-xl text-white block">Sistem Prestasi</span>
                <span class="text-green-100 text-xs">Bimbingan Belajar</span>
            </div>
        </div>

        <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Menu Utama</p>

            <a href="<?= base_url('dashboard') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-home w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= base_url('jurnal') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-book w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Jurnal</span>
            </a>
            <a href="<?= base_url('guru') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-chalkboard-teacher w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Guru</span>
            </a>
            <a href="<?= base_url('kelas') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-school w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Kelas</span>
            </a>
            <a href="<?= base_url('mapel') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-book-open w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Mata Pelajaran</span>
            </a>
            <a href="<?= base_url('ekstra') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-star w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Ekstrakurikuler</span>
            </a>
            <a href="<?= base_url('laporan') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-file-alt w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Laporan</span>
            </a>
            <a href="<?= base_url('users') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-users w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Users</span>
            </a>
            <a href="<?= base_url('sekolah') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-building w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span>Data Sekolah</span>
            </a>
            <a href="<?= base_url('whatsapp') ?>" class="nav-item nav-active flex items-center gap-3 p-3 rounded-lg transition-all duration-200 group">
                <i class="fab fa-whatsapp w-5 text-center"></i>
                <span>WhatsApp Bot</span>
            </a>
            <a href="<?= base_url('billing') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
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

    <!-- overlay mobile -->
    <div id="overlay" class="fixed inset-0 bg-black/40 hidden z-40"></div>

    <!-- ===== MAIN ===== -->
    <div class="md:ml-64 min-h-screen">

        <!-- TOPBAR -->
        <header class="bg-white shadow-sm flex items-center justify-between px-4 h-16 sticky top-0 z-40 border-b border-gray-100">
            <div class="flex items-center gap-4">
                <button id="btnSidebar" class="md:hidden text-2xl text-school-green hover:text-school-dark-green transition-colors">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h2 class="text-lg font-bold text-gray-800 hidden sm:block">WhatsApp Bot Management</h2>
                    <p class="text-xs text-gray-500 hidden sm:block">Kelola sesi dan bot WhatsApp</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button onclick="refreshSessions()" title="Refresh"
                    class="p-2 rounded-lg bg-green-50 hover:bg-school-green text-school-dark-green hover:text-white transition-all duration-200 group">
                    <i class="fas fa-sync-alt group-hover:rotate-180 transition-transform duration-500 text-sm" id="refreshIcon"></i>
                </button>
                <button onclick="openAddSessionModal()"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg bg-school-green text-white hover:bg-school-dark-green transition-all duration-200 text-sm font-medium">
                    <i class="fas fa-plus text-xs"></i>
                    <span class="hidden sm:inline">Tambah Session</span>
                </button>
                <div class="flex items-center gap-2 bg-green-50 px-3 py-2 rounded-lg">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-school-green to-school-dark-green flex items-center justify-center text-white font-bold text-xs">
                        <?= strtoupper(substr($user['nama'], 0, 1)); ?>
                    </div>
                    <span class="text-sm font-medium text-gray-700 hidden sm:block"><?= $user['nama']; ?></span>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="p-4 md:p-6 space-y-6">

            <!-- ===== HEADER BANNER ===== -->
            <div class="bg-gradient-to-r from-green-600 to-green-400 rounded-2xl p-6 text-white shadow-lg fade-in-up">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center">
                            <i class="fab fa-whatsapp text-3xl text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">WhatsApp Bot Manager</h1>
                            <p class="text-green-100 text-sm mt-1">Kelola sesi bot WhatsApp menggunakan Baileys</p>
                        </div>
                    </div>
                    <div class="flex gap-3 flex-wrap">
                        <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-xl text-center">
                            <p class="text-2xl font-bold" id="totalSessions"><?= count($sessions); ?></p>
                            <p class="text-xs text-green-100">Total Session</p>
                        </div>
                        <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-xl text-center">
                            <p class="text-2xl font-bold" id="activeSessions">
                                <?= count(array_filter((array)$sessions, function($s){ return $s->status === 'connected'; })); ?>
                            </p>
                            <p class="text-xs text-green-100">Terhubung</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== TABS ===== -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 fade-in-up overflow-hidden">
                <div class="flex border-b border-gray-100 overflow-x-auto">
                    <button onclick="switchTab('sessions')" id="tab-sessions"
                        class="tab-btn tab-active px-6 py-4 text-sm font-medium whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-mobile-alt"></i> Sesi Bot
                    </button>
                    <button onclick="switchTab('send')" id="tab-send"
                        class="tab-btn px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i> Kirim Pesan
                    </button>
                    <button onclick="switchTab('logs')" id="tab-logs"
                        class="tab-btn px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-history"></i> Log Pesan
                    </button>
                    <button onclick="switchTab('settings')" id="tab-settings"
                        class="tab-btn px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-cog"></i> Pengaturan Bot
                    </button>
                    <button onclick="switchTab('info')" id="tab-info"
                        class="tab-btn px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-info-circle"></i> Informasi
                    </button>
                </div>

                <!-- TAB: SESSIONS -->
                <div id="content-sessions" class="tab-content p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-gray-800">Daftar Sesi WhatsApp</h3>
                        <button onclick="openAddSessionModal()"
                            class="flex items-center gap-2 px-4 py-2 bg-school-green text-white rounded-xl text-sm font-medium hover:bg-school-dark-green transition-all">
                            <i class="fas fa-plus text-xs"></i> Tambah Sesi
                        </button>
                    </div>

                    <div id="sessionsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        <?php if (!empty($sessions)): ?>
                            <?php foreach ($sessions as $session): ?>
                            <div class="session-card bg-white border border-gray-100 rounded-2xl p-5 shadow-sm" id="card-<?= $session->session_id ?>">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center">
                                            <i class="fab fa-whatsapp text-school-green text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800 text-sm"><?= htmlspecialchars($session->session_name) ?></h4>
                                            <p class="text-xs text-gray-400 font-mono"><?= htmlspecialchars($session->session_id) ?></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <div class="pulse-dot <?= $session->status ?>"></div>
                                        <span class="text-xs px-2 py-0.5 rounded-full font-medium status-<?= $session->status ?>">
                                            <?= ucfirst($session->status) ?>
                                        </span>
                                    </div>
                                </div>

                                <?php if (!empty($session->phone_number)): ?>
                                <div class="flex items-center gap-2 mb-3 bg-gray-50 rounded-lg px-3 py-2">
                                    <i class="fas fa-phone text-gray-400 text-xs"></i>
                                    <span class="text-sm text-gray-600 font-medium"><?= htmlspecialchars($session->phone_number) ?></span>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($session->description)): ?>
                                <p class="text-xs text-gray-500 mb-3 line-clamp-2"><?= htmlspecialchars($session->description) ?></p>
                                <?php endif; ?>

                                <div class="text-xs text-gray-400 mb-4">
                                    <i class="fas fa-clock mr-1"></i>
                                    Dibuat: <?= date('d M Y H:i', strtotime($session->created_at)) ?>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-2 flex-wrap">
                                    <?php if ($session->status !== 'connected'): ?>
                                    <div class="flex-1 flex gap-1.5">
                                        <button onclick="showQR('<?= $session->session_id ?>')"
                                            class="flex-1 flex items-center justify-center gap-1 px-2 py-2 bg-green-50 text-school-green hover:bg-school-green hover:text-white rounded-lg text-xs font-medium transition-all"
                                            title="Hubungkan via QR Code">
                                            <i class="fas fa-qrcode"></i>
                                            <span class="hidden sm:inline">QR</span>
                                        </button>
                                        <button onclick="showPairingCode('<?= $session->session_id ?>')"
                                            class="flex-1 flex items-center justify-center gap-1 px-2 py-2 bg-purple-50 text-purple-600 hover:bg-purple-500 hover:text-white rounded-lg text-xs font-medium transition-all"
                                            title="Hubungkan via Kode Pairing">
                                            <i class="fas fa-key"></i>
                                            <span class="hidden sm:inline">Kode</span>
                                        </button>
                                    </div>
                                    <?php else: ?>
                                    <button onclick="logoutSession('<?= $session->session_id ?>')"
                                        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 bg-orange-50 text-orange-600 hover:bg-orange-500 hover:text-white rounded-lg text-xs font-medium transition-all">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                    <?php endif; ?>
                                    <button onclick="checkStatus('<?= $session->session_id ?>')"
                                        class="flex items-center justify-center gap-1.5 px-3 py-2 bg-blue-50 text-blue-600 hover:bg-blue-500 hover:text-white rounded-lg text-xs font-medium transition-all">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    <button onclick="deleteSession('<?= $session->session_id ?>', '<?= htmlspecialchars($session->session_name) ?>')"
                                        class="flex items-center justify-center gap-1.5 px-3 py-2 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-lg text-xs font-medium transition-all">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <div class="col-span-3 flex flex-col items-center justify-center py-16 text-gray-400">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fab fa-whatsapp text-4xl text-gray-300"></i>
                            </div>
                            <p class="text-base font-medium text-gray-500">Belum ada sesi WhatsApp</p>
                            <p class="text-sm text-gray-400 mt-1 mb-4">Klik tombol "Tambah Sesi" untuk memulai</p>
                            <button onclick="openAddSessionModal()"
                                class="flex items-center gap-2 px-5 py-2.5 bg-school-green text-white rounded-xl text-sm font-medium hover:bg-school-dark-green transition-all">
                                <i class="fas fa-plus text-xs"></i> Tambah Sesi Pertama
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- TAB: SEND MESSAGE -->
                <div id="content-send" class="tab-content hidden p-6">
                    <h3 class="text-base font-bold text-gray-800 mb-4">Kirim Pesan WhatsApp</h3>

                    <div class="max-w-xl">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih Sesi Bot</label>
                                <select id="sendSessionId" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green focus:border-transparent">
                                    <option value="">-- Pilih Sesi --</option>
                                    <?php foreach ($sessions as $session): ?>
                                    <?php if ($session->status === 'connected'): ?>
                                    <option value="<?= $session->session_id ?>"><?= htmlspecialchars($session->session_name) ?> (<?= $session->session_id ?>)</option>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Penerima</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">+</span>
                                    <input type="text" id="sendReceiver" placeholder="628xxxxxxxxxx"
                                        class="w-full border border-gray-200 rounded-xl pl-7 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green focus:border-transparent">
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Format: 628xxxxxxxxxx (tanpa + atau 0 di depan)</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Pesan</label>
                                <textarea id="sendMessage" rows="5" placeholder="Ketik pesan di sini..."
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green focus:border-transparent resize-none"></textarea>
                                <p class="text-xs text-gray-400 mt-1 text-right"><span id="charCount">0</span> karakter</p>
                            </div>

                            <button onclick="sendMessage()"
                                class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-school-green text-white rounded-xl font-medium hover:bg-school-dark-green transition-all">
                                <i class="fas fa-paper-plane"></i> Kirim Pesan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- TAB: LOGS -->
                <div id="content-logs" class="tab-content hidden p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-gray-800">Log Pesan Terkirim</h3>
                        <div class="flex gap-2">
                            <select id="logSessionFilter" onchange="loadLogs()" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-school-green">
                                <option value="">Semua Sesi</option>
                                <?php foreach ($sessions as $session): ?>
                                <option value="<?= $session->session_id ?>"><?= htmlspecialchars($session->session_name) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button onclick="loadLogs()" class="px-3 py-2 bg-green-50 text-school-green rounded-xl text-sm hover:bg-school-green hover:text-white transition-all">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Sesi</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Penerima</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Pesan</th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Dikirim Oleh</th>
                                </tr>
                            </thead>
                            <tbody id="logsTableBody">
                                <tr>
                                    <td colspan="6" class="text-center py-10 text-gray-400">
                                        <i class="fas fa-spinner fa-spin text-2xl mb-2 block"></i>
                                        Memuat log...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB: SETTINGS -->
                <div id="content-settings" class="tab-content hidden p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-base font-bold text-gray-800">Pengaturan Bot WhatsApp</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Konfigurasi ENV untuk server Baileys dan bot WA</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="testBaileysConnection()" id="btnTestConn"
                                class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 hover:bg-blue-500 hover:text-white rounded-xl text-sm font-medium transition-all">
                                <i class="fas fa-plug text-xs"></i> Test Koneksi
                            </button>
                            <button onclick="saveBotSettings()"
                                class="flex items-center gap-2 px-4 py-2 bg-school-green text-white hover:bg-school-dark-green rounded-xl text-sm font-medium transition-all">
                                <i class="fas fa-save text-xs"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </div>

                    <!-- Connection Status Banner -->
                    <div id="connStatusBanner" class="hidden mb-4 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2"></div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                        <!-- ===== SECTION: SERVER BAILEYS ===== -->
                        <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-9 h-9 bg-green-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-server text-school-green text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm">Server Baileys</h4>
                                    <p class="text-xs text-gray-500">Konfigurasi koneksi ke Node.js Baileys API</p>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        URL Baileys API <span class="text-red-500">*</span>
                                        <span class="font-mono text-gray-400 font-normal ml-1">BAILEYS_API_URL</span>
                                    </label>
                                    <input type="text" id="env_BAILEYS_API_URL" placeholder="http://localhost:3000"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-school-green focus:border-transparent font-mono">
                                    <p class="text-xs text-gray-400 mt-1">URL server Baileys yang sedang berjalan</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Port Server
                                        <span class="font-mono text-gray-400 font-normal ml-1">APP_PORT</span>
                                    </label>
                                    <input type="number" id="env_APP_PORT" placeholder="3000" min="1" max="65535"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-school-green focus:border-transparent font-mono">
                                    <p class="text-xs text-gray-400 mt-1">Port yang digunakan server Baileys</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Maks Percobaan Reconnect
                                        <span class="font-mono text-gray-400 font-normal ml-1">MAX_RETRIES</span>
                                    </label>
                                    <input type="number" id="env_MAX_RETRIES" placeholder="0" min="-1"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-school-green focus:border-transparent font-mono">
                                    <p class="text-xs text-gray-400 mt-1">-1 = unlimited, 0 = tidak reconnect</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Interval Reconnect (ms)
                                        <span class="font-mono text-gray-400 font-normal ml-1">RECONNECT_INTERVAL</span>
                                    </label>
                                    <input type="number" id="env_RECONNECT_INTERVAL" placeholder="5000" min="0"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-school-green focus:border-transparent font-mono">
                                    <p class="text-xs text-gray-400 mt-1">Jeda sebelum mencoba reconnect (milidetik)</p>
                                </div>
                            </div>
                        </div>

                        <!-- ===== SECTION: PERILAKU BOT ===== -->
                        <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-9 h-9 bg-purple-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-robot text-purple-500 text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm">Perilaku Bot</h4>
                                    <p class="text-xs text-gray-500">Pengaturan perilaku bot saat menerima pesan</p>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Auto Read Pesan
                                        <span class="font-mono text-gray-400 font-normal ml-1">AUTO_READ_MESSAGES</span>
                                    </label>
                                    <select id="env_AUTO_READ_MESSAGES"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-school-green focus:border-transparent">
                                        <option value="false">false — Tidak otomatis dibaca</option>
                                        <option value="true">true — Otomatis tandai dibaca</option>
                                    </select>
                                    <p class="text-xs text-gray-400 mt-1">Otomatis tandai pesan masuk sebagai sudah dibaca</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Nomor HP Authorized
                                        <span class="font-mono text-gray-400 font-normal ml-1">BOT_AUTHORIZED_NUMBERS</span>
                                    </label>
                                    <textarea id="env_BOT_AUTHORIZED_NUMBERS" rows="3" placeholder="6281234567890,6289876543210"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-school-green focus:border-transparent resize-none font-mono"></textarea>
                                    <p class="text-xs text-gray-400 mt-1">Nomor yang bisa menggunakan perintah bot, pisahkan dengan koma</p>
                                </div>
                            </div>
                        </div>

                        <!-- ===== SECTION: WEBHOOK ===== -->
                        <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-9 h-9 bg-orange-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-broadcast-tower text-orange-500 text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm">Webhook</h4>
                                    <p class="text-xs text-gray-500">Konfigurasi pengiriman event ke URL eksternal</p>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        URL Webhook
                                        <span class="font-mono text-gray-400 font-normal ml-1">APP_WEBHOOK_URL</span>
                                    </label>
                                    <input type="text" id="env_APP_WEBHOOK_URL" placeholder="https://example.com/webhook"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-school-green focus:border-transparent font-mono">
                                    <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak menggunakan webhook</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Event yang Diizinkan
                                        <span class="font-mono text-gray-400 font-normal ml-1">APP_WEBHOOK_ALLOWED_EVENTS</span>
                                    </label>
                                    <input type="text" id="env_APP_WEBHOOK_ALLOWED_EVENTS" placeholder="ALL"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-school-green focus:border-transparent font-mono">
                                    <p class="text-xs text-gray-400 mt-1">ALL atau pisahkan dengan koma: MESSAGES_UPSERT,CONNECTION_UPDATE</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        File Media sebagai Base64
                                        <span class="font-mono text-gray-400 font-normal ml-1">APP_WEBHOOK_FILE_IN_BASE64</span>
                                    </label>
                                    <select id="env_APP_WEBHOOK_FILE_IN_BASE64"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-school-green focus:border-transparent">
                                        <option value="false">false — Tidak kirim base64</option>
                                        <option value="true">true — Kirim file media sebagai base64</option>
                                    </select>
                                    <p class="text-xs text-gray-400 mt-1">Kirim file media (gambar, dokumen) sebagai base64 ke webhook</p>
                                </div>
                            </div>
                        </div>

                        <!-- ===== SECTION: API BACKEND ===== -->
                        <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-link text-blue-500 text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm">API Backend</h4>
                                    <p class="text-xs text-gray-500">Koneksi ke API backend untuk perintah #jurnal & #laporan</p>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        URL API Backend
                                        <span class="font-mono text-gray-400 font-normal ml-1">BOT_API_URL</span>
                                    </label>
                                    <input type="text" id="env_BOT_API_URL" placeholder="http://localhost:9998/api"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-school-green focus:border-transparent font-mono">
                                    <p class="text-xs text-gray-400 mt-1">URL API yang digunakan bot untuk #jurnal dan #laporan</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        API Key Backend
                                        <span class="font-mono text-gray-400 font-normal ml-1">BOT_API_KEY</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="env_BOT_API_KEY" placeholder="••••••••••••••••"
                                            class="w-full border border-gray-200 rounded-xl px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-school-green focus:border-transparent font-mono">
                                        <button type="button" onclick="toggleApiKeyVisibility()"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                            <i class="fas fa-eye text-xs" id="apiKeyEyeIcon"></i>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">API Key untuk autentikasi ke backend API</p>
                                </div>
                            </div>
                        </div>

                        <!-- ===== SECTION: GENERATE .ENV ===== -->
                        <div class="lg:col-span-2 bg-gray-900 rounded-2xl p-5 border border-gray-700">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-gray-700 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-file-code text-green-400 text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-white text-sm">Preview File .env</h4>
                                        <p class="text-xs text-gray-400">Salin konten ini ke file <code class="text-green-400">.env</code> di folder Baileys</p>
                                    </div>
                                </div>
                                <button onclick="copyEnvContent()"
                                    class="flex items-center gap-2 px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-gray-200 rounded-lg text-xs font-medium transition-all">
                                    <i class="fas fa-copy text-xs"></i> Salin
                                </button>
                            </div>
                            <pre id="envPreview" class="text-green-400 text-xs font-mono leading-relaxed overflow-x-auto whitespace-pre-wrap bg-gray-800 rounded-xl p-4 max-h-64 overflow-y-auto">
# Klik "Muat Pengaturan" atau isi form di atas untuk melihat preview .env
                            </pre>
                        </div>

                    </div>
                </div>

                <!-- TAB: INFO -->
                <div id="content-info" class="tab-content hidden p-6">
                    <h3 class="text-base font-bold text-gray-800 mb-4">Informasi Baileys API</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Baileys Info -->
                        <div class="bg-gray-50 rounded-2xl p-5">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                    <i class="fab fa-whatsapp text-school-green text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">Baileys WhatsApp API</h4>
                                    <p class="text-xs text-gray-500">Node.js WhatsApp Web API</p>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Status Server</span>
                                    <span id="serverStatus" class="font-medium text-yellow-600">Memeriksa...</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">URL API</span>
                                    <span class="font-mono text-xs text-gray-700" id="apiUrl">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Commands Info -->
                        <div class="bg-gray-50 rounded-2xl p-5">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-terminal text-blue-500 text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">Perintah Bot</h4>
                                    <p class="text-xs text-gray-500">Daftar perintah yang tersedia</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-start gap-2">
                                    <span class="bg-green-100 text-green-700 text-xs font-mono px-2 py-0.5 rounded font-bold">#jurnal</span>
                                    <span class="text-xs text-gray-600">Input jurnal via WhatsApp dengan gambar</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="bg-blue-100 text-blue-700 text-xs font-mono px-2 py-0.5 rounded font-bold">#laporan</span>
                                    <span class="text-xs text-gray-600">Minta laporan bulanan atau per guru</span>
                                </div>
                            </div>
                        </div>

                        <!-- Setup Guide -->
                        <div class="md:col-span-2 bg-blue-50 rounded-2xl p-5 border border-blue-100">
                            <h4 class="font-bold text-blue-800 mb-3 flex items-center gap-2">
                                <i class="fas fa-book-open text-blue-500"></i>
                                Panduan Penggunaan
                            </h4>
                            <ol class="space-y-2 text-sm text-blue-700">
                                <li class="flex gap-2">
                                    <span class="w-5 h-5 bg-blue-200 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">1</span>
                                    <span>Pastikan server Baileys berjalan (<code class="bg-blue-100 px-1 rounded font-mono text-xs">node whatsapp.js</code>)</span>
                                </li>
                                <li class="flex gap-2">
                                    <span class="w-5 h-5 bg-blue-200 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">2</span>
                                    <span>Klik <strong>Tambah Sesi</strong> dan masukkan ID sesi yang unik</span>
                                </li>
                                <li class="flex gap-2">
                                     <span class="w-5 h-5 bg-blue-200 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">3</span>
                                     <span>Pilih metode koneksi: klik <strong><i class="fas fa-qrcode text-xs"></i> QR</strong> untuk scan QR Code, atau <strong><i class="fas fa-key text-xs"></i> Kode</strong> untuk pairing code</span>
                                 </li>
                                 <li class="flex gap-2">
                                     <span class="w-5 h-5 bg-blue-200 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">4</span>
                                     <span><strong>Via QR:</strong> Scan QR Code menggunakan WhatsApp &rarr; Perangkat Tertaut &rarr; Tautkan Perangkat</span>
                                 </li>
                                 <li class="flex gap-2">
                                     <span class="w-5 h-5 bg-blue-200 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">4b</span>
                                     <span><strong>Via Kode:</strong> Masukkan nomor WA, salin kode 8 digit, lalu masukkan di WhatsApp &rarr; Perangkat Tertaut &rarr; Tautkan dengan nomor telepon</span>
                                 </li>
                                 <li class="flex gap-2">
                                     <span class="w-5 h-5 bg-blue-200 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">5</span>
                                     <span>Setelah terhubung, bot siap menerima perintah dari grup WhatsApp</span>
                                 </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- ===== MODAL: TAMBAH SESSION ===== -->
    <div id="modalAddSession" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fab fa-whatsapp text-school-green text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Tambah Sesi WhatsApp</h3>
                </div>
                <button onclick="closeAddSessionModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Session ID <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="newSessionId" placeholder="contoh: bot_utama"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green focus:border-transparent font-mono">
                    <p class="text-xs text-gray-400 mt-1">Hanya huruf, angka, underscore (_), dan dash (-)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nama Sesi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="newSessionName" placeholder="contoh: Bot Utama Bimbel"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                    <textarea id="newSessionDesc" rows="3" placeholder="Deskripsi opsional..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green focus:border-transparent resize-none"></textarea>
                </div>
            </div>

            <div class="flex gap-3 p-6 pt-0">
                <button onclick="closeAddSessionModal()"
                    class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all">
                    Batal
                </button>
                <button onclick="saveNewSession()"
                    class="flex-1 px-4 py-2.5 bg-school-green text-white rounded-xl text-sm font-medium hover:bg-school-dark-green transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-plus text-xs"></i> Buat Sesi
                </button>
            </div>
        </div>
    </div>

    <!-- ===== MODAL: QR CODE ===== -->
    <div id="modalQR" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Scan QR Code</h3>
                <button onclick="closeQRModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-6 text-center">
                <p class="text-sm text-gray-500 mb-4">Scan QR Code ini dengan WhatsApp di ponsel Anda</p>
                <p class="text-xs font-mono text-gray-400 mb-4" id="qrSessionLabel">-</p>

                <div id="qrContent" class="flex items-center justify-center min-h-48">
                    <div class="text-center text-gray-400">
                        <i class="fas fa-spinner fa-spin text-3xl mb-3 block text-school-green"></i>
                        <p class="text-sm">Memuat QR Code...</p>
                    </div>
                </div>

                <div id="qrStatus" class="mt-4 hidden">
                    <div class="flex items-center justify-center gap-2 text-green-600 bg-green-50 rounded-xl px-4 py-2">
                        <i class="fas fa-check-circle"></i>
                        <span class="text-sm font-medium">Berhasil terhubung!</span>
                    </div>
                </div>

                <p class="text-xs text-gray-400 mt-4">QR Code akan diperbarui otomatis setiap 30 detik</p>
            </div>

            <div class="p-6 pt-0 flex gap-3">
                <button onclick="refreshQR()" id="btnRefreshQR"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-green-50 text-school-green rounded-xl text-sm font-medium hover:bg-school-green hover:text-white transition-all">
                    <i class="fas fa-sync-alt"></i> Perbarui QR
                </button>
                <button onclick="closeQRModal()"
                    class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ===== MODAL: PAIRING CODE ===== -->
    <div id="modalPairingCode" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-key text-purple-500 text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Kode Pairing</h3>
                </div>
                <button onclick="closePairingModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Step 1: Input phone number -->
            <div id="pairingStep1" class="p-6">
                <p class="text-sm text-gray-600 mb-1">Masukkan nomor WhatsApp yang akan dihubungkan ke sesi:</p>
                <p class="text-xs font-mono text-gray-400 mb-4" id="pairingSessionLabel">-</p>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor WhatsApp</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">+</span>
                        <input type="text" id="pairingPhone" placeholder="628xxxxxxxxxx"
                            class="w-full border border-gray-200 rounded-xl pl-7 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent font-mono">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Format: 628xxxxxxxxxx (tanpa + atau 0 di depan)</p>
                </div>

                <div class="bg-purple-50 rounded-xl p-3 mb-4 text-xs text-purple-700">
                    <i class="fas fa-info-circle mr-1"></i>
                    Kode pairing memungkinkan Anda menghubungkan WhatsApp tanpa scan QR Code. Buka WhatsApp &rarr; Perangkat Tertaut &rarr; Tautkan Perangkat &rarr; Tautkan dengan nomor telepon.
                </div>

                <div class="flex gap-3">
                    <button onclick="closePairingModal()"
                        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all">
                        Batal
                    </button>
                    <button onclick="requestPairingCode()"
                        class="flex-1 px-4 py-2.5 bg-purple-500 text-white rounded-xl text-sm font-medium hover:bg-purple-600 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-key text-xs"></i> Dapatkan Kode
                    </button>
                </div>
            </div>

            <!-- Step 2: Show pairing code -->
            <div id="pairingStep2" class="p-6 hidden">
                <p class="text-sm text-gray-600 mb-1 text-center">Masukkan kode ini di WhatsApp Anda:</p>
                <p class="text-xs font-mono text-gray-400 mb-4 text-center" id="pairingStep2SessionLabel">-</p>

                <div id="pairingCodeDisplay" class="flex items-center justify-center min-h-24 mb-4">
                    <div class="text-center text-gray-400">
                        <i class="fas fa-spinner fa-spin text-3xl mb-3 block text-purple-500"></i>
                        <p class="text-sm">Memuat kode pairing...</p>
                    </div>
                </div>

                <div id="pairingStatus" class="hidden mb-4">
                    <div class="flex items-center justify-center gap-2 text-green-600 bg-green-50 rounded-xl px-4 py-2">
                        <i class="fas fa-check-circle"></i>
                        <span class="text-sm font-medium">Berhasil terhubung!</span>
                    </div>
                </div>

                <div class="bg-blue-50 rounded-xl p-3 mb-4 text-xs text-blue-700">
                    <strong><i class="fas fa-mobile-alt mr-1"></i>Cara menggunakan:</strong>
                    <ol class="mt-1 space-y-0.5 list-decimal list-inside">
                        <li>Buka WhatsApp di ponsel Anda</li>
                        <li>Ketuk <strong>Perangkat Tertaut</strong></li>
                        <li>Ketuk <strong>Tautkan Perangkat</strong></li>
                        <li>Pilih <strong>Tautkan dengan nomor telepon</strong></li>
                        <li>Masukkan kode 8 digit di atas</li>
                    </ol>
                </div>

                <div class="flex gap-3">
                    <button onclick="backToPairingStep1()"
                        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-arrow-left text-xs"></i> Kembali
                    </button>
                    <button onclick="closePairingModal()"
                        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/whatsapp/whatsapp.js') ?>"></script>
    <script>
        // Sidebar toggle
        document.getElementById('btnSidebar').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });
        document.getElementById('overlay').addEventListener('click', function() {
            document.getElementById('sidebar').classList.add('-translate-x-full');
            this.classList.add('hidden');
        });

        // Char counter
        document.getElementById('sendMessage').addEventListener('input', function() {
            document.getElementById('charCount').textContent = this.value.length;
        });

        // Load logs on tab switch
        document.addEventListener('DOMContentLoaded', function() {
            checkServerStatus();
        });

        // Close pairing modal on outside click
        document.getElementById('modalPairingCode').addEventListener('click', function(e) {
            if (e.target === this) closePairingModal();
        });
    </script>
</body>
</html>
