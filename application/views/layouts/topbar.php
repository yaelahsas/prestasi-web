        <!-- TOPBAR -->
        <header class="bg-white shadow-sm flex items-center justify-between px-4 h-16 sticky top-0 z-40 border-b border-gray-100">
            <div class="flex items-center gap-4">
                <button id="btnSidebar" class="md:hidden text-2xl text-school-green hover:text-school-dark-green transition-colors">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h2 class="text-lg font-bold text-gray-800 hidden sm:block"><?= $page_title; ?></h2>
                    <p class="text-xs text-gray-500 hidden sm:block" id="currentDateTime"></p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button id="refreshBtn" title="Refresh Data (F5)"
                    class="p-2 rounded-lg bg-green-50 hover:bg-school-green text-school-dark-green hover:text-white transition-all duration-200 group">
                    <i class="fas fa-sync-alt group-hover:rotate-180 transition-transform duration-500 text-sm"></i>
                </button>
                <div class="flex items-center gap-2 bg-green-50 px-3 py-2 rounded-lg">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-school-green to-school-dark-green flex items-center justify-center text-white font-bold text-xs">
                        <?= strtoupper(substr($user['nama'], 0, 1)); ?>
                    </div>
                    <span class="text-sm font-medium text-gray-700 hidden sm:block"><?= $user['nama']; ?></span>
                </div>
            </div>
        </header>
