<?php
$question = array_merge(questionDefaults(), $question ?? []);
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
    <div>
        <label class="block text-sm font-medium mb-2">คำถาม</label>
        <textarea name="question_text" required rows="4" class="w-full rounded border border-gray-200 px-3 py-2"><?= e($question['question_text']) ?></textarea>
    </div>
    <div class="grid gap-5 md:grid-cols-3">
        <div>
            <label class="block text-sm font-medium mb-2">ประเภท</label>
            <select name="type" class="w-full rounded border border-gray-200 px-3 py-2">
                <?php foreach (['multiple_choice' => 'ปรนัย', 'true_false' => 'ถูก/ผิด', 'fill_blank' => 'เติมคำ'] as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $question['type'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">ระดับความยาก</label>
            <select name="difficulty" class="w-full rounded border border-gray-200 px-3 py-2">
                <?php foreach (['easy' => 'ง่าย', 'medium' => 'ปานกลาง', 'hard' => 'ยาก'] as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $question['difficulty'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">คะแนน</label>
            <input type="number" step="0.01" min="0.01" name="score_weight" value="<?= e((string) $question['score_weight']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
    </div>
    <div class="grid gap-5 md:grid-cols-2">
        <?php foreach (['a', 'b', 'c', 'd'] as $key): ?>
            <div>
                <label class="block text-sm font-medium mb-2">ตัวเลือก <?= strtoupper($key) ?></label>
                <input name="choice_<?= e($key) ?>" value="<?= e($question['choice_' . $key] ?? '') ?>" class="w-full rounded border border-gray-200 px-3 py-2">
            </div>
        <?php endforeach; ?>
    </div>
    <div class="grid gap-5 md:grid-cols-3">
        <div>
            <label class="block text-sm font-medium mb-2">คำตอบที่ถูกต้อง</label>
            <input name="correct_answer" required value="<?= e($question['correct_answer']) ?>" class="w-full rounded border border-gray-200 px-3 py-2" placeholder="a, b, true, false หรือคำตอบ">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">หมวดหมู่</label>
            <input name="category" value="<?= e($question['category']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">ลำดับ</label>
            <input type="number" name="order_num" value="<?= e((string) $question['order_num']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium mb-2">คำอธิบายเฉลย</label>
        <textarea name="explanation" rows="3" class="w-full rounded border border-gray-200 px-3 py-2"><?= e($question['explanation']) ?></textarea>
    </div>
    <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" <?= $question['is_active'] ? 'checked' : '' ?>> เปิดใช้งาน</label>
    <div class="flex justify-end gap-3">
        <a href="<?= e(BASE_URL) ?>/admin/questions/?project_id=<?= (int) $project['id'] ?>" class="rounded border border-gray-200 px-4 py-2">ยกเลิก</a>
        <button class="rounded bg-primary-600 px-4 py-2 text-white hover:bg-primary-700">บันทึก</button>
    </div>
</form>
