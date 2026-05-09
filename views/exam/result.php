<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สรุปผลการสอบ | <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Thai:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'Noto Sans Thai', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#FFF3EB', 100: '#FFE4D1', 200: '#FFC8A3', 300: '#FFA56E',
                            400: '#FF813A', 500: '#E87722', 600: '#C76118', 700: '#A34D10',
                        },
                        danger: '#EF4444',
                    }
                }
            }
        }
    </script>
    
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            background-color: #F8FAFC !important;
            background-image: 
                radial-gradient(at 0% 0%, hsla(25,100%,93%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(25,100%,93%,1) 0, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-premium {
            background-color: white !important;
            border-radius: 2.5rem;
            border: 1px solid #F1F5F9;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
            width: 100%;
            max-width: 640px;
            overflow: hidden;
        }
        /* Polyfill for missing tailwind classes to prevent syntax errors */
        .shadow-soft { box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); }
        .shadow-premium { box-shadow: 0 20px 50px -12px rgba(0, 0, 0, 0.1); }
        .btn-premium {
            background-color: #E87722;
            color: white;
            font-weight: 800;
            border-radius: 1.25rem;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            transition: all 0.3s;
            width: 100%;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-premium:hover {
            background-color: #C76118;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="w-full max-w-2xl px-6 py-12">
        <div class="card-premium">
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

                <!-- Actions -->
                <div class="space-y-4 pt-4">
                    <?php if ($session['result'] === 'pass'): ?>
                        <a href="<?= e(BASE_URL) ?>/public/verify.php?token=<?= e($certificate['verify_token'] ?? '') ?>" class="btn-premium">
                            <i class="fas fa-certificate"></i> ดูใบประกาศนียบัตรของคุณ
                        </a>
                    <?php else: ?>
                        <a href="<?= e(BASE_URL) ?>/public/exam.php?project=<?= e($project['code']) ?>" class="btn-premium bg-gray-900 hover:bg-black">
                            <i class="fas fa-sync-alt"></i> พยายามอีกครั้ง
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?= e(BASE_URL) ?>/" class="w-full h-16 bg-white border-2 border-gray-100 hover:border-primary-500 hover:text-primary-500 text-gray-500 font-bold rounded-[2rem] transition-all flex items-center justify-center gap-3 no-underline">
                        <i class="fas fa-home"></i> กลับหน้าหลัก
                    </a>
                </div>
            </div>

            <div class="bg-gray-50/50 py-6 text-center border-t border-gray-50">
                <p class="text-[10px] font-bold text-gray-300 uppercase tracking-[0.2em]">ExamCert Digital Result Verification System</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Use window property to avoid redeclaration error if it's already in footer
        window.APP_BASE_URL = '<?= e(BASE_URL) ?>';
    </script>
</body>
</html>