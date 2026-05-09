  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
  <?php if (!empty($useCharts)): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <?php endif; ?>
  <script src="<?= e(BASE_URL) ?>/assets/js/app.js"></script>
  <?php if (!empty($pageScripts)): ?>
    <?= $pageScripts ?>
  <?php endif; ?>
  <?php if (isset($_SESSION['flash'])): ?>
    <?php $sessionFlash = $_SESSION['flash']; unset($_SESSION['flash']); ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const type = <?= json_encode($sessionFlash['type'] ?? 'info') ?>;
        const message = <?= json_encode($sessionFlash['message'] ?? '') ?>;
        if (!message) return;
        setTimeout(() => {
          if (type === 'error') {
            showAlert('เกิดข้อผิดพลาด', message, 'error');
          } else if (window.toast) {
            // Success/Info/Warning: Use Smooth Toast
            if (type === 'success' && toast.success) toast.success(message);
            else if (type === 'warning' && toast.warning) toast.warning(message);
            else if (toast.info) toast.info(message);
          } else {
            // Fallback
            Swal.fire({ icon: type, title: message, toast: true, position: 'top-end', timer: 3000, showConfirmButton: false });
          }
        }, 150);
      });
    </script>
  <?php endif; ?>
</body>
</html>
