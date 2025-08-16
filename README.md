# EquipRent — Equipment Rental Platform

EquipRent is a PHP/MySQL web app for renting tools and equipment. Users can register/login, browse listings with filters, enlist their own equipment, and create rental requests with simple availability checks and pricing.

Wireframe/Design (Figma): https://www.figma.com/design/L1WsIp7H8ORrjy7otvuVPg/HACKATHON?node-id=0-1

## Features
- User authentication: register, login, logout (session based)
- Browse equipment with search, filters, sort, and pagination
- Enlist equipment (owners) with optional photo uploads
- Rental flow with date validation and cost breakdown (daily rate + 5% platform fee)
- Basic availability tracking per day
- Responsive UI with modals and clean navigation

## Tech Stack
- PHP 8+ (XAMPP recommended)
- MySQL/MariaDB
- HTML/CSS/Vanilla JS (no framework)
- Font Awesome for icons

## Prerequisites
- XAMPP (Apache + MySQL) on Windows
- PHP enabled in Apache
- MySQL user with access to a database named `hackathondb`

## Setup (Local)
1) Move the project folder to your XAMPP `htdocs` directory, e.g.
   `C:\xampp\htdocs\New folder\hackathin`

2) Create database and schema
   - Option A: Import the SQL dump
     - Open phpMyAdmin → create database `hackathondb`
     - Import `hackathondb (1).sql`
   - Option B: Minimal setup for users table
     - Visit `http://localhost/New%20folder/hackathin/controller/setup_db.php`
       (creates `users` table and a test user)

3) Configure database connection
   - Edit `controller/db_connect.php` if needed:
     ```php
     $servername = "localhost";
     $username = "root";
     $password = ""; // default for XAMPP
     $dbname = "hackathondb";
     ```

4) Start Apache and MySQL in XAMPP.

5) Open the app
   - `http://localhost/New%20folder/hackathin/`
   - If your folder name differs, adjust the URL accordingly.

## Test Account
If you used `controller/setup_db.php`, the script creates:
- Email: `test@example.com`
- Password: `password123`

## Project Structure (high-level)
```
hackathin/
  index.php                # Home page + auth/enlist modals
  browse.php               # Browse/search equipment
  rent.php                 # Rental checkout page
  dashboard.php            # Placeholder dashboard
  includes/navigation.php  # Shared navigation
  controller/
    db_connect.php         # DB connection
    setup_db.php           # Creates users table + test user
    auth.php               # Login/Register/Status (AJAX or form)
    logout.php             # Session destroy (AJAX or redirect)
    equipment.php          # Equipment API (browse/view/categories/availability)
    enlist_item.php        # Owner equipment enlist (form post + uploads)
    booking.php            # Create rental request
  JS/                      # Frontend scripts (auth, browse, modals)
  css/                     # Stylesheets
```

## How to Use
- Home: browse categories, open auth or enlist modals
- Register/Login: available on `index.php` and `browse.php` modals
- Browse: `browse.php` supports search, filters, sorting, and pagination (via `controller/equipment.php?action=browse`)
- Enlist: from the home modal → posts to `controller/enlist_item.php`
- Rent: open `rent.php?equipment_id=...` when logged in → posts to `controller/booking.php`
- Logout: via `controller/logout.php` (AJAX-safe)

## Endpoints (server-side)
- `controller/auth.php`
  - POST `action=login` → sets session; JSON or redirect
  - POST `action=register` → creates user; JSON or redirect
  - GET `action=check_status` → `{ logged_in, user_id, username, email }`

- `controller/equipment.php`
  - GET `action=browse` → list equipment with filters: `category, location, min_price, max_price, search, sort, order, page, limit`
  - GET `action=view&id=...` → details for one equipment (with availability + reviews)
  - GET `action=categories` → active categories
  - GET `action=availability&id=...&date=YYYY-MM-DD` → availability flag

- `controller/booking.php`
  - POST `action=create_rental` with: `equipment_id, start_date, end_date, pickup_location?, return_location?, special_instructions?`

- `controller/logout.php`
  - GET → destroys session; JSON when requested with `X-Requested-With: XMLHttpRequest`

## Notes on Data Model
There are two paths in this repo used during development:
- `enlist_item.php` creates a simple `equipment` table on first use (owner_id, item_name, category, daily_rate, ...)
- `equipment.php` expects a richer schema with `categories` and more user fields (as in the SQL dump)

If you use only `enlist_item.php`, browsing via `equipment.php` filters may require aligning column names or importing the full SQL dump. For a complete demo experience, prefer importing `hackathondb (1).sql`.

## File Uploads
Enlisting can upload images to `uploads/equipment/`.
- Ensure Apache/PHP can write to that directory
- Adjust `upload_max_filesize` / `post_max_size` in `php.ini` if needed

## Troubleshooting
- Blank or error on login/register: verify DB connection in `controller/db_connect.php` and that the `users` table exists (use `setup_db.php`).
- Browse shows no data: seed the database by importing `hackathondb (1).sql` or create equipment that matches the expected schema.
- Cannot upload images: create `uploads/equipment/` with write permissions and increase PHP upload limits if necessary.
- Redirect paths on Windows: spaces in `New folder` become `%20` in URLs (use `New%20folder`).

## Security
- Passwords are hashed using `password_hash`
- Sessions are used for authentication; avoid outputting debug in production

## License
No license specified. Add one if you plan to publish.

