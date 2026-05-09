<?php
$project = array_merge(projectDefaults(), $project ?? []);
$errors = $errors ?? [];
$action = $action ?? '';
?>
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 fade-up">
    <div>
        <h2 class="text-xl font-semibold text-gray-800"><?= e($pageTitle ?? 'จัดการโครงการ') ?></h2>
        <p class="text-sm text-gray-400 mt-0.5">ระบุรายละเอียดและกำหนดกฎเกณฑ์สำหรับโครงการสอบ</p>
    </div>
    <a href="<?= e(BASE_URL) ?>/admin/projects/" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-primary-400 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i> กลับหน้าโครงการทั้งหมด
    </a>
</div>

<?php if ($errors): ?>
    <div class="mb-6 rounded-2xl border border-red-100 bg-red-50/50 p-4 text-sm text-red-700 shadow-sm fade-up">
        <div class="flex items-center gap-2 font-bold mb-1">
            <i class="fas fa-circle-exclamation text-red-400"></i>
            <span>พบข้อผิดพลาด:</span>
        </div>
        <ul class="list-disc list-inside space-y-0.5 ml-1">
            <?php foreach ($errors as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= e($action) ?>" class="fade-up fade-up-1">
    <?= csrfField() ?>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Form Column -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- General Info -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-6 md:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center text-primary-400">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="font-bold text-gray-800">ข้อมูลทั่วไป</h3>
                </div>
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-2">ชื่อโครงการสอบ <span class="text-primary-400">*</span></label>
                        <input name="name" required value="<?= e($project['name']) ?>" 
                               class="w-full px-4 py-3 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-4 focus:ring-primary-400/10 transition-all"
                               placeholder="เช่น โครงการสอบมาตรฐานฝีมือแรงงาน ครั้งที่ 1/2567">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-2">รหัสโครงการ</label>
                            <input name="code" value="<?= e($project['code']) ?>" 
                                   class="w-full px-4 py-3 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-4 focus:ring-primary-400/10 transition-all"
                                   placeholder="เช่น PRJ-2567-001">
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-2">สถานะเริ่มต้น</label>
                            <select name="status" class="w-full px-4 py-3 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-4 focus:ring-primary-400/10 transition-all appearance-none">
                                <?php foreach (['draft' => 'ฉบับร่าง (Draft)', 'active' => 'เปิดใช้งานทันที (Active)', 'closed' => 'ปิดการใช้งาน (Closed)'] as $value => $label): ?>
                                    <option value="<?= e($value) ?>" <?= $project['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-2">รายละเอียดเพิ่มเติม</label>
                        <textarea name="description" rows="3" 
                                  class="w-full px-4 py-3 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-4 focus:ring-primary-400/10 transition-all"
                                  placeholder="คำอธิบายสั้นๆ เกี่ยวกับโครงการนี้..."><?= e($project['description']) ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                        <div>
                            <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-2">หน่วยงานผู้รับผิดชอบ</label>
                            <div class="relative">
                                <i class="fas fa-building absolute left-4 top-3.5 text-gray-300"></i>
                                <input name="organizer" value="<?= e($project['organizer']) ?>" 
                                       class="w-full pl-10 pr-4 py-3 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-4 focus:ring-primary-400/10 transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-2">สถานที่จัดสอบ</label>
                            <div class="relative">
                                <i class="fas fa-location-dot absolute left-4 top-3.5 text-gray-300"></i>
                                <input name="location" value="<?= e($project['location']) ?>" 
                                       class="w-full pl-10 pr-4 py-3 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-4 focus:ring-primary-400/10 transition-all">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-6 md:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3 class="font-bold text-gray-800">ช่วงเวลาดำเนินการ</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-2">วันเริ่มโครงการ</label>
                        <input type="date" name="start_date" value="<?= e($project['start_date']) ?>" 
                               class="w-full px-4 py-3 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-4 focus:ring-primary-400/10 transition-all">
                    </div>
                    <div>
                        <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-2">วันสิ้นสุดโครงการ</label>
                        <input type="date" name="end_date" value="<?= e($project['end_date']) ?>" 
                               class="w-full px-4 py-3 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-4 focus:ring-primary-400/10 transition-all">
                    </div>
                    <div class="pt-2">
                        <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-2">วัน-เวลา เริ่มทำข้อสอบได้</label>
                        <input type="datetime-local" name="exam_start" value="<?= e($project['exam_start'] ? str_replace(' ', 'T', substr($project['exam_start'], 0, 16)) : '') ?>" 
                               class="w-full px-4 py-3 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-4 focus:ring-primary-400/10 transition-all">
                    </div>
                    <div class="pt-2">
                        <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-2">วัน-เวลา สิ้นสุดการทำข้อสอบ</label>
                        <input type="datetime-local" name="exam_end" value="<?= e($project['exam_end'] ? str_replace(' ', 'T', substr($project['exam_end'], 0, 16)) : '') ?>" 
                               class="w-full px-4 py-3 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-4 focus:ring-primary-400/10 transition-all">
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar (Rules & Settings) -->
        <div class="space-y-6">
            
            <!-- Exam Rules -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-6 md:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center text-green-500">
                        <i class="fas fa-gavel"></i>
                    </div>
                    <h3 class="font-bold text-gray-800">เกณฑ์และกฎเกณฑ์</h3>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-2">เกณฑ์การสอบผ่าน (%)</label>
                        <div class="relative">
                            <input type="number" step="0.01" min="0" max="100" name="pass_score" value="<?= e((string) $project['pass_score']) ?>" 
                                   class="w-full px-4 py-3 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-4 focus:ring-primary-400/10 transition-all">
                            <span class="absolute right-4 top-3 text-gray-400 text-sm">%</span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-2">สอบได้ (ครั้ง)</label>
                            <input type="number" min="1" name="max_attempts" value="<?= e((string) $project['max_attempts']) ?>" 
                                   class="w-full px-4 py-3 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-4 focus:ring-primary-400/10 transition-all">
                        </div>
                        <div>
                            <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-2">เวลา (นาที)</label>
                            <input type="number" min="1" name="time_limit_min" value="<?= e((string) $project['time_limit_min']) ?>" 
                                   class="w-full px-4 py-3 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-4 focus:ring-primary-400/10 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-2">จำนวนข้อสอบต่อรอบ</label>
                        <input type="number" min="0" name="question_count" value="<?= e((string) $project['question_count']) ?>" 
                               class="w-full px-4 py-3 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-4 focus:ring-primary-400/10 transition-all"
                               placeholder="0 = ใช้ทั้งหมด">
                        <p class="text-[10px] text-gray-400 mt-1.5">หากกำหนดเป็น 0 ระบบจะดึงข้อสอบทั้งหมดในคลังมาใช้</p>
                    </div>

                    <div class="pt-4 space-y-3">
                        <label class="flex items-center justify-between p-3 rounded-xl border border-gray-50 bg-gray-50/30 cursor-pointer hover:bg-primary-50/30 transition-colors group">
                            <span class="text-xs font-medium text-gray-700 group-hover:text-primary-600 transition-colors">สุ่มลำดับข้อสอบ</span>
                            <input type="checkbox" name="randomize_questions" value="1" <?= $project['randomize_questions'] ? 'checked' : '' ?> class="w-4 h-4 rounded text-primary-400 focus:ring-primary-400/20">
                        </label>
                        <label class="flex items-center justify-between p-3 rounded-xl border border-gray-50 bg-gray-50/30 cursor-pointer hover:bg-primary-50/30 transition-colors group">
                            <span class="text-xs font-medium text-gray-700 group-hover:text-primary-600 transition-colors">สุ่มลำดับตัวเลือก</span>
                            <input type="checkbox" name="randomize_choices" value="1" <?= $project['randomize_choices'] ? 'checked' : '' ?> class="w-4 h-4 rounded text-primary-400 focus:ring-primary-400/20">
                        </label>
                        <label class="flex items-center justify-between p-3 rounded-xl border border-gray-50 bg-gray-50/30 cursor-pointer hover:bg-primary-50/30 transition-colors group">
                            <span class="text-xs font-medium text-gray-700 group-hover:text-primary-600 transition-colors">แสดงคะแนนทันที</span>
                            <input type="checkbox" name="show_result_immediately" value="1" <?= $project['show_result_immediately'] ? 'checked' : '' ?> class="w-4 h-4 rounded text-primary-400 focus:ring-primary-400/20">
                        </label>
                    </div>
                </div>
            </div>

            <!-- Templates -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-6 md:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center text-primary-400">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h3 class="font-bold text-gray-800">เกียรติบัตร</h3>
                </div>
                
                <div>
                    <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-2">เทมเพลตที่จะใช้</label>
                    <select name="cert_template_id" class="w-full px-4 py-3 text-sm bg-gray-50/50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-4 focus:ring-primary-400/10 transition-all appearance-none">
                        <option value="">-- ไม่ระบุ (ใช้ค่าเริ่มต้น) --</option>
                        <?php foreach ($templates as $tmpl): ?>
                            <option value="<?= (int) $tmpl['id'] ?>" <?= (int) ($project['cert_template_id'] ?? 0) === (int) $tmpl['id'] ? 'selected' : '' ?>><?= e($tmpl['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex flex-col gap-3 pt-2">
                <button type="submit" class="w-full py-4 bg-primary-400 hover:bg-primary-500 text-white font-bold rounded-2xl transition-all shadow-lg shadow-orange-100 flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> <?= !empty($project['id']) ? 'บันทึกการแก้ไข' : 'สร้างโครงการสอบ' ?>
                </button>
                <a href="<?= e(BASE_URL) ?>/admin/projects/" class="w-full py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-2xl transition-all text-center">
                    ยกเลิกและย้อนกลับ
                </a>
            </div>
        </div>
    </div>
</form>
