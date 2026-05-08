<div class="mb-6 flex items-start justify-between gap-4">
    <div>
        <h2 class="text-2xl font-semibold">คลังข้อสอบ</h2>
        <p class="text-sm text-gray-600"><?= e($project['name']) ?></p>
    </div>
    <div class="flex gap-2">
        <a href="<?= e(BASE_URL) ?>/admin/projects/detail.php?id=<?= (int) $projectId ?>" class="rounded border border-gray-200 px-4 py-2">กลับโครงการ</a>
        <a href="<?= e(BASE_URL) ?>/admin/questions/create.php?project_id=<?= (int) $projectId ?>" class="rounded bg-primary-600 px-4 py-2 text-white">เพิ่มข้อสอบ</a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="mb-4 rounded border border-success-600 bg-success-50 px-4 py-3 text-success-600"><?= e($flash['message'] ?? '') ?></div>
<?php endif; ?>

<div class="rounded-lg border border-gray-200 bg-white">
    <?php foreach ($questions as $question): ?>
        <div class="border-b border-gray-200 p-4">
            <div class="flex justify-between gap-4">
                <div>
                    <p class="font-medium"><?= e($question['question_text']) ?></p>
                    <p class="mt-1 text-xs text-gray-500"><?= e($question['type']) ?> | <?= e($question['difficulty']) ?> | <?= e((string) $question['score_weight']) ?> คะแนน</p>
                </div>
                <a class="text-primary-600 hover:underline" href="<?= e(BASE_URL) ?>/admin/questions/edit.php?id=<?= (int) $question['id'] ?>">แก้ไข</a>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (!$questions): ?>
        <div class="p-8 text-center text-gray-500">ยังไม่มีข้อสอบ</div>
    <?php endif; ?>
</div>
