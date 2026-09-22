-- ---------------------------------------------------------------------------
-- 06_catalogue_content.sql
--
-- Run this AFTER 01 to 05.
--
-- Files 01 to 05 create the vehicle catalogue tables but put nothing in them.
-- An empty catalogue means an empty New Autos page, an empty Compare page and
-- no lenders under Finance Options. This file fills them.
--
-- It holds no personal data: no customers, no listings, no orders. Only the
-- catalogue itself.
--
-- Safe to run twice: every row is INSERT IGNORE, so anything already there is
-- left alone.
-- ---------------------------------------------------------------------------

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------------------------
-- vehicle_models — the 78 autos behind New Autos, Compare and the model pages
-- --------------------------------------------------------------------------
INSERT IGNORE INTO `vehicle_models` (`id`, `auto_brand_id`, `auto_model_id`, `name`, `slug`, `model_slug`, `tagline`, `description`, `badge`, `segment`, `seating`, `use_case`, `image`, `maintenance_level`, `best_for`, `rating_avg`, `rating_count`, `score_overall`, `score_rank_note`, `score_summary`, `warranty_years`, `warranty_km`, `engine_warranty_years`, `engine_warranty_km`, `service_interval_km`, `service_interval_months`, `free_services`, `free_services_km`, `dealer_id`, `popularity`, `is_popular`, `sort_order`, `meta_title`, `meta_description`, `status_id`, `created_by`, `created_at`, `updated_at`, `ip_address`) VALUES
  (1, 3, 25, 'TVS King Deluxe', 'tvs-king-deluxe', 'king-deluxe', 'Reliable. Efficient. Built for Your Success.', 'The TVS King Deluxe is designed for superior mileage, low maintenance and high earnings. Ideal for city and commercial usage, it offers comfort, durability and better performance for drivers.', 'Most Popular', 'passenger', 3, '[\"commercial\",\"high-mileage\"]', 'assets/image/auto_brands/tvs.png', 'Low', 'All Rounder', '4.7', 3, '8.4', '#2 in Petrol Segment', 'Ranked among the best in its segment for mileage, low operating cost and high reliability.', 2, 72000, 2, 72000, 5000, 6, 3, 15000, NULL, 100, 1, 1, NULL, NULL, 1, NULL, '2026-09-18 13:38:38', '2026-09-19 18:17:38', NULL),
  (2, 1, 1, 'Bajaj RE', 'bajaj-re', 're', 'The mileage leader, trusted on every route.', 'The Bajaj RE is the segment benchmark for fuel efficiency, with a proven service network and among the lowest running costs available to auto drivers.', NULL, 'passenger', 3, '[\"commercial\",\"high-mileage\",\"low-maintenance\"]', 'assets/image/auto_brands/bajaj_auto.png', 'Low', 'High Mileage', '4.7', 3, '8.6', '#1 in Mileage', 'Best-in-class fuel efficiency and the widest service network in the segment.', 2, 72000, 2, 72000, 5000, 6, 3, 15000, NULL, 95, 1, 2, NULL, NULL, 1, NULL, '2026-09-18 13:38:39', '2026-09-19 18:17:38', NULL),
  (3, 2, 19, 'Piaggio Ape Xtra', 'piaggio-ape-xtra', 'ape-xtra', 'Built tough for heavy-duty daily work.', 'The Piaggio Ape Xtra pairs the largest engine in its class with a reinforced chassis, making it the pick for heavy-duty and long-shift commercial use.', NULL, 'passenger', 3, '[\"commercial\"]', 'assets/image/auto_brands/piaggio_auto.png', 'Moderate', 'Heavy Duty', '4.7', 3, '8.1', '#1 in Heavy Duty', 'The strongest engine in the segment, best suited to sustained heavy-duty running.', 2, 72000, 2, 72000, 5000, 6, 3, 15000, NULL, 88, 1, 3, NULL, NULL, 1, NULL, '2026-09-18 13:38:39', '2026-09-19 18:17:38', NULL),
  (4, 33, 61, 'Mahindra Treo Plus', 'mahindra-treo-plus', 'treo-plus', 'Go electric. Cut your running cost to a fraction.', 'The Mahindra Treo Plus is a lithium-ion electric autorickshaw with four-passenger seating and the lowest running cost in the line-up — around ₹1,200 a month in energy.', 'EV', 'passenger', 4, '[\"low-maintenance\",\"personal\"]', 'assets/image/auto_brands/montra_auto1f.png', 'Very Low', 'Low Running Cost', '4.7', 3, '8.7', '#1 in Electric Segment', 'Unmatched running cost and refinement; the higher purchase price pays back over long daily distances.', 2, 72000, 2, 72000, 5000, 6, 3, 15000, NULL, 82, 1, 4, NULL, NULL, 1, NULL, '2026-09-18 13:38:40', '2026-09-19 18:17:38', NULL),
  (5, 33, 31, 'Mahindra Alfa', 'mahindra-alfa', 'alfa', 'Diesel torque for the long haul.', 'The Mahindra Alfa offers diesel torque and a load-friendly body, favoured by drivers running longer routes and heavier daily loads.', NULL, 'passenger', 3, '[\"commercial\"]', 'assets/image/auto_brands/mahindra.png', 'Moderate', 'Long Distance', '4.7', 3, '7.9', '#1 in Diesel Segment', 'Strong low-end torque and load capacity, at the cost of refinement and service intervals.', 2, 72000, 2, 72000, 5000, 6, 3, 15000, NULL, 74, 1, 5, NULL, NULL, 1, NULL, '2026-09-18 13:38:40', '2026-09-19 18:17:38', NULL),
  (6, 4, 50, 'Atul Gemini', 'atul-gemini', 'gemini', 'Value-first, easy on the pocket.', 'The Atul Gemini is the value pick of the segment — a low entry price and simple mechanicals that keep servicing costs down for owner-drivers.', NULL, 'passenger', 3, '[\"personal\",\"low-maintenance\"]', 'assets/image/auto_brands/atul.png', 'Low', 'Best Value', '4.7', 3, '7.6', '#1 in Value for Money', 'The lowest cost of entry in the segment, with simple mechanicals that are cheap to service.', 2, 72000, 2, 72000, 5000, 6, 3, 15000, NULL, 66, 1, 6, NULL, NULL, 1, NULL, '2026-09-18 13:38:40', '2026-09-19 18:17:38', NULL),
  (7, 1, 2, 'Bajaj Maxima Z', 'bajaj-maxima-z', 'maxima-z', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:40', '2026-09-18 13:38:40', NULL),
  (8, 1, 3, 'Bajaj Maxima X Wide', 'bajaj-maxima-x-wide', 'maxima-x-wide', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:40', '2026-09-18 13:38:40', NULL),
  (9, 1, 4, 'Bajaj Maxima C', 'bajaj-maxima-c', 'maxima-c', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:40', '2026-09-18 13:38:40', NULL),
  (10, 1, 5, 'Bajaj RE E-TEC 9.0', 'bajaj-re-e-tec-90', 're-e-tec-90', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:40', '2026-09-18 13:38:40', NULL),
  (11, 1, 6, 'Bajaj GoGo P5009', 'bajaj-gogo-p5009', 'gogo-p5009', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:40', '2026-09-18 13:38:40', NULL),
  (12, 1, 7, 'Bajaj GoGo P5012', 'bajaj-gogo-p5012', 'gogo-p5012', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:40', '2026-09-18 13:38:40', NULL),
  (13, 1, 8, 'Bajaj GoGo P7012', 'bajaj-gogo-p7012', 'gogo-p7012', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:40', '2026-09-18 13:38:40', NULL),
  (14, 1, 9, 'Bajaj GoGo C9012', 'bajaj-gogo-c9012', 'gogo-c9012', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:40', '2026-09-18 13:38:40', NULL),
  (15, 1, 10, 'Bajaj CARGO E-Tec 9.0', 'bajaj-cargo-e-tec-90', 'cargo-e-tec-90', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:40', '2026-09-18 13:38:40', NULL),
  (16, 1, 11, 'Bajaj CARGO E-Tec 12', 'bajaj-cargo-e-tec-12', 'cargo-e-tec-12', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:40', '2026-09-18 13:38:40', NULL),
  (17, 2, 12, 'Piaggio Ape City', 'piaggio-ape-city', 'ape-city', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:40', '2026-09-18 13:38:40', NULL),
  (18, 2, 13, 'Piaggio Ape City Plus', 'piaggio-ape-city-plus', 'ape-city-plus', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:40', '2026-09-18 13:38:40', NULL),
  (19, 2, 14, 'Piaggio Ape Metro', 'piaggio-ape-metro', 'ape-metro', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (20, 2, 15, 'Piaggio Ape Auto Classic or Classic', 'piaggio-ape-auto-classic-or-classic', 'ape-auto-classic-or-classic', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (21, 2, 16, 'Piaggio Ape Auto DX', 'piaggio-ape-auto-dx', 'ape-auto-dx', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (22, 2, 17, 'Piaggio Ape NXT Plus', 'piaggio-ape-nxt-plus', 'ape-nxt-plus', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (23, 2, 18, 'Piaggio Ape City 200', 'piaggio-ape-city-200', 'ape-city-200', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (24, 2, 20, 'Piaggio Ape Xtra LD', 'piaggio-ape-xtra-ld', 'ape-xtra-ld', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (25, 2, 21, 'Piaggio Ape Xtra Passenger', 'piaggio-ape-xtra-passenger', 'ape-xtra-passenger', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (26, 2, 22, 'Piaggio Ape Xtra LDX / Auto DXL', 'piaggio-ape-xtra-ldx-auto-dxl', 'ape-xtra-ldx-auto-dxl', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (27, 2, 23, 'Piaggio Ape E-City', 'piaggio-ape-e-city', 'ape-e-city', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (28, 2, 24, 'Piaggio Ape E-City FX', 'piaggio-ape-e-city-fx', 'ape-e-city-fx', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (29, 3, 26, 'Tvs King Duramax', 'tvs-king-duramax', 'king-duramax', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (30, 3, 27, 'Tvs King Duramax Plus', 'tvs-king-duramax-plus', 'king-duramax-plus', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (31, 3, 28, 'Tvs King Kargo', 'tvs-king-kargo', 'king-kargo', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (32, 3, 29, 'Tvs King EV Max', 'tvs-king-ev-max', 'king-ev-max', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (33, 3, 30, 'Tvs King EPI', 'tvs-king-epi', 'king-epi', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (34, 33, 32, 'Mahindra Alfa Plus Diesel', 'mahindra-alfa-plus-diesel', 'alfa-plus-diesel', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (35, 33, 33, 'Mahindra Alfa Load', 'mahindra-alfa-load', 'alfa-load', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (36, 33, 34, 'Mahindra E-Alfa Mini', 'mahindra-e-alfa-mini', 'e-alfa-mini', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (37, 33, 35, 'Mahindra E-Alfa Super', 'mahindra-e-alfa-super', 'e-alfa-super', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (38, 33, 36, 'Mahindra E-Alfa Plus', 'mahindra-e-alfa-plus', 'e-alfa-plus', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (39, 33, 37, 'Mahindra Treo', 'mahindra-treo', 'treo', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (40, 33, 38, 'Mahindra Treo Yaari', 'mahindra-treo-yaari', 'treo-yaari', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (41, 33, 39, 'Mahindra Treo Zor', 'mahindra-treo-zor', 'treo-zor', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (42, 33, 40, 'Mahindra Zor Grand', 'mahindra-zor-grand', 'zor-grand', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (43, 4, 41, 'Atul Rik Petrol', 'atul-rik-petrol', 'rik-petrol', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (44, 4, 42, 'Atul Rik CNG', 'atul-rik-cng', 'rik-cng', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (45, 4, 43, 'Atul Rik LPG', 'atul-rik-lpg', 'rik-lpg', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (46, 4, 44, 'Atul Gem Paxx', 'atul-gem-paxx', 'gem-paxx', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (47, 4, 45, 'Atul Gem Cargo', 'atul-gem-cargo', 'gem-cargo', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (48, 4, 46, 'Atul Gem Cargo CNG Aqua 6F', 'atul-gem-cargo-cng-aqua-6f', 'gem-cargo-cng-aqua-6f', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (49, 4, 47, 'Atul Shakti Cargo', 'atul-shakti-cargo', 'shakti-cargo', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (50, 4, 48, 'Atul Elite Passenger', 'atul-elite-passenger', 'elite-passenger', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL);

INSERT IGNORE INTO `vehicle_models` (`id`, `auto_brand_id`, `auto_model_id`, `name`, `slug`, `model_slug`, `tagline`, `description`, `badge`, `segment`, `seating`, `use_case`, `image`, `maintenance_level`, `best_for`, `rating_avg`, `rating_count`, `score_overall`, `score_rank_note`, `score_summary`, `warranty_years`, `warranty_km`, `engine_warranty_years`, `engine_warranty_km`, `service_interval_km`, `service_interval_months`, `free_services`, `free_services_km`, `dealer_id`, `popularity`, `is_popular`, `sort_order`, `meta_title`, `meta_description`, `status_id`, `created_by`, `created_at`, `updated_at`, `ip_address`) VALUES
  (51, 4, 49, 'Atul Elite Cargo', 'atul-elite-cargo', 'elite-cargo', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (52, 4, 51, 'Atul RIK + PLUS', 'atul-rik-plus', 'rik-plus', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (53, 24, 52, 'Euler HiRANGE MAXX', 'euler-hirange-maxx', 'hirange-maxx', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (54, 24, 53, 'Euler HiCITY MAXX', 'euler-hicity-maxx', 'hicity-maxx', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (55, 24, 54, 'Euler HiRANGE PLUS', 'euler-hirange-plus', 'hirange-plus', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (56, 24, 55, 'Euler HiRANGE', 'euler-hirange', 'hirange', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (57, 24, 56, 'Euler HiCITY PLUS', 'euler-hicity-plus', 'hicity-plus', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (58, 24, 57, 'Euler HiLoad EV', 'euler-hiload-ev', 'hiload-ev', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (59, 34, 58, 'Osm stream nrg', 'osm-stream-nrg', 'stream-nrg', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (60, 34, 59, 'Osm Stream', 'osm-stream', 'stream', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (61, 34, 60, 'Osm RAGE+ NRG', 'osm-rage-nrg', 'rage-nrg', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (62, 33, 62, 'Mahindra TREO PLUS HRT SM', 'mahindra-treo-plus-hrt-sm', 'treo-plus-hrt-sm', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (63, 33, 63, 'Mahindra TREO ZOR FB', 'mahindra-treo-zor-fb', 'treo-zor-fb', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (64, 33, 64, 'Mahindra TREO ZOR PU', 'mahindra-treo-zor-pu', 'treo-zor-pu', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (65, 33, 65, 'Mahindra TREO ZOR DV', 'mahindra-treo-zor-dv', 'treo-zor-dv', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (66, 33, 66, 'Mahindra TREO YAARI CARGO FB', 'mahindra-treo-yaari-cargo-fb', 'treo-yaari-cargo-fb', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (67, 33, 67, 'Mahindra TREO YAARI CARGO PU', 'mahindra-treo-yaari-cargo-pu', 'treo-yaari-cargo-pu', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (68, 33, 68, 'Mahindra ZOR GRAND PU', 'mahindra-zor-grand-pu', 'zor-grand-pu', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (69, 33, 69, 'Mahindra ZOR GRAND DV+', 'mahindra-zor-grand-dv', 'zor-grand-dv', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (70, 33, 70, 'Mahindra ALFA DUO', 'mahindra-alfa-duo', 'alfa-duo', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (71, 33, 71, 'Mahindra ALFA DUO PLUS', 'mahindra-alfa-duo-plus', 'alfa-duo-plus', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (72, 24, 72, 'Euler HIGH CITY SR EV', 'euler-high-city-sr-ev', 'high-city-sr-ev', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (73, 24, 73, 'Euler HiCITY TR EV', 'euler-hicity-tr-ev', 'hicity-tr-ev', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (74, 24, 74, 'Euler NEO HIGHRANGE SR EV', 'euler-neo-highrange-sr-ev', 'neo-highrange-sr-ev', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (75, 24, 75, 'Euler NEO HIRANGE TR EV', 'euler-neo-hirange-tr-ev', 'neo-hirange-tr-ev', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (76, 24, 76, 'Euler NEO HIGHRANGE XR EV', 'euler-neo-highrange-xr-ev', 'neo-highrange-xr-ev', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (77, 24, 77, 'Euler HILOAD EV TR PV 120', 'euler-hiload-ev-tr-pv-120', 'hiload-ev-tr-pv-120', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (78, 35, 78, 'Montra ELECTRIC', 'montra-electric', 'electric', NULL, NULL, NULL, 'passenger', 3, NULL, NULL, 'Low', NULL, '0.0', 0, '0.0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, 0, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL);

-- --------------------------------------------------------------------------
-- vehicle_variants — each model's variants
-- --------------------------------------------------------------------------
INSERT IGNORE INTO `vehicle_variants` (`id`, `vehicle_model_id`, `name`, `fuel_key`, `fuel_type_id`, `icon`, `engine_cc`, `power`, `mileage`, `mileage_unit`, `payload_kg`, `transmission`, `ex_showroom_price`, `is_default`, `sort_order`, `status_id`, `created_at`, `updated_at`) VALUES
  (1, 1, 'Petrol', 'petrol', 1, 'fuel', '225.8 cc, 4 Stroke', '9.5 PS @ 5,500 rpm', '32.0', 'km/litre', NULL, '4 Speed Constant Mesh', '220000.00', 1, 0, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (2, 1, 'CNG', 'cng', 3, 'gas', '225.8 cc, 4 Stroke', '9.5 PS @ 5,500 rpm', '26.0', 'km/kg', NULL, '4 Speed Constant Mesh', '220000.00', 0, 1, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (3, 1, 'Electric', 'electric', 5, 'bolt', NULL, '9.5 PS @ 5,500 rpm', '45.0', 'km/unit', NULL, '4 Speed Constant Mesh', '220000.00', 0, 2, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (4, 2, 'Petrol', 'petrol', 1, 'fuel', '198.88 cc, 4 Stroke', '8.6 PS @ 5,000 rpm', '18.0', 'km/litre', NULL, '4 Speed Constant Mesh', '245000.00', 1, 0, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (5, 2, 'CNG', 'cng', 3, 'gas', '198.88 cc, 4 Stroke', '8.6 PS @ 5,000 rpm', '32.0', 'km/kg', NULL, '4 Speed Constant Mesh', '245000.00', 0, 1, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (6, 2, 'Electric', 'electric', 5, 'bolt', NULL, '8.6 PS @ 5,000 rpm', NULL, 'km/litre', NULL, '4 Speed Constant Mesh', '245000.00', 0, 2, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (7, 3, 'Petrol', 'petrol', 1, 'fuel', '230.9 cc, 4 Stroke', '8.5 PS @ 4,000 rpm', '18.0', 'km/litre', NULL, '4 Speed + Reverse', '260000.00', 1, 0, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (8, 3, 'CNG', 'cng', 3, 'gas', '230.9 cc, 4 Stroke', '8.5 PS @ 4,000 rpm', '28.0', 'km/kg', NULL, '4 Speed + Reverse', '260000.00', 0, 1, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (9, 3, 'Electric', 'electric', 5, 'bolt', NULL, '8.5 PS @ 4,000 rpm', NULL, 'km/litre', NULL, '4 Speed + Reverse', '260000.00', 0, 2, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (10, 4, 'Electric', 'electric', 5, 'bolt', NULL, '8.0 kW', '45.0', 'km/unit', NULL, NULL, '370000.00', 1, 0, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (11, 5, 'Diesel', 'diesel', 2, 'fuel', '436 cc, Direct Injection Diesel', '8.9 PS @ 3,600 rpm', '35.0', 'km/litre', NULL, '4 Speed + Reverse', '255000.00', 1, 0, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (12, 5, 'CNG', 'cng', 3, 'gas', '436 cc, Direct Injection Diesel', '8.9 PS @ 3,600 rpm', '24.0', 'km/kg', NULL, '4 Speed + Reverse', '255000.00', 0, 1, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (13, 6, 'Petrol', 'petrol', 1, 'fuel', '236.7 cc, 4 Stroke', '8.2 PS @ 3,600 rpm', '17.0', 'km/litre', NULL, '4 Speed + Reverse', '249000.00', 1, 0, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (14, 6, 'CNG', 'cng', 3, 'gas', '236.7 cc, 4 Stroke', '8.2 PS @ 3,600 rpm', '25.0', 'km/kg', NULL, '4 Speed + Reverse', '249000.00', 0, 1, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (15, 6, 'Electric', 'electric', 5, 'bolt', NULL, '8.2 PS @ 3,600 rpm', NULL, 'km/litre', NULL, '4 Speed + Reverse', '249000.00', 0, 2, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40');

-- --------------------------------------------------------------------------
-- vehicle_prices — on-road prices
-- --------------------------------------------------------------------------
INSERT IGNORE INTO `vehicle_prices` (`id`, `vehicle_model_id`, `vehicle_variant_id`, `location`, `state`, `ex_showroom`, `rto`, `insurance`, `registration`, `other`, `accessories`, `is_default`, `status_id`, `created_at`, `updated_at`) VALUES
  (1, 1, NULL, 'Chennai', 'Tamil Nadu', '220000.00', '18500.00', '9800.00', '3000.00', '2700.00', '0.00', 1, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (2, 2, NULL, 'Chennai', 'Tamil Nadu', '245000.00', '18200.00', '9600.00', '3000.00', '2600.00', '0.00', 1, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (3, 3, NULL, 'Chennai', 'Tamil Nadu', '260000.00', '19100.00', '10200.00', '3000.00', '2800.00', '0.00', 1, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (4, 4, NULL, 'Chennai', 'Tamil Nadu', '370000.00', '0.00', '12400.00', '3000.00', '2900.00', '0.00', 1, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (5, 5, NULL, 'Chennai', 'Tamil Nadu', '255000.00', '18800.00', '10000.00', '3000.00', '2700.00', '0.00', 1, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (6, 6, NULL, 'Chennai', 'Tamil Nadu', '249000.00', '18000.00', '9500.00', '3000.00', '2500.00', '0.00', 1, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40');

-- --------------------------------------------------------------------------
-- vehicle_specifications — the specification tables
-- --------------------------------------------------------------------------
INSERT IGNORE INTO `vehicle_specifications` (`id`, `vehicle_model_id`, `spec_group`, `label`, `value`, `is_key`, `sort_order`, `created_at`, `updated_at`) VALUES
  (1, 1, 'General', 'Engine', '225.8 cc, 4 Stroke', 1, 0, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (2, 1, 'General', 'Max Power', '9.5 PS @ 5,500 rpm', 1, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (3, 1, 'General', 'Max Torque', '15.5 Nm @ 4,000 rpm', 1, 2, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (4, 1, 'General', 'Fuel Type', 'Petrol | CNG | LPG', 1, 3, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (5, 1, 'General', 'Mileage', '30 – 35 kmpl (Petrol)', 1, 4, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (6, 1, 'General', 'Transmission', '4 Speed Constant Mesh', 1, 5, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (7, 1, 'General', 'Seating Capacity', 'Driver + 3 Passengers', 0, 6, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (8, 1, 'General', 'Dimensions (L x W x H)', '2635 x 1300 x 1700 mm', 0, 7, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (9, 1, 'General', 'Ground Clearance', '180 mm', 0, 8, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (10, 1, 'General', 'Kerb Weight', '394 kg', 0, 9, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (11, 1, 'General', 'Fuel Tank Capacity', '10.5 litres', 0, 10, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (12, 1, 'General', 'Warranty', '2 Years / 50,000 km', 0, 11, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (13, 2, 'General', 'Engine', '198.88 cc, 4 Stroke', 1, 0, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (14, 2, 'General', 'Max Power', '8.6 PS @ 5,000 rpm', 1, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (15, 2, 'General', 'Max Torque', '16.6 Nm @ 3,500 rpm', 1, 2, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (16, 2, 'General', 'Fuel Type', 'Petrol | CNG | LPG', 1, 3, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (17, 2, 'General', 'Mileage', '32 km/kg (CNG)', 1, 4, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (18, 2, 'General', 'Transmission', '4 Speed Constant Mesh', 1, 5, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (19, 2, 'General', 'Seating Capacity', 'Driver + 3 Passengers', 0, 6, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (20, 2, 'General', 'Ground Clearance', '190 mm', 0, 7, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (21, 2, 'General', 'Kerb Weight', '379 kg', 0, 8, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (22, 2, 'General', 'Fuel Tank Capacity', '8 litres', 0, 9, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (23, 2, 'General', 'Warranty', '2 Years / 50,000 km', 0, 10, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (24, 3, 'General', 'Engine', '230.9 cc, 4 Stroke', 1, 0, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (25, 3, 'General', 'Max Power', '8.5 PS @ 4,000 rpm', 1, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (26, 3, 'General', 'Max Torque', '17.6 Nm @ 2,750 rpm', 1, 2, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (27, 3, 'General', 'Fuel Type', 'Petrol | CNG', 1, 3, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (28, 3, 'General', 'Mileage', '28 km/kg (CNG)', 1, 4, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (29, 3, 'General', 'Transmission', '4 Speed + Reverse', 1, 5, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (30, 3, 'General', 'Seating Capacity', 'Driver + 3 Passengers', 0, 6, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (31, 3, 'General', 'Ground Clearance', '200 mm', 0, 7, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (32, 3, 'General', 'Kerb Weight', '410 kg', 0, 8, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (33, 3, 'General', 'Fuel Tank Capacity', '9 litres', 0, 9, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (34, 3, 'General', 'Warranty', '2 Years / 50,000 km', 0, 10, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (35, 4, 'General', 'Motor', 'Electric Motor (48V)', 1, 0, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (36, 4, 'General', 'Max Power', '8.0 kW', 1, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (37, 4, 'General', 'Battery', '7.37 kWh Lithium-Ion', 1, 2, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (38, 4, 'General', 'Fuel Type', 'Electric', 1, 3, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (39, 4, 'General', 'Range', '120 – 140 km per charge', 1, 4, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (40, 4, 'General', 'Charging Time', '3 hours 50 minutes', 1, 5, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (41, 4, 'General', 'Seating Capacity', 'Driver + 4 Passengers', 0, 6, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (42, 4, 'General', 'Ground Clearance', '170 mm', 0, 7, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (43, 4, 'General', 'Kerb Weight', '445 kg', 0, 8, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (44, 4, 'General', 'Warranty', '3 Years / 80,000 km', 0, 9, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (45, 5, 'General', 'Engine', '436 cc, Direct Injection Diesel', 1, 0, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (46, 5, 'General', 'Max Power', '8.9 PS @ 3,600 rpm', 1, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (47, 5, 'General', 'Max Torque', '19.6 Nm @ 2,200 rpm', 1, 2, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (48, 5, 'General', 'Fuel Type', 'Diesel | CNG', 1, 3, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (49, 5, 'General', 'Mileage', '35 kmpl (Diesel)', 1, 4, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (50, 5, 'General', 'Transmission', '4 Speed + Reverse', 1, 5, '2026-09-18 13:38:40', '2026-09-18 13:38:40');

INSERT IGNORE INTO `vehicle_specifications` (`id`, `vehicle_model_id`, `spec_group`, `label`, `value`, `is_key`, `sort_order`, `created_at`, `updated_at`) VALUES
  (51, 5, 'General', 'Seating Capacity', 'Driver + 3 Passengers', 0, 6, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (52, 5, 'General', 'Ground Clearance', '185 mm', 0, 7, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (53, 5, 'General', 'Kerb Weight', '425 kg', 0, 8, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (54, 5, 'General', 'Fuel Tank Capacity', '10 litres', 0, 9, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (55, 5, 'General', 'Warranty', '2 Years / 50,000 km', 0, 10, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (56, 6, 'General', 'Engine', '236.7 cc, 4 Stroke', 1, 0, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (57, 6, 'General', 'Max Power', '8.2 PS @ 3,600 rpm', 1, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (58, 6, 'General', 'Max Torque', '16.2 Nm @ 2,400 rpm', 1, 2, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (59, 6, 'General', 'Fuel Type', 'Petrol | CNG', 1, 3, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (60, 6, 'General', 'Mileage', '25 km/kg (CNG)', 1, 4, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (61, 6, 'General', 'Transmission', '4 Speed + Reverse', 1, 5, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (62, 6, 'General', 'Seating Capacity', 'Driver + 3 Passengers', 0, 6, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (63, 6, 'General', 'Ground Clearance', '175 mm', 0, 7, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (64, 6, 'General', 'Kerb Weight', '398 kg', 0, 8, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (65, 6, 'General', 'Fuel Tank Capacity', '8 litres', 0, 9, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (66, 6, 'General', 'Warranty', '2 Years / 40,000 km', 0, 10, '2026-09-18 13:38:40', '2026-09-18 13:38:40');

-- --------------------------------------------------------------------------
-- vehicle_features — feature lists
-- --------------------------------------------------------------------------
INSERT IGNORE INTO `vehicle_features` (`id`, `vehicle_model_id`, `icon`, `label`, `sort_order`, `created_at`, `updated_at`) VALUES
  (1, 1, 'fuel', 'Best Mileage', 0, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (2, 1, 'wrench', 'Low Maintenance', 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (3, 1, 'rupee', 'High Earnings', 2, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (4, 1, 'shield', 'Trusted Brand', 3, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (5, 2, 'fuel', 'Best Mileage', 0, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (6, 2, 'wrench', 'Low Maintenance', 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (7, 2, 'shield', 'Trusted Brand', 2, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (8, 3, 'rupee', 'High Earnings', 0, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (9, 3, 'shield', 'Trusted Brand', 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (10, 4, 'bolt', 'Zero Emission', 0, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (11, 4, 'wrench', 'Very Low Maintenance', 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (12, 4, 'rupee', 'Lowest Running Cost', 2, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (13, 4, 'shield', 'Trusted Brand', 3, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (14, 5, 'rupee', 'High Earnings', 0, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (15, 5, 'shield', 'Trusted Brand', 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (16, 6, 'wrench', 'Low Maintenance', 0, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (17, 6, 'rupee', 'Best Value', 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40');

-- --------------------------------------------------------------------------
-- vehicle_images — model photos
-- --------------------------------------------------------------------------
INSERT IGNORE INTO `vehicle_images` (`id`, `vehicle_model_id`, `image`, `caption`, `sort_order`, `status_id`, `created_at`, `updated_at`) VALUES
  (1, 1, 'assets/image/auto_brands/tvs.png', NULL, 0, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (2, 2, 'assets/image/auto_brands/bajaj_auto.png', NULL, 0, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (3, 3, 'assets/image/auto_brands/piaggio_auto.png', NULL, 0, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (4, 4, 'assets/image/auto_brands/montra_auto1f.png', NULL, 0, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (5, 5, 'assets/image/auto_brands/mahindra.png', NULL, 0, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (6, 6, 'assets/image/auto_brands/atul.png', NULL, 0, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40');

-- --------------------------------------------------------------------------
-- vehicle_offers — the offers shown on model pages
-- --------------------------------------------------------------------------
INSERT IGNORE INTO `vehicle_offers` (`id`, `vehicle_model_id`, `title`, `value_amount`, `valid_from`, `valid_to`, `sort_order`, `status_id`, `created_by`, `created_at`, `updated_at`) VALUES
  (1, 1, 'Exchange Bonus up to ₹25,000', '25000.00', NULL, '2026-12-31', 0, 1, NULL, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (2, 1, 'Low Down Payment Finance Available', NULL, NULL, '2026-12-31', 1, 1, NULL, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (3, 2, 'Low Down Payment Finance Available', NULL, NULL, '2026-12-31', 0, 1, NULL, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (4, 4, 'EV Subsidy up to ₹50,000 (Tamil Nadu)', '50000.00', NULL, '2026-12-31', 0, 1, NULL, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (5, 4, 'Zero RTO Charges on Electric Vehicles', NULL, NULL, '2026-12-31', 1, 1, NULL, '2026-09-18 13:38:40', '2026-09-18 13:38:40');

-- --------------------------------------------------------------------------
-- vehicle_scores — the comparison scores
-- --------------------------------------------------------------------------
INSERT IGNORE INTO `vehicle_scores` (`id`, `vehicle_model_id`, `label`, `score`, `tone`, `sort_order`, `created_at`, `updated_at`) VALUES
  (1, 1, 'Specifications', '8.5', 'brand', 0, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (2, 1, 'Performance', '8.0', 'accent', 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (3, 1, 'Mileage', '8.8', 'brand', 2, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (4, 1, 'Operating Cost', '8.6', 'info', 3, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (5, 1, 'Maintenance', '8.2', 'brand', 4, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (6, 1, 'Comfort', '8.0', 'violet', 5, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (7, 1, 'Reliability', '8.5', 'violet', 6, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (8, 1, 'Value for Money', '8.3', 'brand', 7, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (9, 2, 'Specifications', '8.2', 'brand', 0, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (10, 2, 'Performance', '7.8', 'accent', 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (11, 2, 'Mileage', '9.2', 'brand', 2, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (12, 2, 'Operating Cost', '9.0', 'info', 3, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (13, 2, 'Maintenance', '8.6', 'brand', 4, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (14, 2, 'Comfort', '7.9', 'violet', 5, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (15, 2, 'Reliability', '8.8', 'violet', 6, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (16, 2, 'Value for Money', '8.5', 'brand', 7, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (17, 3, 'Specifications', '8.7', 'brand', 0, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (18, 3, 'Performance', '8.6', 'accent', 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (19, 3, 'Mileage', '7.6', 'brand', 2, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (20, 3, 'Operating Cost', '7.4', 'info', 3, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (21, 3, 'Maintenance', '7.5', 'brand', 4, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (22, 3, 'Comfort', '8.2', 'violet', 5, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (23, 3, 'Reliability', '8.4', 'violet', 6, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (24, 3, 'Value for Money', '7.9', 'brand', 7, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (25, 4, 'Specifications', '8.6', 'brand', 0, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (26, 4, 'Performance', '8.4', 'accent', 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (27, 4, 'Mileage', '9.4', 'brand', 2, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (28, 4, 'Operating Cost', '9.6', 'info', 3, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (29, 4, 'Maintenance', '9.2', 'brand', 4, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (30, 4, 'Comfort', '8.8', 'violet', 5, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (31, 4, 'Reliability', '8.3', 'violet', 6, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (32, 4, 'Value for Money', '7.8', 'brand', 7, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (33, 5, 'Specifications', '8.3', 'brand', 0, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (34, 5, 'Performance', '8.5', 'accent', 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (35, 5, 'Mileage', '8.1', 'brand', 2, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (36, 5, 'Operating Cost', '7.8', 'info', 3, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (37, 5, 'Maintenance', '7.2', 'brand', 4, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (38, 5, 'Comfort', '7.4', 'violet', 5, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (39, 5, 'Reliability', '8.0', 'violet', 6, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (40, 5, 'Value for Money', '7.9', 'brand', 7, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (41, 6, 'Specifications', '7.5', 'brand', 0, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (42, 6, 'Performance', '7.2', 'accent', 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (43, 6, 'Mileage', '7.6', 'brand', 2, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (44, 6, 'Operating Cost', '7.9', 'info', 3, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (45, 6, 'Maintenance', '8.0', 'brand', 4, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (46, 6, 'Comfort', '7.0', 'violet', 5, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (47, 6, 'Reliability', '7.4', 'violet', 6, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (48, 6, 'Value for Money', '8.9', 'brand', 7, '2026-09-18 13:38:40', '2026-09-18 13:38:40');

-- --------------------------------------------------------------------------
-- vehicle_stock — showroom availability
-- --------------------------------------------------------------------------
INSERT IGNORE INTO `vehicle_stock` (`id`, `vehicle_model_id`, `vehicle_variant_id`, `dealer_id`, `colour`, `qty`, `delivery_days_min`, `delivery_days_max`, `status_id`, `created_at`, `updated_at`) VALUES
  (1, 1, 1, NULL, NULL, 2, 1, 3, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (2, 2, 4, NULL, NULL, 2, 1, 3, 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (3, 3, 7, NULL, NULL, 2, 1, 3, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (4, 4, 10, NULL, NULL, 2, 1, 3, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (5, 5, 11, NULL, NULL, 2, 1, 3, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (6, 6, 13, NULL, NULL, 2, 1, 3, 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40');

-- --------------------------------------------------------------------------
-- vehicle_suitability — "who is it for" ratings
-- --------------------------------------------------------------------------
INSERT IGNORE INTO `vehicle_suitability` (`id`, `vehicle_model_id`, `type`, `label`, `sort_order`, `created_at`, `updated_at`) VALUES
  (1, 1, 'suitable', 'City Usage', 0, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (2, 1, 'suitable', 'Daily Auto Service', 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (3, 1, 'suitable', 'Commercial Usage', 2, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (4, 1, 'suitable', 'Fleet Business', 3, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (5, 1, 'suitable', 'Rental Business', 4, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (6, 1, 'not_recommended', 'Long Distance', 0, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (7, 1, 'not_recommended', 'Hilly Areas', 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (8, 1, 'not_recommended', 'Heavy Load Transport', 2, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (9, 2, 'suitable', 'City Usage', 0, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (10, 2, 'suitable', 'High Mileage Routes', 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (11, 2, 'suitable', 'Commercial Usage', 2, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (12, 2, 'suitable', 'Fleet Business', 3, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (13, 2, 'not_recommended', 'Heavy Load Transport', 0, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (14, 2, 'not_recommended', 'Hilly Areas', 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (15, 3, 'suitable', 'Heavy Load Transport', 0, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (16, 3, 'suitable', 'Commercial Usage', 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (17, 3, 'suitable', 'Long Shifts', 2, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (18, 3, 'suitable', 'Fleet Business', 3, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (19, 3, 'not_recommended', 'Low Budget Buyers', 0, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (20, 3, 'not_recommended', 'Short City Trips', 1, '2026-09-18 13:38:39', '2026-09-18 13:38:39'),
  (21, 4, 'suitable', 'City Usage', 0, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (22, 4, 'suitable', 'Low Running Cost', 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (23, 4, 'suitable', 'Daily Auto Service', 2, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (24, 4, 'suitable', 'Fleet Business', 3, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (25, 4, 'not_recommended', 'Long Distance', 0, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (26, 4, 'not_recommended', 'Areas Without Charging', 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (27, 5, 'suitable', 'Long Distance', 0, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (28, 5, 'suitable', 'Heavy Load Transport', 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (29, 5, 'suitable', 'Commercial Usage', 2, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (30, 5, 'not_recommended', 'City Stop-Start Usage', 0, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (31, 5, 'not_recommended', 'Low Noise Requirements', 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (32, 6, 'suitable', 'City Usage', 0, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (33, 6, 'suitable', 'Owner Drivers', 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (34, 6, 'suitable', 'Budget Buyers', 2, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (35, 6, 'not_recommended', 'Heavy Load Transport', 0, '2026-09-18 13:38:40', '2026-09-18 13:38:40'),
  (36, 6, 'not_recommended', 'Long Distance', 1, '2026-09-18 13:38:40', '2026-09-18 13:38:40');

-- --------------------------------------------------------------------------
-- vehicle_reviews — star ratings on the model pages — SEE THE NOTE AT THE END
-- --------------------------------------------------------------------------
INSERT IGNORE INTO `vehicle_reviews` (`id`, `vehicle_model_id`, `user_id`, `name`, `city`, `mobile`, `rating`, `title`, `body`, `is_verified`, `review_status`, `status_id`, `created_at`, `updated_at`, `ip_address`) VALUES
  (1, 1, NULL, 'Murugan S', 'Tiruvallur', NULL, 5, 'Excellent mileage, low running cost', 'Running it for eight months now on CNG. The mileage is exactly as claimed and servicing has been cheap. Very happy with the purchase.', 1, 'approved', 1, '2026-09-04 13:38:39', '2026-09-18 13:38:39', NULL),
  (2, 1, NULL, 'Selvam K', 'Kanchipuram', NULL, 4, 'Good for city, average on highway', 'Perfect for daily city routes. On longer runs the pickup drops with a full load, but for my usage it is more than enough.', 1, 'approved', 1, '2026-08-21 13:38:39', '2026-09-18 13:38:39', NULL),
  (3, 1, NULL, 'Prakash R', 'Chengalpattu', NULL, 5, 'Comfortable and reliable', 'Seating is comfortable for passengers and I have had no breakdowns so far. Service centre nearby which makes it easy.', 0, 'approved', 1, '2026-08-07 13:38:39', '2026-09-18 13:38:39', NULL),
  (4, 2, NULL, 'Murugan S', 'Tiruvallur', NULL, 5, 'Excellent mileage, low running cost', 'Running it for eight months now on CNG. The mileage is exactly as claimed and servicing has been cheap. Very happy with the purchase.', 1, 'approved', 1, '2026-09-04 13:38:39', '2026-09-18 13:38:39', NULL),
  (5, 2, NULL, 'Selvam K', 'Kanchipuram', NULL, 4, 'Good for city, average on highway', 'Perfect for daily city routes. On longer runs the pickup drops with a full load, but for my usage it is more than enough.', 1, 'approved', 1, '2026-08-21 13:38:39', '2026-09-18 13:38:39', NULL),
  (6, 2, NULL, 'Prakash R', 'Chengalpattu', NULL, 5, 'Comfortable and reliable', 'Seating is comfortable for passengers and I have had no breakdowns so far. Service centre nearby which makes it easy.', 0, 'approved', 1, '2026-08-07 13:38:39', '2026-09-18 13:38:39', NULL),
  (7, 3, NULL, 'Murugan S', 'Tiruvallur', NULL, 5, 'Excellent mileage, low running cost', 'Running it for eight months now on CNG. The mileage is exactly as claimed and servicing has been cheap. Very happy with the purchase.', 1, 'approved', 1, '2026-09-04 13:38:40', '2026-09-18 13:38:40', NULL),
  (8, 3, NULL, 'Selvam K', 'Kanchipuram', NULL, 4, 'Good for city, average on highway', 'Perfect for daily city routes. On longer runs the pickup drops with a full load, but for my usage it is more than enough.', 1, 'approved', 1, '2026-08-21 13:38:40', '2026-09-18 13:38:40', NULL),
  (9, 3, NULL, 'Prakash R', 'Chengalpattu', NULL, 5, 'Comfortable and reliable', 'Seating is comfortable for passengers and I have had no breakdowns so far. Service centre nearby which makes it easy.', 0, 'approved', 1, '2026-08-07 13:38:40', '2026-09-18 13:38:40', NULL),
  (10, 4, NULL, 'Murugan S', 'Tiruvallur', NULL, 5, 'Excellent mileage, low running cost', 'Running it for eight months now on CNG. The mileage is exactly as claimed and servicing has been cheap. Very happy with the purchase.', 1, 'approved', 1, '2026-09-04 13:38:40', '2026-09-18 13:38:40', NULL),
  (11, 4, NULL, 'Selvam K', 'Kanchipuram', NULL, 4, 'Good for city, average on highway', 'Perfect for daily city routes. On longer runs the pickup drops with a full load, but for my usage it is more than enough.', 1, 'approved', 1, '2026-08-21 13:38:40', '2026-09-18 13:38:40', NULL),
  (12, 4, NULL, 'Prakash R', 'Chengalpattu', NULL, 5, 'Comfortable and reliable', 'Seating is comfortable for passengers and I have had no breakdowns so far. Service centre nearby which makes it easy.', 0, 'approved', 1, '2026-08-07 13:38:40', '2026-09-18 13:38:40', NULL),
  (13, 5, NULL, 'Murugan S', 'Tiruvallur', NULL, 5, 'Excellent mileage, low running cost', 'Running it for eight months now on CNG. The mileage is exactly as claimed and servicing has been cheap. Very happy with the purchase.', 1, 'approved', 1, '2026-09-04 13:38:40', '2026-09-18 13:38:40', NULL),
  (14, 5, NULL, 'Selvam K', 'Kanchipuram', NULL, 4, 'Good for city, average on highway', 'Perfect for daily city routes. On longer runs the pickup drops with a full load, but for my usage it is more than enough.', 1, 'approved', 1, '2026-08-21 13:38:40', '2026-09-18 13:38:40', NULL),
  (15, 5, NULL, 'Prakash R', 'Chengalpattu', NULL, 5, 'Comfortable and reliable', 'Seating is comfortable for passengers and I have had no breakdowns so far. Service centre nearby which makes it easy.', 0, 'approved', 1, '2026-08-07 13:38:40', '2026-09-18 13:38:40', NULL),
  (16, 6, NULL, 'Murugan S', 'Tiruvallur', NULL, 5, 'Excellent mileage, low running cost', 'Running it for eight months now on CNG. The mileage is exactly as claimed and servicing has been cheap. Very happy with the purchase.', 1, 'approved', 1, '2026-09-04 13:38:40', '2026-09-18 13:38:40', NULL),
  (17, 6, NULL, 'Selvam K', 'Kanchipuram', NULL, 4, 'Good for city, average on highway', 'Perfect for daily city routes. On longer runs the pickup drops with a full load, but for my usage it is more than enough.', 1, 'approved', 1, '2026-08-21 13:38:40', '2026-09-18 13:38:40', NULL),
  (18, 6, NULL, 'Prakash R', 'Chengalpattu', NULL, 5, 'Comfortable and reliable', 'Seating is comfortable for passengers and I have had no breakdowns so far. Service centre nearby which makes it easy.', 0, 'approved', 1, '2026-08-07 13:38:40', '2026-09-18 13:38:40', NULL);

-- --------------------------------------------------------------------------
-- finance_lender_rates — the lenders behind Finance Options, with their rates
-- --------------------------------------------------------------------------
INSERT IGNORE INTO `finance_lender_rates` (`id`, `auto_financiar_id`, `interest_rate`, `min_tenure_months`, `max_tenure_months`, `max_loan_pct`, `max_loan_pct_no_cibil`, `processing_fee_pct`, `documents`, `is_featured`, `sort_order`, `status_id`, `created_by`, `created_at`, `updated_at`, `ip_address`) VALUES
  (1, 6, '11.50', 12, 60, 85, 70, '1.00', 'Aadhaar card\nPAN card\nDriving licence\nBadge / permit copy\n3 months bank statement\nAddress proof\n2 passport photos', 1, 0, 1, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (2, 7, '11.50', 12, 60, 85, 70, '1.00', 'Aadhaar card\nPAN card\nDriving licence\nBadge / permit copy\n3 months bank statement\nAddress proof\n2 passport photos', 0, 1, 1, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (3, 8, '11.50', 12, 60, 85, 70, '1.00', 'Aadhaar card\nPAN card\nDriving licence\nBadge / permit copy\n3 months bank statement\nAddress proof\n2 passport photos', 0, 2, 1, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (4, 9, '11.50', 12, 60, 85, 70, '1.00', 'Aadhaar card\nPAN card\nDriving licence\nBadge / permit copy\n3 months bank statement\nAddress proof\n2 passport photos', 0, 3, 1, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL),
  (5, 10, '11.50', 12, 60, 85, 70, '1.00', 'Aadhaar card\nPAN card\nDriving licence\nBadge / permit copy\n3 months bank statement\nAddress proof\n2 passport photos', 0, 4, 1, NULL, '2026-09-18 13:38:41', '2026-09-18 13:38:41', NULL);

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------------
-- NOTE ON vehicle_reviews
--
-- Those reviews are made up — every model shows 4.7 from 3 reviews. Google
-- penalises invented ratings, so before you launch either replace them with
-- real ones or empty the table:
--
--   DELETE FROM `vehicle_reviews`;
-- ---------------------------------------------------------------------------
