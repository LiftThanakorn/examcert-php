<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
    <main class="mx-auto max-w-4xl p-6">
        <h1 class="mb-2 text-2xl font-semibold"><?= e($project['name'] ?? 'Exam') ?></h1>
        <div class="sticky top-0 z-10 mb-6 rounded-lg border border-orange-200 bg-orange-50 px-4 py-3 text-sm text-orange-900">
            เหลือเวลา <strong id="countdown" data-seconds="<?= (int) $secondsLeft ?>"></strong> | หมดเวลา: <?= e($session['expires_at']) ?>
        </div>
        <form id="exam-form" method="post" class="space-y-5">
            <?= csrfField() ?>
            <input type="hidden" name="session_id" value="<?= (int) $sessionId ?>">
            <?php foreach ($questions as $index => $question): ?>
                <?php $choices = json_decode((string) $question['choices'], true) ?: []; ?>
                <section class="rounded-lg border border-gray-200 bg-white p-5">
                    <h2 class="mb-4 font-semibold">ข้อ <?= $index + 1 ?>. <?= e($question['question_text']) ?></h2>
                    <?php if ($question['type'] === 'fill_blank'): ?>
                        <input name="answers[<?= (int) $question['id'] ?>]" class="w-full rounded border border-gray-200 px-3 py-2">
                    <?php else: ?>
                        <?php foreach ($choices as $choice): ?>
                            <label class="mb-2 flex gap-2">
                                <input type="radio" name="answers[<?= (int) $question['id'] ?>]" value="<?= e((string) $choice['key']) ?>">
                                <span><?= e((string) $choice['text']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
            <button class="rounded bg-orange-600 px-5 py-2 text-white" onclick="return confirm('ยืนยันส่งข้อสอบ?');">ส่งข้อสอบ</button>
        </form>
    </main>
    <script>
    const countdown = document.getElementById('countdown');
    const form = document.getElementById('exam-form');
    let secondsLeft = parseInt(countdown.dataset.seconds || '0', 10);
    let submitting = false;

    function renderTime(total) {
        const h = Math.floor(total / 3600);
        const m = Math.floor((total % 3600) / 60);
        const s = total % 60;
        countdown.textContent = [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
    }

    renderTime(secondsLeft);
    const timer = setInterval(() => {
        secondsLeft -= 1;
        renderTime(Math.max(0, secondsLeft));
        if (secondsLeft <= 0 && !submitting) {
            submitting = true;
            clearInterval(timer);
            form.submit();
        }
    }, 1000);
    </script>
</body>
</html>
