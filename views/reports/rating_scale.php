<?php
$totalResponses = count(array_unique(array_map(static fn($row) => (int)$row['session_id'], $responseRows)));
$totalAnswers = count($responseRows);
$overallAverage = 0.0;
if ($totalAnswers > 0) {
    $overallAverage = array_sum(array_map(static fn($row) => (float)$row['given_answer'], $responseRows)) / $totalAnswers;
}
?>

<div class="mb-6 flex flex-col md:flex-row md:items-start justify-between gap-4 fade-up">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">Rating Scale Report</h2>
        <p class="text-sm text-gray-400 mt-0.5">สรุปผลแบบสอบถามรายโครงการ พร้อมรายละเอียดคำตอบและส่งออก CSV</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <form method="get" action="<?= e(BASE_URL) ?>/admin/reports/rating-scale.php" class="relative">
            <select name="project_id" onchange="this.form.submit()" class="h-10 pl-4 pr-9 text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-primary-400 focus:ring-[4px] focus:ring-orange-100 transition-all appearance-none">
                <?php foreach ($projectOptions as $option): ?>
                    <option value="<?= (int)$option['id'] ?>" <?= $project && (int)$project['id'] === (int)$option['id'] ? 'selected' : '' ?>>
                        <?= e($option['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
        </form>
        <?php if ($project): ?>
            <a href="<?= e(BASE_URL) ?>/admin/reports/rating-scale-export.php?project_id=<?= (int)$project['id'] ?>" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-xl transition-colors shadow-sm">
                <i class="fas fa-file-csv mr-2 text-primary-400"></i> Export CSV
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if (!$project): ?>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-12 text-center text-gray-400 fade-up">
        <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-chart-simple text-3xl text-gray-200"></i>
        </div>
        <p class="text-sm font-medium">ยังไม่มีโครงการที่มีคำถาม Rating Scale</p>
    </div>
<?php else: ?>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 fade-up fade-up-1">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-5">
        <p class="text-xxs font-bold text-gray-400 uppercase tracking-widest">Project</p>
        <p class="text-sm font-bold text-gray-800 mt-2 truncate"><?= e($project['name']) ?></p>
        <p class="text-xs text-gray-400 mt-1"><?= e($project['code'] ?: '-') ?></p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-5">
        <p class="text-xxs font-bold text-gray-400 uppercase tracking-widest">Responses</p>
        <p class="text-3xl font-black text-gray-800 mt-2"><?= $totalResponses ?></p>
        <p class="text-xs text-gray-400 mt-1">submitted sessions</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-5">
        <p class="text-xxs font-bold text-gray-400 uppercase tracking-widest">Average</p>
        <p class="text-3xl font-black text-primary-500 mt-2"><?= e(number_format($overallAverage, 2)) ?></p>
        <p class="text-xs text-gray-400 mt-1">จาก 5.00</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-5">
        <p class="text-xxs font-bold text-gray-400 uppercase tracking-widest">Meaning</p>
        <p class="text-xl font-black text-gray-800 mt-3"><?= $overallAverage > 0 ? e(ratingScaleMeaning($overallAverage)) : '-' ?></p>
        <p class="text-xs text-gray-400 mt-1"><?= $totalAnswers ?> answers</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6 fade-up fade-up-2">
    <div class="lg:col-span-1 bg-white rounded-2xl border border-gray-100 shadow-card p-6">
        <h3 class="text-xxs font-bold text-gray-400 uppercase tracking-widest mb-4">Category Average</h3>
        <div class="space-y-3">
            <?php foreach ($categoryRows as $row): ?>
                <?php $avg = (float)$row['average_score']; $pct = max(0, min(100, ($avg / 5) * 100)); ?>
                <div>
                    <div class="flex items-center justify-between gap-3 mb-1">
                        <span class="text-sm font-semibold text-gray-700 truncate"><?= e($row['category']) ?></span>
                        <span class="text-sm font-black text-primary-500"><?= e(number_format($avg, 2)) ?></span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-primary-400 rounded-full" style="width:<?= e(number_format($pct, 2, '.', '')) ?>%"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1"><?= (int)$row['response_count'] ?> answers · <?= e(ratingScaleMeaning($avg)) ?></p>
                </div>
            <?php endforeach; ?>
            <?php if (!$categoryRows): ?>
                <p class="text-sm text-gray-400">ยังไม่มีคำตอบ</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-card p-6">
        <h3 class="text-xxs font-bold text-gray-400 uppercase tracking-widest mb-4">Question Summary</h3>
        <div class="space-y-4">
            <?php foreach ($summaryRows as $row): ?>
                <?php
                    $avg = $row['average_score'] !== null ? (float)$row['average_score'] : 0.0;
                    $count = max(1, (int)$row['response_count']);
                ?>
                <div class="rounded-xl border border-gray-100 bg-gray-50/60 p-4">
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-800 leading-snug"><?= e($row['question_text']) ?></p>
                            <p class="text-xs text-gray-400 mt-1"><?= e($row['category']) ?> · <?= (int)$row['response_count'] ?> responses</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-2xl font-black text-primary-500 leading-none"><?= e(number_format($avg, 2)) ?></p>
                            <p class="text-[10px] font-bold text-gray-400 mt-1"><?= $avg > 0 ? e(ratingScaleMeaning($avg)) : '-' ?></p>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <?php foreach ([5, 4, 3, 2, 1] as $score): ?>
                            <?php $scoreCount = (int)$row['score_' . $score]; $pct = $count > 0 ? ($scoreCount / $count) * 100 : 0; ?>
                            <div class="grid grid-cols-[24px_1fr_36px] items-center gap-2">
                                <span class="text-xs font-bold text-gray-500"><?= $score ?></span>
                                <div class="h-2 bg-white border border-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-primary-400 rounded-full" style="width:<?= e(number_format($pct, 2, '.', '')) ?>%"></div>
                                </div>
                                <span class="text-xs text-gray-500 text-right"><?= $scoreCount ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden fade-up fade-up-3">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-sm font-bold text-gray-800">Individual Responses</h3>
        <span class="text-xs text-gray-400"><?= $totalAnswers ?> answers</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse table-row-hover">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100 text-xs font-semibold text-gray-500 tracking-wide uppercase">
                    <th class="px-5 py-3 font-medium">Participant</th>
                    <th class="px-5 py-3 font-medium">Category</th>
                    <th class="px-5 py-3 font-medium">Question</th>
                    <th class="px-5 py-3 font-medium text-center">Answer</th>
                    <th class="px-5 py-3 font-medium">Submitted</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <?php foreach ($responseRows as $row): ?>
                    <tr>
                        <td class="px-5 py-3">
                            <div class="font-semibold text-gray-800"><?= e($row['participant_name']) ?></div>
                            <div class="text-[10px] text-gray-400"><?= e($row['organization'] ?: '-') ?></div>
                        </td>
                        <td class="px-5 py-3 text-gray-600"><?= e($row['category']) ?></td>
                        <td class="px-5 py-3 text-gray-700 min-w-[280px]"><?= e($row['question_text']) ?></td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-primary-50 text-primary-600 font-black border border-primary-100"><?= e((string)$row['given_answer']) ?></span>
                        </td>
                        <td class="px-5 py-3 text-gray-500 whitespace-nowrap"><?= e((string)$row['submitted_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$responseRows): ?>
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">ยังไม่มีคำตอบ Rating Scale</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
