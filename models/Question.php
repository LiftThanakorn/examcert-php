<?php
declare(strict_types=1);

function questionDefaults(): array
{
    return [
        'question_text' => '',
        'type' => 'multiple_choice',
        'choice_a' => '',
        'choice_b' => '',
        'choice_c' => '',
        'choice_d' => '',
        'correct_answer' => '',
        'explanation' => '',
        'score_weight' => '1.00',
        'category' => '',
        'difficulty' => 'medium',
        'order_num' => '0',
        'is_active' => '1',
    ];
}

function questionChoiceKeys(): array
{
    return ['a', 'b', 'c', 'd'];
}

function thaiChoiceLabels(): array
{
    return [
        'a' => 'ก',
        'b' => 'ข',
        'c' => 'ค',
        'd' => 'ง',
    ];
}

function normalizeChoiceKey(string $value): string
{
    $normalized = textLower(trim($value));
    $thaiToKey = [
        'ก' => 'a',
        'ข' => 'b',
        'ค' => 'c',
        'ง' => 'd',
    ];

    return $thaiToKey[$normalized] ?? $normalized;
}

function getQuestionsByProject(int $projectId, bool $activeOnly = false): array
{
    ensureQuestionSubjectiveSupport();

    $sql = 'SELECT * FROM questions WHERE project_id = ?';
    if ($activeOnly) {
        $sql .= ' AND is_active = 1';
    }
    $sql .= ' ORDER BY order_num ASC, id ASC';

    $stmt = getDB()->prepare($sql);
    $stmt->execute([$projectId]);
    return $stmt->fetchAll();
}

function getQuestion(int $id): ?array
{
    ensureQuestionSubjectiveSupport();

    $stmt = getDB()->prepare('SELECT * FROM questions WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $question = $stmt->fetch();
    return $question ?: null;
}

function decodeQuestionForForm(array $question): array
{
    $choices = json_decode((string) ($question['choices'] ?? ''), true);
    if (!is_array($choices)) {
        $choices = [];
    }

    foreach ($choices as $choice) {
        $key = normalizeChoiceKey((string) ($choice['key'] ?? ''));
        if (in_array($key, questionChoiceKeys(), true)) {
            $question['choice_' . $key] = (string) ($choice['text'] ?? '');
        }
    }

    if (($question['type'] ?? '') === 'multiple_choice') {
        $question['correct_answer'] = normalizeChoiceKey((string) ($question['correct_answer'] ?? ''));
    }

    return array_merge(questionDefaults(), $question);
}

function validateQuestionInput(array $data): array
{
    $errors = [];
    $type = (string) ($data['type'] ?? 'multiple_choice');

    if (trim((string) ($data['question_text'] ?? '')) === '') {
        $errors[] = 'กรุณากรอกคำถาม';
    }

    if (!in_array($type, ['multiple_choice', 'true_false', 'fill_blank', 'subjective'], true)) {
        $errors[] = 'ประเภทคำถามไม่ถูกต้อง';
    }

    if ($type === 'multiple_choice') {
        $hasChoice = false;
        foreach (questionChoiceKeys() as $key) {
            if (trim((string) ($data['choice_' . $key] ?? '')) !== '') {
                $hasChoice = true;
            }
        }
        if (!$hasChoice) {
            $errors[] = 'กรุณากรอกตัวเลือกอย่างน้อย 1 ตัวเลือก';
        }
        if (!in_array(normalizeChoiceKey((string) ($data['correct_answer'] ?? '')), questionChoiceKeys(), true)) {
            $errors[] = 'คำตอบที่ถูกต้องต้องเป็น ก, ข, ค หรือ ง';
        }
    } elseif ($type === 'true_false') {
        if (!in_array((string) ($data['correct_answer'] ?? ''), ['true', 'false'], true)) {
            $errors[] = 'คำตอบที่ถูกต้องต้องเป็น true หรือ false';
        }
    } elseif ($type === 'subjective') {
        // Subjective answers are stored for manual review and are not auto-scored.
    } elseif (trim((string) ($data['correct_answer'] ?? '')) === '') {
        $errors[] = 'กรุณากรอกคำตอบที่ถูกต้อง';
    }

    if ($type !== 'subjective' && (float) ($data['score_weight'] ?? 0) <= 0) {
        $errors[] = 'คะแนนต้องมากกว่า 0';
    }

    return $errors;
}

function questionPayload(array $data): array
{
    $type = (string) ($data['type'] ?? 'multiple_choice');
    $choices = null;

    if ($type === 'multiple_choice') {
        $choiceRows = [];
        foreach (questionChoiceKeys() as $key) {
            $text = trim((string) ($data['choice_' . $key] ?? ''));
            if ($text !== '') {
                $choiceRows[] = ['key' => $key, 'text' => $text];
            }
        }
        $choices = json_encode($choiceRows, JSON_UNESCAPED_UNICODE);
    } elseif ($type === 'true_false') {
        $choices = json_encode([
            ['key' => 'true', 'text' => 'ถูก'],
            ['key' => 'false', 'text' => 'ผิด'],
        ], JSON_UNESCAPED_UNICODE);
    }

    return [
        'question_text' => trim((string) $data['question_text']),
        'type' => $type,
        'choices' => $choices,
        'correct_answer' => $type === 'subjective'
            ? ''
            : ($type === 'multiple_choice'
                ? normalizeChoiceKey((string) ($data['correct_answer'] ?? ''))
                : trim((string) ($data['correct_answer'] ?? ''))),
        'explanation' => trim((string) ($data['explanation'] ?? '')) ?: null,
        'score_weight' => (float) ($data['score_weight'] ?? 1),
        'category' => trim((string) ($data['category'] ?? '')) ?: null,
        'difficulty' => in_array($data['difficulty'] ?? 'medium', ['easy', 'medium', 'hard'], true) ? $data['difficulty'] : 'medium',
        'order_num' => (int) ($data['order_num'] ?? 0),
        'is_active' => !empty($data['is_active']) ? 1 : 0,
    ];
}

function createQuestion(int $projectId, array $data, ?int $adminId): array
{
    ensureQuestionSubjectiveSupport();

    $errors = validateQuestionInput($data);
    if ($errors) {
        return ['success' => false, 'errors' => $errors];
    }

    $payload = questionPayload($data);

    try {
        $stmt = getDB()->prepare('
            INSERT INTO questions (
                project_id, question_text, type, choices, correct_answer, explanation,
                score_weight, category, difficulty, order_num, is_active, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $projectId,
            $payload['question_text'],
            $payload['type'],
            $payload['choices'],
            $payload['correct_answer'],
            $payload['explanation'],
            $payload['score_weight'],
            $payload['category'],
            $payload['difficulty'],
            $payload['order_num'],
            $payload['is_active'],
            $adminId,
        ]);

        return ['success' => true, 'id' => (int) getDB()->lastInsertId()];
    } catch (Throwable $e) {
        logError('Create question failed', ['project_id' => $projectId, 'error' => $e->getMessage()]);
        return ['success' => false, 'errors' => ['เกิดข้อผิดพลาดในการเพิ่มข้อสอบ']];
    }
}

function updateQuestion(int $id, array $data): array
{
    ensureQuestionSubjectiveSupport();

    $errors = validateQuestionInput($data);
    if ($errors) {
        return ['success' => false, 'errors' => $errors];
    }

    $payload = questionPayload($data);

    try {
        $stmt = getDB()->prepare('
            UPDATE questions SET
                question_text = ?, type = ?, choices = ?, correct_answer = ?, explanation = ?,
                score_weight = ?, category = ?, difficulty = ?, order_num = ?, is_active = ?
            WHERE id = ?
        ');
        $stmt->execute([
            $payload['question_text'],
            $payload['type'],
            $payload['choices'],
            $payload['correct_answer'],
            $payload['explanation'],
            $payload['score_weight'],
            $payload['category'],
            $payload['difficulty'],
            $payload['order_num'],
            $payload['is_active'],
            $id,
        ]);

        return ['success' => true, 'id' => $id];
    } catch (Throwable $e) {
        logError('Update question failed', ['id' => $id, 'error' => $e->getMessage()]);
        return ['success' => false, 'errors' => ['เกิดข้อผิดพลาดในการแก้ไขข้อสอบ']];
    }
}

function deleteQuestion(int $id): bool
{
    try {
        $stmt = getDB()->prepare('DELETE FROM questions WHERE id = ?');
        return $stmt->execute([$id]);
    } catch (Throwable $e) {
        logError('Delete question failed', ['id' => $id, 'error' => $e->getMessage()]);
        return false;
    }
}

function bulkInsertQuestions(array $questions): bool
{
    ensureQuestionSubjectiveSupport();

    if (empty($questions)) return true;
    $db = getDB();
    try {
        $db->beginTransaction();
        $stmt = $db->prepare('
            INSERT INTO questions (
                project_id, question_text, type, choices, 
                correct_answer, difficulty, category, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        foreach ($questions as $q) {
            $stmt->execute([
                (int) $q['project_id'],
                $q['question_text'],
                $q['type'],
                $q['choices'],
                $q['correct_answer'],
                $q['difficulty'],
                $q['category'],
                (int) $q['created_by']
            ]);
        }
        $db->commit();
        return true;
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        logError('Bulk insert questions failed', ['error' => $e->getMessage()]);
        return false;
    }
}

function ensureQuestionSubjectiveSupport(): void
{
    static $checked = false;
    if ($checked) {
        return;
    }

    $stmt = getDB()->query("SHOW COLUMNS FROM questions LIKE 'type'");
    $column = $stmt->fetch();
    $type = (string) ($column['Type'] ?? '');

    if ($type !== '' && strpos($type, 'subjective') === false) {
        getDB()->exec("ALTER TABLE questions MODIFY type ENUM('multiple_choice','true_false','fill_blank','subjective') DEFAULT 'multiple_choice'");
    }

    $checked = true;
}
