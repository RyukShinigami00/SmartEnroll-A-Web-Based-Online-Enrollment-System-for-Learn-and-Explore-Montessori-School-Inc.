-- ============================================================
-- Migration 003: Add a simple key-value settings table for
-- Super Admin system configuration (Sprint 6).
-- ============================================================

USE smartenroll;

CREATE TABLE IF NOT EXISTS settings (
    setting_key   VARCHAR(100) PRIMARY KEY,
    setting_value TEXT NULL,
    updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO settings (setting_key, setting_value) VALUES
    ('school_name', 'Learn and Explore Montessori School'),
    ('school_email', 'info@lems.local'),
    ('school_contact_number', '')
ON DUPLICATE KEY UPDATE setting_key = setting_key;
