<?php
$participantMode = $participantMode ?? 'list';
$errors = $errors ?? [];
?>



<?php if ($participantMode === 'list'): ?>
    <div class="mb-6 flex flex-col md:flex-row md:items-start justify-between gap-4 fade-up">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">ผู้มีสิทธิ์สอบ</h2>
            <p class="text-sm text-gray-400 mt-0.5"><?= e($project['name']) ?></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?= e(BASE_URL) ?>/admin/projects/detail.php?id=<?= (int) $projectId ?>" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                <i class="fas fa-arrow-left mr-2 text-gray-400"></i> กลับโครงการ
            </a>
            <a href="<?= e(BASE_URL) ?>/admin/participants/import.php?project_id=<?= (int) $projectId ?>" class="inline-flex items-center px-4 py-2 bg-blue-50 border border-blue-100 hover:bg-blue-100 text-blue-700 text-sm font-medium rounded-xl transition-colors">
                <i class="fas fa-file-import mr-2"></i> นำเข้า (CSV)
            </a>
            <a href="<?= e(BASE_URL) ?>/admin/participants/create.php?project_id=<?= (int) $projectId ?>" class="inline-flex items-center px-4 py-2 bg-primary-400 hover:bg-primary-500 text-white text-sm font-medium rounded-xl transition-colors shadow-orange shadow-md">
                <i class="fas fa-plus mr-2"></i> เพิ่มรายชื่อ
            </a>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 shadow-sm fade-up">
            <i class="fas fa-check-circle mr-2"></i> <?= e($flash['message'] ?? '') ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden fade-up fade-up-1">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs font-semibold text-gray-500 tracking-wide uppercase">
                        <th class="px-6 py-4 font-medium">ชื่อ-นามสกุล</th>
                        <th class="px-6 py-4 font-medium">หน่วยงาน / ตำแหน่ง</th>
                        <th class="px-6 py-4 font-medium">อีเมล</th>
                        <th class="px-6 py-4 font-medium">Access Token</th>
                        <th class="px-6 py-4 text-right font-medium">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                <?php foreach ($participants as $participant): ?>
                    <tr class="hover:bg-primary-50/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800"><?= e(trim(($participant['title'] ? $participant['title'] . ' ' : '') . $participant['first_name'] . ' ' . $participant['last_name'])) ?></div>
                            <div class="text-xs text-gray-400 mt-0.5">ID: <?= e($participant['id_card'] ?: '-') ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-700 font-medium"><?= e($participant['organization'] ?: '-') ?></div>
                            <div class="text-xxs text-gray-400"><?= e($participant['position'] ?: '-') ?></div>
                        </td>
                        <td class="px-6 py-4 text-gray-600"><?= e($participant['email'] ?: '-') ?></td>
                        <td class="px-6 py-4">
                            <code class="px-2 py-1 bg-gray-100 rounded text-xs font-mono text-gray-500 select-all cursor-pointer" title="<?= e($participant['access_token']) ?>">
                                <?= e(substr($participant['access_token'], 0, 8)) ?>...
                            </code>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-colors" title="แก้ไข" href="<?= e(BASE_URL) ?>/admin/participants/edit.php?id=<?= (int) $participant['id'] ?>">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                <form method="post" action="<?= e(BASE_URL) ?>/admin/participants/delete.php" id="delete-form-<?= (int) $participant['id'] ?>" class="inline">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int) $participant['id'] ?>">
                                    <button type="button" 
                                            onclick="confirmDelete('ยืนยันการลบรายชื่อคุณ <?= e($participant['first_name']) ?>?', () => document.getElementById('delete-form-<?= (int) $participant['id'] ?>').submit())"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:text-red-600 hover:border-red-100 hover:bg-red-50 transition-all" 
                                            title="ลบรายชื่อ">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$participants): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-users-slash text-4xl text-gray-100 mb-3"></i>
                                <p class="text-sm font-medium">ยังไม่มีรายชื่อผู้มีสิทธิ์สอบ</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <?php $participant = array_merge(participantDefaults(), $participant ?? []); ?>
    <div class="mb-6 fade-up">
        <a href="<?= e(BASE_URL) ?>/admin/participants/?project_id=<?= (int) $project['id'] ?>" class="inline-flex items-center text-xs text-gray-400 hover:text-primary-400 transition-colors mb-2">
            <i class="fas fa-arrow-left mr-1.5"></i> กลับหน้ารายชื่อ
        </a>
        <h2 class="text-xl font-semibold text-gray-800"><?= e($pageTitle ?? 'Participant') ?></h2>
        <p class="text-sm text-gray-400 mt-0.5"><?= e($project['name']) ?></p>
    </div>

    <?php if ($errors): ?>
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-sm fade-up">
            <ul class="list-disc pl-5">
            <?php foreach ($errors as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= e($action) ?>" class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden fade-up fade-up-1">
        <?= csrfField() ?>
        <div class="p-6 md:p-8 grid gap-5 md:grid-cols-2">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">คำนำหน้า</label>
                <input name="title" value="<?= e($participant['title']) ?>" class="w-full h-11 px-4 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-[4px] focus:ring-primary-400/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">ตำแหน่ง</label>
                <input name="position" value="<?= e($participant['position']) ?>" class="w-full h-11 px-4 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-[4px] focus:ring-primary-400/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">ชื่อจริง <span class="text-primary-400">*</span></label>
                <input name="first_name" required value="<?= e($participant['first_name']) ?>" class="w-full h-11 px-4 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-[4px] focus:ring-primary-400/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">นามสกุล <span class="text-primary-400">*</span></label>
                <input name="last_name" required value="<?= e($participant['last_name']) ?>" class="w-full h-11 px-4 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-[4px] focus:ring-primary-400/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">หน่วยงาน / สังกัด</label>
                <input name="organization" value="<?= e($participant['organization']) ?>" class="w-full h-11 px-4 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-[4px] focus:ring-primary-400/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">อีเมล</label>
                <input type="email" name="email" value="<?= e($participant['email']) ?>" class="w-full h-11 px-4 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-[4px] focus:ring-primary-400/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">เบอร์โทรศัพท์</label>
                <input name="phone" value="<?= e($participant['phone']) ?>" class="w-full h-11 px-4 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-[4px] focus:ring-primary-400/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">เลขบัตรประชาชน / เลขประจำตัว</label>
                <input name="id_card" maxlength="13" value="<?= e($participant['id_card']) ?>" class="w-full h-11 px-4 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-[4px] focus:ring-primary-400/10 transition-all">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">หมายเหตุ</label>
                <textarea name="note" rows="3" class="w-full px-4 py-3 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-[4px] focus:ring-primary-400/10 transition-all"><?= e($participant['note']) ?></textarea>
            </div>
        </div>
        <div class="px-6 md:px-8 py-5 bg-gray-50/80 border-t border-gray-100 flex items-center justify-end gap-3">
            <a href="<?= e(BASE_URL) ?>/admin/participants/?project_id=<?= (int) $project['id'] ?>" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">ยกเลิก</a>
            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-primary-400 hover:bg-primary-500 rounded-xl text-sm font-semibold text-white transition-colors shadow-md shadow-orange-100">
                <i class="fas fa-save mr-2"></i> บันทึกข้อมูล
            </button>
        </div>
    </form>

    <?php if ($participantMode === 'edit'): ?>
        <div class="mt-8 bg-red-50/50 rounded-2xl border border-red-100 p-6 flex flex-col md:flex-row items-center justify-between gap-4 fade-up fade-up-2">
            <div class="flex items-center gap-4 text-center md:text-left">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-trash-can text-red-600 text-lg"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-red-800">ลบรายชื่อผู้มีสิทธิ์สอบ</h3>
                    <p class="text-xs text-red-600/70 mt-1">การลบข้อมูลจะไม่สามารถกู้คืนได้ และผลการสอบที่เกี่ยวข้องจะถูกลบออกด้วย</p>
                </div>
            </div>
            <form method="post" action="<?= e(BASE_URL) ?>/admin/participants/delete.php" id="delete-part-form-<?= (int) $participant['id'] ?>">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= (int) $participant['id'] ?>">
                <input type="hidden" name="project_id" value="<?= (int) $project['id'] ?>">
                <button type="button" onclick="confirmDelete('ยืนยันลบรายชื่อคุณ <?= e($participant['first_name']) ?> หรือไม่?', () => document.getElementById('delete-part-form-<?= (int) $participant['id'] ?>').submit())" class="inline-flex items-center px-5 py-2.5 bg-white border border-red-200 text-red-600 hover:bg-red-600 hover:text-white rounded-xl text-sm font-bold transition-all">
                    ลบรายชื่อนี้
                </button>
            </form>
        </div>
    <?php endif; ?>
<?php endif; ?>
