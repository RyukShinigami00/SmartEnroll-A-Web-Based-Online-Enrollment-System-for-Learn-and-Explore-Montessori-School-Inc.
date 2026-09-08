-- ============================================================
-- Seeds an initial Super Admin account.
-- Password below is a bcrypt hash for: ChangeMe123!
-- CHANGE THIS PASSWORD IMMEDIATELY AFTER FIRST LOGIN.
-- Generate a new hash with: php -r "echo password_hash('yourpassword', PASSWORD_BCRYPT);"
-- ============================================================

USE smartenroll;

INSERT INTO users (first_name, last_name, email, password_hash, role, is_active, email_verified_at)
VALUES (
    'System',
    'Super Admin',
    'superadmin@lems.local',
    '$2y$10$kEWdpizxY2nS5kzwKCWMQ.CT0F/bgYxErxdAYRQMGRiSKIkH/b0Iy',
    'super_admin',
    1,
    NOW()
)
ON DUPLICATE KEY UPDATE email = email;

-- A couple of starter sections so Phase 3 sprints have something to assign into
INSERT INTO sections (grade_level, name, capacity) VALUES
    ('Toddler', 'Toddler A', 15),
    ('Toddler', 'Toddler B', 15),
    ('Primary', 'Primary A', 20),
    ('Primary', 'Primary B', 20)
ON DUPLICATE KEY UPDATE capacity = VALUES(capacity);
