<?php
$question = array_merge(questionDefaults(), $question ?? []);
$projectId = (int) ($projectId ?? $question['project_id']);
?>



<div class="mb-6 fade-up">
    <a href="<?= e(BASE_URL) ?>/admin/questions/?project_id=<?= $projectId ?>" class="inline-flex items-center text-xxs font-bold text-gray-400 hover:text-primary-400 uppercase tracking-widest transition-all mb-2">
        <i class="fas fa-arrow-left mr-1.5"></i> กลับคลังข้อสอบ
    </a>
    <h2 class="text-2xl font-black text-gray-800 font-outfit tracking-tight"><?= e($pageTitle ?? 'จัดการข้อสอบ') ?></h2>
    <p class="text-sm text-gray-400 mt-0.5"><?= e($project['name']) ?></p>
</div>

<form method="post" action="<?= e($action) ?>" class="bg-white rounded-3xl border border-gray-100 shadow-card overflow-hidden fade-up fade-up-1">
    <?= csrfField() ?>
    <div class="p-8 md:p-10 space-y-10">
        
        <!-- Question Text -->
        <div>
            <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-3">โจทย์ข้อสอบ <span class="text-primary-400">*</span></label>
            <textarea name="question_text" required rows="3" class="w-full px-5 py-4 text-sm bg-gray-50/30 border border-gray-200 rounded-2xl focus:outline-none focus:border-primary-400 focus:ring-[4px] focus:ring-orange-100 transition-all outline-none" placeholder="ป้อนคำถามที่นี่..."><?= e($question['question_text']) ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-3">ประเภทข้อสอบ</label>
                <div class="relative">
                    <select name="type" class="w-full h-12 px-5 text-sm bg-gray-50/30 border border-gray-200 rounded-2xl focus:outline-none focus:border-primary-400 focus:ring-[4px] focus:ring-orange-100 transition-all appearance-none cursor-pointer outline-none">
                        <option value="multiple_choice" <?= $question['type'] === 'multiple_choice' ? 'selected' : '' ?>>ปรนัย (4 ตัวเลือก)</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
                </div>
            </div>
            <div>
                <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-3">ระดับความยาก</label>
                <div class="relative">
                    <select name="difficulty" class="w-full h-12 px-5 text-sm bg-gray-50/30 border border-gray-200 rounded-2xl focus:outline-none focus:border-primary-400 focus:ring-[4px] focus:ring-orange-100 transition-all appearance-none cursor-pointer outline-none">
                        <option value="easy" <?= $question['difficulty'] === 'easy' ? 'selected' : '' ?>>ง่าย</option>
                        <option value="medium" <?= $question['difficulty'] === 'medium' ? 'selected' : '' ?>>ปานกลาง</option>
                        <option value="hard" <?= $question['difficulty'] === 'hard' ? 'selected' : '' ?>>ยาก</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
                </div>
            </div>
            <div>
                <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-3">น้ำหนักคะแนน</label>
                <input type="number" name="score_weight" step="0.1" value="<?= e((string)($question['score_weight'] ?: 1.0)) ?>" class="w-full h-12 px-5 text-sm bg-gray-50/30 border border-gray-200 rounded-2xl focus:outline-none focus:border-primary-400 focus:ring-[4px] focus:ring-orange-100 transition-all outline-none">
            </div>
        </div>

        <div class="pt-2">
            <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-4">ตัวเลือกและกำหนดคำตอบที่ถูกต้อง</label>
            <div class="grid gap-4">
                <?php 
                $options = ['a', 'b', 'c', 'd'];
                foreach ($options as $key): 
                    $field = "choice_$key";
                    $isCorrect = strtolower((string)($question['correct_answer'] ?? '')) === $key;
                ?>
                    <div class="flex items-center gap-4 p-4 rounded-2xl border <?= $isCorrect ? 'bg-orange-50/50 border-primary-200 ring-4 ring-orange-100/50' : 'bg-gray-50/20 border-gray-100 hover:border-gray-200' ?> transition-all group relative">
                        <label class="flex items-center justify-center w-7 h-7 rounded-full border-2 cursor-pointer transition-all <?= $isCorrect ? 'border-primary-400 bg-primary-400 text-white' : 'border-gray-300 bg-white hover:border-primary-300' ?> shadow-sm">
                            <input type="radio" name="correct_answer" value="<?= $key ?>" <?= $isCorrect ? 'checked' : '' ?> class="hidden" onchange="updateCorrectUI(this)">
                            <i class="fas fa-check text-[10px] <?= $isCorrect ? '' : 'opacity-0' ?>"></i>
                        </label>
                        <div class="flex-1">
                            <input name="choice_<?= $key ?>" value="<?= e($question[$field] ?? '') ?>" placeholder="ป้อนข้อความตัวเลือก <?= strtoupper($key) ?>" class="w-full bg-transparent border-none focus:ring-0 text-sm font-semibold text-gray-700 placeholder:text-gray-300 outline-none" required>
                        </div>
                        <?php if ($isCorrect): ?>
                            <span class="text-[10px] font-black text-primary-400 uppercase tracking-widest px-3 py-1 bg-white rounded-full border border-orange-100 shadow-sm">เฉลย</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Explanation -->
        <div class="pt-2">
            <label class="block text-xxs font-bold text-gray-400 uppercase tracking-widest mb-3">คำอธิบายเฉลย (Optional)</label>
            <textarea name="explanation" rows="2" class="w-full px-5 py-4 text-sm bg-gray-50/30 border border-gray-200 rounded-2xl focus:outline-none focus:border-primary-400 focus:ring-[4px] focus:ring-orange-100 transition-all outline-none" placeholder="คำอธิบายเพิ่มเติมเกี่ยวกับข้อนี้..."><?= e($question['explanation'] ?? '') ?></textarea>
        </div>

    </div>
    <div class="px-8 md:px-10 py-6 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-400 font-medium italic">ตรวจสอบความถูกต้องก่อนกดบันทึก</p>
        <div class="flex items-center gap-3">
            <a href="<?= e(BASE_URL) ?>/admin/questions/?project_id=<?= $projectId ?>" class="px-6 py-2.5 text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors">ยกเลิก</a>
            <button type="submit" class="inline-flex items-center px-8 py-3 bg-primary-400 hover:bg-primary-500 rounded-2xl text-sm font-black text-white transition-all shadow-lg shadow-orange-200/50 active:scale-95">
                <i class="fas fa-save mr-2"></i> บันทึกข้อมูล
            </button>
        </div>
    </div>
</form>

<script>
function updateCorrectUI(radio) {
    const form = radio.closest('form');
    const containers = form.querySelectorAll('.p-4');
    
    // Reset all
    containers.forEach(el => {
        el.classList.remove('bg-orange-50/50', 'border-primary-200', 'ring-4', 'ring-orange-100/50');
        el.classList.add('bg-gray-50/20', 'border-gray-100');
        
        // Hide badge if exists
        const badge = el.querySelector('span.text-primary-400');
        if(badge) badge.remove();
        
        // Uncheck icon
        const icon = el.querySelector('i.fa-check');
        icon.classList.add('opacity-0');
        el.querySelector('label').classList.remove('border-primary-400', 'bg-primary-400', 'text-white');
        el.querySelector('label').classList.add('border-gray-300', 'bg-white');
    });

    // Set active
    const active = radio.closest('.p-4');
    active.classList.add('bg-orange-50/50', 'border-primary-200', 'ring-4', 'ring-orange-100/50');
    active.classList.remove('bg-gray-50/20', 'border-gray-100');
    
    const icon = active.querySelector('i.fa-check');
    icon.classList.remove('opacity-0');
    active.querySelector('label').classList.add('border-primary-400', 'bg-primary-400', 'text-white');
    active.querySelector('label').classList.remove('border-gray-300', 'bg-white');
    
    // Add badge
    const badge = document.createElement('span');
    badge.className = 'text-[10px] font-black text-primary-400 uppercase tracking-widest px-3 py-1 bg-white rounded-full border border-orange-100 shadow-sm';
    badge.textContent = 'เฉลย';
    active.appendChild(badge);
}
</script>
