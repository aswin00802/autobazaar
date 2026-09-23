-- ---------------------------------------------------------------------------
-- 07_activity_and_notifications.sql
--
-- Run this AFTER 01 to 06.
--
-- Two things the earlier files do not know about, because they were added
-- later:
--
--   users.last_seen_at   when each person was last doing something. The admin
--                        Users list shows it as "Last Active", with a green dot
--                        for anyone seen in the last five minutes. Without this
--                        column that screen cannot load.
--
--   notifications        messages to a customer that live on the site — "your
--                        order has been packed" — shown under My Account. The
--                        push sent to their phone at the same moment is not
--                        stored here; this is what they can still find days
--                        later.
--
-- Safe to run twice. Each step checks first and does nothing if it is already
-- there, so running the whole file again changes nothing.
-- ---------------------------------------------------------------------------

SET NAMES utf8mb4;

-- --------------------------------------------------------------------------
-- users.last_seen_at
-- --------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS ab_add_last_seen;
DELIMITER //
CREATE PROCEDURE ab_add_last_seen()
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'last_seen_at'
    ) THEN
        ALTER TABLE `users` ADD COLUMN `last_seen_at` TIMESTAMP NULL DEFAULT NULL AFTER `last_login`;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND INDEX_NAME = 'users_last_seen_at_index'
    ) THEN
        ALTER TABLE `users` ADD INDEX `users_last_seen_at_index` (`last_seen_at`);
    END IF;
END //
DELIMITER ;

CALL ab_add_last_seen();
DROP PROCEDURE IF EXISTS ab_add_last_seen;

-- --------------------------------------------------------------------------
-- notifications
-- --------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`),
  KEY `notifications_unread_index` (`notifiable_type`,`notifiable_id`,`read_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- Record both as applied, so `php artisan migrate` later does not try again.
-- --------------------------------------------------------------------------
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_23_090000_add_last_seen_at_to_users_table', COALESCE((SELECT MAX(batch) FROM `migrations` m), 1)
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` m2 WHERE m2.migration = '2026_09_23_090000_add_last_seen_at_to_users_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_23_100000_create_notifications_table', COALESCE((SELECT MAX(batch) FROM `migrations` m), 1)
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` m2 WHERE m2.migration = '2026_09_23_100000_create_notifications_table'
);
