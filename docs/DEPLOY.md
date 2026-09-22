# Moving AutoBazaar to the new server

Written for the plan you chose: a **brand new server**, the live database
**cloned under a new name**, and a zip that already contains `vendor` and
`public/build` so nothing has to be installed on the server.

The old site keeps running untouched the whole way through. Nothing here
changes it.

---

## Before you start, from the OLD server

**One thing to take a copy of: the database.** In phpMyAdmin, export
`auto_bazar` — structure and data — as a `.sql` file.

### The pictures are already sorted

The database pointed at 761 pictures that were not on the development machine —
they had only ever existed on the live server. They have since been downloaded,
so **`public/uploads` now holds them and the zip carries them across**. Nothing
to copy by hand.

| Folder | Fetched | Used for |
|---|---:|---|
| `uploads/auto_images/` | 439 | Photos on customers' auto listings |
| `uploads/profile_pictures/` | 131 | User profile pictures |
| `uploads/spareparts/products/` | 130 | Accessory product photos |
| `uploads/events/` | 26 | Event pictures |
| `uploads/authorize_seller/` | 6 | Dealer logos |
| `uploads/finance/` | 5 | Finance Options lender logos |
| `uploads/brands/` | 3 | Brand logos |

21 more were missing from the live server too — the database points at files
that were deleted there at some point. None of them reach a page: 18 belong to
three listings that are not active, 2 are profile pictures the website never
shows, and 1 is the logo of a lender that is switched off. Those pages use their
own fallback.

To check the position at any time, or after uploading more pictures:

```bash
php docs/tests/missing-uploads-report.php     # what is referenced but not here
php docs/tools/fetch-missing-uploads.php      # fetch them from the old server
```

The second one takes the address as an argument if the old site moves:
`php docs/tools/fetch-missing-uploads.php https://old-address.com`

---

## 1. Make the zip (on the development machine)

Clear anything that was built for this machine first:

```bash
php artisan view:clear
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

Then zip the project folder, **leaving out**:

| Leave out | Why |
|---|---|
| `.env` | It holds this machine's settings. The server gets its own. |
| `.git` | Not needed to run, and it is 532 MB. |
| `node_modules` | Only needed to build, and `public/build` is already built. |
| `storage/logs/*` | This machine's logs. |
| `storage/framework/cache/*`, `sessions/*`, `views/*` | Built for this machine; rebuilt automatically. |

Keep everything else — **including `vendor` and `public/build`**.

---

## 2. Set up the database (on the new server)

```sql
CREATE DATABASE auto_bazar_new CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Import the `auto_bazar` export into `auto_bazar_new`, then run the six files
from `docs/sql/` **in this order**:

1. `01_ecommerce_tables.sql`
2. `02_migrate_old_app_orders.sql`
3. `03_vehicle_catalog_tables.sql`
4. `04_vehicle_catalog_permissions.sql`
5. `05_performance_indexes.sql`
6. `06_catalogue_content.sql`

All six are safe to run twice; each skips whatever is already there. This takes
the database from **74 tables to 96**.

**That is the whole database step. No artisan commands, no seeders.**

File 06 exists because files 01–05 create the vehicle catalogue tables but put
nothing in them, and an empty catalogue means an empty New Autos page, an empty
Compare page and no lenders under Finance Options. It carries 306 rows across 12
tables — the catalogue and the finance lenders. No personal data.

---

## 3. Put the files on the server

1. Extract the zip into the web folder. The pictures come with it.
2. Make `storage/` and `bootstrap/cache/` writable:

```bash
chmod -R 775 storage bootstrap/cache
```

---

## 4. Create the `.env` file

Copy `.env.example` to `.env` and set these. **Everything in this table matters.**

```
APP_NAME=AutoBazaar
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-real-domain.com

DB_DATABASE=auto_bazar_new
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

PING4SMS_KEY=ask-for-this-key
```

| Setting | What happens if it is wrong |
|---|---|
| `APP_ENV=production` | Left as `local`, **Google never lists the site**. |
| `APP_DEBUG=false` | Left as `true`, any error shows your file paths and settings to the public. |
| `APP_URL` | Wrong, and images and links point at the wrong address. No trailing slash. |
| **`PING4SMS_KEY`** | **Missing, and nobody can sign in** — not on the website, not on either mobile app. It used to sit in the code; it does not any more. |

Then generate the key:

```bash
php artisan key:generate
```

---

## 5. Finish up

```bash
php artisan config:clear
php artisan permission:cache-reset
php artisan storage:link      # only if you get missing-file errors under /storage
```

Point the web server at the **`public`** folder, never the project folder.

---

## 6. Check it worked

- The home page opens and shows autos
- Open any auto (New Autos → pick one) — **Finance Options** shows all 5 lenders with their logos
- **Accessories** shows product photos, not grey boxes
- **Used Autos** shows listings with photos
- `/dashboard` — sign in, and the menus match what you had
- Send yourself an OTP from `/user/login` to prove the SMS key works
- Open Settings → Payments and enter the Razorpay keys (the old database has
  none saved), then press Save

---

## If something goes wrong

Nothing here touched the old site. Point the domain back at the old server and
it is exactly as it was — the old database was never modified, only copied.

---

## What was rehearsed before writing this

Not theory. On a copy of your real `auto_bazar`:

| Check | Result |
|---|---|
| The six SQL files run in order | **74 → 96 tables** |
| Everything the new features need exists afterwards | 11 tables checked |
| The site and admin open on the upgraded database | 12 pages |
| Real data survives | users, listings, enquiries, quotations all intact |
| New Autos lists autos, Finance Options lists all 5 lenders | verified |
| Running the files twice changes nothing | verified |

Re-run it yourself any time:

```bash
php docs/tests/deploy-sql-test.php
```
