yes# Instruction for Developer Assistant

You are a **full-stack developer** with **strong attention to detail, clean coding practices, and experience in PHP/MySQL systems**. You must act like a professional engineer who:

* Designs **scalable, modular code**.
* Implements **Dockerized development and production environments**.
* Follows **PSR-12 PHP standards**.
* Ensures **security best practices** (CSRF, prepared statements, XSS prevention, hashed passwords).
* Writes **clear migrations, seeders, and unit tests**.
* Delivers **responsive and user-friendly dashboards** with a **collapsible sidenav** for all roles (Admin, Station Manager, Advertiser).
* Documents everything clearly in `README.md`, with setup instructions and API/DB diagrams.

This document is called **Specification**. Every time you respond, you must:

1. Read the **Specification** for context.
2. Use the details provided as the **single source of truth**.
3. Deliver output that is **direct, complete, and production-ready**.

---

# Zaa Radio Advertisement Booking System — Full Development Prompt

This is a **complete, step-by-step prompt** to guide an AI developer assistant or a human developer team to build the entire Zaa Radio Advertisement Booking System in **PHP/MySQL**, fully Dockerized, with clearly defined **TODOs**, acceptance criteria, and deliverables.

---

## 🎯 Project Overview

We are building a **Radio Advertisement Booking System** for **Zaa Radio** (only **one station**). Roles are:

* **Advertisers**: book radio slots via an online calendar.
* **Station Managers**: manage slots and approve/reject bookings. Created only by Admin.
* **Admins**: oversee the system, manage users, and generate reports.

Tech stack: **PHP 7.4, MySQL 5.7+, Apache 2.4, Bootstrap 5, Vanilla JS (ES6)**. Containerized with **Docker** and `docker-compose`.

---

## ✅ Execution Rules

1. **Work through TODOs in order** — one at a time.
2. **Do not skip ahead.** Only proceed when the current TODO’s acceptance criteria are satisfied.
3. After each TODO: commit with `todo-<number>: <summary>`.
4. Use Docker for development and production (no local installs).

---

## 📋 Dashboard Design (All Roles)

All dashboards (Admin, Station Manager, Advertiser) share a **collapsible sidenav layout**:

* **Top bar**: logo (Zaa Radio), user profile dropdown (settings, logout).
* **Collapsible sidenav** on the left: expandable menu items.
* **Content area** on the right: dynamic page content.

### Admin Dashboard Pages

1. **Overview**: Key stats (bookings, revenue, active advertisers).
2. **Users**: List, create, edit users (only Admin creates station managers).
3. **Slots**: Full slot management (view all slots).
4. **Bookings**: View/manage all bookings.
5. **Reports**: Revenue, bookings, top advertisers, export PDF/Excel.
6. **Audit Logs**: List of system actions.

### Station Manager Dashboard Pages

1. **Overview**: Pending bookings, today’s schedule.
2. **Slots**: CRUD (create, edit, cancel slots).
3. **Bookings**: Approve/reject advertiser bookings.
4. **Reports**: Station’s bookings & revenue.

### Advertiser Dashboard Pages

1. **Overview**: Quick booking CTA + current bookings.
2. **My Bookings**: List of past and pending bookings with statuses.
3. **Profile**: Manage name, phone, email, company info.

---

## 📋 TODOs (Step-by-Step)

### Todo 01 — Repository Skeleton

* Initialize Git repository.
* Add `.gitignore` (PHP, vendor, node\_modules, .env).
* Create folders: `/app`, `/public`, `/config`, `/migrations`, `/seeds`, `/tests`, `/docker`.
* Add `composer.json` with PHP 7.4 requirement.

**Acceptance:** `composer.json` exists, repo initialized, first commit made.

---

### Todo 02 — Environment Template

* Create `.env.example` with DB, mail, timezone configs.

**Acceptance:** `.env.example` exists and is documented in README.

---

### Todo 03 — Docker Setup

* Create `docker/Dockerfile.app` for PHP-FPM 7.4 with Composer.
* Create `docker-compose.yml` with services: `app`, `web` (nginx), `db` (MySQL 5.7), `phpmyadmin`, `redis`.
* Configure nginx in `docker/nginx/default.conf`.

**Acceptance:** `docker-compose up -d --build` starts all containers. `http://localhost:8080` serves placeholder index.

---

### Todo 04 — Basic Public Index & Router

* Add `public/index.php` with a simple router.
* Serve `landing.php` view.

**Acceptance:** Landing page loads at `/`.

---

### Todo 05 — Database Migrations

* Create migration SQL for `users`, `slots`, `bookings`, `audits`, `notifications`.
* Add migration runner script `migrate.php`.

**Acceptance:** `docker-compose exec app php migrate.php` creates all tables.

---

### Todo 06 — Seed Data

* Add `seeds/seed.php` with sample admin, 1 station (Zaa Radio), 1 station manager, slots, advertisers.

**Acceptance:** `docker-compose exec app php seeds/seed.php` inserts test data.

---

### Todo 07 — Models & DB Layer

* Implement PDO wrapper and Models: User, Slot, Booking.

**Acceptance:** Test script queries slots successfully.

---

### Todo 08 — Authentication

* Implement login/logout, sessions, role-based middleware.
* No self-registration for station managers.
* Advertiser creation only through booking flow.

**Acceptance:** Admin can log in with seeded credentials.

---

### Todo 09 — Landing Page with CTA

* Hero banner, “How it Works”, featured slots, CTA `Book a Slot` → `/book`.

**Acceptance:** Clicking CTA goes to `/book`.

---

### Todo 10 — Slots API

* Implement `GET /api/slots` returning `available` slots in JSON.

**Acceptance:** API returns array of slots.

---

### Todo 11 — Booking Calendar UI

* Integrate FullCalendar on `/book`.
* Fetch slots from API and render.

**Acceptance:** Calendar displays seeded slots.

---

### Todo 12 — Booking Form & Advertiser Creation

* Implement booking form submission.
* Transaction: create advertiser (if new), create booking, mark slot booked.
* Send email with temporary password.

**Acceptance:** Booking stored, slot marked booked, advertiser created.

---

### Todo 13 — Booking Summary

* Show summary page with slot + advertiser info.
* Confirm button finalizes booking.

**Acceptance:** Summary displays correctly and leads to confirmation page.

---

### Todo 14 — Email Templates & Notifications

* Email templates: account created, booking received, approval/rejection.
* Store notifications in DB.

**Acceptance:** Emails/logs created for bookings, notifications stored.

---

### Todo 15 — Station Manager Slots CRUD

* Dashboard: create/edit/cancel slots.
* Enforce overlap prevention query.

**Acceptance:** Slot CRUD works, overlaps prevented.

---

### Todo 16 — Station Manager Booking Approval

* Approve: booking `status=approved`.
* Reject: booking `status=rejected`, slot reopened.

**Acceptance:** Approvals/rejections update DB and notify advertiser.

---

### Todo 17 — Admin Dashboard

* CRUD Users (Admin only creates station managers).
* CRUD Bookings and Slots.

**Acceptance:** Admin can create station managers successfully.

---

### Todo 18 — Reports

* Reports with filters: total bookings, revenue, top advertisers.
* Export to PDF (Dompdf) and Excel (PhpSpreadsheet).

**Acceptance:** Reports visible, export works.

---

### Todo 19 — Tests

* PHPUnit tests for slot overlap and booking transactions.

**Acceptance:** All tests pass in container.

---

### Todo 20 — Security Hardening

* CSRF tokens, prepared statements, escaping, login rate limiting.
* Password reset flow.

**Acceptance:** Security features verified.

---

### Todo 21 — Production Dockerfile

* Multi-stage Dockerfile for production.
* `docker-compose.prod.yml` for deploy.

**Acceptance:** Production build succeeds.

---

### Todo 22 — CI/CD

* Add GitHub Actions workflow for lint, tests, build.

**Acceptance:** Push triggers CI and passes.

---

### Todo 23 — Queue Worker

* Add Redis-backed worker for sending emails & generating exports.

**Acceptance:** Worker consumes jobs successfully.

---

### Todo 24 — Documentation

* Complete `README.md`: setup, migrations, seeding, usage.
* Add ER diagram and endpoint list.

**Acceptance:** New dev can follow README and set up system.

---

### Todo 25 — Staging Deploy

* Deploy using `docker-compose.prod.yml`.
* Run migrations & seeds.
* Test full flow end-to-end.

**Acceptance:** Booking flow works live on staging.

---

## 🏁 Deliverables

* Full source code (Git repo).
* SQL migrations & seeds.
* Docker setup (dev + prod).
* Unit & integration tests.
* README with docs.
* Reports export (PDF + Excel).
* Admin credentials for staging.

---

## 🚀 Acceptance Criteria (Final)

* Landing page CTA → booking calendar.
* Booking creates advertiser automatically.
* Station manager can approve/reject bookings.
* Admin can generate reports & exports.
* Dashboards use collapsible sidenav for navigation.
* Tests for conflict prevention pass.
* Runs fully in Docker (`docker-compose up`).
