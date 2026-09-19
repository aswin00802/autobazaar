-- =====================================================================
-- AutoBazaar e-commerce upgrade for the LIVE database
-- New tables only. No existing live table is altered.
-- Safe to re-run: every statement skips what already exists.
-- Take a full backup before running.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- 1. Cart
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `shop_carts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `cart_status` varchar(20) NOT NULL DEFAULT 'active',
  `status_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_carts_user_id_index` (`user_id`),
  KEY `shop_carts_session_id_index` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `shop_cart_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint(20) unsigned NOT NULL,
  `product_model_id` bigint(20) unsigned NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit_mrp` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shop_cart_items_cart_id_product_model_id_unique` (`cart_id`,`product_model_id`),
  KEY `shop_cart_items_cart_id_index` (`cart_id`),
  KEY `shop_cart_items_product_model_id_index` (`product_model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. Address book
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `shop_addresses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `label` varchar(50) NOT NULL DEFAULT 'Home',
  `name` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `address_line_1` varchar(255) NOT NULL,
  `address_line_2` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `is_default` tinyint(4) NOT NULL DEFAULT 0,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_addresses_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. Coupons
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `shop_coupons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  `discount_type` varchar(20) NOT NULL DEFAULT 'percent',
  `discount_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `min_order_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_discount_amount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `per_user_limit` int(11) NOT NULL DEFAULT 1,
  `first_order_only` tinyint(4) NOT NULL DEFAULT 0,
  `valid_from` date DEFAULT NULL,
  `valid_to` date DEFAULT NULL,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shop_coupons_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `shop_coupon_usages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` bigint(20) unsigned NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_coupon_usages_coupon_id_index` (`coupon_id`),
  KEY `shop_coupon_usages_user_id_index` (`user_id`),
  KEY `shop_coupon_usages_order_id_index` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. Orders
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `shop_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(40) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `coupon_code` varchar(50) DEFAULT NULL,
  `shipping_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) NOT NULL DEFAULT 'INR',
  `delivery_option` varchar(30) NOT NULL DEFAULT 'standard',
  `shipping_name` varchar(255) NOT NULL,
  `shipping_mobile` varchar(20) NOT NULL,
  `shipping_address_line_1` varchar(255) NOT NULL,
  `shipping_address_line_2` varchar(255) DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_district` varchar(100) DEFAULT NULL,
  `shipping_state` varchar(100) DEFAULT NULL,
  `shipping_pincode` varchar(10) DEFAULT NULL,
  `payment_gateway` varchar(255) DEFAULT NULL,
  `payment_mode` varchar(255) DEFAULT NULL,
  `payment_status` varchar(30) NOT NULL DEFAULT 'pending',
  `payment_id` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `gateway_signature` varchar(255) DEFAULT NULL,
  `payment_response` json DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `order_status` varchar(30) NOT NULL DEFAULT 'placed',
  `notes` text DEFAULT NULL,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shop_orders_order_number_unique` (`order_number`),
  KEY `shop_orders_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `shop_order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_model_id` bigint(20) unsigned DEFAULT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `brand_name` varchar(100) DEFAULT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit_mrp` decimal(10,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_order_items_order_id_index` (`order_id`),
  KEY `shop_order_items_product_model_id_index` (`product_model_id`),
  KEY `shop_order_items_product_id_index` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `shop_order_status_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `order_status` varchar(30) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_order_status_histories_order_id_index` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. Seed data
-- ---------------------------------------------------------------------

-- AUTO5: 5% off a customer's first order
INSERT IGNORE INTO `shop_coupons`
  (`code`, `label`, `discount_type`, `discount_value`, `min_order_amount`, `per_user_limit`, `first_order_only`, `status_id`, `created_at`, `updated_at`)
VALUES
  ('AUTO5', '5% off on your first order', 'percent', 5.00, 0.00, 1, 1, 1, NOW(), NOW());

-- Admin permissions (inserted by name; live ids may differ from local)
INSERT INTO `permissions` (`name`, `group_name`, `guard_name`, `created_at`, `updated_at`)
SELECT p.name, 'ecommerce', 'web', NOW(), NOW()
FROM (
  SELECT 'ecommerce_orders' AS name UNION ALL
  SELECT 'ecommerce_coupons' UNION ALL
  SELECT 'add_ecommerce_coupons' UNION ALL
  SELECT 'edit_ecommerce_coupons' UNION ALL
  SELECT 'delete_ecommerce_coupons'
) p
WHERE NOT EXISTS (
  SELECT 1 FROM `permissions` x WHERE x.name = p.name AND x.guard_name = 'web'
);

-- Grant them to the `admin` role (Super Admin already has every permission)
INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.id, r.id
FROM `permissions` p
JOIN `roles` r ON r.name = 'admin' AND r.guard_name = 'web'
WHERE p.guard_name = 'web'
  AND p.name IN ('ecommerce_orders', 'ecommerce_coupons', 'add_ecommerce_coupons',
                 'edit_ecommerce_coupons', 'delete_ecommerce_coupons');

-- ---------------------------------------------------------------------
-- 6. Mark the four migrations as run, so `php artisan migrate`
--    does not try to create these tables again
-- ---------------------------------------------------------------------
SET @batch := (SELECT IFNULL(MAX(`batch`), 0) + 1 FROM `migrations`);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT m.migration, @batch
FROM (
  SELECT '2026_09_10_120000_create_shop_carts_table' AS migration UNION ALL
  SELECT '2026_09_10_120100_create_shop_addresses_table' UNION ALL
  SELECT '2026_09_10_120200_create_shop_coupons_table' UNION ALL
  SELECT '2026_09_10_120300_create_shop_orders_table'
) m
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations` x WHERE x.migration = m.migration
);

-- After running: php artisan permission:cache-reset
