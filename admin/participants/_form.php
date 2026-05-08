<?php
$participant = array_merge(participantDefaults(), $participant ?? []);
$errors = $errors ?? [];
$action = $action ?? '';
?>
<?php if ($errors): ?>
    <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-red-700">
        <?php foreach ($errors as $error): ?>
            <div><?= e($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= e($action) ?>" class="space-y-6 rounded-lg border border-gray-200 bg-white p-6">
    <?= csrfField() ?>
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium mb-2">คำนำหน้า</label>
            <input name="title" value="<?= e($participant['title']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">ตำแหน่ง</label>
            <input name="position" value="<?= e($participant['position']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">ชื่อ</label>
            <input name="first_name" required value="<?= e($participant['first_name']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">นามสกุล</label>
            <input name="last_name" required value="<?= e($participant['last_name']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">หน่วยงาน</label>
            <input name="organization" value="<?= e($participant['organization']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">อีเมล</label>
            <input type="email" name="email" value="<?= e($participant['email']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">โทรศัพท์</label>
            <input name="phone" value="<?= e($participant['phone']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">เลขบัตรประชาชน</label>
            <input name="id_card" maxlength="13" value="<?= e($participant['id_card']) ?>" class="w-full rounded border border-gray-200 px-3 py-2">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-2">หมายเหตุ</label>
            <textarea name="note" rows="4" class="w-full rounded border border-gray-200 px-3 py-2"><?= e($participant['note']) ?></textarea>
        </div>
    </div>
    <div class="flex items-center justify-end gap-3">
        <a href="<?= e(BASE_URL) ?>/admin/participants/?project_id=<?= (int) $project['id'] ?>" class="rounded border border-gray-200 px-4 py-2">ยกเลิก</a>
        <button class="rounded bg-orange-600 px-4 py-2 text-white hover:bg-orange-700">บันทึก</button>
    </div>
</form>

