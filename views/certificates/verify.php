
<div class="max-w-xl w-full">
    <!-- Logo & Header -->
    <div class="text-center mb-10 fade-up">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-3xl shadow-soft mb-6 border border-orange-50">
            <i class="fas fa-certificate text-4xl text-primary-500"></i>
        </div>
        <h1 class="text-3xl font-black text-gray-900 font-outfit tracking-tight mb-2">ExamCert Verification</h1>
        <p class="text-sm text-gray-400 font-medium tracking-wide uppercase">Digital Achievement Verification System</p>
    </div>

    <!-- Main Card -->
    <main class="glass-card rounded-[3rem] overflow-hidden fade-up relative">
        <!-- Decorative Element -->
        <div class="absolute top-0 right-0 w-40 h-40 bg-primary-500/5 rounded-bl-[10rem] -mr-10 -mt-10"></div>

        <div class="p-10 md:p-14 relative z-10">
            <?php if (!$certificate): ?>
                <div class="text-center py-10">
                    <div class="w-24 h-24 bg-red-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-sm">
                        <i class="fas fa-search text-4xl text-red-400"></i>
                    </div>
                    <h2 class="text-3xl font-black text-gray-900 mb-4">ไม่พบข้อมูล</h2>
                    <p class="text-gray-400 leading-relaxed max-w-sm mx-auto mb-10">
                        ขออภัย ไม่พบเกียรติบัตรที่ระบุในระบบ กรุณาตรวจสอบลิงก์ หรือสแกน QR Code ใหม่อีกครั้ง
                    </p>
                    <a href="<?= e(BASE_URL) ?>" class="inline-flex items-center gap-3 px-10 py-4 bg-gray-900 text-white font-bold rounded-2xl hover:bg-black transition-all hover:-translate-y-1 shadow-lg active:scale-95 no-underline">
                        <i class="fas fa-home text-sm"></i>
                        กลับหน้าหลัก
                    </a>
                </div>
            <?php elseif ((int) $certificate['is_revoked'] === 1): ?>
                <div class="text-center py-10">
                    <div class="w-24 h-24 bg-orange-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-sm">
                        <i class="fas fa-ban text-4xl text-orange-400"></i>
                    </div>
                    <h2 class="text-3xl font-black text-gray-900 mb-4">สถานะ: ยกเลิก</h2>
                    <p class="text-gray-400 leading-relaxed max-w-sm mx-auto mb-8">
                        ใบเกียรติบัตรฉบับนี้ถูกยกเลิกโดยผู้ดูแลระบบและไม่สามารถนำไปใช้อ้างอิงได้
                    </p>
                    <div class="p-6 bg-orange-50/50 rounded-3xl border border-orange-100 text-left">
                        <span class="text-[10px] font-black text-orange-500 uppercase tracking-widest block mb-2">เหตุผลการยกเลิก</span>
                        <p class="text-sm text-gray-700 font-medium italic">"<?= e($certificate['revoke_reason'] ?: 'ไม่ระบุเหตุผล') ?>"</p>
                    </div>
                </div>
            <?php else: ?>
                <?php $fullName = ($certificate['title'] ? $certificate['title'] . ' ' : '') . $certificate['first_name'] . ' ' . $certificate['last_name']; ?>
                
                <!-- Success Header -->
                <div class="flex flex-col items-center mb-12">
                    <div class="inline-flex items-center gap-3 px-6 py-2 bg-green-50 text-green-600 rounded-full border border-green-100 mb-8 shadow-sm">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-xs font-black tracking-widest uppercase">Verified Success</span>
                    </div>
                    
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.4em] mb-6">Official Achievement</p>
                    <h3 class="text-4xl font-black text-gray-900 text-center leading-[1.2]"><?= e($fullName) ?></h3>
                </div>

                <div class="space-y-8">
                    <div class="h-px bg-gradient-to-r from-transparent via-gray-100 to-transparent"></div>
                    
                    <div class="grid gap-8">
                        <div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-3">โครงการ / หลักสูตร</span>
                            <p class="text-xl font-bold text-gray-800 leading-relaxed"><?= e($certificate['project_name']) ?></p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-8 pt-4">
                            <div class="p-6 bg-gray-50/50 rounded-3xl border border-gray-100">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">เลขที่ใบประกาศ</span>
                                <p class="text-lg font-black text-gray-900 font-outfit"><?= e($certificate['cert_number']) ?></p>
                            </div>
                            <div class="p-6 bg-gray-50/50 rounded-3xl border border-gray-100 text-right">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">วันที่ออกใบประกาศ</span>
                                <p class="text-lg font-black text-gray-900 font-outfit"><?= date('d/m/Y', strtotime($certificate['issued_date'])) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 p-8 bg-primary-50 rounded-[2.5rem] border border-primary-100 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-primary-500 shadow-sm border border-primary-100">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-primary-600 uppercase tracking-widest mb-1">สถานะการสอบ</p>
                                <p class="text-sm font-bold text-gray-900">ผ่านการทดสอบออนไลน์</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-primary-400 uppercase tracking-widest mb-1">คะแนนสอบ</p>
                            <p class="text-lg font-black text-primary-600 font-outfit"><?= e((string) $certificate['percent']) ?><span class="text-xs ml-0.5">%</span></p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-6">
                        <a href="<?= e(BASE_URL) ?>/public/download-cert.php?token=<?= e($certificate['verify_token']) ?>" 
                           class="inline-flex items-center justify-center gap-3 w-full h-16 bg-gray-900 hover:bg-black text-white font-black rounded-3xl transition-all shadow-xl active:scale-95 no-underline">
                            <i class="fas fa-file-pdf text-xl"></i>
                            ดาวน์โหลดใบประกาศนียบัตร (PDF)
                        </a>
                    </div>
                </div>

                <!-- Footer Info -->
                <div class="mt-14 pt-8 border-t border-gray-100 flex flex-col items-center gap-6">
                    <div class="text-center">
                        <p class="text-[9px] font-bold text-gray-300 uppercase tracking-[0.2em] mb-3">Verification Token (Blockchain Signature)</p>
                        <code class="text-[10px] font-mono bg-gray-50 text-gray-400 px-4 py-2 rounded-xl border border-gray-100 select-all"><?= e($certificate['verify_token']) ?></code>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Bottom Branding -->
        <div class="bg-gray-50/50 py-6 text-center border-t border-gray-100">
            <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.3em]">ExamCert Digital Verification System v1.0</p>
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-12 text-center fade-up">
        <div class="flex items-center justify-center gap-4 mb-4 text-gray-300">
            <i class="fas fa-shield-halved text-sm"></i>
            <div class="h-4 w-px bg-gray-200"></div>
            <span class="text-[10px] font-bold uppercase tracking-widest">Secured & Verified Achievement</span>
        </div>
        <p class="text-[10px] text-gray-400 font-medium">Roi Et Rajabhat University | 2026</p>
    </footer>
</div>

<style>
    .glass-card {
        @apply bg-white/90 backdrop-blur-2xl border border-white/50 shadow-premium;
    }
</style>
