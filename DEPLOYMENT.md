# RMPH Hospital Consignment Inventory System (HECS) - Production Deployment & Operations Guide

**Location:** Roxas Memorial Provincial Hospital (RMPH), Roxas City, Capiz, Philippines  
**System:** Hospital Consignment Inventory System (HECS) — Version 1  
**Target Audience:** RMPH IT Administrators, Medical & Drug Custodians, Chief of Hospital  
**Source of Truth:** [PROJECT_BRIEF.md](file:///c:/laragon/www/rmph-consignment-system/PROJECT_BRIEF.md)

---

## 1. System Overview & Architecture

HECS is a dedicated hospital inventory system designed to track consigned **Medical Supplies** and **Drugs & Medicines** delivered to RMPH, manage stock in the central storage depot, and streamline fulfillment of requisitions from 6 hospital units:
1. **CSR** (Central Supply Room)
2. **HDU** (High Dependency Unit)
3. **Gugma Dialysis Center**
4. **Pharmacy Main**
5. **Pharmacy OPD**
6. **Yakap**

### Role & Access Separation
- **Operations Panel (`/admin`):** Strictly for Custodians (Aliana Marie Dula & Kristine Mae Batan), Viewer (Chief of Hospital), and IT Administrators. Complete catalog, delivery recording with unit costs, FEFO fulfillment, physical count adjustments, and executive reports.
- **Ward Requisition Portal (`/portal`):** Dedicated simplified portal for requesting unit staff. Staff can only see their unit's requests, check available catalog items, and submit regular (Mon–Fri before 5:00 PM) or 24/7 STAT emergency requisitions. **Unit costs are strictly hidden from ward staff.**

---

## 2. Server Environment Prerequisites

HECS is designed to operate on a hospital-owned server connected to the internal RMPH local area network (LAN) for 24/7 reliability.

- **Operating System:** Windows Server / Windows with Laragon, or Ubuntu Linux (22.04 LTS or newer)
- **Web Server:** Apache 2.4+ (with `mod_rewrite`) or Nginx
- **PHP:** PHP 8.3 or PHP 8.5 with the following extensions enabled:
  - `pdo_mysql`, `mbstring`, `intl`, `gd`, `zip`, `bcmath`, `curl`, `xml`
- **Database:** MySQL 8.0+ or MariaDB 10.5+
- **Local Hostname:** `http://rmph-consignment-system.test/` (or designated internal hospital LAN IP address, e.g. `http://192.168.1.50/`)

---

## 3. Step-by-Step Production Go-Live Checklist

Follow these steps when transitioning from development/staging to live hospital production:

### Step 1: Configure Production Environment (`.env`)
Ensure your `.env` file contains production-safe settings:

```dotenv
APP_NAME="RMPH HECS"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://rmph-consignment-system.test
APP_TIMEZONE=Asia/Manila

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rmph_consignment
DB_USERNAME=root
DB_PASSWORD=your_secure_mysql_password
```

> [!IMPORTANT]
> **Safety Guard:** When `APP_ENV=production` is set, the login screen automatically hides all quick-login demo credential buttons. Only authorized hospital personnel with secure credentials will be able to log in.

### Step 2: Set Strong Passwords for Real Hospital Accounts
Before opening the system to hospital staff, change the default passwords from `password` to strong, secure hospital credentials.

Run Laravel Tinker on the server:
```bash
php artisan tinker
```
Execute the password updates for real users:
```php
// IT Administrator
$admin = \App\Models\User::where('email', 'admin@rmph.local')->first();
$admin->update(['password' => bcrypt('StrongAdminPass2026!')]);

// Supplies Custodian (Aliana Marie Dula)
$aliana = \App\Models\User::where('email', 'aliana.dula@rmph.local')->first();
$aliana->update(['password' => bcrypt('StrongAlianaPass2026!')]);

// Drugs Custodian (Kristine Mae Batan)
$kristine = \App\Models\User::where('email', 'kristine.batan@rmph.local')->first();
$kristine->update(['password' => bcrypt('StrongKristinePass2026!')]);

// Chief of Hospital (Viewer)
$chief = \App\Models\User::where('email', 'chief@rmph.local')->first();
$chief->update(['password' => bcrypt('StrongChiefPass2026!')]);

// Ward Units (CSR, HDU, Dialysis, Pharmacies, Yakap)
// Repeat for unit staff accounts:
// csr@rmph.local, hdu@rmph.local, dialysis@rmph.local,
// pharmacy.main@rmph.local, pharmacy.opd@rmph.local, yakap@rmph.local
```

### Step 3: Run Production Performance Optimization
Cache all configurations, routes, Blade views, and Filament panel components for maximum speed and sub-second page loads:

```bash
# Cache configuration and environment variables
php artisan config:cache

# Cache routing tables
php artisan route:cache

# Pre-compile Blade templates
php artisan view:cache

# Optimize Filament components and icons
php artisan filament:optimize
```

*(Note: Whenever `.env` or application code is updated in the future, run `php artisan optimize:clear` followed by the cache commands above).*

---

## 4. Automated Daily Database Backups

Hospital inventory and delivery records must be protected against hardware failure or accidental data loss. HECS includes a built-in automated backup utility.

### Running a Manual Backup
```bash
php artisan app:backup-database
```
This command:
1. Generates a compressed, timestamped SQL dump in `storage/app/backups/rmph_consignment_backup_YYYY-MM-DD_HHmmss.sql`.
2. Automatically purges backup files older than 30 days to conserve storage space.

### Configuring Automated Nightly Backups (Windows Server / Laragon)
HECS schedules the backup command to run every night at **11:00 PM** in `routes/console.php`.

To ensure the schedule runs automatically without user intervention:
1. Open **Windows Task Scheduler** on the host server.
2. Click **Create Basic Task**.
3. Name: `RMPH HECS Daily Scheduler`.
4. Trigger: **Daily** at **11:00 PM**.
5. Action: **Start a program**.
   - Program/script: `C:\laragon\bin\php\php-8.3.36-Win64\php.exe` (or your PHP path)
   - Arguments: `artisan schedule:run`
   - Start in: `C:\laragon\www\rmph-consignment-system`
6. Check "Run whether user is logged on or not" with highest privileges.

> [!TIP]
> **Off-Site Storage Recommendation:** IT Administrators should configure a secondary synchronization task (or Robocopy) to copy files from `storage/app/backups/` to an external physical hard drive or secure hospital network storage daily.

---

## 5. Hospital Downtime & Disaster Recovery Procedures

In the event of a power outage, network disruption, or server maintenance, the hospital must maintain patient care without inventory disruption.

### Step 1: Pre-Printed "DOWNTIME REQUISITION" Slips
- Every requesting unit (CSR, HDU, Dialysis, Pharmacies, Yakap) must keep a supply of pre-printed carbon-copy **DOWNTIME REQUISITION** paper slips.
- During downtime, unit staff write down:
  - Unit Name & Date/Time
  - Urgency (`Regular` or `STAT Emergency`)
  - Item Description & Quantity Requested
  - Medical/Clinical Reason (strictly **no patient names or MRNs**)
  - Requesting Nurse/Staff signature

### Step 2: Emergency Physical Issuance
- Stock custodians (Aliana Marie Dula / Kristine Mae Batan) verify the slip at the central depot and physically issue the items using manual FEFO (checking batch expiration dates printed on manufacturer boxes).
- Custodian signs and retains the original white slip.

### Step 3: Mandatory Post-Recovery Catch-Up (Within 4 Hours)
As mandated in the Project Brief:
1. As soon as server connectivity is restored, the custodians must log into `http://rmph-consignment-system.test/admin`.
2. Enter each paper downtime requisition into HECS.
3. Fulfill the requisition against the actual batches issued.
4. **Deadline:** All downtime backlog entries must be completed **within 4 hours of server recovery** to maintain an accurate stock count and prevent audit discrepancies.

---

## 6. Regulatory Compliance & Data Retention

### 10-Year Archival Mandate
- Commission on Audit (COA) and provincial auditing standards require that hospital consignment transactions, delivery receipts, and stock cards be archived for **at least 10 years**.
- Database backups and yearly archive dumps must be permanently stored in read-only digital media (e.g., optical discs, encrypted NAS archives) under the supervision of RMPH IT.

### Strict Patient Data Privacy (Republic Act 10173)
- HECS is exclusively an inventory and supply-chain management system.
- Staff must **NEVER** enter patient names, hospital case numbers, or Medical Record Numbers (MRN) into the system (e.g., in requisition notes or STAT justification fields).
- Only departmental and clinical justifications (e.g., *"Emergency surgical tray replenishment"*, *"Acute post-op trauma resuscitation"*) are permitted.

---

## 7. Performance & Quality Benchmarks

| Metric | Target Benchmark | How It Is Achieved in HECS |
|---|---|---|
| **Regular Requisition Fulfillment** | **< 2 hours** | Custodians receive live dashboard alerts and 1-click FEFO batch allocation. |
| **Urgent (STAT) Requisition Fulfillment** | **< 15 minutes** | High-visibility red pulsating banner on central dashboard alerting custodians 24/7. |
| **Depot Physical Count Accuracy** | **100% agreement** | Strict atomic transactions with `lockForUpdate()` prevents stock drift or double-issuance. |
| **Near-Expiry Loss Prevention** | **Zero expired discards** | 90-Day Early Warning Report flags aging batches before expiration. |
| **Automated Test Coverage** | **100% Core Passing** | 62 automated feature tests covering all roles, security, FEFO, and exports. |

---

*Prepared for Roxas Memorial Provincial Hospital (RMPH), Roxas City, Capiz.*
