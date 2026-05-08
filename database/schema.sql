-- ============================
-- DATABASE: examcert
-- ExamCert Standalone v1
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
    bg_image VARCHAR(255),
    orientation ENUM('L','P') DEFAULT 'L',
    layout_json JSON,
    font_name VARCHAR(100) DEFAULT 'thsarabun',
    logo_path VARCHAR(255),
    signature_paths JSON,
    show_score TINYINT(1) DEFAULT 0,
    show_qr TINYINT(1) DEFAULT 1,
    show_date TINYINT(1) DEFAULT 1,
    color_primary VARCHAR(7) DEFAULT '#E87722',
    preview_image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE,
    description TEXT,
    organizer VARCHAR(255),
    location VARCHAR(255),
    start_date DATE,
    end_date DATE,
    exam_start DATETIME,
    exam_end DATETIME,
    manual_override TINYINT(1) DEFAULT 0,
    warning_before INT DEFAULT 30,
    allow_early_login TINYINT(1) DEFAULT 0,
    auto_submit_on_close TINYINT(1) DEFAULT 1,
    pass_score DECIMAL(5,2) DEFAULT 70.00,
    max_attempts INT DEFAULT 1,
    time_limit_min INT DEFAULT 60,
    question_count INT DEFAULT 0,
    randomize_questions TINYINT(1) DEFAULT 1,
    randomize_choices TINYINT(1) DEFAULT 1,
    show_result_immediately TINYINT(1) DEFAULT 1,
    cert_template_id INT,
    cert_number_prefix VARCHAR(20) DEFAULT 'CERT',
    cert_sequence INT DEFAULT 1,
    status ENUM('draft','active','closed') DEFAULT 'draft',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cert_template_id) REFERENCES cert_templates(id),
    FOREIGN KEY (created_by) REFERENCES admins(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    title VARCHAR(30),
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    organization VARCHAR(200),
    position VARCHAR(150),
    email VARCHAR(150),
    phone VARCHAR(20),
    id_card VARCHAR(13),
    access_token VARCHAR(64) NOT NULL UNIQUE,
    note TEXT,
    import_batch VARCHAR(50),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES admins(id),
    INDEX idx_project_name (project_id, first_name, last_name),
    INDEX idx_token (access_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_image VARCHAR(255),
    type ENUM('multiple_choice','true_false','fill_blank') DEFAULT 'multiple_choice',
    choices JSON,
    correct_answer TEXT NOT NULL,
    explanation TEXT,
    score_weight DECIMAL(5,2) DEFAULT 1.00,
    category VARCHAR(100),
    difficulty ENUM('easy','medium','hard') DEFAULT 'medium',
    order_num INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES admins(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS exam_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participant_id INT NOT NULL,
    project_id INT NOT NULL,
    attempt_no INT DEFAULT 1,
    question_order JSON,
    score DECIMAL(8,2) DEFAULT 0,
    total_score DECIMAL(8,2) DEFAULT 0,
    percent DECIMAL(5,2) DEFAULT 0,
    status ENUM('in_progress','submitted','expired') DEFAULT 'in_progress',
    result ENUM('pass','fail') DEFAULT 'fail',
    started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    submitted_at DATETIME,
    expires_at DATETIME,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    FOREIGN KEY (participant_id) REFERENCES participants(id),
    FOREIGN KEY (project_id) REFERENCES projects(id),
    INDEX idx_participant_project (participant_id, project_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS answer_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    question_id INT NOT NULL,
    given_answer TEXT,
    is_correct TINYINT(1),
    score_earned DECIMAL(5,2) DEFAULT 0,
    answered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES exam_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cert_number VARCHAR(50) NOT NULL UNIQUE,
    participant_id INT NOT NULL,
    project_id INT NOT NULL,
    session_id INT NOT NULL,
    template_id INT NOT NULL,
    issued_date DATE NOT NULL,
    issued_by INT,
    file_path VARCHAR(255),
    verify_token VARCHAR(64) NOT NULL UNIQUE,
    verify_url VARCHAR(500),
    download_count INT DEFAULT 0,
    last_downloaded_at DATETIME,
    is_revoked TINYINT(1) DEFAULT 0,
    revoke_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (participant_id) REFERENCES participants(id),
    FOREIGN KEY (project_id) REFERENCES projects(id),
    FOREIGN KEY (session_id) REFERENCES exam_sessions(id),
    FOREIGN KEY (template_id) REFERENCES cert_templates(id),
    FOREIGN KEY (issued_by) REFERENCES admins(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

