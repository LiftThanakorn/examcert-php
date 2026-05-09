

<div class="mb-6 flex items-center justify-between gap-4 fade-up">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">โครงการสอบ</h2>
        <p class="text-sm text-gray-400 mt-0.5">จัดการโครงการสอบและการออกใบเกียรติบัตร</p>
    </div>
    <a href="<?= e(BASE_URL) ?>/admin/projects/create.php" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-400 hover:bg-primary-500 text-white text-sm font-medium rounded-xl transition-colors shadow-orange shadow-md">
        <i class="fas fa-plus"></i> สร้างโครงการ
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden fade-up fade-up-1">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100 text-xs font-semibold text-gray-500 tracking-wide uppercase">
                    <th class="px-6 py-4 font-medium">ชื่อโครงการ</th>
                    <th class="px-6 py-4 font-medium">สถานะ</th>
                    <th class="px-6 py-4 font-medium">ช่วงสอบ</th>
                    <th class="px-6 py-4 font-medium text-center">ผู้เข้าสอบ</th>
                    <th class="px-6 py-4 font-medium text-center">ข้อสอบ</th>
                    <th class="px-6 py-4 text-right font-medium">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
            <?php foreach ($projects as $project): ?>
                <tr class="hover:bg-primary-50/30 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-800"><?= e($project['name']) ?></div>
                        <div class="text-xs text-gray-400 mt-0.5">ID: <?= e($project['code'] ?: '-') ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <?php if ($project['status'] === 'active'): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xxs font-medium bg-green-50 text-green-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>เปิดใช้งาน
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xxs font-medium bg-gray-100 text-gray-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>ปิดใช้งาน
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-xs">
                        <div class="flex flex-col gap-1">
                            <span class="flex items-center gap-1.5"><i class="far fa-calendar-check text-green-500"></i> <?= e($project['exam_start'] ? date('d/m/Y H:i', strtotime($project['exam_start'])) : '-') ?></span>
                            <span class="flex items-center gap-1.5"><i class="far fa-calendar-xmark text-red-400"></i> <?= e($project['exam_end'] ? date('d/m/Y H:i', strtotime($project['exam_end'])) : '-') ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center min-w-[2.5rem] h-7 px-2 bg-blue-50 text-blue-600 font-semibold rounded-lg text-xs">
                            <?= (int) $project['participant_count'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center min-w-[2.5rem] h-7 px-2 bg-orange-50 text-primary-600 font-semibold rounded-lg text-xs">
                            <?= (int) $project['question_count_total'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a class="w-8 h-8 inline-flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors" title="ดูรายละเอียด" href="<?= e(BASE_URL) ?>/admin/projects/detail.php?id=<?= (int) $project['id'] ?>">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a class="w-8 h-8 inline-flex items-center justify-center rounded-lg hover:bg-primary-50 text-gray-400 hover:text-primary-600 transition-colors" title="แก้ไข" href="<?= e(BASE_URL) ?>/admin/projects/edit.php?id=<?= (int) $project['id'] ?>">
                                <i class="fas fa-pen text-xs"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$projects): ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-folder-open text-4xl text-gray-200 mb-3"></i>
                            <p class="text-sm">ยังไม่มีโครงการสอบ</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
