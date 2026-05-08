<h2 class="mb-1 text-2xl font-semibold">แก้ไขข้อสอบ</h2>
<p class="mb-6 text-sm text-gray-600"><?= e($project['name']) ?></p>

<?php require __DIR__ . '/_form.php'; ?>

<form method="post" action="<?= e(BASE_URL) ?>/admin/questions/delete.php" class="mt-6" onsubmit="return confirm('ยืนยันลบข้อสอบนี้?');">
    <?= csrfField() ?>
    <input type="hidden" name="id" value="<?= (int) $id ?>">
    <button class="rounded border border-danger-600 px-4 py-2 text-danger-600 hover:bg-danger-50">ลบข้อสอบ</button>
</form>
