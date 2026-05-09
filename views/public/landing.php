<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'ExamCert Platform') ?></title>
    
    <!-- Fonts: Inter & Noto Sans Thai -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS (CDN for standalone) -->
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
                            50: '#FFF3EB',
                            100: '#FFE4D1',
                            200: '#FFC8A3',
                            300: '#FFA56E',
                            400: '#FF813A',
                            500: '#E87722', // Brand Primary
                            600: '#C76118',
                            700: '#A34D10',
                        }
                    },
                    boxShadow: {
                        'card': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                        'card-hover': '0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04)',
                    }
                }
            }
        }
    </script>
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .bg-pattern {
            background-image: radial-gradient(#FFC8A3 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col antialiased relative overflow-x-hidden">

    <!-- Decorative Background Elements -->
    <div class="absolute top-0 right-0 w-1/2 h-1/2 bg-gradient-to-bl from-primary-100/50 to-transparent rounded-bl-full pointer-events-none -z-10"></div>
    <div class="absolute bottom-0 left-0 w-full h-64 bg-pattern opacity-30 pointer-events-none -z-10"></div>

    <!-- Navigation -->
    <nav class="glass-panel border-b border-white/40 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/30">
                        <i class="fas fa-certificate text-white text-xl"></i>
                    </div>
                    <span class="ml-3 text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 tracking-tight">ExamCert</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="<?= e(BASE_URL) ?>/admin/login.php" class="text-sm font-semibold text-gray-500 hover:text-primary-500 transition-colors">
                        <i class="fas fa-lock mr-1.5"></i> ผู้ดูแลระบบ
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl w-full">
            
            <!-- Hero Section -->
            <div class="text-center mb-16">
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight mb-4 leading-tight">
                    ระบบสอบออนไลน์<span class="text-primary-500 block sm:inline">พร้อมออกใบเกียรติบัตร</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-500 max-w-2xl mx-auto font-medium">
                    สำหรับบุคลากรมหาวิทยาลัยราชภัฏร้อยเอ็ด
                </p>
            </div>

            <!-- Portal Cards -->
            <div class="grid md:grid-cols-2 gap-8 max-w-3xl mx-auto">
                
                <!-- Take Exam Card -->
                <a href="<?= e(BASE_URL) ?>/public/exam.php" class="group block relative rounded-3xl bg-white border border-gray-100 shadow-card hover:shadow-card-hover transition-all duration-300 overflow-hidden transform hover:-translate-y-1">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-primary-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    <div class="p-8 sm:p-10">
                        <div class="w-16 h-16 bg-orange-50 text-primary-500 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:bg-primary-500 group-hover:text-white transition-colors duration-300 shadow-inner">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-primary-600 transition-colors">เข้าสู่ระบบการสอบ</h2>
                        <p class="text-gray-500 text-sm leading-relaxed mb-6">
                            สำหรับผู้มีสิทธิ์สอบ กรุณาเตรียม <strong>รหัสโครงการ</strong> และ <strong>รหัสผ่าน (Token)</strong> เพื่อเข้าสู่ห้องสอบออนไลน์
                        </p>
                        <div class="inline-flex items-center text-primary-500 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300">
                            เริ่มต้นทำข้อสอบ <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </div>
                </a>

                <!-- Verify Cert Card -->
                <a href="<?= e(BASE_URL) ?>/public/verify.php" class="group block relative rounded-3xl bg-white border border-gray-100 shadow-card hover:shadow-card-hover transition-all duration-300 overflow-hidden transform hover:-translate-y-1">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-blue-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    <div class="p-8 sm:p-10">
                        <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:bg-blue-500 group-hover:text-white transition-colors duration-300 shadow-inner">
                            <i class="fas fa-search-location"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">ตรวจสอบใบประกาศ</h2>
                        <p class="text-gray-500 text-sm leading-relaxed mb-6">
                            สำหรับหน่วยงานหรือ HR เพื่อใช้ในการตรวจสอบความถูกต้องของใบประกาศนียบัตร โดยกรอกรหัสอ้างอิง
                        </p>
                        <div class="inline-flex items-center text-blue-500 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300">
                            ตรวจสอบทันที <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </div>
                </a>

            </div>

            <!-- Active Projects Section -->
            <?php if (!empty($projects)): ?>
            <div class="mt-20 fade-up" style="animation-delay: 0.4s;">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center text-sm">
                            <i class="fas fa-list-ul"></i>
                        </span>
                        โครงการสอบที่เปิดให้เข้าสอบ
                    </h3>
                    <div class="h-px flex-grow ml-6 bg-gray-100 hidden sm:block"></div>
                </div>
                
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($projects as $proj): ?>
                    <a href="<?= e(BASE_URL) ?>/public/exam.php?project=<?= e($proj['code']) ?>" class="group p-6 bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-card-hover hover:border-primary-200 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-10 h-10 bg-gray-50 text-gray-400 group-hover:bg-primary-50 group-hover:text-primary-500 rounded-xl flex items-center justify-center transition-colors">
                                <i class="fas fa-file-signature text-lg"></i>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-1 bg-gray-100 text-gray-500 rounded-md group-hover:bg-primary-100 group-hover:text-primary-600 transition-colors">
                                รหัส: <?= e($proj['code']) ?>
                            </span>
                        </div>
                        <h4 class="font-bold text-gray-800 group-hover:text-primary-700 transition-colors leading-tight mb-2"><?= e($proj['name']) ?></h4>
                        <div class="flex items-center text-primary-500 text-xs font-bold uppercase tracking-wider mt-4 opacity-0 group-hover:opacity-100 transition-all transform translate-x-[-10px] group-hover:translate-x-0">
                            เข้าสู่ห้องสอบ <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="mt-20 text-center p-12 bg-white rounded-3xl border border-dashed border-gray-200">
                <i class="fas fa-inbox text-4xl text-gray-100 mb-4"></i>
                <p class="text-gray-400 font-medium">ขณะนี้ยังไม่มีโครงการสอบที่เปิดให้บริการ</p>
            </div>
            <?php endif; ?>

            <!-- Footer Note -->
            <div class="mt-16 text-center text-sm text-gray-400">
                <p>&copy; <?= date('Y') ?> ระบบสอบออนไลน์ มหาวิทยาลัยราชภัฏร้อยเอ็ด. All rights reserved.</p>
                <p class="mt-3 leading-relaxed">
                    พัฒนาระบบโดย นายธนากร อินทพันธ์<br>
                    <span class="text-xs">ตำแหน่งบุคลากร สังกัดงานบริหารทรัพยากรบุคคลและนิติการ มหาวิทยาลัยราชภัฏร้อยเอ็ด</span>
                </p>
            </div>

        </div>
    </main>

</body>
</html>
