<!-- Topbar โ€” fixed top, offset left by sidebar width -->
<header class="fixed top-0 left-56 right-0 h-14 bg-white border-b border-gray-100 flex items-center justify-between px-6 z-30">
  <!-- Page title -->
  <div>
    <h1 class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($pageTitle ?? '') ?></h1>
    <?php if (!empty($breadcrumb)): ?>
    <p class="text-xxs text-gray-400 mt-0.5">
      <?= implode(' / ', array_map('htmlspecialchars', $breadcrumb)) ?>
    </p>
    <?php endif; ?>
  </div>
  <!-- Actions slot -->
  <div class="flex items-center gap-2">
    <?php if (!empty($topbarActions)) echo $topbarActions; ?>
  </div>
</header>
