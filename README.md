# AutoBazaar

An autorickshaw marketplace built with Laravel 12. One codebase serves three things:

| Part | Who uses it | Where it lives |
|---|---|---|
| **Website** | Customers buying autos and accessories | `/` |
| **Admin panel** | Staff | `/dashboard` |
| **Mobile APIs** | The driver/dealer app and the FairPrice ride app | `/api/…` |

---

## Getting it running

You need **PHP 8.2+**, **Composer**, **Node 18+** and **MySQL**. XAMPP has all but Node.

```bash
git clone https://github.com/aswin00802/autobazaar.git
cd autobazaar

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
```

Open `.env` and set your database. Create the database first — it can be completely empty:

```
DB_DATABASE=autobazaar
DB_USERNAME=root
DB_PASSWORD=
```

Then build it:

```bash
php artisan migrate --seed
```

That takes about half a minute. It creates all 91 tables and fills in the lists the site cannot work without — roles, permissions, 8 auto brands, 78 models, 246 countries, 4,092 states, 47,941 cities and 16,701 areas.

Now serve it:

```bash
php artisan serve
```

and open **http://localhost:8000**.

> **Using XAMPP instead?** Put the project in `C:\xampp\htdocs`, point your browser at
> `http://localhost/autobazaar/public`, and set `APP_URL` in `.env` to that same address.

### Signing in

| | |
|---|---|
| Address | `/dashboard` |
| Mobile | `9999900000` |
| Password | `autobazaar` |

**Change that password the moment you sign in.** It is written here in a public repository, so it is not a secret.

Customers sign in on the website with a mobile number and a one-time code by SMS — that needs `PING4SMS_KEY` in `.env` (see below).

---

## Opening it from another device

To see the site on your phone, or on a second computer on the same wifi:

**1. Serve it on every network interface, not just this machine:**

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

**2. Find this machine's address on the network:**

```bash
ipconfig          # Windows — look for "IPv4 Address", e.g. 192.168.1.5
ifconfig          # macOS / Linux
```

**3. Set `APP_URL` in `.env` to that address**, then `php artisan config:clear`:

```
APP_URL=http://192.168.1.5:8000
```

This step matters. Without it, images, stylesheets and form links are still built with `localhost`, which on your phone means *your phone* — so the page loads half-broken.

**4. Open `http://192.168.1.5:8000` on the other device.**

If nothing loads, it is almost always the firewall. On Windows, allow PHP through Windows Defender Firewall for private networks, or run once as administrator:

```powershell
New-NetFirewallRule -DisplayName "PHP dev server" -Direction Inbound -LocalPort 8000 -Protocol TCP -Action Allow
```

Both devices have to be on the same network — a phone on mobile data cannot reach it.

**On XAMPP**, the equivalent is `http://192.168.1.5/autobazaar/public`, with Apache allowed through the firewall. Apache already listens on every interface.

---

## What is where

```
app/Http/Controllers/Api/          the mobile app APIs  — see the warning below
app/Http/Controllers/Web/          the customer website
app/Http/Controllers/admin/        the admin panel
app/Services/                      cart, fares, payments, used-auto listings
app/Support/                       shared helpers (site data, settings)
config/site_design.php             website appearance switches
config/admin_lists.php             paging on the long admin screens
database/migrations/               all 110 migrations
database/seeders/                  the seed data and its source files
docs/USER-MANUAL.md                written for whoever runs the business
docs/tests/                        the test scripts below
resources/views/site/              the customer website
resources/views/admin/             the admin panel
```

### Never break the mobile apps

Drivers keep old versions of the app installed for months. Anything under
`app/Http/Controllers/Api/` is a contract with phones that will never be updated.
Add new endpoints rather than changing existing ones.

---

## Settings that are not in the repository

`.env` is not committed, and neither are any keys. Copy `.env.example` and fill in:

| Setting | What breaks without it |
|---|---|
| `PING4SMS_KEY` | **Nobody can sign in.** Every OTP on the website and both apps goes through this gateway. Ask the project owner for the key. |
| `RAZORPAY_KEY` / `RAZORPAY_SECRET` | Online payments. Only a fallback — the real keys are entered in the admin panel under Settings, Payments, and stored encrypted. |

Mail is set up in the admin panel under Settings, SMTP, not in `.env`.

---

## Pictures

Uploaded pictures — auto photos, product images, profile pictures — are **not** in
this repository. They are server content and are restored from a backup.

A fresh clone therefore has none of them, and that is fine: anything missing under
`public/uploads` is served as `_no-image.png` instead of a broken image. The rule
doing that is `public/uploads/.htaccess`. On nginx it is one line:

```nginx
location /uploads/ { try_files $uri /uploads/_no-image.png; }
```

---

## Testing

All of these are plain scripts — no test framework to set up. Run them from the
project folder.

```bash
php docs/tests/fresh-install-test.php   # builds a database from nothing and checks a clone runs
php docs/tests/schema-diff-test.php     # compares a fresh database against the live one
php docs/tests/run-all-tests.php        # 40 checks: smoke, UAT, design, security
php docs/tests/crawl-site.php           # opens all 80 pages, checks links, images, SEO
php docs/tests/full-flow-test.php       # a customer signs up, orders, and is found in the admin
php docs/tests/admin-screens.php        # opens all 50 admin screens and counts their rows
php docs/tests/admin-lists-test.php     # the paged admin lists
```

The first two build a scratch database called `autobazaar_fresh`. They never write
to the one you are working on. `full-flow-test.php` creates a throwaway customer on
a test number and deletes it again — that number is not a real Indian mobile series,
so no SMS reaches anybody.

`crawl-site.php` needs the site reachable at the address written at the top of it.

---

## Deploying

1. Back up the database.
2. Pull, then `composer install --no-dev --optimize-autoloader` and `npm run build`.
3. `php artisan migrate --force` — safe on the existing database, every migration checks before it changes anything.
4. `php artisan config:clear` and `php artisan permission:cache-reset`.
5. Set `APP_ENV=production` and `APP_DEBUG=false`. **Without this Google will never list the site.**
6. Put `PING4SMS_KEY` in the server's `.env`, or no one can sign in.
7. Open Settings, Payments and press Save once, so the keys are re-encrypted for that server.

The full go-live checklist is at the end of [docs/USER-MANUAL.md](docs/USER-MANUAL.md).
