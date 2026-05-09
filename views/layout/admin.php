<?php
$bodyClass = 'bg-[#F9F8F6] font-sans text-gray-900';
require VIEWS_PATH . '/layout/header.php';
?>
<div class="min-h-screen bg-[#F9F8F6]">
  <?php require VIEWS_PATH . '/layout/sidebar.php'; ?>
  <?php require VIEWS_PATH . '/layout/topbar.php'; ?>

  <div class="ml-56 pt-14 min-h-screen flex flex-col">
    <main class="flex-1 p-6">
      <?php require $viewFile; ?>
    </main>
  </div>
</div>
<?php require VIEWS_PATH . '/layout/footer.php'; ?>
