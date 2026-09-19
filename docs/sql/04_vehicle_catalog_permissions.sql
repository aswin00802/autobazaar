-- =====================================================================
-- Vehicle catalogue admin permissions for the LIVE database
--
-- Run AFTER the three vehicle migrations have been applied on live:
--   2026_09_18_100000_create_vehicle_catalog_tables
--   2026_09_18_100100_create_vehicle_reviews_and_enquiries_tables
--   2026_09_18_100200_create_finance_lender_rates_table
--
-- Inserts only rows that do not already exist, so it is safe to re-run.
-- Then run:  php artisan permission:cache-reset
-- =====================================================================

SET NAMES utf8mb4;

-- Admin permissions (inserted by name; live ids may differ from local)
INSERT INTO `permissions` (`name`, `group_name`, `guard_name`, `created_at`, `updated_at`)
SELECT p.name, 'vehicles', 'web', NOW(), NOW()
FROM (
  SELECT 'vehicle_catalog' AS name UNION ALL
  SELECT 'add_vehicle_catalog' UNION ALL
  SELECT 'edit_vehicle_catalog' UNION ALL
  SELECT 'delete_vehicle_catalog' UNION ALL
  SELECT 'vehicle_reviews' UNION ALL
  SELECT 'vehicle_leads'
) p
WHERE NOT EXISTS (
  SELECT 1 FROM `permissions` x WHERE x.name = p.name AND x.guard_name = 'web'
);

-- Grant them to the `admin` role (Super Admin already passes every gate)
INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.id, r.id
FROM `permissions` p
JOIN `roles` r ON r.name = 'admin' AND r.guard_name = 'web'
WHERE p.guard_name = 'web'
  AND p.name IN ('vehicle_catalog', 'add_vehicle_catalog', 'edit_vehicle_catalog',
                 'delete_vehicle_catalog', 'vehicle_reviews', 'vehicle_leads');
