<?php if ($flash): ?>
    <div class="mb-4 rounded border border-success-600 bg-success-50 px-4 py-3 text-success-600"><?= e($flash['message'] ?? '') ?></div>
<?php endif; ?>

<div class="mb-6 flex items-start justify-between gap-4">
    <div>
        <h2 class="text-2xl font-semibold"><?= e($project['name']) ?></h2>
        <p class="text-sm text-gray-600"><?= e($project['code'] ?: '-') ?> | <?= e($project['status']) ?></p>
    </div>
    <div class="flex gap-2">
        <a href="<?= e(BASE_URL) ?>/admin/projects/" class="rounded border border-gray-200 px-4 py-2">กลับ</a>
        <a href="<?= e(BASE_URL) ?>/admin/participants/?project_id=<?= (int) $project['id'] ?>" class="rounded border border-gray-200 px-4 py-2">ผู้มีสิทธิ์สอบ</a>
        <a href="<?= e(BASE_URL) ?>/admin/questions/?project_id=<?= (int) $project['id'] ?>" class="rounded border border-gray-200 px-4 py-2">คลังข้อสอบ</a>
        <a href="<?= e(BASE_URL) ?>/admin/projects/edit.php?id=<?= (int) $project['id'] ?>" class="rounded bg-primary-600 px-4 py-2 text-white">แก้ไข</a>
    </div>
</div>

<section class="rounded-lg border border-gray-200 bg-white p-6">
    <dl class="grid gap-5 md:grid-cols-2">
        <div><dt class="text-sm text-gray-500">ผู้จัด</dt><dd class="font-medium"><?= e($project['organizer'] ?: '-') ?></dd></div>
        <div><dt class="text-sm text-gray-500">สถานที่</dt><dd class="font-medium"><?= e($project['location'] ?: '-') ?></dd></div>
        <div><dt class="text-sm text-gray-500">ช่วงสอบ</dt><dd class="font-medium"><?= e($project['exam_start'] ?: '-') ?> ถึง <?= e($project['exam_end'] ?: '-') ?></dd></div>
        <div><dt class="text-sm text-gray-500">คะแนนผ่าน</dt><dd class="font-medium"><?= e((string) $project['pass_score']) ?>%</dd></div>
        <div><dt class="text-sm text-gray-500">เวลาทำข้อสอบ</dt><dd class="font-medium"><?= (int) $project['time_limit_min'] ?> นาที</dd></div>
        <div><dt class="text-sm text-gray-500">จำนวนครั้งที่สอบได้</dt><dd class="font-medium"><?= (int) $project['max_attempts'] ?> ครั้ง</dd></div>
    </dl>
    <?php if ($project['description']): ?>
        <div class="mt-6 border-t border-gray-200 pt-5">
            <h3 class="mb-2 font-semibold">รายละเอียด</h3>
            <p class="whitespace-pre-line"><?= e($project['description']) ?></p>
        </div>
    <?php endif; ?>
</section>

<form method="post" action="<?= e(BASE_URL) ?>/admin/projects/delete.php" class="mt-6" onsubmit="return confirm('ยืนยันลบโครงการสอบนี้?');">
    <?= csrfField() ?>
    <input type="hidden" name="id" value="<?= (int) $project['id'] ?>">
    <button class="rounded border border-danger-600 px-4 py-2 text-danger-600 hover:bg-danger-50">ลบโครงการ</button>
</form>

