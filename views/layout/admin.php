<?php
require VIEWS_PATH . '/layout/header.php';
?>
<div class="min-h-screen flex">
    <?php require VIEWS_PATH . '/layout/sidebar.php'; ?>
    <main class="flex-1 flex flex-col min-w-0">
        <?php require VIEWS_PATH . '/layout/topbar.php'; ?>
        <div class="p-6">
            <?php require $viewFile; ?>
        </div>
    </main>
</div>
<?php require VIEWS_PATH . '/layout/footer.php'; ?>

