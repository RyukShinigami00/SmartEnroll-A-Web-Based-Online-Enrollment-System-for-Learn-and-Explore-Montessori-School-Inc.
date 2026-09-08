-- ============================================================
-- SmartEnroll: Web-Based Online Enrollment & Student Scheduling
-- Database Schema (Phase 2 - Database & Cloud Backend)
-- Target: MySQL 8.x (GCP Cloud SQL)
-- ============================================================

CREATE DATABASE IF NOT EXISTS smartenroll
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE smartenroll;

-- ------------------------------------------------------------
-- users: login accounts for Student / Admin / Super Admin
-- ------------------------------------------------------------
CREATE TABLE users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name      VARCHAR(100)        NOT NULL,
    last_name       VARCHAR(100)        NOT NULL,
    email           VARCHAR(150)        NOT NULL UNIQUE,
    password_hash   VARCHAR(255)        NOT NULL,
    role            ENUM('student','admin','super_admin') NOT NULL DEFAULT 'student',
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,
    email_verified_at       TIMESTAMP   NULL DEFAULT NULL,
    verification_token      VARCHAR(100) NULL,
    verification_expires_at TIMESTAMP   NULL DEFAULT NULL,
    remember_token  VARCHAR(100)        NULL,
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_role (role),
    INDEX idx_users_verification_token (verification_token)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- sections: class sections per grade level
-- ------------------------------------------------------------
CREATE TABLE sections (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    grade_level     VARCHAR(50)         NOT NULL,
    name            VARCHAR(100)        NOT NULL,
    capacity        INT UNSIGNED        NOT NULL DEFAULT 20,
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_grade_section (grade_level, name)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- enrollment_applications: raw submissions before a decision
-- ------------------------------------------------------------
CREATE TABLE enrollment_applications (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    submitted_by_user_id INT UNSIGNED  NULL,
    student_name        VARCHAR(150)   NOT NULL,
    date_of_birth       DATE           NOT NULL,
    grade_level         VARCHAR(50)    NOT NULL,
    parent_name         VARCHAR(150)   NOT NULL,
    contact_number      VARCHAR(30)    NOT NULL,
    address              VARCHAR(255)  NOT NULL,
    status               ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    reject_reason        VARCHAR(255)  NULL,
    assigned_section_id  INT UNSIGNED  NULL,
    reviewed_by_user_id  INT UNSIGNED  NULL,
    reviewed_at           TIMESTAMP    NULL,
    created_at             TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_app_submitted_by FOREIGN KEY (submitted_by_user_id)
        REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_app_section FOREIGN KEY (assigned_section_id)
        REFERENCES sections(id) ON DELETE SET NULL,
    CONSTRAINT fk_app_reviewer FOREIGN KEY (reviewed_by_user_id)
        REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_app_status (status)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- students: confirmed / approved student records
-- ------------------------------------------------------------
CREATE TABLE students (
    id                      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    enrollment_application_id INT UNSIGNED NULL,
    user_id                 INT UNSIGNED NULL,
    name                    VARCHAR(150) NOT NULL,
    date_of_birth            DATE        NOT NULL,
    grade_level               VARCHAR(50) NOT NULL,
    parent_name                VARCHAR(150) NOT NULL,
    contact_number               VARCHAR(30) NOT NULL,
    address                        VARCHAR(255) NOT NULL,
    section_id                     INT UNSIGNED NULL,
    status                          ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved',
    created_at                       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_student_application FOREIGN KEY (enrollment_application_id)
        REFERENCES enrollment_applications(id) ON DELETE SET NULL,
    CONSTRAINT fk_student_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_student_section FOREIGN KEY (section_id)
        REFERENCES sections(id) ON DELETE SET NULL,
    INDEX idx_student_section (section_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- schedule_entries: class schedule per section
-- ------------------------------------------------------------
CREATE TABLE schedule_entries (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    section_id      INT UNSIGNED        NOT NULL,
    subject         VARCHAR(100)        NOT NULL,
    day_of_week     ENUM('Mon','Tue','Wed','Thu','Fri','Sat') NOT NULL,
    start_time      TIME                NOT NULL,
    end_time        TIME                NOT NULL,
    room            VARCHAR(50)         NOT NULL,
    teacher         VARCHAR(150)        NOT NULL,
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_schedule_section FOREIGN KEY (section_id)
        REFERENCES sections(id) ON DELETE CASCADE,
    INDEX idx_schedule_section_day (section_id, day_of_week)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- audit_logs: record of admin / super admin actions
-- ------------------------------------------------------------
CREATE TABLE audit_logs (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED        NULL,
    action          VARCHAR(100)        NOT NULL,
    target_table    VARCHAR(100)        NULL,
    target_id       INT UNSIGNED        NULL,
    details         TEXT                NULL,
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_audit_created (created_at)
) ENGINE=InnoDB;
