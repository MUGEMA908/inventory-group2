# Inventory Management System — ITLAW701 (Group 2)

PHP 8 (OOP) + MySQL (PDO prepared statements with `?` placeholders) · Ngoma College, Rwanda Polytechnic

## Login accounts

| Actor            | Username      | Password        |
|------------------|---------------|-----------------|
| Logistic officer | `logistic`    | `Logistic@2026` |
| Coordinator      | `coordinator` | `Coord@2026`    |
| HoD              | `hod`         | `HoD@2026`      |

Passwords are stored with `password_hash()` and checked with `password_verify()`.

## Scenario coverage

| # | Step | Page |
|---|------|------|
| 1, 5, 10 | Logs in | `login.php` |
| 2, 6, 11 | Changes password | `change_password.php` |
| 3 | Logistic registers new equipment | `equipment_register.php` |
| 7 | Coordinator requests new equipment | `request_new.php` |
| 8 | Coordinator views equipment (paginated, searchable) | `equipment_list.php` |
| 12 | HoD approves (or rejects) requests (paginated) | `approve_requests.php` |
| 4, 9, 13 | Logs out | `logout.php` |

## Structure

```
config/        config.php (+ config.local.php for hosting credentials)
src/           Database, Auth, User, Equipment, EquipmentRequest, Paginator (OOP classes)
includes/      bootstrap, layout header/footer, shared table
assets/css/    style.css
database.sql   tables + default accounts + sample data
```

## Run locally (XAMPP/WAMP)

1. Copy the folder into `htdocs/inventory`.
2. In phpMyAdmin create a database `inventory_db`, then **Import** `database.sql`.
3. Open `http://localhost/inventory/`.

## Deploy (000webhost / InfinityFree / any PHP host)

1. Create a MySQL database in the host panel; import `database.sql` with phpMyAdmin.
2. Copy `config/config.local.php.example` to `config/config.local.php` and enter the host's DB name, user, password.
3. Upload all files to `public_html` (File Manager or Git).
4. Open your site URL and test the three logins.

## Security features

Prepared statements (`?`), `password_hash`/`password_verify`, CSRF tokens on every form, output escaping, role-based access, session ID regeneration on login.
