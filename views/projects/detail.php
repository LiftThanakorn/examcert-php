<?php
$project = $project ?? [];
$runtimeStatus = $runtimeStatus ?? ['allowed' => false, 'status' => 'draft', 'message' => '', 'seconds_left' => null, 'warning' => false];
?>

<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 fade-up">
    <div>
        <h2 class="text-2xl font-semibold text-gray-800"><?= e($project['name']) ?></h2>
        <div class="flex items-center gap-2 mt-1">
            <span class="text-sm text-gray-500">ID: <?= e($project['code'] ?: '-') ?></span>
            <span class="text-gray-300">|</span>
            <?php 
            $statusClass = 'bg-gray-100 text-gray-600';
            $statusDot = 'bg-gray-400';
            $statusLabel = 'Draft';
            if ($project['status'] === 'active') {
                $statusClass = 'bg-green-50 text-green-700';
                $statusDot = 'bg-green-500';
                $statusLabel = 'Active';
            } elseif ($project['status'] === 'closed') {
                $statusClass = 'bg-red-50 text-red-700';
                $statusDot = 'bg-red-500';
                $statusLabel = 'Closed';
            }
            ?>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xxs font-medium <?= $statusClass ?>">
                <span class="w-1.5 h-1.5 rounded-full <?= $statusDot ?>"></span>
                <?= e($statusLabel) ?>
            </span>
        </div>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="<?= e(BASE_URL) ?>/admin/projects/" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-xl transition-colors shadow-sm">
            <i class="fas fa-arrow-left mr-2 text-gray-400"></i> กลับ
        </a>
        <a href="<?= e(BASE_URL) ?>/admin/participants/?project_id=<?= (int) $project['id'] ?>" class="inline-flex items-center px-4 py-2 bg-orange-50 text-primary-600 hover:bg-orange-100 text-sm font-medium rounded-xl transition-colors">
            <i class="fas fa-users mr-2 text-primary-400"></i> ผู้มีสิทธิ์สอบ
        </a>
        <a href="<?= e(BASE_URL) ?>/admin/questions/?project_id=<?= (int) $project['id'] ?>" class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 text-sm font-medium rounded-xl transition-colors">
            <i class="fas fa-file-lines mr-2 text-blue-400"></i> คลังข้อสอบ
        </a>
        <a href="<?= e(BASE_URL) ?>/admin/projects/edit.php?id=<?= (int) $project['id'] ?>" class="inline-flex items-center px-4 py-2 bg-primary-400 hover:bg-primary-500 text-white text-sm font-medium rounded-xl transition-colors shadow-orange shadow-md">
            <i class="fas fa-pen mr-2"></i> แก้ไขโครงการ
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <!-- Control Center Card (Mockup Integrated) -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden fade-up fade-up-1">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
                <div class="flex items-center gap-2">
                    <i class="ti ti-calendar-time text-primary-400 text-xl"></i>
                    <h3 class="text-base font-semibold text-gray-800">ตั้งค่าช่วงเวลาและการเข้าสอบ</h3>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-primary-50 text-primary-700">
                    Control Center
                </span>
            </div>

            <div class="p-6">
                <!-- Status Preview -->
                <?php
                $runtimeStatusType = $runtimeStatus['status'] ?? 'draft';
                $statusPreviewCls = 'bg-gray-50 text-gray-600';
                $statusDotCls = 'bg-gray-400';
                $statusText = 'สถานะ: ' . ucfirst($runtimeStatusType);

                if ($project['manual_override']) {
                    $statusPreviewCls = 'bg-orange-50 text-orange-800 border border-orange-100';
                    $statusDotCls = 'bg-orange-500';
                    $statusText = 'Manual Override: ' . ($project['status'] === 'active' ? 'เปิดใช้งานแบบกำหนดเอง' : 'ปิดการใช้งานแบบกำหนดเอง');
                } elseif ($runtimeStatusType === 'open') {
                    $statusPreviewCls = 'bg-green-50 text-green-800 border border-green-100';
                    $statusDotCls = 'bg-green-500';
                    $statusText = 'สถานะ: กำลังเปิดให้เข้าสอบ (Open)';
                } elseif ($runtimeStatusType === 'scheduled') {
                    $statusPreviewCls = 'bg-blue-50 text-blue-800 border border-blue-100';
                    $statusDotCls = 'bg-blue-500';
                    $statusText = 'สถานะ: รอเปิดอัตโนมัติ (Scheduled)';
                } elseif ($runtimeStatusType === 'closed') {
                    $statusPreviewCls = 'bg-red-50 text-red-800 border border-red-100';
                    $statusDotCls = 'bg-red-500';
                    $statusText = 'สถานะ: สิ้นสุดช่วงเวลาสอบแล้ว (Closed)';
                }
                ?>
                <div class="flex items-center gap-3 px-4 py-4 rounded-2xl mb-8 <?= $statusPreviewCls ?>">
                    <span class="w-3 h-3 rounded-full animate-pulse <?= $statusDotCls ?>"></span>
                    <span class="text-sm font-semibold">
                        <?= e($statusText) ?>
                        <?php if ($runtimeStatus['message']): ?>
                            — <span class="opacity-75 font-normal"><?= e($runtimeStatus['message']) ?></span>
                        <?php endif; ?>
                    </span>
                </div>

                <form method="post" action="<?= e(BASE_URL) ?>/admin/projects/schedule.php" class="space-y-8">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= (int) $project['id'] ?>">

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest">วันเวลาเปิดรับสอบ</label>
                            <input type="datetime-local" name="exam_start" value="<?= $project['exam_start'] ? date('Y-m-d\TH:i', strtotime($project['exam_start'])) : '' ?>" 
                                   class="w-full h-12 px-4 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:bg-white transition-all">
                            <p class="text-xxs text-gray-400">ระบบจะเปิดให้ทำข้อสอบอัตโนมัติเมื่อถึงเวลา</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest">วันเวลาปิดรับสอบ</label>
                            <input type="datetime-local" name="exam_end" value="<?= $project['exam_end'] ? date('Y-m-d\TH:i', strtotime($project['exam_end'])) : '' ?>" 
                                   class="w-full h-12 px-4 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:bg-white transition-all">
                            <p class="text-xxs text-gray-400">หลังจากนี้จะทำข้อสอบไม่ได้ แม้ยังค้างอยู่กลางคัน</p>
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-3">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest">เกณฑ์ผ่าน (%)</label>
                            <input type="number" name="pass_score" value="<?= e((string) $project['pass_score']) ?>" step="0.01" min="0" max="100" 
                                   class="w-full h-12 px-4 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:bg-white transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest">จำกัดเวลา (นาที)</label>
                            <input type="number" name="time_limit_min" value="<?= (int) $project['time_limit_min'] ?>" min="0" 
                                   class="w-full h-12 px-4 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:bg-white transition-all">
                            <p class="text-xxs text-gray-400">0 = ไม่จำกัดเวลา</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest">ครั้งที่สอบได้สูงสุด</label>
                            <input type="number" name="max_attempts" value="<?= (int) $project['max_attempts'] ?>" min="1" 
                                   class="w-full h-12 px-4 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:bg-white transition-all">
                        </div>
                    </div>

                    <div class="border-t border-gray-50 pt-8 space-y-6">
                        <div class="flex items-center gap-2">
                            <i class="ti ti-settings text-gray-400"></i>
                            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide">ตัวเลือกการเข้าสอบเพิ่มเติม</h4>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="flex items-center justify-between p-4 rounded-2xl border border-gray-100 bg-gray-50/30 hover:border-primary-100 cursor-pointer transition-all group">
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-sm font-bold text-gray-700 group-hover:text-primary-600 transition-colors">Manual Override</span>
                                    <span class="text-[10px] text-gray-400 font-medium">ควบคุมสถานะด้วยตนเอง ไม่ใช้อัตโนมัติ</span>
                                </div>
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="manual_override" value="1" <?= $project['manual_override'] ? 'checked' : '' ?> class="sr-only peer">
                                    <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary-400"></div>
                                </div>
                            </label>

                            <label class="flex items-center justify-between p-4 rounded-2xl border border-gray-100 bg-gray-50/30 hover:border-primary-100 cursor-pointer transition-all group">
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-sm font-bold text-gray-700 group-hover:text-primary-600 transition-colors">แจ้งเตือนก่อนปิด</span>
                                    <span class="text-[10px] text-gray-400 font-medium">แสดงเวลาถอยหลัง (นาที)</span>
                                </div>
                                <input type="number" name="warning_before" value="<?= (int) $project['warning_before'] ?>" min="0" class="w-16 h-9 text-xs text-center border-gray-200 rounded-xl focus:ring-primary-400/10">
                            </label>

                            <label class="flex items-center justify-between p-4 rounded-2xl border border-gray-100 bg-gray-50/30 hover:border-primary-100 cursor-pointer transition-all group">
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-sm font-bold text-gray-700 group-hover:text-primary-600 transition-colors">Auto-submit</span>
                                    <span class="text-[10px] text-gray-400 font-medium">ส่งข้อสอบทันทีเมื่อหมดเวลา</span>
                                </div>
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="auto_submit_on_close" value="1" <?= $project['auto_submit_on_close'] ? 'checked' : '' ?> class="sr-only peer">
                                    <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary-400"></div>
                                </div>
                            </label>

                            <label class="flex items-center justify-between p-4 rounded-2xl border border-gray-100 bg-gray-50/30 hover:border-primary-100 cursor-pointer transition-all group">
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-sm font-bold text-gray-700 group-hover:text-primary-600 transition-colors">ล่วงหน้า</span>
                                    <span class="text-[10px] text-gray-400 font-medium">อนุญาต Login ก่อนเวลาเริ่ม</span>
                                </div>
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="allow_early_login" value="1" <?= $project['allow_early_login'] ? 'checked' : '' ?> class="sr-only peer">
                                    <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary-400"></div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pt-8 border-t border-gray-50 gap-4">
                        <div class="flex items-center gap-2 text-orange-600 bg-orange-50 px-4 py-2 rounded-xl text-xxs font-medium">
                            <i class="ti ti-alert-triangle text-base"></i>
                            <span>หากปิดการสอบขณะมีผู้ทำข้อสอบค้างอยู่ ระบบจะบันทึกผลสอบทันที</span>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center px-8 py-3 bg-primary-400 hover:bg-primary-500 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-orange-100">
                            <i class="fas fa-save mr-2"></i> บันทึกการตั้งค่า
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="space-y-6 fade-up fade-up-2">
        <!-- Quick Manual Override -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-6">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center">
                    <i class="ti ti-switch text-orange-400 text-lg"></i>
                </div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest">Manual Control</h3>
            </div>
            
            <div class="grid grid-cols-1 gap-3">
                <form method="post" action="<?= e(BASE_URL) ?>/admin/projects/force-status.php">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= (int) $project['id'] ?>">
                    <input type="hidden" name="status" value="active">
                    <button type="submit" class="w-full h-12 flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white text-sm font-bold rounded-xl transition-all shadow-sm">
                        <i class="ti ti-player-play text-lg"></i> เปิดสอบทันที
                    </button>
                </form>

                <form method="post" action="<?= e(BASE_URL) ?>/admin/projects/force-status.php">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= (int) $project['id'] ?>">
                    <input type="hidden" name="status" value="closed">
                    <button type="submit" class="w-full h-12 flex items-center justify-center gap-2 bg-white border border-red-100 text-red-500 hover:bg-red-50 text-sm font-bold rounded-xl transition-all shadow-sm">
                        <i class="ti ti-player-stop text-lg"></i> ปิดการสอบ
                    </button>
                </form>

                <button onclick="extendTimeModal()" class="w-full h-12 flex items-center justify-center gap-2 bg-gray-50 border border-gray-100 text-gray-600 hover:bg-gray-100 text-sm font-bold rounded-xl transition-all shadow-sm">
                    <i class="ti ti-clock-plus text-lg"></i> ขยายเวลา
                </button>
            </div>
        </div>

        <section class="bg-white rounded-2xl border border-gray-100 shadow-card p-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest border-b border-gray-50 pb-4 mb-5">โครงการสรุป</h3>
            <ul class="space-y-4">
                <li class="flex items-center justify-between">
                    <span class="text-xs font-medium text-gray-400">จำนวนข้อสอบ</span>
                    <span class="text-sm font-bold text-gray-800"><?= (int) $project['question_count_total'] ?> ข้อ</span>
                </li>
                <li class="flex items-center justify-between">
                    <span class="text-xs font-medium text-gray-400">ผู้มีสิทธิ์สอบ</span>
                    <span class="text-sm font-bold text-gray-800"><?= (int) $project['participant_count'] ?> คน</span>
                </li>
                <li class="flex items-center justify-between">
                    <span class="text-xs font-medium text-gray-400">สุ่มข้อสอบ</span>
                    <span class="text-sm font-bold"><?= $project['randomize_questions'] ? '<i class="fas fa-check text-green-500"></i>' : '<i class="fas fa-times text-gray-300"></i>' ?></span>
                </li>
                <li class="flex items-center justify-between">
                    <span class="text-xs font-medium text-gray-400">สุ่มตัวเลือก</span>
                    <span class="text-sm font-bold"><?= $project['randomize_choices'] ? '<i class="fas fa-check text-green-500"></i>' : '<i class="fas fa-times text-gray-300"></i>' ?></span>
                </li>
            </ul>
        </section>

        <section class="bg-red-50/50 rounded-2xl border border-red-100 p-6">
            <h3 class="text-xs font-bold text-red-800 uppercase tracking-widest mb-2"><i class="fas fa-triangle-exclamation mr-1"></i> Danger Zone</h3>
            <p class="text-[10px] text-red-600/70 mb-4 leading-relaxed">การลบโครงการจะทำลายข้อมูลทั้งหมดที่เกี่ยวข้องถาวร</p>
            <form method="post" action="<?= e(BASE_URL) ?>/admin/projects/delete.php" id="delete-form-<?= (int) $project['id'] ?>">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= (int) $project['id'] ?>">
                <button type="button" onclick="confirmDelete('ยืนยันลบโครงการ <?= e($project['name']) ?> หรือไม่?', () => document.getElementById('delete-form-<?= (int) $project['id'] ?>').submit())" class="w-full h-10 inline-flex items-center justify-center bg-white border border-red-200 text-red-600 hover:bg-red-600 hover:text-white text-xs font-bold rounded-xl transition-all">
                    ลบโครงการนี้
                </button>
            </form>
        </section>
    </div>
</div>

<script>
function extendTimeModal() {
    Swal.fire({
        title: 'ขยายเวลาปิดสอบ',
        text: 'ระบุจำนวนนาทีที่ต้องการขยายออกไป',
        input: 'number',
        inputAttributes: { min: 1, max: 1440, step: 1 },
        inputValue: 30,
        showCancelButton: true,
        confirmButtonText: 'ขยายเวลา',
        cancelButtonText: 'ยกเลิก',
        customClass: {
            popup: 'rounded-2xl font-sans',
            confirmButton: '!bg-primary-400 !px-6 !py-2.5 !rounded-xl !text-sm !font-bold',
            cancelButton: '!bg-white !text-gray-500 !px-6 !py-2.5 !rounded-xl !text-sm !font-bold'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            Swal.showLoading();
            $.ajax({
                url: '<?= e(BASE_URL) ?>/admin/projects/extend.php',
                type: 'POST',
                data: {
                    id: '<?= (int) $project['id'] ?>',
                    minutes: result.value,
                    csrf_token: window.CSRF_TOKEN
                },
                success: function(res) {
                    Swal.fire({
                        icon: res.success ? 'success' : 'error',
                        title: res.success ? 'สำเร็จ' : 'เกิดข้อผิดพลาด',
                        text: res.message || 'ดำเนินการเรียบร้อยแล้ว',
                        timer: res.success ? 1500 : null,
                        showConfirmButton: !res.success
                    }).then(() => {
                        if (res.success) location.reload();
                    });
                },
                error: function(xhr) {
                    let msg = 'ไม่สามารถขยายเวลาได้';
                    try {
                        const res = JSON.parse(xhr.responseText);
                        msg = res.message || msg;
                    } catch(e) {}
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: msg
                    });
                }
            });
        }
    });
}
</script>
