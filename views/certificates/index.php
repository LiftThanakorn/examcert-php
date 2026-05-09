<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 fade-up">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">จัดการใบเกียรติบัตร</h2>
        <p class="text-sm text-gray-400 mt-0.5">ตรวจสอบ ดาวน์โหลด และจัดการสถานะใบเกียรติบัตรที่ออกให้ผู้เข้าสอบ</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="<?= e(BASE_URL) ?>/admin/certificates/templates.php" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-xl transition-colors shadow-sm">
            <i class="fas fa-palette mr-2 text-gray-400"></i> จัดการเทมเพลต
        </a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="mb-6 rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-sm text-green-700 shadow-sm fade-up">
        <i class="fas fa-check-circle mr-2 text-green-500"></i> <?= e($flash['message'] ?? '') ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden fade-up fade-up-1">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse table-row-hover">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100 text-xs font-semibold text-gray-500 tracking-wide uppercase">
                    <th class="px-6 py-4 font-medium">เลขที่ใบเซอร์</th>
                    <th class="px-6 py-4 font-medium">ผู้ได้รับ</th>
                    <th class="px-6 py-4 font-medium">โครงการ</th>
                    <th class="px-6 py-4 font-medium text-center">วันที่ออก</th>
                    <th class="px-6 py-4 text-center font-medium">สถานะ</th>
                    <th class="px-6 py-4 text-right font-medium">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <?php foreach ($certificates as $cert): ?>
                    <tr class="group transition-colors">
                        <td class="px-6 py-4 font-mono text-xs font-bold text-gray-800">
                            <?= e($cert['cert_number']) ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800"><?= e($cert['participant_name']) ?></div>
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            <?= e($cert['project_name']) ?>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-500">
                            <?= e($cert['issued_date']) ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if ((int) $cert['is_revoked'] === 1): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xxs font-bold bg-red-50 text-red-600 border border-red-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>ยกเลิกแล้ว
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xxs font-bold bg-green-50 text-green-700 border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>ปกติ
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-primary-400 hover:border-primary-200 hover:bg-primary-50/50 transition-all" title="ดาวน์โหลด" href="<?= e(BASE_URL) ?>/admin/certificates/download.php?token=<?= e($cert['verify_token']) ?>">
                                    <i class="fas fa-download text-xs"></i>
                                </a>
                                <a class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-blue-500 hover:border-blue-200 hover:bg-blue-50/50 transition-all" title="ตรวจสอบ" href="<?= e($cert['verify_url']) ?>" target="_blank">
                                    <i class="fas fa-external-link-alt text-xs"></i>
                                </a>
                                <form method="post" action="<?= e(BASE_URL) ?>/admin/certificates/revoke.php" class="inline" id="revoke-form-<?= (int) $cert['id'] ?>">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int) $cert['id'] ?>">
                                    <input type="hidden" name="action" value="<?= (int) $cert['is_revoked'] === 1 ? 'restore' : 'revoke' ?>">
                                    <button type="button" 
                                            onclick="confirmDelete('ยืนยัน<?= (int) $cert['is_revoked'] === 1 ? 'คืนสถานะ' : 'ยกเลิก' ?>ใบเกียรติบัตรนี้?', () => document.getElementById('revoke-form-<?= (int) $cert['id'] ?>').submit())"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-400 <?= (int) $cert['is_revoked'] === 1 ? 'hover:text-green-600 hover:border-green-200 hover:bg-green-50' : 'hover:text-red-600 hover:border-red-200 hover:bg-red-50' ?> transition-all" 
                                            title="<?= (int) $cert['is_revoked'] === 1 ? 'คืนสถานะ' : 'ยกเลิกใบเซอร์' ?>">
                                        <i class="fas <?= (int) $cert['is_revoked'] === 1 ? 'fa-rotate-left' : 'fa-ban' ?> text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$certificates): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mb-4">
                                    <i class="fas fa-certificate text-3xl text-gray-200"></i>
                                </div>
                                <p class="text-sm font-medium">ยังไม่มีใบเกียรติบัตรที่ถูกออก</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
