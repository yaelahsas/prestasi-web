<?php
/**
 * Footer partial — place at the very end of each view, after closing </div><!-- /.md:ml-64 -->
 *
 * Variables expected:
 *   $extra_js - array of JS file URLs to include before </body>  (optional)
 */
?>
    <?php if (isset($extra_js)): ?>
        <?php foreach ($extra_js as $js): ?>
    <script src="<?= $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- ===== SIDEBAR TOGGLE SCRIPT ===== -->
    <script>
        (function () {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('overlay');
            var btnOpen = document.getElementById('btnSidebar');

            function openSidebar() {
                if (!sidebar) return;
                sidebar.classList.remove('-translate-x-full');
                if (overlay) overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                if (!sidebar) return;
                sidebar.classList.add('-translate-x-full');
                if (overlay) overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }

            if (btnOpen) btnOpen.addEventListener('click', openSidebar);
            if (overlay)  overlay.addEventListener('click', closeSidebar);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeSidebar();
            });
        })();
    </script>

</body>
</html>
