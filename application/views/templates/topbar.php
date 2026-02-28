<?php
/**
 * Topbar partial
 *
 * Variables expected:
 *   $user            - array with 'nama' key
 *   $topbar_title    - string: page title shown in topbar (e.g. 'Dashboard')
 *   $topbar_subtitle - string (optional): subtitle below title
 *   $topbar_actions  - string (optional): raw HTML for action buttons on the right side
 */
?>
<!-- ===== TOPBAR ===== -->
<header class="bg-white shadow-sm flex items-center justify-between px-4 h-16 sticky top-0 z-40 border-b border-gray-100">
    <div class="flex items-center gap-4">
        <!-- Hamburger (mobile) -->
        <button id="btnSidebar" class="md:hidden text-2xl text-school-green hover:text-school-dark-green transition-colors">
            <i class="fas fa-bars"></i>
        </button>
        <div>
            <h2 class="text-lg font-bold text-gray-800 hidden sm:block">
                <?= isset($topbar_title) ? $topbar_title : ''; ?>
            </h2>
            <?php if (!empty($topbar_subtitle)): ?>
            <p class="text-xs text-gray-500 hidden sm:block"><?= $topbar_subtitle; ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <?php if (!empty($topbar_actions)): ?>
            <?= $topbar_actions; ?>
        <?php endif; ?>

        <!-- User badge -->
        <div class="flex items-center gap-2 bg-green-50 px-3 py-2 rounded-lg">
            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-school-green to-school-dark-green flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                <?= strtoupper(substr($user['nama'], 0, 1)); ?>
            </div>
            <span class="text-sm font-medium text-gray-700 hidden sm:block"><?= $user['nama']; ?></span>
        </div>
    </div>
</header>
