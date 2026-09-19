-- =====================================================================
-- AutoBazaar vehicle catalogue tables for the LIVE database
-- New tables only. No existing table is altered. Safe to re-run.
-- Run after 02; then run 04_vehicle_catalog_permissions.sql.
-- Take a full backup before running.
-- =====================================================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `vehicle_models` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `auto_brand_id` bigint(20) unsigned NOT NULL,
  `auto_model_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `model_slug` varchar(80) NOT NULL,
  `tagline` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `badge` varchar(40) DEFAULT NULL,
  `segment` varchar(30) NOT NULL DEFAULT 'passenger',
  `seating` tinyint(4) NOT NULL DEFAULT 3,
  `use_case` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`use_case`)),
  `image` varchar(255) DEFAULT NULL,
  `maintenance_level` varchar(20) NOT NULL DEFAULT 'Low',
  `best_for` varchar(60) DEFAULT NULL,
  `rating_avg` decimal(2,1) NOT NULL DEFAULT 0.0,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `score_overall` decimal(3,1) NOT NULL DEFAULT 0.0,
  `score_rank_note` varchar(80) DEFAULT NULL,
  `score_summary` varchar(255) DEFAULT NULL,
  `warranty_years` tinyint(4) DEFAULT NULL,
  `warranty_km` int(11) DEFAULT NULL,
  `engine_warranty_years` tinyint(4) DEFAULT NULL,
  `engine_warranty_km` int(11) DEFAULT NULL,
  `service_interval_km` int(11) DEFAULT NULL,
  `service_interval_months` tinyint(4) DEFAULT NULL,
  `free_services` tinyint(4) DEFAULT NULL,
  `free_services_km` int(11) DEFAULT NULL,
  `dealer_id` bigint(20) unsigned DEFAULT NULL,
  `popularity` int(11) NOT NULL DEFAULT 0,
  `is_popular` tinyint(4) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicle_models_slug_unique` (`slug`),
  KEY `vehicle_models_auto_brand_id_index` (`auto_brand_id`),
  KEY `vehicle_models_auto_model_id_index` (`auto_model_id`),
  KEY `vehicle_models_dealer_id_index` (`dealer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
;

CREATE TABLE IF NOT EXISTS `vehicle_variants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_model_id` bigint(20) unsigned NOT NULL,
  `name` varchar(60) NOT NULL,
  `fuel_key` varchar(20) NOT NULL DEFAULT 'petrol',
  `fuel_type_id` bigint(20) unsigned DEFAULT NULL,
  `icon` varchar(20) NOT NULL DEFAULT 'fuel',
  `engine_cc` varchar(40) DEFAULT NULL,
  `power` varchar(40) DEFAULT NULL,
  `mileage` decimal(6,1) DEFAULT NULL,
  `mileage_unit` varchar(20) NOT NULL DEFAULT 'km/litre',
  `payload_kg` int(11) DEFAULT NULL,
  `transmission` varchar(40) DEFAULT NULL,
  `ex_showroom_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_default` tinyint(4) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_variants_vehicle_model_id_index` (`vehicle_model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
;

CREATE TABLE IF NOT EXISTS `vehicle_specifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_model_id` bigint(20) unsigned NOT NULL,
  `spec_group` varchar(40) NOT NULL DEFAULT 'General',
  `label` varchar(80) NOT NULL,
  `value` varchar(255) NOT NULL,
  `is_key` tinyint(4) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_specifications_vehicle_model_id_index` (`vehicle_model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
;

CREATE TABLE IF NOT EXISTS `vehicle_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_model_id` bigint(20) unsigned NOT NULL,
  `image` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_images_vehicle_model_id_index` (`vehicle_model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
;

CREATE TABLE IF NOT EXISTS `vehicle_prices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_model_id` bigint(20) unsigned NOT NULL,
  `vehicle_variant_id` bigint(20) unsigned DEFAULT NULL,
  `location` varchar(80) NOT NULL DEFAULT 'Chennai',
  `state` varchar(60) NOT NULL DEFAULT 'Tamil Nadu',
  `ex_showroom` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `insurance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `registration` decimal(10,2) NOT NULL DEFAULT 0.00,
  `other` decimal(10,2) NOT NULL DEFAULT 0.00,
  `accessories` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_default` tinyint(4) NOT NULL DEFAULT 0,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_prices_vehicle_model_id_index` (`vehicle_model_id`),
  KEY `vehicle_prices_vehicle_variant_id_index` (`vehicle_variant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
;

CREATE TABLE IF NOT EXISTS `vehicle_scores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_model_id` bigint(20) unsigned NOT NULL,
  `label` varchar(60) NOT NULL,
  `score` decimal(3,1) NOT NULL DEFAULT 0.0,
  `tone` varchar(20) NOT NULL DEFAULT 'brand',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_scores_vehicle_model_id_index` (`vehicle_model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
;

CREATE TABLE IF NOT EXISTS `vehicle_features` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_model_id` bigint(20) unsigned NOT NULL,
  `icon` varchar(30) NOT NULL DEFAULT 'check',
  `label` varchar(80) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_features_vehicle_model_id_index` (`vehicle_model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
;

CREATE TABLE IF NOT EXISTS `vehicle_suitability` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_model_id` bigint(20) unsigned NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'suitable',
  `label` varchar(80) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_suitability_vehicle_model_id_index` (`vehicle_model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
;

CREATE TABLE IF NOT EXISTS `vehicle_offers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_model_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `value_amount` decimal(10,2) DEFAULT NULL,
  `valid_from` date DEFAULT NULL,
  `valid_to` date DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_offers_vehicle_model_id_index` (`vehicle_model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
;

CREATE TABLE IF NOT EXISTS `vehicle_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_model_id` bigint(20) unsigned NOT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'brochure',
  `title` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_documents_vehicle_model_id_index` (`vehicle_model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
;

CREATE TABLE IF NOT EXISTS `vehicle_stock` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_model_id` bigint(20) unsigned NOT NULL,
  `vehicle_variant_id` bigint(20) unsigned DEFAULT NULL,
  `dealer_id` bigint(20) unsigned DEFAULT NULL,
  `colour` varchar(40) DEFAULT NULL,
  `qty` int(11) NOT NULL DEFAULT 0,
  `delivery_days_min` tinyint(4) NOT NULL DEFAULT 1,
  `delivery_days_max` tinyint(4) NOT NULL DEFAULT 3,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_stock_vehicle_model_id_index` (`vehicle_model_id`),
  KEY `vehicle_stock_vehicle_variant_id_index` (`vehicle_variant_id`),
  KEY `vehicle_stock_dealer_id_index` (`dealer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
;

CREATE TABLE IF NOT EXISTS `vehicle_reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_model_id` bigint(20) unsigned NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(80) NOT NULL,
  `city` varchar(80) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `rating` tinyint(4) NOT NULL DEFAULT 5,
  `title` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `is_verified` tinyint(4) NOT NULL DEFAULT 0,
  `review_status` varchar(20) NOT NULL DEFAULT 'pending',
  `status_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_reviews_vehicle_model_id_index` (`vehicle_model_id`),
  KEY `vehicle_reviews_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
;

CREATE TABLE IF NOT EXISTS `vehicle_enquiries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `enquiry_no` varchar(30) NOT NULL,
  `vehicle_model_id` bigint(20) unsigned DEFAULT NULL,
  `vehicle_variant_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(80) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `state` varchar(60) DEFAULT NULL,
  `district` varchar(80) DEFAULT NULL,
  `city` varchar(80) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `source` varchar(20) NOT NULL DEFAULT 'enquiry',
  `preferred_at` datetime DEFAULT NULL,
  `time_slot` varchar(30) DEFAULT NULL,
  `buying_timeframe` varchar(40) DEFAULT NULL,
  `buying_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`buying_options`)),
  `loan_amount` decimal(10,2) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `lead_status` varchar(20) NOT NULL DEFAULT 'new',
  `assigned_to` int(11) DEFAULT NULL,
  `otp_verified` tinyint(4) NOT NULL DEFAULT 0,
  `page_url` varchar(255) DEFAULT NULL,
  `admin_note` text DEFAULT NULL,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicle_enquiries_enquiry_no_unique` (`enquiry_no`),
  KEY `vehicle_enquiries_vehicle_model_id_index` (`vehicle_model_id`),
  KEY `vehicle_enquiries_user_id_index` (`user_id`),
  KEY `vehicle_enquiries_mobile_index` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
;

CREATE TABLE IF NOT EXISTS `finance_lender_rates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `auto_financiar_id` bigint(20) unsigned NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL DEFAULT 11.50,
  `min_tenure_months` tinyint(4) NOT NULL DEFAULT 12,
  `max_tenure_months` tinyint(4) NOT NULL DEFAULT 60,
  `max_loan_pct` tinyint(4) NOT NULL DEFAULT 85,
  `max_loan_pct_no_cibil` tinyint(4) NOT NULL DEFAULT 70,
  `processing_fee_pct` decimal(4,2) NOT NULL DEFAULT 0.00,
  `documents` text DEFAULT NULL,
  `is_featured` tinyint(4) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `finance_lender_rates_auto_financiar_id_unique` (`auto_financiar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
;

-- Record the three migrations as run so `php artisan migrate` never re-creates these tables
SET @batch := (SELECT IFNULL(MAX(`batch`), 0) + 1 FROM `migrations`);
INSERT INTO `migrations` (`migration`, `batch`)
SELECT m.migration, @batch FROM (
  SELECT '2026_09_18_100000_create_vehicle_catalog_tables' AS migration UNION ALL
  SELECT '2026_09_18_100100_create_vehicle_reviews_and_enquiries_tables' UNION ALL
  SELECT '2026_09_18_100200_create_finance_lender_rates_table'
) m WHERE NOT EXISTS (SELECT 1 FROM `migrations` x WHERE x.migration = m.migration);

-- After the tables exist, seed the catalogue on the server:
--   php artisan db:seed --class=VehicleCatalogSeeder --force
