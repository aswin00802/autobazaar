-- =====================================================================
-- AutoBazaar speed indexes for the LIVE database            (file 5 of 5)
--
-- Run AFTER 01, 02, 03 and 04.
--
-- What it does: adds 19 indexes on columns the admin lists, the dashboard
-- and the mobile APIs filter on (listing status, user phone, order status...).
-- Indexes only. No table, column or row is created, changed or removed, so
-- no API response and no screen changes — queries just get faster.
--
-- Same result as the Laravel migration
--   2026_09_21_100000_add_performance_indexes
-- and it records that migration as done, so `php artisan migrate` will not
-- try to run it again afterwards.
--
-- Safe to re-run: an index that already exists is skipped.
-- Safe on a server that differs: a missing table or column is skipped.
-- Take a full backup before running.
--
-- phpMyAdmin: use the SQL tab or Import. The DELIMITER lines are needed.
-- =====================================================================

SET NAMES utf8mb4;

DROP PROCEDURE IF EXISTS `ab_add_index`;

DELIMITER $$

CREATE PROCEDURE `ab_add_index`(IN p_table VARCHAR(64), IN p_index VARCHAR(64), IN p_columns VARCHAR(255))
BEGIN
    DECLARE v_wanted INT DEFAULT 0;
    DECLARE v_found  INT DEFAULT 0;

    -- how many columns the index needs, and how many of them this table really has
    SET v_wanted = 1 + LENGTH(p_columns) - LENGTH(REPLACE(p_columns, ',', ''));

    SELECT COUNT(*) INTO v_found
      FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME   = p_table
       AND FIND_IN_SET(COLUMN_NAME, p_columns) > 0;

    IF v_found = v_wanted
       AND NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS
                        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = p_table AND INDEX_NAME = p_index)
       -- a single column that already leads some other index (for example a UNIQUE key) needs nothing more
       AND NOT (v_wanted = 1 AND EXISTS (SELECT 1 FROM information_schema.STATISTICS
                        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = p_table
                          AND COLUMN_NAME = p_columns AND SEQ_IN_INDEX = 1))
    THEN
        SET @ab_sql = CONCAT('ALTER TABLE `', p_table, '` ADD INDEX `', p_index, '` (`', REPLACE(p_columns, ',', '`,`'), '`)');
        PREPARE ab_stmt FROM @ab_sql;
        EXECUTE ab_stmt;
        DEALLOCATE PREPARE ab_stmt;
    END IF;
END$$

DELIMITER ;

-- ---------------------------------------------------------------------
-- Listings (auto_posts): almost every admin list and app search filters here
-- ---------------------------------------------------------------------
CALL ab_add_index('auto_posts', 'perf_auto_posts_auto_usage_status_auto_status_idx', 'auto_usage_status,auto_status');
CALL ab_add_index('auto_posts', 'perf_auto_posts_auto_status_idx', 'auto_status');
CALL ab_add_index('auto_posts', 'perf_auto_posts_user_id_idx', 'user_id');
CALL ab_add_index('auto_posts', 'perf_auto_posts_created_at_idx', 'created_at');

-- ---------------------------------------------------------------------
-- Users and login: every OTP login looks a user up by phone number
-- ---------------------------------------------------------------------
CALL ab_add_index('users', 'perf_users_phone_number_idx', 'phone_number');
CALL ab_add_index('users', 'perf_users_created_at_idx', 'created_at');
CALL ab_add_index('users', 'perf_users_status_idx', 'status');
CALL ab_add_index('customers', 'perf_customers_phone_idx', 'phone');
CALL ab_add_index('auto_otps', 'perf_auto_otps_phone_idx', 'phone');

-- ---------------------------------------------------------------------
-- Leads
-- ---------------------------------------------------------------------
CALL ab_add_index('auto_enquiries', 'perf_auto_enquiries_created_at_idx', 'created_at');
CALL ab_add_index('quotations', 'perf_quotations_created_at_idx', 'created_at');

-- ---------------------------------------------------------------------
-- Requests waiting on an admin (dashboard "Needs Attention")
-- ---------------------------------------------------------------------
CALL ab_add_index('driver_requests', 'perf_driver_requests_status_idx', 'status');
CALL ab_add_index('driver_requests', 'perf_driver_requests_created_at_idx', 'created_at');
CALL ab_add_index('auto_emergency_services', 'perf_auto_emergency_services_status_idx', 'status');
CALL ab_add_index('auto_rto_services', 'perf_auto_rto_services_status_idx', 'status');
CALL ab_add_index('sos_alerts', 'perf_sos_alerts_status_idx', 'status');

-- ---------------------------------------------------------------------
-- Orders and rides
-- ---------------------------------------------------------------------
CALL ab_add_index('sparepart_orders', 'perf_sparepart_orders_order_status_idx', 'order_status');
CALL ab_add_index('sparepart_orders', 'perf_sparepart_orders_user_id_idx', 'user_id');
CALL ab_add_index('ride_requests', 'perf_ride_requests_created_at_idx', 'created_at');
CALL ab_add_index('ride_requests', 'perf_ride_requests_booking_type_status_idx', 'booking_type,status');

DROP PROCEDURE IF EXISTS `ab_add_index`;

-- ---------------------------------------------------------------------
-- Tell Laravel this migration is done
-- ---------------------------------------------------------------------
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_21_100000_add_performance_indexes',
       (SELECT COALESCE(MAX(b.`batch`), 0) + 1 FROM (SELECT `batch` FROM `migrations`) b)
  FROM DUAL
 WHERE NOT EXISTS (SELECT 1 FROM (SELECT `migration` FROM `migrations`) m
                    WHERE m.`migration` = '2026_09_21_100000_add_performance_indexes');

-- ---------------------------------------------------------------------
-- Check (optional): should list 19 or 20 rows
-- ---------------------------------------------------------------------
-- SELECT TABLE_NAME, INDEX_NAME FROM information_schema.STATISTICS
--  WHERE TABLE_SCHEMA = DATABASE() AND INDEX_NAME LIKE 'perf\_%'
--  GROUP BY TABLE_NAME, INDEX_NAME ORDER BY TABLE_NAME, INDEX_NAME;
