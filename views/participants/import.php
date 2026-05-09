<div class="mb-6 flex items-start justify-between gap-4">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">นำเข้ารายชื่อผู้มีสิทธิ์สอบ</h2>
        <p class="text-sm text-gray-400 mt-0.5">โครงการ: <?= e($project['name']) ?></p>
    </div>
    <a href="<?= e(BASE_URL) ?>/admin/participants/?project_id=<?= (int) $project['id'] ?>" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-medium rounded-xl transition-colors shadow-sm">
        <i class="fas fa-arrow-left mr-2 text-gray-400"></i> ย้อนกลับ
    </a>
</div>

<?php if ($errors): ?>
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-sm">
        <i class="fas fa-exclamation-circle mr-2"></i> <?= implode('<br>', array_map('e', $errors)) ?>
    </div>
<?php endif; ?>

<div class="grid gap-8 lg:grid-cols-3">
    <!-- Import Form -->
    <div class="lg:col-span-1">
        <section class="bg-white rounded-2xl border border-gray-100 shadow-card p-6">
            <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-file-import mr-2 text-primary-400"></i> เลือกไฟล์ข้อมูล
            </h3>
            <form method="post" enctype="multipart/form-data" class="space-y-6">
                <?= csrfField() ?>
                <input type="hidden" name="project_id" value="<?= (int) $project['id'] ?>">
                <div>
                    <label class="mb-2 block text-xs font-semibold text-gray-500 uppercase tracking-wider">ไฟล์ CSV / Excel</label>
                    <div class="relative group">
                        <input type="file" name="participant_file" accept=".csv,.xlsx,.xls" required 
                               class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary-400 focus:ring-4 focus:ring-orange-100 transition-all outline-none bg-gray-50/50">
                    </div>
                    <div class="mt-4 p-4 rounded-xl bg-orange-50/50 border border-orange-100 text-xxs text-orange-700 leading-relaxed">
                        <p class="font-bold mb-1">รูปแบบไฟล์ที่รองรับ:</p>
                        <p>• ไฟล์ CSV (แนะนำ) จะทำงานได้ทันที</p>
                        <p>• ไฟล์ Excel (.xlsx, .xls) ต้องมีไลบรารี PhpSpreadsheet</p>
                        <p class="mt-2 font-bold mb-1">ลำดับคอลัมน์ (เรียงจากซ้าย):</p>
                        <p>ชื่อ, นามสกุล, อีเมล, องค์กร, ตำแหน่ง, โทรศัพท์, เลขบัตรประชาชน, หมายเหตุ</p>
                    </div>
                </div>
                <button class="w-full py-3 bg-primary-400 hover:bg-primary-500 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-orange-100">
                    เริ่มการนำเข้าข้อมูล
                </button>
            </form>
        </section>
    </div>

    <!-- Instructions / Summary -->
    <div class="lg:col-span-2">
        <?php if ($summary): ?>
            <section class="bg-white rounded-2xl border border-gray-100 shadow-card p-6 mb-6">
                <h3 class="text-sm font-bold text-gray-800 mb-4">สรุปผลการนำเข้า</h3>
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-xl border border-green-100 bg-green-50 p-4">
                        <p class="text-xxs font-bold text-green-600 uppercase mb-1">สำเร็จ</p>
                        <p class="text-2xl font-black text-green-700"><?= (int) $summary['created'] ?></p>
                    </div>
                    <div class="rounded-xl border border-yellow-100 bg-yellow-50 p-4">
                        <p class="text-xxs font-bold text-yellow-600 uppercase mb-1">ข้าม / ผิดพลาด</p>
                        <p class="text-2xl font-black text-yellow-700"><?= (int) $summary['skipped'] ?></p>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                        <p class="text-xxs font-bold text-gray-400 uppercase mb-1">Batch ID</p>
                        <p class="text-xs font-mono text-gray-600 truncate"><?= e($summary['batch'] ?? '-') ?></p>
                    </div>
                </div>
                
                <div class="mt-6">
                    <h4 class="text-xs font-bold text-gray-500 mb-3">รายละเอียดรายบรรทัด</h4>
                    <div class="max-h-[400px] overflow-auto rounded-xl border border-gray-100">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 text-gray-500 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">บรรทัด</th>
                                    <th class="px-4 py-3 font-semibold">สถานะ</th>
                                    <th class="px-4 py-3 font-semibold">รายละเอียด</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php foreach ($summary['rows'] as $row): ?>
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-4 py-3 text-gray-400 font-mono"><?= (int) $row['row'] ?></td>
                                        <td class="px-4 py-3">
                                            <?php if ($row['status'] === 'created'): ?>
                                                <span class="text-green-600 font-bold"><i class="fas fa-check mr-1"></i> สำเร็จ</span>
                                            <?php else: ?>
                                                <span class="text-yellow-600 font-bold"><i class="fas fa-exclamation-triangle mr-1"></i> ข้าม</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600"><?= e($row['message']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        <?php else: ?>
            <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-12 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-200"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">ยังไม่มีการนำเข้าข้อมูล</h3>
                <p class="text-sm text-gray-400 max-w-sm mx-auto">เลือกไฟล์ CSV หรือ Excel ที่มีรายชื่อผู้เข้าสอบเพื่อเริ่มต้นระบบจะตรวจสอบความถูกต้องให้อัตโนมัติ</p>
            </div>
        <?php endif; ?>
    </div>
</div>
