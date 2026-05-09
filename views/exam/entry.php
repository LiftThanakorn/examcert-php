<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-3xl shadow-orange mb-6">
                <i class="fas fa-graduation-cap text-4xl text-primary-400"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">เข้าสู่ระบบการสอบ</h1>
            <p class="text-white/70">กรุณาระบุข้อมูลส่วนตัวตามที่ลงทะเบียนไว้</p>
        </div>

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden p-8">
            <?php if (!empty($error)): ?>
                <div class="mb-6 p-4 rounded-2xl bg-red-50 text-red-600 text-sm font-medium border border-red-100 flex items-center gap-3">
                    <i class="fas fa-circle-exclamation text-lg"></i>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= e(BASE_URL) ?>/public/exam.php?project=<?= e($projectCode) ?>" class="space-y-5">
                <?= csrfField() ?>
                
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">คำนำหน้า</label>
                    <input type="text" name="title" placeholder="นาย / นาง / นางสาว" class="w-full h-12 px-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-primary-400 focus:ring-[4px] focus:ring-primary-400/10 transition-all outline-none text-sm" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">ชื่อ</label>
                        <input type="text" name="first_name" placeholder="ชื่อจริง" class="w-full h-12 px-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-primary-400 focus:ring-[4px] focus:ring-primary-400/10 transition-all outline-none text-sm" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">นามสกุล</label>
                        <input type="text" name="last_name" placeholder="นามสกุล" class="w-full h-12 px-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-primary-400 focus:ring-[4px] focus:ring-primary-400/10 transition-all outline-none text-sm" required>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">รหัสผ่านเข้าสอบ (Token)</label>
                    <input type="text" name="access_token" placeholder="ระบุรหัสผ่านที่ได้รับจากผู้ดูแล" class="w-full h-12 px-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-primary-400 focus:ring-[4px] focus:ring-primary-400/10 transition-all outline-none text-sm" required>
                </div>

                <input type="hidden" name="project_code" value="<?= e($projectCode) ?>">

                <button type="submit" class="w-full h-14 mt-4 bg-primary-400 hover:bg-primary-500 text-white font-bold rounded-2xl shadow-orange transition-all flex items-center justify-center gap-3 group">
                    ยืนยันตัวตนเพื่อเข้าสอบ
                    <i class="fas fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>
        </div>

        <div class="text-center mt-8">
            <p class="text-white/70 text-sm">ระบบสอบออนไลน์พร้อมออกใบเกียรติบัตร สำหรับบุคลากรมหาวิทยาลัยราชภัฏร้อยเอ็ด</p>
            <p class="text-white/40 text-xs mt-1">พัฒนาระบบโดย นายธนากร อินทพันธ์</p>
        </div>
    </div>
</div>