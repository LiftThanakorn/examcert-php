-- ============================
-- DATABASE: examcert
-- ExamCert Standalone v1 (Full Schema)
-- ============================

CREATE DATABASE IF NOT EXISTS `examcert`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE `examcert`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150),
    role ENUM('superadmin','admin') DEFAULT 'admin',
    last_login DATETIME,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cert_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    orientation ENUM('L','P') DEFAULT 'L',
    width_mm DECIMAL(6,2) DEFAULT 297.00,
    height_mm DECIMAL(6,2) DEFAULT 210.00,
    bg_type ENUM('color','image') DEFAULT 'color',
    bg_color VARCHAR(7) DEFAULT '#FFFFFF',
    bg_image VARCHAR(255),
    elements JSON,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(20),
    description TEXT,
    organizer VARCHAR(255),
    location VARCHAR(255),
    start_date DATE,
    end_date DATE,
    exam_start DATETIME,
    exam_end DATETIME,
    pass_score DECIMAL(5,2) DEFAULT 70.00,
    max_attempts INT DEFAULT 1,
    time_limit_min INT DEFAULT 60,
    question_count INT DEFAULT 0,
    randomize_questions TINYINT(1) DEFAULT 1,
    randomize_choices TINYINT(1) DEFAULT 1,
    show_result_immediately TINYINT(1) DEFAULT 1,
    warning_before INT DEFAULT 30,
    allow_early_login TINYINT(1) DEFAULT 0,
    auto_submit_on_close TINYINT(1) DEFAULT 1,
    manual_override TINYINT(1) DEFAULT 0,
    cert_template_id INT,
    cert_number_prefix VARCHAR(50) DEFAULT 'CERT',
    status ENUM('draft', 'active', 'closed') DEFAULT 'draft',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cert_template_id) REFERENCES cert_templates(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    prefix VARCHAR(20),
    title VARCHAR(50),
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    id_card VARCHAR(20),
    email VARCHAR(150),
    phone VARCHAR(20),
    username VARCHAR(50) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (project_id, username),
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_image VARCHAR(255),
    question_type ENUM('multiple_choice', 'subjective') DEFAULT 'multiple_choice',
    options JSON,
    correct_answer TEXT,
    points INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS exam_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participant_id INT NOT NULL,
    project_id INT NOT NULL,
    started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    finished_at DATETIME,
    expires_at DATETIME,
    score DECIMAL(5,2) DEFAULT 0.00,
    is_passed TINYINT(1) DEFAULT 0,
    status ENUM('in_progress', 'completed', 'cancelled') DEFAULT 'in_progress',
    FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS answer_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    question_id INT NOT NULL,
    selected_option VARCHAR(255),
    subjective_answer TEXT,
    is_correct TINYINT(1) DEFAULT 0,
    points_earned INT DEFAULT 0,
    FOREIGN KEY (session_id) REFERENCES exam_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participant_id INT NOT NULL,
    project_id INT NOT NULL,
    template_id INT NOT NULL,
    certificate_no VARCHAR(50) NOT NULL UNIQUE,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    verify_token VARCHAR(100) NOT NULL UNIQUE,
    is_revoked TINYINT(1) DEFAULT 0,
    revoke_reason TEXT NULL,
    FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES cert_templates(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
