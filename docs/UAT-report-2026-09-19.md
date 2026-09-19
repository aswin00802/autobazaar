# AutoBazaar — User Acceptance Test Report

## 1. Summary

| Item | Value |
|---|---|
| Build tested | `d33d23a Vehicle page: Why Choose card hugs its content` (branch `aswin`, **plus the uncommitted working tree** — shop, checkout, vehicle catalogue, leads, coupons are all still untracked/modified files) |
| Date | 2026-09-19 |
| Environment | Windows 10 / XAMPP, Apache 2.4.58 + mod_php 8.2.12, MariaDB, DB `autobazaar`, `APP_ENV=local`, `APP_DEBUG=true`, session/cache/queue = database. URL `http://localhost/autobazaar/public` |
| Tester role | Black/grey-box tester. No application code, view, route, config or `.env` was changed. |
| Method | (1) In-process HTTP-kernel dispatch (PHP CLI harness `uat_lib.php`: cookie-jar sessions, acting-as users, JSON/form/multipart bodies, query counting, CSRF middleware swapped out). (2) Real HTTP with `curl` for CSRF, redirects, headers and file exposure. (3) Headless Chrome screenshots at 1440 px and 390 px (390 px through an `<iframe>` wrapper; authenticated pages rendered in-process and served from a throw-away local static server). (4) Static code review where a call would hit a live third-party service. |
| Test users | Two throw-away customers created for the test ("UAT Tester A/B", no roles), Super Admin 2027, role-admin 10. All test rows were deleted afterwards (section 5). |

### Counts

| | Count |
|---|---|
| Test cases defined | 248 |
| Executed | 238 |
| Passed | 183 |
| Failed | 55 |
| Blocked | 0 |
| Not executed (would hit live SMS / FCM / Sheets / Razorpay, feature absent, or needs true concurrency) | 10 |

The 55 failed cases collapse into **35 defects**: 1 Critical, 7 High, 14 Medium, 13 Low.

### Recommendation: **NO-GO**

Reasons, in order of weight:

1. **DEF-01 (Critical)** – the new E-commerce admin screens have no permission check. Any signed-in account – an OTP customer, or an account anyone can create at the still-open `/register` page – can read every customer's order (name, mobile, address), change order status, mark an order **Paid**, and create a 100 % coupon. Reproduced end to end from an anonymous visitor.
2. **DEF-02 (High)** – the customer OTP check has no attempt limit, no expiry and OTPs are reusable: a 4-digit code can be brute-forced, which is the entry ticket for DEF-01.
3. **DEF-05 (High)** – the "My Account" area is still prototype data: every customer is greeted as "Sivam", sees a fake vehicle order, and cannot see their own real orders. The pages are also open to guests.
4. **DEF-06 / DEF-07 (High)** – site search returns everything for every query; the New Autos grid (and parts of home/compare) overflow the screen on phones, which is the primary device for this audience.
5. **DEF-03 / DEF-04 (High, configuration)** – `APP_DEBUG=true` stack traces and a web-reachable `.env`/`.git`/log file. Harmless on a developer PC, critical if the same layout (`…/public` in the URL) is deployed.

What is solid: cart maths, server-side re-pricing, order creation (rows, snapshots, totals, coupon usage, replay protection), customer-side IDOR protection, lead/review validation + throttling + moderation, XSS escaping on every screen tested, the catalogue admin (children sync, live/draft, offer windows), finance-rate propagation, the vehicle API and EMI maths, query counts and warm response times.

A **Conditional Go for a closed pilot** becomes reasonable once DEF-01, DEF-02, DEF-05 (at least: protect the routes and hide the fake data) and the production config items DEF-03/DEF-04 are fixed and re-tested.

### Severity rule used

| Severity | Rule |
|---|---|
| Critical | Unauthorised access to other customers' data or to money-affecting functions, data loss, or a core flow unusable for everyone. No workaround. |
| High | A major feature does not work / shows wrong or fake data to every user, a security weakness that needs only modest effort, or a production-config exposure. |
| Medium | Wrong behaviour in a common edge case, business rule not enforced, misleading content, admin validation gap with customer-visible impact. Workaround exists. |
| Low | Cosmetic, hardening, consistency, rare crafted input, data hygiene. |

---

## 2. Test cases

Status: **P** pass, **F** fail, **NE** not executed. "IP" = in-process kernel dispatch, "HTTP" = real curl/Chrome. Evidence files are in the scratchpad directory `C:\Users\Ziga\AppData\Local\Temp\claude\c--xampp-htdocs-jp-auto\b491e5db-a472-4100-811a-503b70c05c27\scratchpad\` (scripts `uat_*.php`, outputs `uat_out_*.txt|html`, screenshots `uat_shot_*.png`).

### A. Browsing (guest)

| ID | Scenario | Steps | Expected | Actual | St |
|---|---|---|---|---|---|
| A-01 | All public pages render | IP GET of 42 URLs: `/`, `/new-autos`, `/used-autos`, brand pages, 4 vehicle pages, `/compare`, combo, `/buying-options`, `/enquiry[/slug]`, `/offers`, `/finance-emi`, `/government-schemes`, `/auto-news`, `/app`, `/accessories`, `/accessories/shop`, `/about-us`, `/contact`, `/faq`, 6 × `/page/*`, legacy `/terms-conditions`, `/privacy-policy`, `/account-delete`, `/search`, `/user/login`, `/user/register`, `/login`, `/up`, 3 × `/fareprice/*` | 200 | 42 × 200 | P |
| A-02 | Vehicle detail – all sections, 3 models | king-deluxe, treo-plus, gemini: 16 section markers + all 6 in-page tab anchors resolve; screenshot review | every section present | all present (the old `#gallery` tab no longer exists – by design after today's layout change) | P |
| A-03 | DRAFT model slug | `/new-autos/bajaj/maxima-z` | 404 | 404 branded | P |
| A-04 | Unknown brand / model / page / url | 5 URLs | branded 404, no trace | 404 "Page Not Found — AutoBazaar" | P |
| A-05 | Compare default | `/compare` | 4 models | 200, 4 columns | P |
| A-06 | Compare combo URL | `/compare/tvs-king-deluxe-vs-bajaj-re` | 2 models | 200 | P |
| A-07 | Compare > 4 models | 6 slugs joined with `-vs-` | capped at 4 | 4 columns | P |
| A-08 | Unknown slug inside combo | `…-vs-not-a-model` | ignored, no 500 | 200 | P |
| A-09 | Combo with only unknown slugs | `/compare/not-a-model-vs-also-not` | no 500 | 200, empty table with picker | P |
| A-10 | Draft slug inside combo | `bajaj-maxima-z-vs-bajaj-re` | draft not shown | not shown | P |
| A-11 | Price breakup arithmetic | 6 live models via API: Σ breakup rows vs on-road price | equal | equal on all 6 (e.g. 220000+18500+9800+3000+2700 = 254000) | P |
| A-12 | Search – empty | `/search` | 200 | 200 | P |
| A-13 | Search – normal query filters results | `/search?q=bajaj` vs `/search?q=zzzzqqqq-no-such-thing` | different, relevant results | identical: Autos 6 / Accessories 59 / Schemes 6 / News 6 for both | **F** (DEF-06) |
| A-14 | Search – 5 000 chars | | 200 | 200 | P |
| A-15 | Search – HTML/JS payload | `<script>alert(1)</script>"'><img src=x onerror=…>` | escaped | escaped everywhere (title, h1, input value) | P |
| A-16 | Search – SQL-looking | `' OR 1=1 -- ; DROP TABLE users;` | 200 | 200 | P |
| A-17 | Search – `q[]=x` | | 4xx or treated as empty | **500** `Array to string conversion` SiteController.php:457 | **F** (DEF-25) |
| A-18 | Search heading text | `/search?q=bajaj` | `Results for "bajaj"` | h1 source is `Results for &amp;quot;bajaj&amp;quot;` → user sees `&quot;bajaj&quot;` | **F** (DEF-26) |
| A-19 | Brand page title | `/new-autos/tvs` | "TVS Autorickshaws" | "Tvs Autorickshaws" (also "Brand: Tvs" on detail page and filter rail) | **F** (DEF-27) |
| A-20 | `/up` health | | 200 | 200 | P |
| A-21 | Compare – EMI Comparison chart | screenshot `uat_shot_compare_1440.png` | 4 bars | values and labels only, **no bars drawn** (1440 and 390 px) | **F** (DEF-23) |
| A-22 | News article + unknown slug | first fixture slug; `/auto-news/nope-nope` | 200 / 404 | 200 / 404 | P |

### B. Accessories shop & cart (guest)

| ID | Scenario | Steps | Expected | Actual | St |
|---|---|---|---|---|---|
| B-01 | Shop listing | `/accessories/shop` (Chrome DOM) | live products | 59 products, placeholders for photos (env note) | P |
| B-02 | Category via query string | `?category=floor-mats`, `rain-cutters`, `nope` (Chrome `--dump-dom`) | pre-filtered | 16 / 14 / 0 visible cards, counter matches | P |
| B-03 | Price filter + sort via query string | `?sort=price-asc&min=…` | applied | not a feature: filters/sort are client-side Alpine state, only `category` is read from the URL | NE (gap) |
| B-04 | Add valid item | POST `/cart/add` `{product_model_id:10, qty:1}` | count 1, offer price captured | count 1, `unit_price 560 / unit_mrp 700`, cart bound to session id | P |
| B-05 | Add – qty 0, −3, "abc", 99999999999, 11, 1.5 | | 422 | 422 × 6 | P |
| B-06 | Add – nonexistent id / missing id / `"10 OR 1=1"` | | 422 | 422 × 3 | P |
| B-07 | Add variant of an INACTIVE product | product 1 has `products.status_id = 2` (not listed in shop); POST `product_model_id:1` | rejected | **200 "Added to your cart."**, count 2 | **F** (DEF-14) |
| B-08 | Quantity accumulates and caps | add 6 + 6 | 10 | 10 | P |
| B-09 | Update qty | `/cart/update` qty 3 | subtotal 1680 | 1680, count 3 | P |
| B-10 | Update – −1, 11, "x", "" | | 422 | 422 × 4 | P |
| B-11 | IDOR – update/remove another cart's item id | `item_id:1` (other guest's cart) | ignored | unchanged | P |
| B-12 | Remove / qty 0 | | line gone, totals 0, shipping 0 | as expected | P |
| B-13 | Cart count endpoint | `/cart/count` | matches | matches in every step | P |
| B-14 | Coupon AUTO5 | subtotal 1680 | −84, total 1596 | −84 / 1596 | P |
| B-15 | Coupon `"  auto5 "` | | normalised | applied | P |
| B-16 | Unknown / empty / 51 chars / array | | message / 422 | "That coupon code is not valid." (previous coupon kept) / 422 × 3 | P |
| B-17 | Coupon remove | | discount 0 | 0 | P |
| B-18 | Shipping at exactly ₹999 / ₹1000; express fee | line price forced by SQL on my own cart | free ≥ 999; express +69 | 999→0, 1000→0; express 998/999/1000 → 1067/1068/1069 | P |
| B-19 | Standard shipping BELOW ₹999 | subtotal ₹998 (and a real ₹560 order) | a shipping charge (UI says "Free (on orders above ₹999)") | **₹0 – the threshold has no effect** | **F** (DEF-11) |
| B-20 | Discount rounding | 5 % of 999.99 | documented rule | ₹49 (floored to whole rupee – deliberate `floor()` in `Coupon::discountOn`) | P (note) |
| B-21 | Coupon on an EMPTY cart | new guest, POST `/cart/coupon` AUTO5 | refused | `success:true`, creates an empty cart row | **F** (DEF-29) |
| B-22 | No-JS form add | form POST with Referer | 302 back + flash | 302 to shop | P |
| B-23 | Guest can see / edit the cart | look for a cart page | guest can review cart | **no cart page exists**; header cart icon → `/checkout` → login. Update/remove/coupon forms exist only inside checkout | **F** (DEF-20) |

### C. Auth gate

| ID | Scenario | Steps | Expected | Actual | St |
|---|---|---|---|---|---|
| C-01 | Checkout while logged out | HTTP + IP `GET /checkout?delivery_option=express` | 302 `/user/login`, intended URL stored | 302; session `url.intended` = full checkout URL | P |
| C-02 | Order-confirmed while logged out | HTTP | 302 login | 302 | P |
| C-03 | Login page renders | | 200 | 200 | P |
| C-04 | Register page renders | | 200 | 200 (but 1.6 MB – see K-04) | P |
| C-05 | OTP send validation | `POST /user/otp/send` | – | **Not executed – sends a live SMS.** Static review findings listed in section 4 | NE |
| C-06 | OTP verify validation | bad phone / 2-digit otp | `success:false` | "Please Fill OTP" | P |
| C-07 | OTP verify brute-force protection | 12 wrong OTPs in a row for an unregistered number (no outbound call in `verifyOTP`) | 429 / lock-out | **12 × 200 "Invalid Otp"**; no throttle, no expiry, OTP stays valid after use | **F** (DEF-02) |
| C-08 | OTP page escapes `{mobile}` | `"><script>` in URL | escaped / 404 | 404 | P |
| C-09 | `POST /logout` ends session | session login planted in the session row, then cookie-only requests | login key removed, id rotated, `/checkout` redirects again | all true | P |
| C-10 | `GET /logout` | | 405 (POST only) | 405 (rendered as a debug page – DEF-03) | P |
| C-11 | Open redirect via forged `Host` | `Host: evil.example` on `/checkout` | intended URL stays on own host | stays `http://localhost/...` | P |
| C-12 | Guest `POST /checkout/place-order` | | 302 login, nothing remembered | 302, no intended URL | P |
| C-13 | Guest JSON request to `/checkout` | `Accept: application/json` | 401 JSON | 302 + HTML | **F** (DEF-34) |
| C-14 | Signed-in customer opens `/user/login` | | redirect away | login form shown again | **F** (DEF-34) |
| C-15 | Admin `/login` with a non-email identifier | `email=uat-not-an-email` | 302 + error | **500** `Unknown column 'username'` | **F** (DEF-17) |
| C-16 | Admin `/login` wrong credentials | | 302 + "credentials do not match" | as expected | P |
| C-17 | Legacy `/register` is not public | GET + POST | not available | **200; POST creates a user and logs them in** (see J-17) | **F** (DEF-01) |

### D. Checkout (as UAT customer A, in-process)

| ID | Scenario | Expected | Actual | St |
|---|---|---|---|---|
| D-01 | Checkout with empty cart | 200 + empty state | 200, empty state | P |
| D-02 | Place order with empty cart | back to checkout, "Your cart is empty.", no order | as expected | P |
| D-03 | Address – all fields missing | errors on label, name, mobile, address_line_1 | as expected | P |
| D-04 | Address – mobile `abcde`, `12345` | rejected | **saved** ("Address saved.") | **F** (DEF-10) |
| D-05 | Address – pincode `12`, `ABCDEF` | rejected | **saved** | **F** (DEF-10) |
| D-06 | Address – no city, no pincode | rejected (cannot ship) | **saved** | **F** (DEF-10) |
| D-07 | Address – name 300 / label 60 chars | rejected | rejected | P |
| D-08 | Valid address; posted `user_id`, `status_id`, `is_default` | saved for the signed-in user, extras ignored | user_id = A, default = 1 | P |
| D-09 | Second address | not default | is_default 0 | P |
| D-10 | Saved-address reuse | both addresses offered as radios | 2 radios | P |
| D-11 | XSS in address name / line 1 | escaped on checkout, confirmation, admin | escaped on all three | P |
| D-12 | Totals shown (express) | 2×560 + 650 = 1770; AUTO5 → 88; +69 = **1751** | 1,770 / 88 / 69 / 1,751 | P |
| D-13 | Delivery switch to standard | 1,682 | 1,682 | P |
| D-14 | Place order with another user's `address_id` | refused | "Please choose a delivery address.", no order | P |
| D-15 | Invalid delivery_option / payment_mode | validation errors | errors on both | P |
| D-16 | Happy path | 302 → `/order-confirmed/{no}` | 302 → `/order-confirmed/ABZ20260919C001` | P |
| D-17 | Order number format | `ABZ` + yyyymmdd + `C` + NNN | `ABZ20260919C001` | P |
| D-18 | Stored totals = shown totals | 1770 / 88 / 69 / 1751 | `1770.00 / 88.00 / 69.00 / 1751.00` | P |
| D-19 | Mass assignment on place-order (`total_amount=1`, `user_id=1`, `order_status=delivered`, `payment_status=paid`, `status_id=0`) | ignored | ignored | P |
| D-20 | Order items | 2 rows, qty×price = line, Σ = subtotal | correct, product name/brand snapshotted | P |
| D-21 | Status history | one `placed` row by the customer | correct | P |
| D-22 | Address snapshot on order | copied | copied | P |
| D-23 | Cart after order | `converted`, coupon cleared, badge 0 | correct | P |
| D-24 | Coupon usage recorded | coupon 1, user A, 88.00 | recorded | P |
| D-25 | Replay of place-order (sequential double submit) | no second order | "Your cart is empty.", still 1 order | P |
| D-26 | True concurrent double submit | one order | **Not executed** (needs two parallel authenticated HTTP clients). Static risk noted in section 4 | NE |
| D-27 | Confirmation page | 200, order no + total | 200, correct | P |
| D-28 | Confirmation wording for an unpaid order | no claim of payment | total row is labelled **"Total Paid ₹1,751"** on a COD / pending order (status box below correctly says "Cod Pending") | **F** (DEF-24) |
| D-29 | IDOR – B opens A's confirmation; A opens user 1's; SQL-looking number | 404 | 404 × 3 | P |
| D-30 | First-order-only AUTO5 on 2nd order | rejected | "You have already used this coupon.", discount 0 | P |
| D-31 | Per-user limit (flat ₹50 coupon, limit 1, user B) | 1st use ok (9050 − 50 = 9000), 2nd rejected | as expected, usage count shown in admin | P |
| D-32 | Order with `payment_mode=upi` | created unpaid, nothing pretends to charge | `payment_status=pending`, no gateway fields, page says "Online payment is not yet connected…" | P |
| D-33 | Order numbers unique / sequential | C001, C002, C003 | yes (soft-deleted orders keep their number) | P |
| D-34 | Payment options offered at checkout | only methods that work (COD) selectable / default | **UPI is pre-selected**, Card / Net-banking / Wallet selectable; "Place Order ₹…" creates an order that can never be paid online | **F** (DEF-15) |
| D-35 | Coupon whose `valid_to` is today | usable today | **"This coupon has expired."** | **F** (DEF-12) |

### E. Account area

| ID | Scenario | Expected | Actual | St |
|---|---|---|---|---|
| E-01 | `/account`, `/account/orders`, order detail, 9 sections as customer A | 200 | 13 × 200 | P |
| E-02 | Unknown section | 404 | 404 | P |
| E-03 | Same pages as a guest | redirect to login | **200 on every page** (routes are outside the `UserAuth` group) | **F** (DEF-05) |
| E-04 | Dashboard shows the real user | name, counters, orders of A | sidebar shows "UAT Tester A / 9000000011" (real) but heading says **"Welcome back, Sivam"**, counters 1/3/4/2 and the "active order" `ABZ20250905C001 TVS King Deluxe` are fixture data (`uat_shot_account_1440.png`) | **F** (DEF-05) |
| E-05 | Customer can see own orders | A's orders ABZ20260919C001/2 listed; detail by id | never shown; `/account/orders/{anything}` always renders the same fixture order; addresses page lists "Sivam R", not A's saved addresses | **F** (DEF-05) |
| E-06 | IDOR on `/account/orders/1` | no other customer's data | none (page is static) | P |

### F. Guest → login cart merge (`CartService::mergeGuestCart`, the call `OtpController::afterLogin` makes)

| ID | Scenario | Expected | Actual | St |
|---|---|---|---|---|
| F-01 | Guest 10×3 + 12×9 into B's cart 12×5 + 66×1 | 10×3, 12×10 (cap), 66×1, no duplicates | exactly that | P |
| F-02 | Guest cart closed | `cart_status = merged` | merged | P |
| F-03 | Nothing left on the closed guest cart | 0 items | 1 orphan item (the line whose qty was folded into an existing line is not deleted) | **F** (DEF-30) |
| F-04 | Badge after merge | 14 | 14 | P |
| F-05 | Old guest session | sees empty cart | 0 | P |
| F-06 | Merge called twice | idempotent | unchanged | P |
| F-07 | User without an active cart | guest cart claimed | `user_id` set, `session_id` null | P |
| F-08 | Guest-applied AUTO5 on a claimed cart of a user who already ordered | no discount | coupon re-checked, discount 0 | P |

### G. Vehicle leads & reviews (web + API)

| ID | Scenario | Expected | Actual | St |
|---|---|---|---|---|
| G-01 / G-02 | 4 sources, web / API | stored, `lead_status=new`, variant kept | 8 × 200, rows correct | P / P |
| G-03 / G-04 | Required per source (test_drive → date+slot, loan → amount; name+mobile always) web / API | 422 | 422 | P / P |
| G-05 | Loan amount 500 / 99999999999 | 422 | 422 | P |
| G-06 | Unknown source | 422 | 422 | P |
| G-07 | Mobile formats: `12345`, `5876543210`, 11 digits, `+91…`, with space, letters, full-width digits | 422 | 14 × 422 | P |
| G-08 | Test drive in the past | 422 | 422 "cannot be in the past" | P |
| G-09 | Test drive today | 200 | 200 | P |
| G-10 | Test drive 5 years ahead | rejected | accepted | **F** (DEF-35) |
| G-11 | Unknown time slot | 422 | 422 | P |
| G-12 | Variant of another model | dropped (NULL) | NULL | P |
| G-13 | Honeypot filled | 422 | 422 | P |
| G-14 | Bad email / pincode `012345` | 422 | 422 | P |
| G-15 | Message > 1000 | 422 | 422 | P |
| G-16 | Mass assignment (`lead_status`, `status_id`, `user_id`, `assigned_to`, `admin_note`, `enquiry_no`, `ip_address`) | ignored | ignored | P |
| G-17 | Lead on DRAFT / unknown model | 404 | 404 (API in envelope) | P |
| G-18 | Enquiry numbers | `VE-####`, unique, no `VE-TMP` left | 45 rows ok | P |
| G-19 / G-20 | Throttle web / API | 11th POST in a minute → 429 | 10 × 422 then 429, `Retry-After: 60` | P / P |
| G-21 | Review validation (name, rating 0 / 9 / 4.5, body < 10, honeypot) web + API | 422 | 422 | P |
| G-22 | Review mass assignment (`review_status=approved`, `is_verified`, `user_id`) | stored pending | pending, not verified | P |
| G-23 | Pending review hidden on page and API | hidden | hidden | P |
| G-24 | Admin Reviews screen escapes `<script>`, `<img onerror>`, `<svg/onload>` | escaped | escaped | P |
| G-25 | Invalid review status `published` | rejected | rejected | P |
| G-26 | Approve → visible (escaped) on page + API, cached rating recalculated | yes | visible, escaped; avg/count recomputed from approved rows | P |
| G-27 | Rating figures stay sensible after first moderation | count grows by 1 | model 1 showed **4.5 / 120 reviews** (seeded cache, only 3 review rows exist); after approving one review it showed **3.8 / 4** | **F** (DEF-22) |
| G-28 | Reject / delete → hidden, recalculated; delete unknown → 404 | yes | yes | P |
| G-29 | Admin Leads list + view escape XSS name/message/note | escaped | escaped | P |
| G-30 | Leads filters: source, status, q, date range, future date, model, SQL-looking, `from=not-a-date` | 200, right rows | 200, counts match | P |
| G-31 | Leads filter `model_id[]=1` | no 500 | **500** Array to string conversion (leads/index.blade) | **F** (DEF-25) |
| G-32 | Pipeline new → contacted → … → closed | each saved | 7 × ok | P |
| G-33 | Invalid lead status `won` | 422 | 422 | P |
| G-34 | Assign to admin user 10 | saved | saved | P |
| G-35 | Assign to a customer id / non-existent id | 422 | **200, stored `assigned_to = 1` and `99999999`** | **F** (DEF-21) |
| G-36 | Note saved, escaped; > 5000 rejected | yes | yes | P |
| G-37 | No-role user on leads/reviews status endpoints | 403 | 403 | P |

### H. API

| ID | Scenario | Expected | Actual | St |
|---|---|---|---|---|
| H-01 | `GET /api/vehicles` | 200, live models only | 6, no drafts, 10 queries | P |
| H-02 | `?brand=bajaj` | filtered | 1 | P |
| H-03 | `?brand=BAJAJ` | same as lowercase | 0 results | **F** (DEF-28) |
| H-04 | Unknown brand | 200, empty | 200, `count 0` | P |
| H-05 | SQL-looking brand | 200 empty | 200 | P |
| H-06 | `?brand[]=bajaj` | 4xx | **500** Array to string conversion (VehicleController.php:27) | **F** (DEF-25) |
| H-07 | `GET /api/vehicles/{slug}` | vehicle, similar, lead_options | present, 26 queries | P |
| H-08 | Draft / unknown slug | 404 `{success:false,status,message,errors}` | as expected | P |
| H-09 | EMI normal, 0 % rate, P=1, huge (1e8 @ 50 % / 120 m), 9.99 % | matches independent formula | 6694, 8333, 1, 4197970, 3186 – all match; `total_payment = emi × n` | P |
| H-10 | EMI invalid: tenure 0, negative, rate 51, strings, 121, 12.5, 1e9, empty | 422 envelope | 9 × 422 | P |
| H-11 | 422 envelope carries per-field errors | `errors` keyed by field | `errors` is a **string** (first message only) | **F** (DEF-28) |
| H-12 | Framework errors on `/api/*` (unknown route, wrong method) use the envelope | `{success:false,…}` | `{message, exception, file, line, trace}` – no `success`, leaks trace (debug on) | **F** (DEF-28 / DEF-03) |
| H-13 | Protected API without token | 401 JSON | 401 `Unauthenticated.` | P |
| H-14 | `GET /api/get-sold-auto` with a Sanctum user | 200 envelope | 200 | P |
| H-15 | Public masters: brands, fuel types, price ranges, cities, areas, owners, finance partners (+id), authorized sellers, events, test-v1 | 200 JSON | 11 × 200 (`get-areas` is 960 KB) | P |
| H-16 | `GET /api/get-auto-body-types`, `/api/get-transmission-types` | 200 | **500** `Method CommonController::getAutoBodyTypes / getAutoTransmissionType does not exist` | **F** (DEF-19) |
| H-17 | Malformed JSON body | 4xx | 422 | P |

### I. Admin

| ID | Scenario | Expected | Actual | St |
|---|---|---|---|---|
| I-01 | Super Admin 2027 – 75 parameter-free admin pages | 200 | 75 × 200 | P |
| I-02 | Role admin 10 | 200 where permitted, 403 elsewhere | 58 × 200, 17 × 403 (country/state/city, roles, settings, pos-quotation, auto-meter) | P |
| I-03 | No-role customer on every admin URL | 403 / redirect everywhere | 68 × 403 but **200 on** `/dashboard`, `/ecommerce/orders`, `/ecommerce/orders/view/{id}`, `/ecommerce/orders/status/{s}`, `/ecommerce/coupons`, `/ecommerce/coupons/create`, `/ecommerce/coupons/edit/{id}`, `/settings/email-template-settings/create`, `/pos-quotation/create`, `/password/confirm` | **F** (DEF-01) |
| I-04 | Guest on admin URLs (HTTP) | 302 `/login` | 302 | P |
| I-05 | Orders list, status filter, search by name, SQL-looking `q` | correct rows | correct | P |
| I-06 | Order view | number, items, 1,770 / 88 / 69 / 1,751, address | correct | P |
| I-07 | Order view escapes XSS shipping name | escaped | escaped | P |
| I-08 | Status placed → confirmed → packed → shipped → delivered | saved each step | saved | P |
| I-09 | History rows | one per transition, `created_by` = admin | correct | P |
| I-10 | Same status again | error, no row | "Order is already marked delivered." | P |
| I-11 | Invalid status value | rejected | rejected | P |
| I-12 | Backwards / illegal transitions | delivered → placed and delivered → cancelled blocked | **both accepted**; coupon usage of the cancelled order is not released | **F** (DEF-16) |
| I-13 | Payment update → paid | `paid_at` set | set | P |
| I-14 | Invalid payment status | rejected | rejected | P |
| I-15 | Status update for unknown order | 404 | 404 | P |
| I-16 | No-role customer POSTs `status-update`, `payment-update`; opens another customer's order | 403 | **302 success: order set to `delivered`, own order set to `paid`; order 1 of another customer rendered with name + mobile** | **F** (DEF-01) |
| I-17 | Delete order | soft delete, hidden from list, customer page no 500 | `status_id 0`, hidden, confirmation still 200 | P |
| I-18 | Status History order on the view page | chronological | shown as Confirmed, Packed, Shipped, Delivered, Placed, Cancelled, Placed – ties on `created_at` are unordered (`uat_shot_admin_order_view_1440.png`) | **F** (DEF-30) |
| I-19 | Coupon create, `" uatflat50 "` | stored as `UATFLAT50` | yes | P |
| I-20 | Duplicate code, different case | rejected | rejected | P |
| I-21 | Negative value | rejected | rejected | P |
| I-22 | Percent > 100 | rejected | **`UAT150` (150 %) created; applying it gives discount = subtotal, total ₹0** | **F** (DEF-13) |
| I-23 | `valid_to` before `valid_from` | rejected | rejected | P |
| I-24 | Unknown discount type | rejected | rejected | P |
| I-25 | HTML in code/label | escaped in list | escaped | P |
| I-26 | Usage count on list | 1 after B's order | shown | P |
| I-27 | Edit value; rename to an existing code | saved; rejected | saved; rejected | P |
| I-28 | Delete coupon | gone from list | flash "Coupon removed successfully" but the row stays listed (status 0) and its code can never be re-used | **F** (DEF-29) |
| I-29 | No-role customer creates a coupon | 403 | **created (`UATNR`)** | **F** (DEF-01) |
| I-30 | Catalogue – create with children | model + 2 variants, 1 spec, 1 score, 1 feature, 3 suitability, 5 offers, 1 price, 1 stock | exactly | P |
| I-31 | Default variant | exactly one, the chosen row | "UAT CNG" | P |
| I-32 | Duplicate slug | rejected | rejected | P |
| I-33 | Validation: bad slug, empty name, bad segment, seating 0, status 5 | errors | 5 errors | P |
| I-34 | Draft ⇄ live reflected publicly (fresh process each step) | draft: 404 web+API, absent from new-autos / brand / compare / search / API list; live: present | exactly | P |
| I-35 | Offer validity window | only current + ends-today shown; expired / future / disabled hidden; API agrees | exactly | P |
| I-36 | On-road price from price row | 200000+15000+9000+2000+1000 = ₹2,27,000 | shown | P |
| I-37 | Edit keeps variant ids | ids unchanged, rename applied, new variant appended | 16,17 kept, 18 added | P |
| I-38 | Posting another model's variant id | that variant untouched | untouched | P |
| I-39 | Upload valid 1×1 PNG and tiny PDF | accepted | accepted | P |
| I-40 | Upload `.txt` as image, PNG bytes named `.php`, `.exe` as document | rejected | rejected × 3 | P |
| I-41 | Upload PNG bytes + `<script>` named `uat.html` | rejected or stored with a safe extension | **accepted, stored as `uploads/vehicles/6aae76b54a0e2.html`** | **F** (DEF-18) |
| I-42 | `image-delete` | row + file removed | removed | P |
| I-43 | `catalogue/status` with `status_id=7` | rejected | stored 7 | **F** (DEF-30) |
| I-44 | Delete model | model, all child rows and files removed | removed | P |
| I-45 | Leads of a deleted model | removed or re-pointed | 1 orphan lead left (`vehicle_model_id` dangling); list and view still render | **F** (DEF-30) |
| I-46 | Finance partner loan terms save | 9.75 % / 12–48 m / 90 % stored | stored | P |
| I-47 | New rate reflected | public Finance Options card + API lender | 9.75, tenures to 48, 90 %, document line – both | P |
| I-48 | Loan-terms validation (−1, 150, min > max, 101 %, "abc") | rejected | 5 × rejected | P |
| I-49 | No-role user updates finance partner | 403 | 403 | P |
| I-50 | No-role user POSTs `/masters/auto-finance/statustoggle` | 403 | **200 `{"success":true}`** (sent the current value, so nothing changed) | **F** (DEF-01) |
| I-51 | Sidebar per permission (rolled-back probe user) | none → empty; `vehicle_leads` → Leads only; `vehicle_catalog` → Catalogue only | as expected; E-commerce hidden without `ecommerce_*` (only the menu is hidden – the URLs are not protected) | P |

### J. Cross-cutting

| ID | Scenario | Expected | Actual | St |
|---|---|---|---|---|
| J-01 | CSRF – real POST without token (`/cart/add`, lead, `/logout`) | 419 | 419 × 3 | P |
| J-02 | GET on POST routes (`/cart/add`, `/checkout/place-order`, lead, `/cart/coupon`, `/logout`) | 405 friendly page | 405 but a **2 MB Laravel exception page with stack trace** | **F** (DEF-03) |
| J-03 | Forced 500 (`/search?q[]=x`) | generic error page | full debug page: file paths, source excerpt, request, queries. No `.env` values / `APP_KEY` / DB password found in the page | **F** (DEF-03) |
| J-04 | Security headers | X-Frame-Options / CSP frame-ancestors, X-Content-Type-Options, Referrer-Policy, HSTS (prod) | none; `Server: Apache/2.4.58 (Win64) OpenSSL/3.1.3 PHP/8.2.12` and `X-Powered-By` disclosed. Session cookie is HttpOnly + SameSite=Lax | **F** (DEF-31) |
| J-05 | Mass assignment on public POSTs | ignored | ignored on cart, address, place-order, lead, review | P |
| J-06 | No-role user on admin JSON endpoints (`catalogue/status`, `delete`, `image-delete`, leads/reviews status) | 403 | 403 (exceptions: I-50, `/brand/get-model`) | P |
| J-07 | SQL-injection strings in search, shop, brand, order number, admin order search, lead filters | no error, no leak | all bound parameters, 200/404 | P |
| J-08 | Open redirect via intended URL | own host only | only `fullUrl()` of the guarded request; no redirect parameter exists | P |
| J-09 | File / path exposure under `http://localhost/autobazaar/` | not reachable | **200:** `/.env` (1 227 B), `/.git/HEAD`, `/.git/config`, `/storage/logs/laravel.log` (821 KB), `/composer.json|lock`, `/artisan`; directory listings for `/storage/`, `/database/`, `/vendor/`, `/docs/`. `/public/.env` 404, `/public/uploads/` 403 | **F** (DEF-04) |
| J-10 | robots for `APP_ENV=local` | noindex | `<meta name="robots" content="noindex, nofollow">` (layout, non-production) ; `robots.txt` allows all | P |
| J-11 | Links and images on home, shop, vehicle page, checkout, account, new-autos | no 404 | 67 internal URLs checked, 0 broken (2 hits were Alpine `:href/:src` bindings, not real URLs). Product photos: none referenced because `resolveImage()` drops missing files – environment note | P |
| J-12 | Responsive 1440 px: home, new-autos, vehicle, compare, shop, checkout, account, confirmation, 3 admin pages | no overflow / overlap | clean (except the empty EMI chart, A-21) | P |
| J-13 | Responsive ≤ 500 px: `/new-autos` | 2 cards per row inside the viewport | **grid wider than the screen: right-hand card column, "Sort by" and the Filters bar are cut off** – same at a native 500 px window (`uat_shot_newautos_390.png`, `uat_shot_newautos_500.png`) | **F** (DEF-07) |
| J-14 | Responsive 390 px: home | no overflow | CTA tiles and "Latest Offers" cards run off the right edge ("View All", arrows, images cut); **"Popular Accessories" placeholder art is oversized and overlaps the heading and product names** (`uat_crop_home_390_a.png`) | **F** (DEF-08) |
| J-15 | Responsive 390 px: compare | cards fit | EMI, Monthly Fuel Cost, Expert Recommendation cards cut off at the right ("Enquire Now" half visible, fuel amounts hidden) | **F** (DEF-08) |
| J-16 | Responsive 390 px: vehicle detail, shop, checkout, account, admin orders | usable | fine (admin table scrolls inside its card) | P |
| J-17 | Anonymous visitor → admin data | impossible | **`GET /register` 200 → `POST /register` (name, email, password) creates user (role_id 1000, no roles) and logs in → `/dashboard` 200, `/ecommerce/orders` 200 with customer names/mobiles**; the same account can then log in at `/login` | **F** (DEF-01) |
| J-18 | App talks to its own database over real HTTP | always `autobazaar` | **6 of my first ~20 curl requests were served with the connection pointing at database `auto_bazar`** (500 `Table 'auto_bazar.shop_carts' doesn't exist`; session rows written into `auto_bazar.sessions`) | **F** (DEF-09) |
| J-19 | Error pages | branded 403 / 405 / 419 / 429 / 500 | only `errors/404.blade.php` exists; 419 shows the stock "Page Expired" | **F** (DEF-32) |
| J-20 | Session cookie flags | HttpOnly, SameSite | `httponly; samesite=lax` (no `secure` on http – expected locally) | P |

### K. Data integrity & performance smell tests

| ID | Scenario | Expected | Actual | St |
|---|---|---|---|---|
| K-01 | N+1 on shop and new-autos (DB::listen) | flat query count | shop 9 queries for 59 products; new-autos 14; home 18; vehicle detail 30; compare 14; search 18; no SQL repeated > 5× | P |
| K-02 | Any page > 2 s in-process (warm) | none | none: public 84–292 ms, checkout 75 ms, admin 72–392 ms. Cold first hit (Blade compile) was 6.2 s for `/` and 10.1 s for the vehicle page – one-off | P |
| K-03 | Built asset weight | reasonable | `app-*.css` 70 KB (12 KB gzip), `app-*.js` 71 KB (25 KB gzip) | P |
| K-04 | Page weight outliers | < ~500 KB HTML | `/user/register` = **1.6 MB** HTML (every area as an `<option>`) | **F** (DEF-33) |
| K-05 | Other heavy responses | – | shop 303 KB and search 305 KB HTML (all 59 products inline + JSON), `/api/get-areas` 960 KB – noted, not failed | P (note) |

---

## 3. Defect log

### DEF-01 — Critical — New admin E-commerce screens (and a few legacy endpoints) have no permission check; any signed-in account, including a self-registered one, gets them
* **Area:** I / J (I-03, I-16, I-29, I-50, J-17, C-17)
* **Steps (anonymous visitor):**
  1. `GET /register` → 200 (stock laravel/ui form, enabled by `Auth::routes()`).
  2. `POST /register` `name=UAT SelfReg&email=uat-selfreg@example.invalid&password=…&password_confirmation=…` → 302 `/home`, user row created (`role_id 1000`, no Spatie role), session logged in.
  3. `GET /ecommerce/orders` → **200**, lists every website order with customer name + mobile; `GET /ecommerce/orders/view/1` → full address of another customer.
  4. `POST /ecommerce/orders/payment-update` `id=<own order>&payment_status=paid` → 302 "Payment status updated successfully", DB `payment_status=paid`, `paid_at` set.
  5. `POST /ecommerce/orders/status-update` `order_status=delivered` → accepted.
  6. `POST /ecommerce/coupons/store` `code=UATNR&discount_type=percent&discount_value=10&status_id=1` → coupon created (with DEF-13 it could be 100 %+).
  The same works for any OTP-logged-in customer because customers and admins share the `web` guard (verified as customer "UAT Tester A", id 4676).
* **Expected:** 403 for users without `ecommerce_orders` / `ecommerce_coupons` permissions; `/register` not available.
* **Evidence:** `uat_out_i1.txt` (access matrix), `uat_out_i2.txt` lines I-O17/18/19, I-C17; `uat_j2.php` output: `orders=200 dashboard=200 showsCustomerPII=1`; `uat_perm_scan.php` lists 31 `auth` routes with no permission/role middleware.
* **Cause:** `app/Http/Controllers/admin/ecommerce/OrdersController.php:19` and `CouponsController.php` have no `$this->middleware(['permission:…'])` (compare `VehicleLeadsController.php:16`). The permissions `ecommerce_orders`, `ecommerce_coupons`, `add_/edit_/delete_ecommerce_coupons` already exist in the DB and only drive the sidebar. `routes/admin.php:16` protects the group with `auth` + `check.Userstatus` only. `routes/web.php:49` `Auth::routes()` exposes `/register`. Also unprotected: `POST /masters/auto-finance/statustoggle` (`AutoFinanceController.php:17-20` – not in any `only()` list), `/dashboard`, `/settings/email-template-settings/{create,store,edit,delete}`, `/emergency-request-update`, `/auto-management/new-auto/quotation`, `/spare-parts/orders/status/update`, `/spare-parts/product/details`, `/pos-quotation/create`, `/auto-meter/invoice*`.
* **Fix:** add the permission middleware to both e-commerce controllers (index/show → `ecommerce_orders`; store/update/delete → the add/edit/delete permissions) and to the other listed actions; change to `Auth::routes(['register' => false, 'reset' => false])`; add a route-group middleware that rejects users without any admin role as a second line of defence.

### DEF-02 — High — Customer OTP verification can be brute-forced (no attempt limit, no expiry, OTP reusable)
* **Area:** C (C-07). **Steps:** 12 × `POST /user/otp/verify` `{phone_number:"9000000099", type:"login", otp:"1000".."1011"}`. **Expected:** lock-out / 429. **Actual:** 12 × HTTP 200 `{"success":false,"message":"Invalid Otp"}`; nothing limits further guesses (9 000 possibilities).
* **Cause:** `routes/web.php:25` has no `throttle`; `OtpController.php:127-130` compares only the code – no `updated_at` age check, `status='verified'` is never consulted, so the last OTP works for ever.
* **Fix:** `throttle:5,1` per phone + IP on send and verify, 5-minute expiry, reject OTPs already `verified`, invalidate after N failures, 6 digits.

### DEF-03 — High (configuration) — `APP_DEBUG=true`: stack traces on every 405 / 500 and in API errors
* **Area:** J (J-02, J-03, H-12, C-10). **Steps:** `GET /logout`, `GET /cart/add`, `GET /search?q[]=x`, `DELETE /api/vehicles`. **Actual:** 2 MB Laravel exception page (paths, source, queries); API returns `exception`, `file`, `line`, `trace`.
* **Fix:** `APP_DEBUG=false`, `APP_ENV=production`, `LOG_LEVEL=warning` on any shared/staging/live host; add DEF-32 pages.

### DEF-04 — High (deployment; Critical if production is served the same way) — `.env`, `.git`, log file and directory listings reachable over HTTP
* **Area:** J-09. **Steps:** `curl http://localhost/autobazaar/.env` → 200, 1 227 bytes (live SMS / Firebase / Sheets / Razorpay keys); `/.git/config` 200; `/storage/logs/laravel.log` 200 (821 KB); `/vendor/`, `/database/`, `/storage/`, `/docs/` list their contents.
* **Cause:** project root is inside the web root (`APP_URL=…/autobazaar/public`). **Fix:** point the vhost DocumentRoot at `public/`; add a root `.htaccess` that denies everything except `public/`; rotate keys if the production host was ever laid out like this. (The SMS key is also hard-coded in `OtpController.php:73`.)

### DEF-05 — High — "My Account" is prototype data, open to guests, and never shows the customer's real orders
* **Area:** E (E-03, E-04, E-05). **Steps:** place an order as customer A, open `/account`, `/account/orders`, `/account/orders/2`, `/account/addresses`; repeat logged out.
* **Actual:** heading "Welcome back, **Sivam**", counters 1/3/4/2, active order `ABZ20250905C001 TVS King Deluxe … Ready for Delivery`, address book "Sivam R" – all from `resources/fixtures/commerce.php`; A's orders `ABZ20260919C001/2` and A's two saved addresses appear nowhere. Guests get 200 on all 13 URLs. After checkout the customer has no way to find the order again except the confirmation URL.
* **Cause:** `routes/site.php:68-71` are outside the `UserAuth` group; `SiteController.php:402-451` pass `$commerce['account']` / `$commerce['order']`.
* **Fix:** move the four routes into the `UserAuth` group; feed orders/addresses from `Shop\Order` / `Shop\Address` scoped to `Auth::id()`; hide sections that have no backend yet.

### DEF-06 — High — Site search does not search
* **Area:** A-13. **Steps:** `/search?q=bajaj` and `/search?q=zzzzqqqq-no-such-thing`. **Actual:** both show "Autos (6) Accessories (59) Schemes (6) News (6)" – the full catalogue under the heading "Results for …".
* **Cause:** `SiteController.php:455-466` passes every vehicle/product/scheme/post to the view; `$query` is only echoed.
* **Fix:** filter the four collections on name/brand/category (case-insensitive `str_contains`) and show an empty state.

### DEF-07 — High — New Autos grid overflows the viewport on phones
* **Area:** J-13. **Steps:** open `/new-autos` at 390 px (or a native 500 px window). **Actual:** the second card column, the "Sort by" select and the Filters bar are cut off at the right edge; 3 of 6 models are unreadable without sideways scrolling. **Evidence:** `uat_shot_newautos_390.png`, `uat_shot_newautos_500.png`.
* **Cause (suspected):** `resources/views/site/vehicles/index.blade.php:36,76` – the `lg:grid-cols-12` wrapper's single column has no `min-w-0`, so the `grid-cols-2` list takes its min-content width (two buttons side by side per card). **Fix:** `min-w-0` on the grid children / `grid-cols-1` below `sm`, stack the two card buttons on narrow screens.

### DEF-08 — Medium — Home and Compare overflow / overlap at 390 px
* **Area:** J-14, J-15. **Actual:** home – CTA tiles and offer cards clipped on the right, "Popular Accessories" placeholder art rendered oversized over the heading and product names; compare – EMI / Fuel Cost / Expert Recommendation cards clipped ("Enquire Now" half visible). **Evidence:** `uat_shot_home_390.png`, `uat_crop_home_390_a.png`, `uat_shot_compare_390.png`. **Fix:** same `min-w-0` / `overflow-hidden` treatment on the home two-column block and compare sidebar; give the accessory rail tiles a fixed art size.

### DEF-09 — High (environment) — Requests to AutoBazaar intermittently run against another project's database
* **Area:** J-18. **Steps:** plain `curl http://localhost/autobazaar/public/account` while another Laravel project on the same Apache is in use. **Actual:** 500 `SQLSTATE[42S02] Table 'auto_bazar.shop_carts' doesn't exist (Connection: mysql …)` – 16 such log lines today from 16:58; six of my guest sessions were written into `auto_bazar.sessions`. CLI runs are never affected.
* **Cause:** no config cache (`bootstrap/cache/config.php` absent) + mod_php on threaded Windows Apache: phpdotenv's `putenv()` values from one app's request are visible to a concurrent request of the other app and, being "immutable", win over this app's `.env`.
* **Risk:** writes (orders, leads, sessions) landing in the wrong database; random 500s during demos/UAT. **Fix:** `php artisan config:cache` in both projects on this machine (and always in production), or separate PHP-FPM pools / vhosts.

### DEF-10 — Medium — Delivery address accepts junk mobile and pincode, and no city/pincode at all
* **Area:** D-04..06. **Steps:** `POST /checkout/address` with `mobile=abcde` / `12345`, `pincode=12` / `ABCDEF`, or without city and pincode. **Actual:** "Address saved." each time; orders can then be placed to an undeliverable address. **Cause:** `CheckoutController.php:43-53` (`mobile => string|max:20`, `pincode/city => nullable`). **Fix:** reuse the lead rules – `regex:/^[6-9][0-9]{9}$/`, `regex:/^[1-9][0-9]{5}$/`, city + state + pincode required.

### DEF-11 — Medium — "Free shipping above ₹999" is not implemented: standard delivery is free for every order
* **Area:** B-19. **Steps:** cart of ₹560 or ₹998, standard delivery. **Actual:** shipping ₹0 while the UI says "Free (on orders above ₹999)" and the footer strip says "Free Shipping on orders above ₹999". **Cause:** `CartService.php:28` – `'standard' => ['price' => 0, 'free_above' => true]`; with a base price of 0 the threshold at line 261 can never matter. **Fix:** set the real standard fee (business to confirm) or change the copy to "Free standard delivery".

### DEF-12 — Medium — Coupons expire one day early
* **Area:** D-35. **Steps:** admin creates a coupon with `valid_from = valid_to = today`; customer applies it today. **Actual:** "This coupon has expired." **Cause:** `valid_to` is cast to a date (00:00) and tested with `isPast()` in `CartService.php:307` and `:350`. **Fix:** `$coupon->valid_to->endOfDay()->isPast()` (vehicle offers already do this correctly with `gte($today)`).

### DEF-13 — Medium — Percent coupons above 100 % are accepted
* **Area:** I-22. **Steps:** `POST /ecommerce/coupons/store` `discount_type=percent&discount_value=150`. **Actual:** created; on a ₹9,050 cart it yields discount ₹9,050, total ₹0. **Cause:** `CouponsController.php:81` `numeric|min:0` only. **Fix:** `max:100` when type is percent (and `gt:0`); consider requiring `max_discount_amount` for high percentages.

### DEF-14 — Medium — Variants of inactive products can be added to the cart and ordered
* **Area:** B-07. **Steps:** `POST /cart/add {product_model_id:1}` (product 1 has `status_id = 2`, not shown in the shop). **Actual:** 200 "Added to your cart." **Cause:** `CartController.php:22` validates only `exists:product_brand_models,id`; `CartService::add()` (`:111`) checks neither `products.status_id`, `product_brand_models.status_id` nor `is_available`. **Fix:** constrain the lookup to active product + active/available variant, and re-check at `OrderService::placeOrder`.

### DEF-15 — Medium — Checkout offers and pre-selects online payment although no gateway is connected
* **Area:** D-34. **Steps:** open `/checkout` (`uat_shot_checkout_1440.png`). **Actual:** UPI is the default radio; Card / Net-banking / Wallet selectable; "Place Order ₹…" creates an order with `payment_status = pending` that can never be paid (the confirmation page then admits "Online payment is not yet connected"). **Fix until Razorpay is wired:** show COD only (or disable the others with "coming soon") and default to COD.

### DEF-16 — Medium — Order status flow is not enforced
* **Area:** I-12. **Steps:** on a delivered order `POST /ecommerce/orders/status-update` `order_status=placed`, then `cancelled`. **Actual:** both accepted; the coupon usage of the cancelled order stays consumed. **Cause:** `OrdersController.php:56-73` validates membership only. **Fix:** allow only forward moves along `Order::FLOW`, cancel only before `shipped`, release `shop_coupon_usages` on cancel.

### DEF-17 — Medium — Admin login with a username throws 500
* **Area:** C-15. **Steps:** `POST /login` `email=admin&password=x`. **Actual:** 500 `Unknown column 'username' in 'where clause'`. **Cause:** `Auth/LoginController.php:45-48, 63-70` fall back to a `username` column the `users` table does not have. **Fix:** fall back to `phone_number`, or validate the field as an email.

### DEF-18 — Medium — Gallery upload keeps the client's file extension (`.html` accepted)
* **Area:** I-41. **Steps:** edit a model, upload a PNG whose name is `uat.html` and which has `<script>alert(1)</script>` appended. **Actual:** accepted and stored as `public/uploads/vehicles/<uniqid>.html`. `.php` is refused by Laravel's PHP-upload guard; `.html`, `.svg`-like polyglots are not. Not re-requested over HTTP – Apache serves `.html` as `text/html` by default, which would make this a stored-XSS vector for any admin with `edit_vehicle_catalog`.
* **Cause:** `VehicleModelsController.php:553-563` names the file with `getClientOriginalExtension()`. **Fix:** use `$file->extension()` (guessed from content) or `hashName()`, and add the `extensions:jpg,jpeg,png,gif,webp` rule.

### DEF-19 — Medium — Two public master APIs return 500
* **Area:** H-16. **Steps:** `GET /api/get-auto-body-types`, `GET /api/get-transmission-types`. **Actual:** 500 `Method …CommonController::getAutoBodyTypes does not exist` / `getAutoTransmissionType`. **Cause:** `routes/api.php:64,67` point at methods that are not in `Api/V1/CommonController.php`. **Fix:** implement or remove the routes (check whether the mobile app calls them).

### DEF-20 — Medium — A guest cannot see or edit the cart
* **Area:** B-23. **Actual:** there is no cart page; the header cart icon links to `/checkout`, which requires login. The only feedback after "Add to Cart" is the badge number. **Fix:** a public `/cart` page (the partial already exists in `checkout.blade.php:100-180`) or a mini-cart drawer.

### DEF-21 — Low — Leads can be assigned to any or a non-existent user id
* **Area:** G-35. **Steps:** `POST /vehicles/leads/assign {id, assigned_to: 1}` and `99999999`. **Actual:** stored. **Cause:** `VehicleLeadsController.php:91` `nullable|integer`. **Fix:** `Rule::in($this->admins()->pluck('id'))`.

### DEF-22 — Medium — Seeded review counts collapse the first time a review is moderated
* **Area:** G-27. **Steps:** approve any review for TVS King Deluxe. **Actual:** the public page goes from "4.5 (120 reviews)" to "3.8 (4)" because only 3 review rows exist per model while `vehicle_models.rating_count` is seeded with 52–120. The page also says "Trusted by 120+ reviews" next to a list of 3. **Cause:** `VehicleModel::refreshRating()` (`:113-121`) recomputes from real rows; `VehicleCatalogSeeder` seeds marketing numbers. **Fix:** seed `rating_count/avg` from the actual rows (or import the real reviews) before launch.

### DEF-23 — Medium — Compare page "EMI Comparison" chart draws no bars
* **Area:** A-21. **Evidence:** `uat_shot_compare_1440.png`, `uat_shot_compare_390.png`. **Cause:** `compare.blade.php:238-241` – the bar has `height: N%` inside an `<li>` with no definite height, so the percentage resolves to 0. **Fix:** add `h-full` to the `<li>`.

### DEF-24 — Low — Confirmation page labels an unpaid order "Total Paid"
* **Area:** D-28. `order-confirmed.blade.php:140`. **Fix:** "Order Total" unless `payment_status === 'paid'`.

### DEF-25 — Low — Array-valued query parameters cause 500
* **Area:** A-17, H-06, G-31. `/search?q[]=x` (`SiteController.php:457`), `/api/vehicles?brand[]=x` (`Api/V1/VehicleController.php:27`), `/vehicles/leads?model_id[]=1` (`leads/index.blade.php`). **Fix:** `$request->string('q')` / validate as `string`.

### DEF-26 — Low — Search heading shows literal `&quot;`
* **Area:** A-18. `site/search.blade.php:7` builds the title with `&quot;` and the component escapes it again. **Fix:** use real quotes `"` / `“ ”` in the PHP string.

### DEF-27 — Low — Brand names shown as "Tvs"
* **Area:** A-19. Brand page title, breadcrumb, filter rail and "Brand" spec show `Tvs` for `TVS`. **Fix:** print `brand_name` as stored instead of `Str::title()`/`ucfirst` of the slug.

### DEF-28 — Low — API error responses are inconsistent
* **Area:** H-03, H-11, H-12. `ResponseService::validationError()` (`:28-39`) returns `errors` as one string, `error()` returns an array, framework errors (404/405/401/429) use Laravel's `{message}` shape without `success/status`; brand filter is case-sensitive (`VehicleCatalogService.php:54`). **Fix:** return `errors` keyed by field; render API exceptions through the envelope in `bootstrap/app.php`; `strtolower()` the brand.

### DEF-29 — Low — Coupon housekeeping
* **Area:** I-28, B-21. "Remove" only sets `status_id = 0`: the coupon stays in the list and its code is blocked for ever (`CouponsController.php:29,64-73`); a coupon can be "applied" to an empty cart (creates an empty cart row per visitor). **Fix:** filter or label inactive coupons and exclude them from the duplicate check; refuse coupons when the cart has no lines.

### DEF-30 — Low — Data-hygiene nits
* `POST /vehicles/catalogue/status` accepts any integer (`status_id=7` stored) – `VehicleModelsController.php:171` (I-43).
* Deleting a model leaves its leads (and would leave reviews) pointing at a missing model – `:136-158` (I-45).
* Order status history is displayed in `created_at` order with no tie-break, so same-minute rows appear shuffled – `Order::history()` has no `orderBy('id')` (I-18).
* `mergeGuestCart()` leaves the folded line on the closed guest cart – `CartService.php:95-98` (F-03).

### DEF-31 — Low — No security headers; server versions disclosed
* **Area:** J-04. **Fix:** middleware or Apache `Header set` for `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`, HSTS on https; `ServerTokens Prod`, `expose_php = Off`.

### DEF-32 — Low — Only the 404 page is branded
* **Area:** J-19. Add `errors/403, 405, 419, 429, 500, 503.blade.php`; the 419 ("Page Expired") one matters most because every cart/lead form can hit it after a long idle.

### DEF-33 — Low — `/user/register` page is 1.6 MB
* **Area:** K-04. All areas are printed as `<option>`s. **Fix:** load areas by city via the existing `/api/get-areas` filter or a searchable select. (Its `<title>` is also "Login - Auto Bazaar".)

### DEF-34 — Low — `UserAuth` middleware polish
* **Area:** C-13, C-14. JSON requests get a 302 + HTML instead of 401; a signed-in customer still sees the login form at `/user/login`. `UserAuth.php:20-28`, `Web/Auth/LoginController.php`.

### DEF-35 — Low — Test-drive date has no upper bound
* **Area:** G-10. A booking 5 years ahead is accepted. `VehicleLeadService.php:46` – add `before_or_equal:+60 days`.

---

## 4. Known gaps, static-review notes and items not in scope (not counted as defects)

**Known gaps**
* Online payment is not integrated (Razorpay hand-off is a comment in `CheckoutController.php:94`). Orders are COD or "pending". Apart from DEF-15 / DEF-24 nothing claims to take money.
* Fixture-driven content: offers page, government schemes, news, accessories promo banners, account sections (see DEF-05), `/used-autos` (renders the *new* catalogue with a "used" heading), `/enquiry` and `/contact` forms were not exercised as submit flows (no POST route exists for them).
* Shop filters, price range and sort are client-side only; only `?category=` is honoured from the URL (B-03).
* Throttle for leads + reviews, web + API is one shared bucket of 10 requests/minute per IP – behind carrier-grade NAT several real customers share that bucket.
* Product photos: none of the `products.image` paths exist under `public/uploads` on this machine, so every product shows a drawn placeholder – environment, not a defect. Lender logos likewise.
* `/api/get-areas` returns 960 KB; `/accessories/shop` and `/search` ship ~300 KB of HTML.

**Static review only (code read, not executed)**
* `OtpController::sendOTP` (`Web/Auth/OtpController.php`): if `type` is neither `login` nor `register`, `$validator` is undefined → 500 (`:25-39`); for `type=register` the **user row is created before the OTP is verified** (`:50-54`), so anyone can register arbitrary numbers; the SMS API key is hard-coded (`:73`); `verifyOTP` overwrites `users.created_at` on every login (`:134`).
* `OrderService::nextOrderNumber()` (`:140-147`) is "count + 1" with no lock: two orders placed in the same instant collide on the unique index and one customer gets a 500. `placeOrder()` reads the cart totals before the transaction, so two truly parallel submits of the same cart could both pass the empty-cart check (D-26 not executed).
* `documents.{idx}.file` and `variant_default` are matched against re-indexed rows (`VehicleModelsController::rows()` + `:527`), so removing a middle row in the form before upload can silently drop a document / pick the wrong default. Not executed.

**Not executed – would hit a live third-party service (hard safety rule)**

| ID | Item | Reason |
|---|---|---|
| N-01 | `POST /auth/send-otp` (admin OTP) | live SMS |
| N-02 | API `auth/sendOtp`, `auth/user-sendOtp`, FairPrice OTP | live SMS |
| N-03 | Admin Events create/store | Firebase push |
| N-04 | API `auto-enquiries`, `quotation/store` | Google Sheets write |
| N-05 | Razorpay order creation (ride payments) | live gateway |
| N-06 | FairPrice ride POST APIs | out of scope beyond GET smoke; FCM/dispatch |
| N-07 | `POST /account-delete-store`, `POST /password/email` | not reviewed for outbound mail/SMS, so not fired |

(plus C-05, D-26 and B-03 inside the tables = 10 not executed.)

---

## 5. Test data clean-up evidence

All data was created through the application, except: two test users inserted by SQL (`UAT Tester A` id 4676, `UAT Tester B` id 4677), line prices forced on my own cart items for the shipping-threshold test, and a login key planted in my own session row for the logout test. A third user (id 4678) was created by the application itself in the `/register` test. Existing rows that a test had to change were restored from a snapshot taken before testing: `finance_lender_rates` (5 rows), `auto_financiar` id 6, and `vehicle_models` ids 1–6 (cached `rating_avg/rating_count`) – restore script reports all three **identical to snapshot**.

| Table | Before | Peak during test | After |
|---|---|---|---|
| shop_carts | 2 | 10 | 2 |
| shop_cart_items | 3 | 15 | 3 |
| shop_addresses | 1 | 4 (+6 junk rows deleted mid-test) | 1 |
| shop_coupons | 1 | 6 | 1 |
| shop_coupon_usages | 1 | 3 | 1 |
| shop_orders | 1 | 4 | 1 |
| shop_order_items | 2 | 8 | 2 |
| shop_order_status_histories | 1 | 11 | 1 |
| vehicle_models | 78 | 79 | 78 |
| vehicle_variants | 15 | 18 | 15 |
| vehicle_prices / specifications / features / offers / stock / scores / suitability | 6 / 66 / 17 / 5 / 6 / 48 / 36 | +1 / +1 / +1 / +5 / +1 / +1 / +3 | 6 / 66 / 17 / 5 / 6 / 48 / 36 |
| vehicle_images / vehicle_documents | 6 / 0 | 8 / 1 | 6 / 0 |
| vehicle_reviews | 18 | 19 | 18 |
| vehicle_enquiries | 0 | 47 | 0 |
| finance_lender_rates / auto_financiar | 5 / 7 | 5 / 7 (row values changed, restored) | 5 / 7 |
| users | 4624 | 4627 | 4624 |
| model_has_roles / model_has_permissions | 2 / 0 | 2 / 0 (probe rows only inside rolled-back transactions) | 2 / 0 |
| personal_access_tokens / auto_otps / jobs / failed_jobs | 5833 / 5550 / 0 / 0 | unchanged | 5833 / 5550 / 0 / 0 |
| sessions | 7 | 296 | 1 |
| cache | 2 | 3 | 1 |

Pre-existing rows verified untouched: coupon `AUTO5` (`updated_at 2026-09-10 15:41:55`), order `ABZ20260910C001` (`placed / cod_pending`), cart items 1–3.

Notes for full transparency:
* **sessions 7 → 1:** I deleted only rows that did not exist in my snapshot (UA `UAT-InProcess`, `UAT-Tester-curl`, `UAT-Tester-chrome`, plus one `curl/8.1.2` row created in the same second as my curl run). The other six pre-existing rows had expired and were removed by Laravel's own session garbage-collection lottery, which my traffic triggered.
* **cache 2 → 1:** the two snapshot rows were a rate-limiter counter + timer for `127.0.0.1` (from an earlier developer test), cleared by my throttle resets; the remaining row is the Spatie permission cache the app rebuilt.
* Auto-increment counters advanced (e.g. next user id, next `VE-` enquiry number, next coupon id). Rows are gone, numbers are not re-used.
* Uploaded test files were removed by the application's own delete; the two directories it had created (`public/uploads/vehicles`, `…/docs`) were empty and I removed them – `public/uploads` is back to its original listing.
* `storage/logs/laravel.log` contains the error entries my negative tests produced (append-only, left as is).
* **Outside the `autobazaar` database (caused by DEF-09):** six guest-session rows with `user_agent = 'UAT-Tester-curl'` were written by Apache into **`auto_bazar.sessions`**. I did not write to that database. They are inert and will expire; to remove them now: `DELETE FROM auto_bazar.sessions WHERE user_agent LIKE 'UAT-Tester-%';`
* While stopping my throw-away static server I ran `taskkill /F /IM php.exe`, which ends every `php.exe` CLI process on the machine, not only mine. Apache/mod_php is unaffected, but if a `php artisan serve`, queue worker or similar was running in another terminal it will need to be restarted.
* No file inside `C:\xampp\htdocs\jp_auto` was read or written. The only file added to `autobazaar` is this report.

---

## 6. Fix status (added 2026-09-19, after the UAT)

Commits `0b88fc0`, `03326d9`, `9a06365`, `edc6f24`, `467c5a2` (tag `release-2026-09-19`).
Re-test after the fixes: 75 admin + 31 public pages load, 411 routes all resolve to an existing
controller method, 56 API GET endpoints return JSON with no 5xx, 29 business-rule checks pass
(run inside a rolled-back transaction), security checks repeated over real HTTP.

| ID | Status | What was done |
|---|---|---|
| DEF-01 | Fixed | Permission middleware on E-commerce Orders/Coupons and the other listed admin actions; new `staff` middleware on the whole admin group (customers get 403); public `/register` disabled. |
| DEF-02 | Fixed | OTP valid 10 min, single use, 5 wrong guesses then discarded; `throttle` on send (5/min) and verify (10/min). Still 4 digits — the SMS template is fixed-length. |
| DEF-03 | Config | Nothing to change in code. Set `APP_DEBUG=false`, `APP_ENV=production` on any shared/live host. API errors no longer carry traces when debug is off; branded error pages added (DEF-32). |
| DEF-04 | Fixed locally | Root `.htaccess` returns 403 for everything outside `/public`. On live, point the domain at `/public`. |
| DEF-05 | Fixed | Account routes behind login; dashboard, orders, order detail, addresses, enquiries and profile show the customer's own data. Sections without a backend say "coming soon" instead of sample data. |
| DEF-06 | Fixed | Search filters autos, accessories, schemes and news on every word; empty state added. |
| DEF-07/08 | Fixed | `min-w-0` on layout columns site-wide; verified at a true 390 px on New Autos, Home, Compare. |
| DEF-09 | Fixed | `Env::disablePutenv()` in `bootstrap/app.php` — the app reads only its own `.env`. |
| DEF-10 | Fixed | 10-digit mobile, 6-digit pincode, city and state required. |
| DEF-11 | Fixed — confirm amount | Standard delivery ₹49 below ₹999, free at/above. The ₹49 is a placeholder: `CartService::DELIVERY`. |
| DEF-12 | Fixed | Coupon valid through the end of `valid_to`. |
| DEF-13 | Fixed | Percent ≤ 100, value > 0. |
| DEF-14 | Fixed | Only active + available variants of active products can be added or ordered (checked again at order time). |
| DEF-15 | Fixed | COD only; other methods shown disabled as "Coming soon". One constant re-enables them: `CheckoutController::ONLINE_PAYMENTS_ENABLED`. |
| DEF-16 | Fixed | Forward-only status flow; cancel only before shipping; delivered/cancelled final; cancel releases the coupon usage. |
| DEF-17 | Fixed | Admin login falls back to `phone_number`, not a non-existent `username` column. |
| DEF-18 | Fixed | Upload extension taken from file content; `extensions:` rule added. |
| DEF-19 | Fixed | `get-transmission-types` lists the master table; `get-auto-body-types` returns an empty list (table never existed). |
| DEF-20 | Fixed | Public `/cart` page; header cart icon points to it. |
| DEF-21 | Fixed | Leads can only be assigned to staff users. |
| DEF-22 | Fixed | Cached rating = approved reviews (seeder + existing rows refreshed: 4.7 from 3). |
| DEF-23 | Fixed | EMI comparison bars render. |
| DEF-24 | Fixed | "Order Total" unless paid. |
| DEF-25 | Fixed | Array query parameters ignored on search, vehicle API and admin leads. |
| DEF-26 | Fixed | Real quotation marks in the search heading. |
| DEF-27 | Fixed | TVS / OSM stay upper-case; BAJAJ → Bajaj. |
| DEF-28 | Partly | All API framework errors (401/403/404/405/429/500) use `{success,status,message}`; brand filter case-insensitive. `ResponseService::validationError` left unchanged on purpose — the mobile app reads its current shape. |
| DEF-29 | Fixed | Removed coupons leave the list and free their code; no coupon on an empty cart. |
| DEF-30 | Fixed | Status toggle accepts 0/1 only; deleting a model keeps its leads (unlinked) and removes its reviews; history ordered by id; merged guest line removed. |
| DEF-31 | Fixed | `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`, HSTS on https. `ServerTokens`/`expose_php` are server settings. |
| DEF-32 | Fixed | Branded 403, 405, 419, 429, 500, 503. |
| DEF-33 | Fixed | Register page 1.6 MB → 7 KB; areas load per district. Title corrected. |
| DEF-34 | Fixed | JSON requests get 401; signed-in customers are redirected away from the login form. |
| DEF-35 | Fixed | Test drive within 60 days. |

**Also fixed from the static-review notes:** `sendOTP` no longer crashes on an unknown `type`; login no longer
overwrites `users.created_at`; SMS key read from `config('services.ping4sms.key')` (`PING4SMS_KEY` in `.env`,
with the old value as fallback so SMS keeps working); order numbers take the highest suffix + 1 under a row lock;
`api/auto_posts/create|edit` stubs removed; `fairprice/v2/v2-test` returns JSON.

**Not changed (needs a decision or is outside code):**
* `register` still creates the user row before the OTP is verified — changing it alters the mobile-web sign-up flow.
* Admin vehicle form: removing a middle row before uploading a document can mis-match the file to its row (not reproduced; needs a form-level fix).
* Lead/review throttle is one 10/min bucket per IP.
* Online payment and courier integration remain future work.
