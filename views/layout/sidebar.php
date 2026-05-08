<aside class="w-64 bg-gray-900 text-white sticky top-0 h-screen overflow-y-auto flex flex-col">
    <div class="p-4 border-b border-gray-800">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-primary-600 rounded-lg flex items-center justify-center text-lg">
                <i class="fas fa-award"></i>
            </div>
            <div>
                <div class="font-semibold text-sm leading-tight">ExamCert</div>
                <div class="text-gray-400 text-xs">Admin Panel</div>
            </div>
        </div>
    </div>
    <nav class="p-2 space-y-1 flex-1">
        <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider px-3 py-2">หลัก</div>
        <a href="<?= e(BASE_URL) ?>/admin/dashboard.php" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-primary-600 text-gray-300 hover:text-white transition-colors font-medium">
            <i class="fas fa-tachometer-alt w-5"></i><span>Dashboard</span>
        </a>
        <a href="<?= e(BASE_URL) ?>/admin/projects/" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-primary-600 text-gray-300 hover:text-white transition-colors font-medium">
            <i class="fas fa-folder w-5"></i><span>โครงการ</span>
        </a>
        <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider px-3 py-2 mt-2">ข้อสอบ</div>
        <a href="<?= e(BASE_URL) ?>/admin/exam-sessions/" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-primary-600 text-gray-300 hover:text-white transition-colors font-medium">
            <i class="fas fa-chart-bar w-5"></i><span>ผลการสอบ</span>
        </a>
        <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider px-3 py-2 mt-2">เกียรติบัตร</div>
        <a href="<?= e(BASE_URL) ?>/admin/certificates/" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-primary-600 text-gray-300 hover:text-white transition-colors font-medium">
            <i class="fas fa-certificate w-5"></i><span>ออกเกียรติบัตร</span>
        </a>
        <a href="<?= e(BASE_URL) ?>/admin/reports/" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-primary-600 text-gray-300 hover:text-white transition-colors font-medium">
            <i class="fas fa-chart-pie w-5"></i><span>รายงาน</span>
        </a>
    </nav>
</aside>

