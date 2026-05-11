-- ============================
-- DATABASE: examcert
-- ExamCert Standalone v1 (MySQL 8 Optimized)
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

INSERT INTO cert_templates (
    id, name, orientation, width_mm, height_mm, bg_type, bg_color, elements, is_active
) VALUES (
    1,
    'Default Certificate',
    'L',
    297.00,
    210.00,
    'color',
    '#FFFFFF',
    '[{"id":"el_1","type":"text","x":148.5,"y":80,"w":200,"h":20,"content":"CERTIFICATE","style":{"fontSize":40,"fontWeight":"bold","textAlign":"center","color":"#000000","fontFamily":"thsarabunnew"}},{"id":"el_2","type":"text","x":148.5,"y":100,"w":200,"h":15,"content":"OF APPRECIATION","style":{"fontSize":20,"fontWeight":"normal","textAlign":"center","color":"#666666","fontFamily":"thsarabunnew"}},{"id":"el_3","type":"text","x":148.5,"y":125,"w":200,"h":25,"content":"[participant_name]","style":{"fontSize":36,"fontWeight":"bold","textAlign":"center","color":"#E87722","fontFamily":"thsarabunnew"}},{"id":"el_4","type":"text","x":148.5,"y":145,"w":200,"h":15,"content":"For successfully completing the exam in [project_name]","style":{"fontSize":18,"fontWeight":"normal","textAlign":"center","color":"#333333","fontFamily":"thsarabunnew"}}]',
    1
) ON DUPLICATE KEY UPDATE name=VALUES(name);

CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    exam_code VARCHAR(20) NOT NULL UNIQUE,
    template_id INT,
    passing_score INT DEFAULT 60,
    exam_duration_minutes INT DEFAULT 60,
    registration_open_at DATETIME,
    registration_close_at DATETIME,
    exam_start_at DATETIME,
    exam_end_at DATETIME,
    status ENUM('draft', 'active', 'closed') DEFAULT 'draft',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (template_id) REFERENCES cert_templates(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    prefix VARCHAR(20),
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
    score INT DEFAULT 0,
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
