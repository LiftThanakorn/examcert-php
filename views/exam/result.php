<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-xl w-full">
        <div class="bg-white rounded-[40px] shadow-2xl overflow-hidden text-center">
            
            <!-- Result Header -->
            <div class="pt-12 pb-8 px-8 <?= $session['result'] === 'pass' ? 'bg-green-50' : 'bg-red-50' ?> transition-colors duration-500">
                <div class="w-24 h-24 mx-auto rounded-[32px] flex items-center justify-center shadow-lg mb-6 <?= $session['result'] === 'pass' ? 'bg-green-600 text-white' : 'bg-red-600 text-white' ?>">
                    <?php if ($session['result'] === 'pass'): ?>
                        <i class="fas fa-trophy text-4xl"></i>
                    <?php else: ?>
                        <i class="fas fa-rotate-right text-4xl"></i>
                    <?php endif; ?>
                </div>
                <h1 class="text-3xl font-bold <?= $session['result'] === 'pass' ? 'text-green-700' : 'text-red-700' ?>">
                    <?= $session['result'] === 'pass' ? 'ขอแสดงความยินดี!' : 'ไม่ผ่านเกณฑ์การสอบ' ?>
                </h1>
                <p class="<?= $session['result'] === 'pass' ? 'text-green-600' : 'text-red-600' ?> mt-2 opacity-80 font-medium">
                    <?= $session['result'] === 'pass' ? 'คุณผ่านการทดสอบตามเกณฑ์ที่กำหนด' : 'ท่านสามารถลองใหม่อีกครั้งได้ตามสิทธิ์ที่เหลือ' ?>
                </p>
            </div>

            <!-- Score Details -->
            <div class="p-8 md:p-12 space-y-10">
                
                <div class="space-y-4">
                    <div class="flex items-end justify-between">
                        <div class="text-left">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">คะแนนที่คุณได้</span>
                            <div class="text-5xl font-black text-gray-800 mt-1"><?= round((float) $session['percent'], 1) ?><span class="text-2xl text-gray-300 ml-1">%</span></div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">เกณฑ์ผ่าน</span>
                            <div class="text-xl font-bold text-gray-500 mt-1"><?= (float) $project['pass_score'] ?>%</div>
                        </div>
                    </div>
                    <div class="h-4 w-full bg-gray-50 rounded-full border border-gray-100 overflow-hidden shadow-inner">
                        <div class="h-full <?= $session['result'] === 'pass' ? 'bg-green-500' : 'bg-red-500' ?> rounded-full progress-bar-fill" style="width: <?= (float) $session['percent'] ?>%"></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-5 rounded-3xl border border-gray-100">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ทำคะแนนได้</div>
                        <div class="text-xl font-bold text-gray-800"><?= (float) $session['score'] ?> / <?= (float) $session['total_score'] ?></div>
                    </div>
                    <div class="bg-gray-50 p-5 rounded-3xl border border-gray-100">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">สถานะ</div>
                        <div class="text-xl font-bold <?= $session['result'] === 'pass' ? 'text-green-600' : 'text-red-600' ?>">
                            <?= $session['result'] === 'pass' ? 'ผ่าน' : 'ไม่ผ่าน' ?>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-3 pt-4">
                    <?php if ($session['result'] === 'pass'): ?>
                        <a href="<?= e(BASE_URL) ?>/public/verify.php?token=<?= e($certificate['verify_token'] ?? '') ?>" class="w-full h-16 bg-primary-400 hover:bg-primary-500 text-white font-bold rounded-2xl shadow-orange transition-all flex items-center justify-center gap-3 group">
                            <i class="fas fa-certificate text-xl"></i>
                            รับเกียรติบัตรของคุณ
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?= e(BASE_URL) ?>/public/exam.php?project=<?= e($project['code']) ?>" class="w-full h-16 bg-white border-2 border-gray-100 hover:border-gray-200 text-gray-600 font-bold rounded-2xl transition-all flex items-center justify-center">
                        กลับสู่หน้าหลัก
                    </a>
                </div>
            </div>

            <div class="pb-8 text-center px-8">
                <p class="text-gray-300 text-[10px] uppercase font-bold tracking-[0.2em]">ExamCert Standalone v1 Security Certified</p>
            </div>
        </div>
    </div>
</div>