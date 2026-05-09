<div class="mb-6 fade-up">
    <h2 class="text-xl font-semibold text-gray-800"><?= e($pageTitle ?? 'เลือกโครงการสอบ') ?></h2>
    <p class="text-sm text-gray-400 mt-0.5">กรุณาเลือกโครงการที่ต้องการจัดการข้อมูล</p>
</div>

<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
    <?php 
    require_once ROOT_PATH . '/models/Project.php';
    $projects = getAllProjects();
    foreach ($projects as $i => $p): 
    ?>
    <a href="<?= e(BASE_URL) ?>/<?= e($targetLink) ?>?project_id=<?= (int) $p['id'] ?>" 
       class="bg-white rounded-2xl border border-gray-100 shadow-card p-6 hover:shadow-card-md hover:border-primary-200 transition-all group fade-up" style="animation-delay: <?= ($i * 0.05) ?>s">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center text-primary-400 group-hover:bg-primary-400 group-hover:text-white transition-colors">
                <i class="fas fa-folder-open text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-800 line-clamp-1"><?= e($p['name']) ?></h3>
                <p class="text-xxs text-gray-400 uppercase tracking-widest"><?= e($p['code']) ?></p>
            </div>
        </div>
        <div class="flex items-center justify-between pt-4 border-t border-gray-50">
            <span class="text-xs font-medium text-gray-500">จัดการข้อมูล</span>
            <i class="fas fa-chevron-right text-xs text-gray-300 group-hover:text-primary-400 group-hover:translate-x-1 transition-all"></i>
        </div>
    </a>
    <?php endforeach; ?>

    <?php if (!$projects): ?>
    <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-dashed border-gray-200 fade-up">
        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-folder-plus text-2xl text-gray-200"></i>
        </div>
        <p class="text-gray-500 font-medium">ยังไม่มีโครงการสอบในระบบ</p>
        <a href="<?= e(BASE_URL) ?>/admin/projects/create.php" class="inline-flex items-center mt-4 text-sm font-bold text-primary-400 hover:text-primary-500">
            สร้างโครงการแรกของคุณที่นี่ <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>
    <?php endif; ?>
</div>
