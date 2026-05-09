<div class="fixed top-0 left-0 right-0 h-16 bg-white border-b border-gray-100 flex items-center px-4 md:px-8 z-50">
    <div class="flex items-center gap-4">
        <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center">
            <i class="fas fa-graduation-cap text-primary-400"></i>
        </div>
        <div>
            <h1 class="text-sm font-bold text-gray-800 line-clamp-1"><?= e($project['name']) ?></h1>
            <p class="text-[10px] text-gray-400 uppercase tracking-widest"><?= e($participant['first_name'] . ' ' . $participant['last_name']) ?></p>
        </div>
    </div>
    
    <div class="ml-auto flex items-center gap-4 md:gap-8">
        <div class="flex flex-col items-end">
            <span class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">เวลาคงเหลือ</span>
            <span id="timer" class="text-xl md:text-2xl font-mono font-bold text-gray-800 tabular-nums">--:--</span>
        </div>
        <button onclick="confirmSubmit()" class="hidden md:flex h-11 px-6 bg-primary-400 hover:bg-primary-500 text-white text-sm font-bold rounded-xl shadow-orange transition-all items-center gap-2">
            <i class="fas fa-paper-plane text-xs"></i>
            ส่งข้อสอบ
        </button>
    </div>
</div>

<div class="pt-20 pb-24 min-h-screen bg-[#F9F8F6]">
    <div class="max-w-4xl mx-auto px-4">
        
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between text-xs font-bold text-gray-400 uppercase mb-2">
                <span>ความคืบหน้า</span>
                <span id="progress-text">0/0 ข้อ (0%)</span>
            </div>
            <div class="h-2 w-full bg-white rounded-full border border-gray-100 overflow-hidden shadow-sm">
                <div id="progress-bar" class="h-full bg-primary-400 progress-bar-fill" style="width: 0%"></div>
            </div>
        </div>

        <form id="exam-form" method="post" action="<?= e(BASE_URL) ?>/public/take-exam.php?session_id=<?= (int) $session['id'] ?>">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="submit">
            
            <div id="questions-container" class="space-y-6">
                <?php foreach ($questions as $index => $q): ?>
                <div class="question-slide <?= $index === 0 ? '' : 'hidden' ?>" data-index="<?= $index ?>" id="q-<?= (int) $q['id'] ?>">
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-card p-6 md:p-10">
                        <div class="flex items-start gap-4 mb-8">
                            <span class="flex-shrink-0 w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-sm font-bold text-gray-400">
                                <?= $index + 1 ?>
                            </span>
                            <h2 class="text-lg md:text-xl font-semibold text-gray-800 leading-relaxed"><?= e($q['question_text']) ?></h2>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <?php 
                            $choices = json_decode($q['choices'], true);
                            if ($q['type'] === 'fill_blank'): 
                            ?>
                                <input type="text" name="answers[<?= (int) $q['id'] ?>]" placeholder="พิมพ์คำตอบของคุณที่นี่..." class="w-full h-14 px-6 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-primary-400 focus:ring-[4px] focus:ring-primary-400/10 transition-all outline-none text-base">
                            <?php else: ?>
                                <?php foreach ($choices as $choice): ?>
                                <div class="exam-option">
                                    <input type="radio" id="choice-<?= (int) $q['id'] ?>-<?= e($choice['key']) ?>" name="answers[<?= (int) $q['id'] ?>]" value="<?= e($choice['key']) ?>" class="hidden">
                                    <label for="choice-<?= (int) $q['id'] ?>-<?= e($choice['key']) ?>" class="flex items-center p-5 rounded-2xl border border-gray-100 bg-white hover:bg-gray-50 hover:border-gray-200 cursor-pointer transition-all">
                                        <span class="option-marker w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-xs font-bold text-gray-400 mr-4 transition-all">
                                            <?= strtoupper($choice['key']) ?>
                                        </span>
                                        <span class="text-gray-700 font-medium"><?= e($choice['text']) ?></span>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </form>
    </div>
</div>

<!-- Bottom Navigation -->
<div class="fixed bottom-0 left-0 right-0 h-20 bg-white border-t border-gray-100 flex items-center justify-center px-4 z-50">
    <div class="max-w-4xl w-full flex items-center justify-between">
        <button id="prev-btn" onclick="prevQuestion()" class="flex h-12 px-6 items-center gap-2 text-sm font-bold text-gray-400 hover:text-gray-600 disabled:opacity-30">
            <i class="fas fa-arrow-left text-xs"></i>
            ก่อนหน้า
        </button>
        
        <div class="flex items-center gap-2">
            <span id="current-page-text" class="text-sm font-bold text-gray-800">1 / <?= count($questions) ?></span>
        </div>

        <button id="next-btn" onclick="nextQuestion()" class="flex h-12 px-6 items-center gap-2 text-sm font-bold text-primary-400 hover:text-primary-500 disabled:opacity-30">
            ถัดไป
            <i class="fas fa-arrow-right text-xs"></i>
        </button>
    </div>
</div>

<script src="<?= e(BASE_URL) ?>/assets/js/exam.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Exam Engine
        initExam(<?= (int) $secondsLeft ?>, <?= (int) $session['id'] ?>);
        
        // Show first question
        showQuestion(0);
    });
</script>
