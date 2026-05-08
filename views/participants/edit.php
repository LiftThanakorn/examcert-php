<h2 class="mb-1 text-2xl font-semibold">แก้ไขผู้มีสิทธิ์สอบ</h2>
<p class="mb-6 text-sm text-gray-600"><?= e($project['name']) ?></p>
<?php require VIEWS_PATH . '/participants/_form.php'; ?>

<form method="post" action="<?= e(BASE_URL) ?>/admin/participants/delete.php" class="mt-6" onsubmit="return confirm('ยืนยันลบรายชื่อนี้?');">
    <?= csrfField() ?>
    <input type="hidden" name="id" value="<?= (int) $participant['id'] ?>">
    <button class="rounded border border-danger-600 px-4 py-2 text-danger-600 hover:bg-danger-50">ลบรายชื่อ</button>
</form>

