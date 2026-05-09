

<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 fade-up">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">ผลการสอบและเกียรติบัตร</h2>
        <p class="text-sm text-gray-400 mt-0.5">ตรวจสอบประวัติการเข้าสอบและจัดการการออกใบเกียรติบัตรรายบุคคล</p>
    </div>
    <a class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-xl transition-colors shadow-sm" href="<?= e(BASE_URL) ?>/admin/exam-sessions/export.php">
        <i class="fas fa-file-export mr-2 text-gray-400"></i> ส่งออกรายงาน (CSV)
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden fade-up fade-up-1">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100 text-xs font-semibold text-gray-500 tracking-wide uppercase">
                    <th class="px-6 py-4 font-medium">ผู้เข้าสอบ</th>
                    <th class="px-6 py-4 font-medium">โครงการ</th>
                    <th class="px-6 py-4 font-medium text-center">คะแนน</th>
                    <th class="px-6 py-4 text-center font-medium">ผลการสอบ</th>
                    <th class="px-6 py-4 text-right font-medium">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <?php foreach ($sessions as $session): ?>
                    <tr class="hover:bg-primary-50/30 transition-colors group">
                        <td class="px-6 py-4 font-medium text-gray-800">
                            <?= e($session['participant_name']) ?>
                            <div class="text-[10px] text-gray-400 font-normal uppercase tracking-wider mt-0.5"><?= e($session['submitted_at'] ? date('d/m/Y H:i', strtotime($session['submitted_at'])) : 'ยังไม่ส่ง') ?></div>
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            <?= e($session['project_name']) ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-bold text-gray-700"><?= e((string) $session['percent']) ?>%</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if ($session['result'] === 'pass'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xxs font-bold bg-green-50 text-green-700 border border-green-100">
                                    <i class="fas fa-check-circle text-xxs"></i> ผ่าน
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xxs font-bold bg-red-50 text-red-600 border border-red-100">
                                    <i class="fas fa-circle-xmark text-xxs"></i> ไม่ผ่าน
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <?php if ($session['result'] === 'pass' && $session['status'] === 'submitted'): ?>
                                    <form method="post" action="<?= e(BASE_URL) ?>/admin/certificates/issue.php">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="session_id" value="<?= (int) $session['id'] ?>">
                                        <button class="inline-flex items-center px-3 py-1.5 bg-primary-400 hover:bg-primary-500 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                                            <i class="fas fa-certificate mr-1.5"></i> ออกใบเซอร์
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-xs text-gray-300">-</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$sessions): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-history text-4xl text-gray-100 mb-3"></i>
                                <p class="text-sm font-medium">ยังไม่มีประวัติการเข้าสอบ</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
