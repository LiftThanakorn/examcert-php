<?php if ($flash): ?>
    <div class="mb-4 rounded border border-success-600 bg-success-50 px-4 py-3 text-success-600"><?= e($flash['message'] ?? '') ?></div>
<?php endif; ?>

<section class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
    <?php foreach ($cards as $label => $value): ?>
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-600"><?= e($label) ?></p>
            <p class="mt-2 text-3xl font-semibold text-primary-600"><?= e((string) $value) ?></p>
        </div>
    <?php endforeach; ?>
</section>

<section class="mt-8 grid gap-6 lg:grid-cols-3">
    <div class="rounded-lg border border-gray-200 bg-white p-5">
        <h2 class="mb-4 font-semibold">ทางลัด</h2>
        <div class="grid gap-3">
            <a class="rounded border border-gray-200 px-4 py-3 hover:bg-gray-50" href="<?= e(BASE_URL) ?>/admin/projects/">จัดการโครงการสอบ</a>
            <a class="rounded border border-gray-200 px-4 py-3 hover:bg-gray-50" href="<?= e(BASE_URL) ?>/admin/exam-sessions/">ดูผลสอบ</a>
            <a class="rounded border border-gray-200 px-4 py-3 hover:bg-gray-50" href="<?= e(BASE_URL) ?>/admin/certificates/">ใบเซอร์/เกียรติบัตร</a>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-5 lg:col-span-2">
        <h2 class="mb-4 font-semibold">โครงการล่าสุด</h2>
        <div class="divide-y divide-gray-200">
            <?php foreach ($projects as $project): ?>
                <div class="flex items-center justify-between gap-4 py-3">
                    <div>
                        <p class="font-medium"><?= e($project['name']) ?></p>
                        <p class="text-xs text-gray-500"><?= e($project['code'] ?: '-') ?> | <?= e($project['status']) ?></p>
                    </div>
                    <a class="text-sm text-primary-600 hover:underline" href="<?= e(BASE_URL) ?>/admin/projects/detail.php?id=<?= (int) $project['id'] ?>">เปิด</a>
                </div>
            <?php endforeach; ?>
            <?php if (!$projects): ?>
                <p class="py-6 text-center text-gray-500">ยังไม่มีโครงการสอบ</p>
            <?php endif; ?>
        </div>
    </div>
</section>

