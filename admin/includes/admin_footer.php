<?php
declare(strict_types=1);
?>
      </main>

      <footer class="bg-white border-t border-slate-200 py-4 px-6 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
          <span>FF Wulften CMS Version 1.0 • Maßgeschneidertes Admin-Panel</span>
          <span class="text-slate-400">Lokale SQLite-Datenbank</span>
        </div>
      </footer>

    </div><!-- Ende Content Column -->
  </div><!-- Ende Wrapper -->

  <script>
  // Mobile Sidebar Drawer Toggle
  document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('admin-mobile-toggle');
    const closeBtn = document.getElementById('admin-mobile-close');
    const drawer = document.getElementById('admin-mobile-drawer');

    if (toggleBtn && drawer) {
      toggleBtn.addEventListener('click', () => {
        drawer.classList.remove('hidden');
      });
    }

    if (closeBtn && drawer) {
      closeBtn.addEventListener('click', () => {
        drawer.classList.add('hidden');
      });
    }

    if (drawer) {
      drawer.addEventListener('click', (e) => {
        if (e.target === drawer) {
          drawer.classList.add('hidden');
        }
      });
    }
  });
  </script>

  <?php if (isset($extraAdminScripts) && is_array($extraAdminScripts)): ?>
    <?php foreach ($extraAdminScripts as $sc): ?>
      <script src="<?= $sc ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>
  <script src="/assets/js/admin-cropper.js"></script>
</body>
</html>
