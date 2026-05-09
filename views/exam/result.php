
<div class="w-full max-w-2xl">
    <div id="result-content" class="card-premium overflow-hidden fade-up">
        <!-- Header Background based on result -->
        <?php if ($session['result'] === 'pass'): ?>
            <div class="bg-gradient-to-br from-green-500 to-green-600 p-12 text-center text-white relative">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-bl-[8rem]"></div>
                <div class="w-24 h-24 bg-white/20 backdrop-blur-lg rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-xl">
                    <i class="fas fa-award text-4xl"></i>
                </div>
                <h1 class="text-4xl font-extrabold tracking-tight mb-2">ยินดีด้วย คุณสอบผ่าน!</h1>
                <p class="text-white/80 font-medium">คุณผ่านการทดสอบตามเกณฑ์ที่มหาวิทยาลัยกำหนด</p>
            </div>
        <?php else: ?>
            <div class="bg-gradient-to-br from-red-500 to-red-600 p-12 text-center text-white relative">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-bl-[8rem]"></div>
                <div class="w-24 h-24 bg-white/20 backdrop-blur-lg rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-xl">
                    <i class="fas fa-redo-alt text-4xl"></i>
                </div>
                <h1 class="text-4xl font-extrabold tracking-tight mb-2">ไม่ผ่านเกณฑ์การสอบ</h1>
                <p class="text-white/80 font-medium">อย่าเพิ่งท้อ! ท่านสามารถทบทวนและลองใหม่อีกครั้ง</p>
            </div>
        <?php endif; ?>

        <div class="p-10 space-y-10 text-center">
            <!-- Score Circle/Bar -->
            <div class="flex flex-col items-center gap-4">
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">คะแนนที่ทำได้</div>
                <div class="flex items-baseline gap-1">
                    <span class="text-7xl font-black text-gray-900"><?= round((float) $session['percent'], 0) ?></span>
                    <span class="text-2xl font-bold text-gray-300">%</span>
                </div>
                <div class="w-full max-w-sm h-3 bg-gray-100 rounded-full overflow-hidden shadow-inner">
                    <div class="h-full <?= $session['result'] === 'pass' ? 'bg-green-500' : 'bg-red-500' ?>" style="width: <?= (float) $session['percent'] ?>%"></div>
                </div>
                <div class="flex justify-between w-full max-w-sm text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">
                    <span>0%</span>
                    <span>เกณฑ์ผ่าน: <?= (float) $project['pass_score'] ?>%</span>
                    <span>100%</span>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-6">
                <div class="p-6 bg-gray-50 rounded-[2rem] border border-gray-100 flex flex-col items-center gap-2">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">คะแนนดิบ</span>
                    <span class="text-2xl font-bold text-gray-900"><?= (float) $session['score'] ?> / <?= (float) $session['total_score'] ?></span>
                </div>
                <div class="p-6 bg-gray-50 rounded-[2rem] border border-gray-100 flex flex-col items-center gap-2">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">สถานะ</span>
                    <span class="text-2xl font-bold <?= $session['result'] === 'pass' ? 'text-green-600' : 'text-red-600' ?>">
                        <?= $session['result'] === 'pass' ? 'ผ่านการสอบ' : 'ไม่ผ่านเกณฑ์' ?>
                    </span>
                </div>
            </div>

            <!-- Actions (Not included in PDF) -->
            <div class="space-y-4 pt-4 no-print">
                <?php if ($session['result'] === 'pass'): ?>
                    <button id="btn-download" onclick="downloadResultPDF()" class="inline-flex items-center justify-center gap-3 w-full h-16 bg-primary-400 hover:bg-primary-500 text-white font-black rounded-[2rem] transition-all shadow-lg active:scale-95 border-none cursor-pointer">
                        <i class="fas fa-file-pdf text-lg"></i> ดาวน์โหลดผลสอบ (PDF)
                    </button>
                    <a href="<?= e(BASE_URL) ?>/public/verify.php?token=<?= e($certificate['verify_token'] ?? '') ?>" class="w-full h-14 bg-white border-2 border-primary-100 text-primary-500 font-bold rounded-[2rem] hover:bg-primary-50 transition-all flex items-center justify-center gap-2 no-underline">
                        <i class="fas fa-certificate"></i> ดูใบเกียรติบัตรออนไลน์
                    </a>
                <?php else: ?>
                    <a href="<?= e(BASE_URL) ?>/public/exam.php?project=<?= e($project['code']) ?>" class="btn-premium bg-gray-900 hover:bg-black">
                        <i class="fas fa-sync-alt"></i> พยายามอีกครั้ง
                    </a>
                <?php endif; ?>
                
                <a href="<?= e(BASE_URL) ?>/" class="w-full h-14 bg-white border-2 border-gray-100 hover:border-gray-300 text-gray-500 font-bold rounded-[2rem] transition-all flex items-center justify-center gap-2 no-underline">
                    <i class="fas fa-home"></i> กลับหน้าหลัก
                </a>
            </div>
        </div>

        <div class="bg-gray-50/50 py-6 text-center border-t border-gray-50">
            <p class="text-[10px] font-bold text-gray-300 uppercase tracking-[0.2em]">ExamCert Digital Result Verification System</p>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function downloadResultPDF() {
        const element = document.getElementById('result-content');
        const actions = element.querySelector('.no-print');
        const btn = document.getElementById('btn-download');
        
        // Hide actions before capture
        actions.style.display = 'none';
        
        const opt = {
            margin:       0.5,
            filename:     'exam-result-<?= e($session['id']) ?>.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(element).save().then(() => {
            actions.style.display = 'block';
        });
    }
</script>

<style>
    .card-premium {
        @apply bg-white rounded-[2.5rem] border border-gray-100 shadow-soft;
    }
    .btn-premium {
        @apply inline-flex items-center justify-center gap-3 w-full h-16 bg-primary-400 hover:bg-primary-500 text-white font-black rounded-[2rem] transition-all shadow-lg shadow-primary-500/20 active:scale-95 no-underline;
    }
</style>