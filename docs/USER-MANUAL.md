# AutoBazaar — User Manual

For the person running AutoBazaar day to day.
Written in plain language. No programming knowledge needed.

Last updated: 21 September 2026

---

## Contents

1. [The three parts of AutoBazaar](#1-the-three-parts-of-autobazaar)
2. [Signing in to the admin panel](#2-signing-in-to-the-admin-panel)
3. [The dashboard](#3-the-dashboard)
4. [Everyday jobs](#4-everyday-jobs)
5. [Settings you can change yourself](#5-settings-you-can-change-yourself)
6. [Changing how the website looks](#6-changing-how-the-website-looks)
7. [What updates by itself and what does not](#7-what-updates-by-itself-and-what-does-not)
8. [Rules to follow](#8-rules-to-follow)
9. [When something looks wrong](#9-when-something-looks-wrong)
10. [For your developer](#10-for-your-developer)

---

## 1. The three parts of AutoBazaar

| Part | Who uses it | Where |
|---|---|---|
| **Website** | Customers looking for an auto | your web address |
| **Admin panel** | You and your staff | your web address + `/dashboard` |
| **Mobile apps** | Drivers and FairPrice customers | Google Play |

All three share one database. Change a price in the admin panel and the website shows it at once.

---

## 2. Signing in to the admin panel

1. Go to your web address followed by `/dashboard`.
2. Sign in with your mobile number and password.
3. After five wrong attempts the system locks that account for a while. This is on purpose, to stop guessing.

**Two kinds of staff account:**

- **Super Admin** sees everything.
- **Admin** sees only what you allow under Users & Access.

**Important:** a staff account cannot shop on the website. If you open My Account or the cart while signed in as staff, you are sent back to the dashboard. To buy something yourself, use a separate customer account with a different mobile number.

---

## 3. The dashboard

The first page after signing in.

- **Needs Attention** at the top lists everything waiting for you: autos pending approval, new orders, driver requests, emergency requests. Click any of them to go straight there. It refreshes by itself every 30 seconds.
- **Quick action buttons** in the welcome bar: Add Used Auto, Add New Auto, Add Product, New POS Quotation, Create Event, Fare Settings.
- **Number tiles** grouped under Users, Listings, Leads, FairPrice Rides and Store. Click any tile to open its full list. Some tiles show a small green or red figure, which compares this week with last week.
- **Last 7 Days chart** showing new registrations against enquiries.
- **Recent activity** panels for enquiries, rides and today's new users.

**Search box at the top:** type a name, phone number, auto ID, registration number, order number or ride number. It searches users, autos, rides and orders together. Press the `/` key from anywhere to jump into it.

**Light and dark:** the moon icon beside the search box switches the admin panel between light and dark. Your choice is remembered.

---

## 4. Everyday jobs

### Approving an auto a user posted

1. Dashboard, then **Autos pending approval** (or Listings, then Pending Approval).
2. Open the auto and check the details and photos.
3. Approve or reject it.

Once approved and marked **active** and **used**, it appears on the website's Used Autos page immediately.

### Adding a used auto yourself

Listings, then Used Autos, then Add.

**Fill these in, because the website shows them:**

| Field | What the website does with it |
|---|---|
| Brand and model | The listing title |
| Registration year | The year badge on the photo |
| Kilometres | Shown under the title |
| Owner | "1st Owner", "2nd Owner" |
| Fuel type | Coloured tag, and used by the fuel filter |
| **Price expectation** | **The price on the website.** Leave it empty and the page says "Price on request" |
| RC, FC, Permit, Insurance | Green or red document badges |
| RTO | Shown as the location. Type a place name like "Redhills" or a code like "TN 05" |
| Photos | The listing photo. Upload JPG or PNG |

**Two things to know about photos:**

- Photos from an iPhone are often in HEIC format, which web browsers cannot show. Convert them to JPG before uploading.
- A listing with no usable photo shows the brand logo and the words "Photos on request". It still works, but listings with real photos get far more enquiries.

**Never shown on the website:** the seller's name, the seller's phone number, or the full registration number. The registration shows only as "TN 18 ** 1902". All enquiries come to you.

### Removing a sold auto

Change its status to **sold** or **deleted**. It disappears from the website straight away, and its page politely shows "not found".

### Handling enquiries

Leads, then Enquiries or Quotation Requests. Each row shows the customer and the auto. Change the status as you follow up.

### Finding a row in a long list

Three screens hold too many rows to show at once — **City** (47,941), **State** (4,092) and **Country** (246). Each shows 50 at a time, with a **search box above the table** and **page numbers below it**.

Type a name into that search box and press Search. It looks through the whole table, not just the 50 rows in front of you, and the line under the table tells you how many matched. Searching a city also matches its state, so "Tamil" finds all 891 Tamil Nadu cities.

These three have **no Export button**. They hold standard reference data — the world's countries and states, and every city in India — so there is nothing there to take away; you look a row up rather than download the list. Every other admin screen, including Users and Quotation Requests, keeps its usual Export.

### Orders

- **E-commerce, then Orders** are website orders, with payment and delivery address.
- **App Orders** are older orders from the mobile app, split into Pending, Completed and Cancelled.

### FairPrice rides

Drivers & Rides, then FairPrice Rides shows every booking. Click a row to see the full ride, the driver, the customer and the payment.

Fare rates are under FairPrice Fares: per kilometre rate, waiting charges, pickup fare, hire and tour rates.

---

## 5. Settings you can change yourself

All under **Settings, General** unless stated.

### Business details

| Setting | Where it appears |
|---|---|
| Business Name | The website, the browser tab, Google |
| **Business Mobile** | Every phone number on the website, the tap-to-call link, **and every WhatsApp button** |
| Business Email | The footer and the contact page |
| Business Address | The footer and the contact page |

Type the mobile as 10 digits. All three ways work: `8608860893`, `86088 60893` or `+91 86088 60893`.

### Social media links

The section called **Website Social Media Links** has Instagram, Facebook, YouTube, X (Twitter) and LinkedIn.

- Paste the full link, starting with `https://`.
- Leave a box empty and the website keeps whatever link it has now.
- YouTube, X and LinkedIn show no icon until you add a link.
- The WhatsApp icon always follows the Business Mobile above. There is no separate box for it.

### App store review login

Google and Apple reviewers cannot receive your SMS, so two phone numbers sign in with a fixed code.

**Switch it on while an app update is being reviewed, and switch it off afterwards.** Leaving it on is a security risk.

### Payment settings

Settings, then Payments.

- Razorpay: Key ID, Key Secret, and optionally a Webhook Secret.
- Stripe: Publishable Key, Secret Key, Webhook Secret. Stored for later; nothing charges through Stripe yet.
- Tick "Enable" for a gateway before entering its keys. Unticking keeps the keys but stops the gateway.
- Secrets are stored scrambled and shown as dots. Click the eye icon to read one.

**Important:** after moving the site to a new server, open this page and press Save once. Scrambled values are tied to one server.

### Mail settings

Settings, then SMTP. Host, port, username, password, encryption and the from address. Saving here takes effect immediately.

---

## 6. Changing how the website looks

Some of the website's appearance is controlled by a small settings file your developer can edit in one line: `config/site_design.php`.

| Setting | What it does | Now |
|---|---|---|
| `loader` | The driving auto when a page loads. `every_page`, `first_visit` or `off` | every_page |
| `card_hover` | Auto cards lift and zoom when pointed at | on |
| `fuel_chips` | Coloured fuel tags on cards | on |
| `amber_accents` | Yellow underlines and price highlights | on |
| `count_up` | Numbers counting up in the green strip | on |
| `short_menu` | Groups three menu items under "More" | on |
| `button_glow` | Buttons glow when pointed at | on |
| `font` | `poppins`, `roboto` or `default` | poppins |
| `footer_credit` | The "Crafted by Ziga Infotech" line | on |
| `footer_browse_links` | Brand and model link rows in the footer | **off** |
| `auth_pages` | Sign in, sign up and OTP screens in the website's design. `false` brings back the old standalone pages | on |

Each is separate. Turning one off never affects the others.

After changing this file on the live server, your developer runs one command: `php artisan config:clear`.

---

## 7. What updates by itself and what does not

### Updates by itself from your data

- Home page, New Autos, brand pages, model pages
- **Used Autos and each used auto page**
- Compare, Enquiry, Finance and EMI, Search
- Accessories shop, cart, checkout, orders, My Account
- The phone number, email, address and social links everywhere
- The sitemap that Google reads

### Needs a developer to change

- About Us, Contact page text, FAQ answers, Buying Options
- Government Schemes, Auto News articles
- Terms, Privacy Policy
- The offer cards on the Offers page
- Menu items and footer link columns

---

## 8. Rules to follow

### Never break the mobile apps

Drivers keep old versions of the app for months. If an existing app feature is changed on the server, those phones stop working. Any new feature must be added separately so old apps keep running.

### One number for WhatsApp

Changing Business Mobile also changes every WhatsApp button. Check that the new number actually has WhatsApp before saving.

### Prices are typed by hand

The website shows exactly what is in the Price Expectation box. Check it before approving a listing.

### Staff accounts do not shop

Use a separate customer account with a different mobile number.

---

## 9. When something looks wrong

| What you see | What to do |
|---|---|
| A change in Settings has not appeared | Wait a minute, then refresh. Settings are remembered for up to 10 minutes, but saving clears that straight away. |
| A used auto is missing from the website | Check its status is **active** and its type is **used auto**. |
| A listing shows "Price on request" | The Price Expectation box is empty. |
| A listing shows a logo instead of a photo | No usable photo. Upload a JPG. HEIC files from iPhones do not work. |
| A document badge says "Ask us" | That box is empty or has unclear text. Use a date like `3/2027`. |
| The website shows an error on every page | Call your developer. Something was changed in a shared file. |
| The app cannot log in | Check whether the app store review login was left switched on or off wrongly. |
| City, State or Country only shows 50 rows | That is on purpose. Use the search box above the table, or the page numbers below it. The search looks through every row, not only the 50 on screen. |

**Every page on the website has been checked.** If a page shows an error, it is new, so report it with the exact address.

---

## 10. For your developer

### Files to know

| File | Purpose |
|---|---|
| `config/site_design.php` | Website appearance switches |
| `config/site_links.php` | Play Store link, and the iPhone link when the app launches |
| `config/admin_lists.php` | How many rows City, State and Country show per page (0 = all of them, the old behaviour) |
| `app/Support/SiteData.php` | Merges admin settings into the website's shared data |
| `app/Services/UsedAutoService.php` | Turns `auto_posts` rows into website listings |
| `docs/sql/` | Six SQL files for the live database |
| `docs/tests/` | The two test scripts below |

### Testing before and after a deploy

```
php docs/tests/run-all-tests.php      # 40 checks: smoke, UAT, design, security
php docs/tests/crawl-site.php         # opens all 80 pages, checks links, images, SEO
php docs/tests/full-flow-test.php     # a customer signs up, orders, and is found in the admin
php docs/tests/admin-screens.php      # opens all 50 admin screens and counts their rows
php docs/tests/admin-lists-test.php   # the paged lists: search, page links, row numbering
```

`full-flow-test.php` creates a throwaway customer on the test number `5000000001`, takes them
all the way through to a placed order, checks the admin can see it, then deletes everything it
made. That number is not a mobile series in India, so no SMS reaches anybody.

Both restore any data they touch. The crawler needs the site reachable at the address written at the top of that file.

If a Blade file gains a style class the site has not used before, run `npm run build` as well — the stylesheet is compiled from the classes actually in use, so an unbuilt class simply does nothing.

### Live database update

Run these in order, in phpMyAdmin, after a full backup:

1. `docs/sql/01_ecommerce_tables.sql` — cart and order tables
2. `docs/sql/02_migrate_old_app_orders.sql` — moves old app orders across
3. `docs/sql/03_vehicle_catalog_tables.sql` — vehicle catalogue tables
4. `docs/sql/04_vehicle_catalog_permissions.sql` — admin permissions
5. `docs/sql/05_performance_indexes.sql` — speed indexes
6. `docs/sql/06_catalogue_content.sql` — the vehicle catalogue and finance lenders

All six are safe to run twice. Each skips whatever already exists.

After running them: `php artisan permission:cache-reset`, then `php artisan config:clear`.

### Checklist for going live

- [ ] Set the environment to production, otherwise Google will never list the site
- [ ] Run the six SQL files
- [ ] Open Payment Settings and press Save once, so the keys are scrambled for this server
- [ ] Check the phone number, email, address and social links in Settings
- [ ] Switch the app store review login off unless an app update is under review
- [ ] Submit the sitemap to Google Search Console
- [ ] Remove the demo reviews. Every model currently shows 4.7 from 3 reviews, and Google penalises invented ratings
- [ ] Turn on compression and asset caching on the server
- [ ] Run both test scripts

---

*Questions about anything in this manual should go to your developer with the page address and a screenshot.*
