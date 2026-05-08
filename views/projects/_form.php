<?php
$project = array_merge(projectDefaults(), $project ?? []);
$errors = $errors ?? [];
$action = $action ?? '';
?>
<?php if ($errors): ?>
    <div class="mb-4 rounded border border-danger-600 bg-danger-50 px-4 py-3 text-danger-600">
        <?php foreach ($errors as $error): ?>
            <div><?= e($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= e($action) ?>" class="space-y-6 rounded-lg border border-gray-200 bg-white p-6">
    <?= csrfField() ?>
    <div class="grid gap-5 md:grid-cols-2">
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-2">ชื่อโครงการสอบ</label>
            <input name="name" required value="<?= e($project['name']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">รหัสโครงการ</label>
            <input name="code" value="<?= e($project['code']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">สถานะ</label>
            <select name="status" class="w-full rounded border border-gray-200 px-3 py-2">
                <?php foreach (['draft' => 'ร่าง', 'active' => 'เปิดใช้งาน', 'closed' => 'ปิดแล้ว'] as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $project['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-2">รายละเอียด</label>
            <textarea name="description" rows="4" class="w-full rounded border border-gray-200 px-3 py-2"><?= e($project['description']) ?></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">ผู้จัด</label>
            <input name="organizer" value="<?= e($project['organizer']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">สถานที่</label>
            <input name="location" value="<?= e($project['location']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">วันที่เริ่มโครงการ</label>
            <input type="date" name="start_date" value="<?= e($project['start_date']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">วันที่สิ้นสุดโครงการ</label>
            <input type="date" name="end_date" value="<?= e($project['end_date']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">เวลาเปิดสอบ</label>
            <input type="datetime-local" name="exam_start" value="<?= e($project['exam_start'] ? str_replace(' ', 'T', substr($project['exam_start'], 0, 16)) : '') ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">เวลาปิดสอบ</label>
            <input type="datetime-local" name="exam_end" value="<?= e($project['exam_end'] ? str_replace(' ', 'T', substr($project['exam_end'], 0, 16)) : '') ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">คะแนนผ่าน (%)</label>
            <input type="number" step="0.01" min="0" max="100" name="pass_score" value="<?= e((string) $project['pass_score']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">จำนวนครั้งที่สอบได้</label>
            <input type="number" min="1" name="max_attempts" value="<?= e((string) $project['max_attempts']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">เวลาทำข้อสอบ (นาที)</label>
            <input type="number" min="1" name="time_limit_min" value="<?= e((string) $project['time_limit_min']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">จำนวนข้อที่ใช้สอบ (0 = ทั้งหมด)</label>
            <input type="number" min="0" name="question_count" value="<?= e((string) $project['question_count']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
    </div>

    <div class="grid gap-3 md:grid-cols-3">
        <label class="flex items-center gap-2"><input type="checkbox" name="randomize_questions" value="1" <?= $project['randomize_questions'] ? 'checked' : '' ?>> สุ่มข้อสอบ</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="randomize_choices" value="1" <?= $project['randomize_choices'] ? 'checked' : '' ?>> สุ่มตัวเลือก</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="show_result_immediately" value="1" <?= $project['show_result_immediately'] ? 'checked' : '' ?>> แสดงผลทันที</label>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="<?= e(BASE_URL) ?>/admin/projects/" class="rounded border border-gray-200 px-4 py-2">ยกเลิก</a>
        <button class="rounded bg-primary-600 px-4 py-2 text-white hover:bg-primary-700">บันทึก</button>
    </div>
</form>

