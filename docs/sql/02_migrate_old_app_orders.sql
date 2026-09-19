-- =====================================================================
-- Migrate the old mobile-app orders (sparepart_orders) into the new
-- order tables (shop_orders / shop_order_items / shop_order_status_histories).
--
-- Run once, after 01_ecommerce_tables.sql.
-- Safe to re-run: every statement skips rows already migrated.
-- Nothing is deleted from sparepart_orders — retire that table later,
-- after the app has been switched to the new API.
--
-- Facts about the old rows (11 on live as of Jan 2026):
--   * one row = one product; product_id -> product_brand_models.id
--   * all pending, no payment, no address captured
--   * order_id (ORD-...) is kept as the new order_number
-- =====================================================================

SET NAMES utf8mb4;

-- 1. One shop_order per old row
INSERT INTO `shop_orders`
  (`order_number`, `user_id`, `subtotal`, `total_amount`, `currency`, `delivery_option`,
   `shipping_name`, `shipping_mobile`, `shipping_address_line_1`,
   `payment_status`, `order_status`, `notes`, `status_id`,
   `created_at`, `updated_at`, `ip_address`)
SELECT
  o.order_id,
  o.user_id,
  o.qnty * COALESCE(pbm.offer_price, pbm.price, 0),
  o.qnty * COALESCE(pbm.offer_price, pbm.price, 0),
  'INR',
  'standard',
  COALESCE(NULLIF(u.name, ''), 'App customer'),
  COALESCE(u.phone_number, ''),
  'Address not captured in app order - confirm with customer',
  'pending',
  'placed',
  CONCAT('Migrated from app order #', o.id),
  1,
  o.created_at,
  COALESCE(o.updated_at, o.created_at),
  o.ip_address
FROM `sparepart_orders` o
LEFT JOIN `product_brand_models` pbm ON pbm.id = o.product_id
LEFT JOIN `users` u ON u.id = o.user_id
WHERE o.order_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `shop_orders` s WHERE s.order_number = o.order_id);

-- 2. One line item per order (product snapshot: name, brand, first image)
INSERT INTO `shop_order_items`
  (`order_id`, `product_model_id`, `product_id`, `product_name`, `product_image`, `brand_name`,
   `qty`, `unit_price`, `unit_mrp`, `line_total`, `status_id`, `created_at`, `updated_at`)
SELECT
  s.id,
  o.product_id,
  pbm.product_id,
  COALESCE(p.name, CONCAT('Product #', o.product_id)),
  COALESCE(
    (SELECT i.image FROM `product_brand_model_images` i WHERE i.product_brand_model_id = pbm.id ORDER BY i.id LIMIT 1),
    p.image
  ),
  b.brand_name,
  o.qnty,
  COALESCE(pbm.offer_price, pbm.price, 0),
  COALESCE(pbm.price, 0),
  o.qnty * COALESCE(pbm.offer_price, pbm.price, 0),
  1,
  o.created_at,
  COALESCE(o.updated_at, o.created_at)
FROM `sparepart_orders` o
JOIN `shop_orders` s ON s.order_number = o.order_id
LEFT JOIN `product_brand_models` pbm ON pbm.id = o.product_id
LEFT JOIN `products` p ON p.id = pbm.product_id
LEFT JOIN `auto_brands` b ON b.id = pbm.brand_id
WHERE NOT EXISTS (SELECT 1 FROM `shop_order_items` i WHERE i.order_id = s.id);

-- 3. Tracking timeline: a single "placed" entry dated when the app order was made
INSERT INTO `shop_order_status_histories` (`order_id`, `order_status`, `note`, `created_at`, `updated_at`)
SELECT s.id, 'placed', 'Migrated from app order', s.created_at, s.created_at
FROM `shop_orders` s
WHERE s.notes LIKE 'Migrated from app order%'
  AND NOT EXISTS (SELECT 1 FROM `shop_order_status_histories` h WHERE h.order_id = s.id);

-- 4. Check
SELECT s.order_number, s.user_id, s.shipping_name, s.total_amount, s.order_status, s.created_at,
       i.product_name, i.brand_name, i.qty
FROM `shop_orders` s
JOIN `shop_order_items` i ON i.order_id = s.id
WHERE s.notes LIKE 'Migrated from app order%'
ORDER BY s.created_at;

-- Known duplicates to review by hand in admin -> E-commerce -> Orders:
--   ORD-6963DF38DD44C / ORD-6963DF3D42219  (user 4560, same product, 5 s apart)
--   ORD-695D48AB79093 / ORD-695D48B20370B / ORD-695D48B6DA2BB  (user 2042, 3 products in 11 s = one order)
