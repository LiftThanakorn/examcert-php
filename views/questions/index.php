

<div class="mb-6 flex flex-col md:flex-row md:items-start justify-between gap-4 fade-up">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">คลังข้อสอบ</h2>
        <p class="text-sm text-gray-400 mt-0.5"><?= e($project['name']) ?></p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="<?= e(BASE_URL) ?>/admin/projects/detail.php?id=<?= (int) $projectId ?>" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-xl transition-colors">
            <i class="fas fa-arrow-left mr-2 text-gray-400"></i> กลับโครงการ
        </a>
        <a href="<?= e(BASE_URL) ?>/admin/questions/import.php?project_id=<?= (int) $projectId ?>" class="inline-flex items-center px-4 py-2 bg-blue-50 border border-blue-100 hover:bg-blue-100 text-blue-700 text-sm font-medium rounded-xl transition-colors">
            <i class="fas fa-file-import mr-2"></i> นำเข้า (CSV)
        </a>
        <a href="<?= e(BASE_URL) ?>/admin/questions/create.php?project_id=<?= (int) $projectId ?>" class="inline-flex items-center px-4 py-2 bg-primary-400 hover:bg-primary-500 text-white text-sm font-medium rounded-xl transition-colors shadow-orange shadow-md">
            <i class="fas fa-plus mr-2"></i> เพิ่มข้อสอบ
        </a>
    </div>
</div>

<div class="space-y-3">
    <?php foreach ($questions as $index => $question): ?>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-5 transition-all hover:shadow-card-md group fade-up fade-up-1">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-4">
                <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-primary-50 text-primary-400 text-xs font-bold flex items-center justify-center">
                    <?= $index + 1 ?>
                </span>
                <div>
                    <h3 class="text-gray-800 font-medium leading-relaxed"><?= e($question['question_text']) ?></h3>
                    <div class="flex items-center gap-2 mt-3">
                        <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded bg-blue-50 text-blue-600 border border-blue-100"><?= e($question['type']) ?></span>
                        <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded bg-gray-50 text-gray-500 border border-gray-100"><?= e($question['difficulty']) ?></span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-primary-400 ml-1"><?= e((string) $question['score_weight']) ?> คะแนน</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <a class="w-8 h-8 inline-flex items-center justify-center rounded-lg hover:bg-primary-50 text-gray-400 hover:text-primary-400 transition-colors" title="แก้ไข" href="<?= e(BASE_URL) ?>/admin/questions/edit.php?id=<?= (int) $question['id'] ?>">
                    <i class="fas fa-pen text-xs"></i>
                </a>
                <form method="post" action="<?= e(BASE_URL) ?>/admin/questions/delete.php" id="del-q-form-<?= (int)$question['id'] ?>" class="inline">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= (int) $question['id'] ?>">
                    <input type="hidden" name="project_id" value="<?= (int) $projectId ?>">
                    <button type="button" onclick="confirmDelete('ยืนยันลบข้อสอบนี้หรือไม่?', () => document.getElementById('del-q-form-<?= (int)$question['id'] ?>').submit())" class="w-8 h-8 inline-flex items-center justify-center rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-500 transition-colors" title="ลบ">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (!$questions): ?>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-12 text-center text-gray-400 fade-up">
        <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-file-circle-question text-3xl text-gray-200"></i>
        </div>
        <p class="text-sm font-medium">ยังไม่มีข้อสอบในคลัง</p>
    </div>
    <?php endif; ?>
</div>
