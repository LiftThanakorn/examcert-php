-- ExamCert production migration: rating scale survey support
-- Run this on the production database once before/with deploying the rating-scale PHP files.
-- Safe for existing data. Does not drop tables.

ALTER TABLE `questions`
  MODIFY `type` ENUM('multiple_choice','true_false','fill_blank','subjective','rating_scale')
  COLLATE utf8mb4_unicode_ci DEFAULT 'multiple_choice';

SET @grading_status_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'answer_logs'
    AND COLUMN_NAME = 'grading_status'
);

SET @grading_status_sql := IF(
  @grading_status_exists = 0,
  'ALTER TABLE `answer_logs` ADD COLUMN `grading_status` VARCHAR(20) DEFAULT ''auto'' AFTER `score_earned`',
  'SELECT ''answer_logs.grading_status already exists'' AS migration_status'
);

PREPARE grading_status_stmt FROM @grading_status_sql;
EXECUTE grading_status_stmt;
DEALLOCATE PREPARE grading_status_stmt;

-- Optional verification queries:
-- SHOW COLUMNS FROM `questions` LIKE 'type';
-- SHOW COLUMNS FROM `answer_logs` LIKE 'grading_status';
