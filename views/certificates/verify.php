<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            400: '#E87722',
                            500: '#C4601A'
                        }
                    },
                    fontFamily: {
                        outfit: ['Outfit', 'sans-serif'],
                        sarabun: ['Sarabun', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .fade-up { animation: fadeUp 0.6s ease-out forwards; opacity: 0; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .bg-pattern {
            background-color: #F9F8F6;
            background-image: radial-gradient(#E87722 0.5px, transparent 0.5px);
            background-size: 24px 24px;
            background-opacity: 0.05;
        }
    </style>
</head>
<body class="bg-pattern text-gray-900 min-h-screen flex flex-col items-center justify-center p-4">
    
    <div class="max-w-md w-full">
        <div class="text-center mb-8 fade-up">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-[24px] shadow-sm mb-5">
                <i class="fas fa-shield-check text-4xl text-primary-400"></i>
            </div>
            <h1 class="text-2xl font-black text-gray-800 font-outfit tracking-tight">ExamCert Verification</h1>
            <p class="text-sm text-gray-400 mt-1 font-medium">ระบบตรวจสอบความถูกต้องของใบเกียรติบัตร</p>
        </div>

        <section class="bg-white rounded-[40px] shadow-2xl shadow-orange/10 overflow-hidden border border-gray-100/50 p-10 text-center relative fade-up" style="animation-delay: 0.1s;">
            <?php if (!$certificate): ?>
                <div class="py-12">
                    <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-8">
                        <i class="fas fa-circle-xmark text-4xl text-red-400"></i>
                    </div>
                    <h2 class="text-2xl font-black text-gray-800 mb-3">ไม่พบข้อมูล</h2>
                    <p class="text-gray-400 text-sm leading-relaxed px-6">ขออภัย ไม่พบใบเกียรติบัตรที่ระบุในระบบ กรุณาตรวจสอบลิงก์หรือ QR Code อีกครั้ง</p>
                    <a href="<?= e(BASE_URL) ?>" class="mt-10 inline-flex items-center justify-center px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-2xl transition-all text-sm">
                        <i class="fas fa-home mr-2"></i> กลับหน้าหลัก
                    </a>
                </div>
            <?php elseif ((int) $certificate['is_revoked'] === 1): ?>
                <div class="py-12">
                    <div class="w-24 h-24 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-8">
                        <i class="fas fa-triangle-exclamation text-4xl text-orange-400"></i>
                    </div>
                    <h2 class="text-2xl font-black text-gray-800 mb-3">ถูกยกเลิกแล้ว</h2>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6 px-6">ใบเกียรติบัตรฉบับนี้ถูกยกเลิกโดยผู้ดูแลระบบและไม่สามารถนำไปอ้างอิงได้</p>
                    <div class="p-5 bg-gray-50 rounded-2xl text-xs text-gray-500 text-left border border-gray-100">
                        <p class="font-bold text-gray-700 mb-1.5 flex items-center gap-2">
                            <i class="fas fa-info-circle text-orange-400"></i> เหตุผลการยกเลิก:
                        </p>
                        <p class="leading-relaxed"><?= e($certificate['revoke_reason'] ?: 'ไม่ระบุเหตุผลการยกเลิก') ?></p>
                    </div>
                </div>
            <?php else: ?>
                <?php $name = trim(($certificate['title'] ? $certificate['title'] . ' ' : '') . $certificate['first_name'] . ' ' . $certificate['last_name']); ?>
                
                <!-- Status Badge -->
                <div class="flex justify-center mb-8">
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-black bg-green-50 text-green-600 border border-green-100 ring-4 ring-green-500/5">
                        <i class="fas fa-check-circle"></i> VERIFIED SUCCESS
                    </span>
                </div>

                <div class="py-2">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.3em] mb-4">Official Certification</p>
                    <h2 class="text-xl font-black text-gray-800 mb-2 font-outfit"><?= e($certificate['cert_number']) ?></h2>
                    <div class="h-1.5 w-16 bg-primary-400 mx-auto rounded-full mb-10 shadow-sm shadow-orange-400/20"></div>

                    <div class="space-y-8">
                        <div>
                            <p class="text-xs text-gray-400 font-medium mb-2">ใบเกียรติบัตรนี้ขอมอบให้แก่</p>
                            <h3 class="text-3xl font-black text-gray-900 leading-tight Sarabun-ExtraBold"><?= e($name) ?></h3>
                        </div>

                        <div class="p-8 bg-gray-50/50 rounded-[32px] space-y-6 border border-gray-100">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">โครงการ / หลักสูตร</p>
                                <p class="text-base font-bold text-gray-800 leading-relaxed"><?= e($certificate['project_name']) ?></p>
                            </div>
                            <div class="grid grid-cols-2 gap-6 border-t border-gray-200/50 pt-6">
                                <div class="text-left">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1.5">ผลการสอบ</p>
                                    <p class="text-lg font-black text-primary-400 font-outfit">PASS <span class="text-xs font-bold text-gray-400 ml-1">(<?= e((string) $certificate['percent']) ?>%)</span></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1.5">วันที่ออกให้</p>
                                    <p class="text-lg font-black text-gray-800 font-outfit"><?= date('d/m/Y', strtotime($certificate['issued_date'])) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 pt-6 border-t border-gray-50 flex flex-col items-center gap-3">
                        <p class="text-[9px] text-gray-300 font-mono tracking-tighter bg-gray-50 px-3 py-1 rounded-md">TOKEN: <?= e($certificate['verify_token']) ?></p>
                        <p class="text-[10px] text-gray-400 font-bold">ออกโดยระบบ ExamCert Management System</p>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <div class="mt-12 flex items-center justify-center gap-6 fade-up" style="animation-delay: 0.2s;">
            <img src="<?= e(BASE_URL) ?>/assets/img/logo-placeholder.png" class="h-6 opacity-20 grayscale" alt="Organizer">
            <div class="w-px h-4 bg-gray-200"></div>
            <p class="text-[10px] text-gray-300 font-black uppercase tracking-widest">Secured by Blockchain Logic</p>
        </div>
    </div>

</body>
</html>
