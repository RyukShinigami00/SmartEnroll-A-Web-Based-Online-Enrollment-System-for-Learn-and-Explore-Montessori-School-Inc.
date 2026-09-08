-- ============================================================
-- Migration 002: Split `name` into first_name/last_name,
-- add email verification support.
-- Run this once against your EXISTING smartenroll database
-- (created before this feature existed). Fresh installs using
-- the current schema.sql already have these columns.
-- ============================================================

USE smartenroll;

ALTER TABLE users
    ADD COLUMN first_name VARCHAR(100) NOT NULL DEFAULT '' AFTER id,
    ADD COLUMN last_name  VARCHAR(100) NOT NULL DEFAULT '' AFTER first_name;

-- Best-effort split of existing `name` values into first_name/last_name
UPDATE users
SET
    first_name = TRIM(SUBSTRING_INDEX(name, ' ', 1)),
    last_name  = TRIM(SUBSTRING(name, LENGTH(SUBSTRING_INDEX(name, ' ', 1)) + 1))
WHERE name IS NOT NULL AND name <> '';

-- If a name had no space (single word), last_name will be empty — backfill it
UPDATE users SET last_name = first_name WHERE last_name = '';

ALTER TABLE users DROP COLUMN name;

ALTER TABLE users
    ADD COLUMN email_verified_at       TIMESTAMP NULL DEFAULT NULL AFTER is_active,
    ADD COLUMN verification_token      VARCHAR(100) NULL AFTER email_verified_at,
    ADD COLUMN verification_expires_at TIMESTAMP NULL DEFAULT NULL AFTER verification_token,
    ADD INDEX idx_users_verification_token (verification_token);

-- Auto-verify accounts that already existed before this feature shipped,
-- so nobody who registered/was seeded earlier gets locked out.
UPDATE users SET email_verified_at = NOW() WHERE email_verified_at IS NULL;
