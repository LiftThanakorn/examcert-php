<?php
$pageTitle = 'ระบบสอบออนไลน์ มรภ.ร้อยเอ็ด';
require VIEWS_PATH . '/layout/header.php';
?>

<style>
    html {
        scroll-behavior: smooth;
    }
    .bg-mesh {
        background-color: #ffffff;
        background-image: 
            radial-gradient(at 0% 0%, hsla(25,100%,93%,1) 0, transparent 50%), 
            radial-gradient(at 50% 0%, hsla(225,39%,30%,0.1) 0, transparent 50%), 
            radial-gradient(at 100% 0%, hsla(25,100%,93%,1) 0, transparent 50%);
    }
    .glass {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .shadow-soft {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
    }
    .shadow-premium {
        box-shadow: 0 20px 50px -12px rgba(0, 0, 0, 0.1);
    }
    .hover-lift {
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px -15px rgba(232, 119, 34, 0.2);
    }
    .btn-premium {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 1rem 2rem;
        background-color: #E87722;
        color: white;
        font-weight: 700;
        border-radius: 1rem;
        transition: all 0.3s;
        box-shadow: 0 10px 25px -5px rgba(232, 119, 34, 0.2);
    }
    .btn-premium:hover {
        background-color: #C76118;
        box-shadow: 0 20px 50px -12px rgba(232, 119, 34, 0.3);
    }
    .btn-premium::after {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: linear-gradient(to right, transparent, rgba(255,255,255,0.2), transparent);
        transform: translateX(-100%);
        transition: transform 0.5s;
    }
    .btn-premium:hover::after {
        transform: translateX(100%);
    }
    .card-premium {
        background: white;
        border-radius: 2.5rem;
        padding: 2rem;
        border: 1px solid #F3F4F6;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }
    .animate-float {
        animation: float 6s ease-in-out infinite;
    }
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }
</style>

<div class="min-h-screen">
    <!-- Navigation -->
    <nav class="fixed top-6 left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-7xl">
        <div class="glass shadow-soft rounded-[2rem] px-8 py-4 flex justify-between items-center border border-white/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-primary-500/30">
                    <i class="fas fa-certificate text-xl"></i>
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tighter text-gray-900 block leading-none">ExamCert</span>
                    <span class="text-[10px] font-bold text-primary-500 uppercase tracking-widest leading-none">RERU Portal</span>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <a href="<?= e(BASE_URL) ?>/admin/login.php" class="text-sm font-bold text-gray-500 hover:text-primary-500 transition-colors flex items-center gap-2">
                    <i class="fas fa-lock text-xs"></i> สำหรับผู้ดูแล
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="pt-40 pb-20 px-6">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
            <div class="space-y-8 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-50 text-primary-600 rounded-full text-xs font-extrabold uppercase tracking-widest animate-bounce">
                    <i class="fas fa-star text-[10px]"></i> งานบริหารทรัพยากรบุคคลและนิติการ
                </div>
                <h1 class="text-5xl md:text-7xl font-extrabold text-gray-900 leading-[1.1] tracking-tighter">
                    ระบบทำข้อสอบ<br>
                    <span class="text-primary-500">พร้อมรับเกียรติบัตร</span>
                </h1>
                <p class="text-lg text-gray-500 font-medium max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    ระบบสอบออนไลน์และออกใบประกาศนียบัตรอัตโนมัติ สำหรับบุคลากรมหาวิทยาลัยราชภัฏร้อยเอ็ด
                </p>
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4">
                    <a href="#active-exams" class="btn-premium flex items-center gap-3 group">
                        เข้าสู่ห้องสอบ <i class="fas fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <a href="<?= e(BASE_URL) ?>/public/verify.php" class="px-8 py-4 bg-white text-gray-700 font-bold rounded-2xl border border-gray-200 hover:bg-gray-50 transition-all flex items-center gap-3 shadow-soft">
                        ตรวจสอบใบประกาศ <i class="fas fa-search-location text-gray-400"></i>
                    </a>
                </div>
            </div>
            <div class="relative hidden lg:block">
                <div class="absolute -top-10 -right-10 w-64 h-64 bg-primary-200/30 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-blue-200/30 rounded-full blur-3xl animate-pulse delay-700"></div>
                <img src="<?= e(BASE_URL) ?>/assets/img/landing_hero.png" alt="Hero" class="relative w-full rounded-[3rem] shadow-premium animate-float border-4 border-white/50">
            </div>
        </div>
    </header>

    <!-- Active Projects -->
    <section id="active-exams" class="py-24 px-6 relative overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
                <div class="space-y-4">
                    <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight">โครงการที่เปิดสอบ</h2>
                    <p class="text-gray-500 font-medium">กรุณาเลือกโครงการที่คุณลงทะเบียนไว้เพื่อเข้าทำข้อสอบ</p>
                </div>
                <div class="flex items-center gap-4 text-sm font-bold">
                    <span class="flex items-center gap-2 text-green-500"><i class="fas fa-circle text-[6px]"></i> เปิดระบบ</span>
                    <span class="flex items-center gap-2 text-gray-400"><i class="fas fa-circle text-[6px]"></i> ปิดระบบ</span>
                </div>
            </div>

            <?php if (!empty($projects)): ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($projects as $proj): ?>
                <a href="<?= e(BASE_URL) ?>/public/entry.php?project=<?= e($proj['code']) ?>" class="card-premium group relative overflow-hidden hover-lift">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-primary-50 rounded-bl-[4rem] flex items-start justify-end p-4 group-hover:bg-primary-500 transition-colors duration-300">
                        <i class="fas fa-arrow-up-right-from-square text-primary-500 group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="space-y-6">
                        <div class="inline-block px-3 py-1 bg-gray-100 text-gray-500 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                            Code: <?= e($proj['code']) ?>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 leading-tight group-hover:text-primary-600 transition-colors">
                            <?= e($proj['name']) ?>
                        </h3>
                        <div class="pt-6 border-t border-gray-50 flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-400">มหาวิทยาลัยราชภัฏร้อยเอ็ด</span>
                            <span class="text-primary-500 font-extrabold text-xs group-hover:mr-2 transition-all">เข้าสู่ห้องสอบ</span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="card-premium text-center py-20 space-y-4 border-dashed bg-transparent shadow-none border-gray-200">
                <div class="w-20 h-20 bg-gray-100 rounded-3xl flex items-center justify-center mx-auto text-gray-300 text-3xl">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-400">ยังไม่มีโครงการสอบในขณะนี้</h3>
                <p class="text-sm text-gray-300">กรุณาติดตามข่าวสารจากทางมหาวิทยาลัย</p>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer Area -->
    <footer class="py-20 px-6 border-t border-gray-100 bg-white/50">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-gray-900 rounded-lg flex items-center justify-center text-white">
                        <i class="fas fa-certificate text-sm"></i>
                    </div>
                    <span class="text-lg font-extrabold tracking-tighter text-gray-900">ExamCert <span class="text-gray-300 font-medium">v1.0</span></span>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed max-w-md">
                    ระบบสอบออนไลน์และออกใบประกาศนียบัตรอย่างเป็นทางการ<br>
                    เพื่อสนับสนุนความเป็นเลิศทางวิชาการและการพัฒนาบุคลากรอย่างต่อเนื่อง
                </p>
            </div>
            <div class="space-y-4 md:text-right">
                <p class="text-sm font-bold text-gray-900">&copy; <?= date('Y') ?> มหาวิทยาลัยราชภัฏร้อยเอ็ด</p>
                <div class="text-xs text-gray-400 space-y-1">
                    <p>ระบบออกข้อสอบพร้อมรับเกียรติบัตร พัฒนาโดย นายธนากร อินทพันธ์</p>
                    <p>บุคลากร งานบริหารทรัพยากรบุคคลและนิติการ มหาวิทยาลัยราชภัฏร้อยเอ็ด</p>
                </div>
            </div>
        </div>
    </footer>
</div>

<?php require VIEWS_PATH . '/layout/footer.php'; ?>
