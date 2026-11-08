# Hospital Consignment Inventory System (HECS) - Progress & Decisions

**Location:** Roxas Memorial Provincial Hospital (RMPH), Roxas City, Capiz, Philippines  
**Source of Truth:** [PROJECT_BRIEF.md](file:///c:/laragon/www/rmph-consignment-system/PROJECT_BRIEF.md)  
**Status:** ALL 7 SLICES COMPLETED & TESTED (Version 1 Ready for Review & Deployment)  

---

## 1. Project Overview & Scope
A focused internal inventory system to record consigned Medical Supplies and Drugs & Medicines delivered to RMPH, manage stock in the central depot, and process supply requests from 6 hospital units.

### Strictly Excluded (Out of Scope)
- No accounting, billing, liquidation, disbursement vouchers, discounts, or selling prices.
- No patient-level tracking or medical records (no patient names or MRNs).
- No general supplies (Lady Ortalez S. Luces' area is excluded).
- No external supplier portals, EMR integrations, or payment gateways.
- No Phase 2 items now (no barcode scanners, SMS alerts, or complex dashboards).

---

## 2. Agreed Decisions (Section 9 Open Questions)

| # | Question | Decision Made |
|---|---|---|
| **1** | **Request Approvals** | **No approval step is needed.** Requisitions go directly from requesting unit staff to the stock custodian for fulfillment. The Head Nurse is not a user in the system. The Chief of Hospital can monitor all requests. |
| **2** | **Read-Only Viewers** | **Chief of Hospital only.** Accounting and Auditors do not require logins or user accounts in this system. |
| **3** | **Depot Stock vs. Unit Stock** | **Confirmed & Accepted.** The system strictly tracks stock inside the central storage depot. Once items are issued to a unit, they are deducted from depot stock; usage inside wards/units is not tracked. |
| **4** | **Requisition Schedule** | **Monday to Friday, closing at 5:00 PM.** All 6 units can submit regular requests during this window. Outside this window (after 5:00 PM and on weekends), only Urgent (STAT) requests with a mandatory reason may be submitted. |
| **5** | **Hosting & Database Stack** | **Laragon (local server environment) with MySQL database.** Built on Laravel 13, Filament panels, and Tailwind CSS. Running at virtual host `http://rmph-consignment-system.test/`. |

---

## 3. User Roles & Permission Matrix

| Role | Who | Key Permissions | Unit Cost Visible? | Panel Access |
|---|---|---|:---:|:---:|
| **Administrator** | RMPH IT | Manage user accounts, logins, and system roles | **Yes** | Operations (`/admin`) |
| **Custodian** | Aliana Marie Dula (Supplies)<br>Kristine Mae Batan (Drugs) | Manage item catalog, record deliveries, enter batches/unit costs, review and fulfill requests via FEFO | **Yes** | Operations (`/admin`) |
| **Viewer** | Chief of Hospital | Read-only access to view stock on hand, deliveries, requests, and unit costs | **Yes** | Operations (`/admin`) |
| **Requesting Unit Staff** | Staff from the 6 units: <br>1. CSR<br>2. HDU<br>3. Gugma Dialysis Center<br>4. Pharmacy Main<br>5. Pharmacy OPD<br>6. Yakap | Browse available catalog items, submit regular requests (Mon–Fri until 5 PM), submit urgent requests anytime, track status | **NO** (Strictly Hidden) | Requisition Portal (`/portal`) |

### Pre-seeded Demo Accounts (Password for all: `password`)
- **Administrator:** `admin@rmph.local`
- **Supplies Custodian:** `aliana.dula@rmph.local`
- **Drugs Custodian:** `kristine.batan@rmph.local`
- **Chief of Hospital (Viewer):** `chief@rmph.local`
- **CSR Staff:** `csr@rmph.local`
- **HDU Staff:** `hdu@rmph.local`
- **Dialysis Staff:** `dialysis@rmph.local`
- **Pharmacy Main Staff:** `pharmacy.main@rmph.local`
- **Pharmacy OPD Staff:** `pharmacy.opd@rmph.local`
- **Yakap Staff:** `yakap@rmph.local`

---

## 4. Technical Specifications
- **Framework:** Laravel 13 (PHP 8.5 / Laragon local environment)
- **UI & Admin Panels:** Filament (Multi-panel setup: Operations `/admin` & Requisition Portal `/portal`)
- **Styling:** Tailwind CSS (responsive on PC desktops and hospital tablets)
- **Database:** MySQL (`rmph_consignment`)
- **Data Integrity:** Database transactions and row locking on all stock movements to eliminate race conditions
- **Key Packages:**
  - `spatie/laravel-permission` (User roles and permissions)
  - `openspout/openspout` (Excel data exports)
  - `barryvdh/laravel-dompdf` (Printable PDF exports)

---

## 5. Implementation Roadmap (Slices)

- [x] **Slice 1: Login and Roles** *(Completed & Tested - 12 Feature Tests Passing)*
  - Setup database connection to MySQL in Laragon (`rmph_consignment`).
  - Implemented the 4 roles (`admin`, `custodian`, `viewer`, `requesting_unit`).
  - Created the 6 hospital units (CSR, HDU, Gugma Dialysis, Pharmacy Main, Pharmacy OPD, Yakap).
  - Configured two Filament panels:
    - **Operations Panel** at `http://rmph-consignment-system.test/admin` (for Custodians, Viewers, Administrators).
    - **Request Portal** at `http://rmph-consignment-system.test/portal` (for Requesting Unit Staff).
  - Enforced unit cost security rule (`canViewUnitCosts()` strictly false for ward staff).
  - Created User Management and Unit Management resources for the Administrator.
- [x] **Slice 2: Catalog and Consignors** *(Completed & Tested - 9 Feature Tests Passing)*
  - Created accredited consignors model, migration, and seeder for all **16 suppliers** (Zuellig, Metro Drug, Endure, Unilab, B. Braun, Fresenius, etc.).
  - Created item catalog model and migration with:
    - Unique item code (e.g. `SUP-001`, `DRG-001`).
    - Explicit category (`supply` for Medical Supply vs `drug` for Drugs & Medicine).
    - Unit of measure (e.g. `box of 100`, `vial`, `ampule`, `piece`, `bottle`).
    - Consignor association.
    - Minimum stock alert level threshold.
  - Built **Consignors Management** in Operations Panel (`/admin/consignors`) with live item counts.
  - Built **Item Catalog Management** in Operations Panel (`/admin/items`) with quick filter tabs ("All Items", "Medical Supplies", "Drugs & Medicine").
  - Built **Read-Only Catalog Browser** in Request Portal (`/portal/items`) for ward staff (shows available items and units of measure, strictly hides unit costs, disables edits).
- [x] **UI/UX Polish & Hospital Experience Enhancement** *(Completed & Tested - 4 Feature Tests Passing, 27 tests total)*
  - **Login Screens Redesigned:** Added hospital header banner, clear distinction between Central Operations and Ward Requisition Portal, and one-click demo account fill buttons.
  - **Cross-Portal Navigation:** Quick links between Operations (`/admin`) and Ward Portal (`/portal`) on login pages.
  - **Ward Portal Dashboard:** Custom unit dashboard widget showing ward name, current time, live request window badge (Open / Closed until 5:00 PM), and catalog quick action.
  - **Operations Dashboard:** Live stats overview widget showing catalog counts (supplies vs drugs), accredited consignors, and active requesting units.
  - **Tables & Readability:** Alternating striped rows, custom hospital empty state illustrations, and default sorting across all catalog and management tables.
  - **Hospital Timezone Alignment:** Configured Philippine Standard Time (`Asia/Manila`) for all timestamps and schedules.
- [x] **Slice 3: Deliveries and Batches with Unit Cost** *(Completed & Tested - 7 Feature Tests Passing, 34 tests total)*
  - **Database Models & Migrations:** Created `deliveries`, `batches`, and `stock_movements` tables.
  - **Delivery Header:** Tracks Consignor, Delivery Receipt (DR) Number, Delivery Date, Received By, and notes.
  - **Batches & Stock:** Each delivery line generates an active inventory batch in the central depot with Lot/Batch number, Expiration Date, Quantity Delivered, Quantity Remaining, and Unit Cost.
  - **Immutable Stock Card History:** Automatically logs initial `RECEIVE` movement with running balance inside database transactions.
  - **Unit Cost Security:** Strictly restricted to Custodians, Administrator, and Chief of Hospital (Viewer). Requesting unit staff cannot view unit costs anywhere.
  - **Operations Panel Deliveries:** Full management screen at `/admin/deliveries` with batch repeater and total DR value.
  - **Depot Batches & Stock Browser:** Operations screen at `/admin/batches` displaying live stock, expiration badges (warning within 90 days, danger if expired), and remaining batch valuation.
  - **Catalog Enhancement:** Added live "Stock on Hand" badges to the Item Catalog tables in both Operations (`/admin/items`) and the Requisition Portal (`/portal/items`).
  - **Sample Deliveries Seeded:** Seeded sample deliveries from Zuellig Pharma, Metro Drug, and Fresenius Medical Care with realistic quantities, lot numbers, and unit costs.
- [x] **Slice 4: Request Portal for the 6 Units** *(Completed & Tested - 7 Feature Tests Passing, 41 tests total)*
  - **Database Models & Migrations:** Created `requests` and `request_lines` tables with auto-incrementing requisition numbers (`REQ-YYYY-XXXX`).
  - **Schedule Enforcement:** Created `RequestWindowService` enforcing Monday to Friday closing at 5:00 PM for regular requests, while permitting 24/7 submissions for Urgent (STAT) requests.
  - **Requisition Portal Screen (`/portal/requests`):** Streamlined for ward staff (CSR, HDU, Dialysis, Pharmacies, Yakap). Scoped strictly to the logged-in staff member's unit.
  - **New Requisition Form (`/portal/requests/create`):**
    - Unit and Requester automatically identified from the authenticated session.
    - Status banner informs staff if the regular window is open or closed.
    - Item picker displays consigned items and current live depot stock availability.
    - Unit costs strictly hidden from ward staff.
  - **Operations Panel Requests Screen (`/admin/requests`):**
    - Central operations view for Custodians and Chief of Hospital across all 6 units.
    - Live sidebar counter badge showing pending requests.
    - Rejection action with required reason for custodians.
  - **Seeded Sample Requisitions:** Seeded 3 sample requisitions (CSR surgery replenishment, Gugma Dialysis dialyzers, and HDU STAT emergency request).
- [x] **Slice 5: Urgent (STAT) Requisitions** *(Completed & Tested - 5 Feature Tests Passing, 46 tests total)*
  - **24/7 STAT Requisitions:** Hospital units can submit emergency requests anytime, even outside the regular Monday–Friday window, including nights and weekends.
  - **Mandatory Clinical Justification:** Enforced minimum 10-character medical reason; strict rule prevents entering patient personal data (names/MRN).
  - **Custodian STAT Alert Widget (`UrgentRequestsAlertWidget`):** High-visibility red pulsating banner on the Central Operations Dashboard alerting custodians the moment an emergency request arrives, targeting fulfillment in < 15 minutes.
  - **Quick Filter Tabs on Requests:** Operations screen (`/admin/requests`) now includes quick tabs: "All Requisitions", "🚨 Urgent (STAT)" (with live red counter badge), "Pending", "Fulfilled", and "Rejected".
  - **High-Visibility Badges:** Clear `STAT (Urgent)` red badge across tables and infolists.
- [x] **Slice 6: FEFO Fulfillment & Stock Movement History** *(Completed & Tested - 6 Feature Tests Passing, 52 tests total)*
  - **First-Expiry-First-Out (FEFO) Recommendation:** `FulfillmentService` queries unexpired depot batches ordered chronologically by expiration date and automatically allocates the earliest expiring stock first.
  - **Fulfill Action on Requisitions:** Custodians have a 1-click `Fulfill (FEFO)` action on `/admin/requests` displaying suggested lot numbers, expiration dates, days remaining, and quantities with override capability.
  - **Database Transactions & Row-Locking:** Uses `lockForUpdate()` on batch rows inside transactions to prevent race conditions or double-issuance.
  - **Immutable Stock Movements (Stock Card):** Every issuance generates an immutable `ISSUE` audit record with running batch balance and reference requisition number.
  - **Physical Stock Adjustments:** Added `Adjust Stock` action on `/admin/batches` allowing custodians to record physical count corrections, discards, or breakages with mandatory written reasons.
  - **Stock Card History Screen (`/admin/stock-movements`):** Dedicated immutable audit screen in Operations tracking every Receive, Issue, and Adjustment with running balances and user attribution.
- [x] **Slice 7: Reports and Exports** *(Completed & Tested - 9 Feature Tests Passing, 61 tests total)*
  - **Reports Center Page (`/admin/reports`):** Dedicated executive hub in Central Operations providing 1-click access to all 4 official consignment reports.
  - **Stock on Hand Report:** Complete depot inventory ledger showing item codes, descriptions, categories, units of measure, consignors, min alert levels, and current stock. Downloadable as PDF and Excel/CSV.
  - **Near-Expiry Early Warning Report (90 Days):** Critical audit list flagging batches expiring within the next 90 days (or expired) to prevent stock loss. Downloadable as PDF and Excel/CSV.
  - **Delivery Receipts (DR) History:** Filterable chronological shipment ledger by date range and supplier. Downloadable as PDF and Excel/CSV.
  - **Unit Requisitions History:** Comprehensive distribution audit ledger filterable by date, unit, status, and urgency (Regular vs STAT). Downloadable as PDF and Excel/CSV.
  - **PDF Generation Engine:** Clean hospital-grade layout via Dompdf with official RMPH headers, metadata, and confidentiality markings.
  - **Excel/CSV Export Engine:** Streamed UTF-8 CSV with Microsoft Excel BOM for zero-corruption character display on Windows desktop.
  - **Role Security:** Reports center and download routes are strictly restricted to Custodians, Viewers, and Administrators; ward staff cannot access reports.
- [x] **Production Deployment Verification & Hardening** *(Completed & Tested - 62 tests passing, 188 assertions)*
  - **Demo Button Environment Guard:** Wrapped demo quick-login credential buttons in `@if (! app()->environment('production'))` so they automatically disappear in production mode (`APP_ENV=production`).
  - **Automated Database Backup Command:** Built `php artisan app:backup-database` which dumps timestamped MySQL SQL dumps to `storage/app/backups/` and auto-cleans backups older than 30 days. Scheduled daily at 11:00 PM in `routes/console.php`. Verified with unit and feature test.
  - **Production Caching & Optimization Verified:** Successfully validated `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`, and `php artisan filament:optimize`.
  - **Asset Publishing & Zero-Latency CSS/JS:** Ensured pre-compiled Filament assets are published in `public/css/filament` and `public/js/filament`, auto-hooked to `composer.json`.
  - **Production Deployment Guide Created:** Created comprehensive [DEPLOYMENT.md](file:///c:/laragon/www/rmph-consignment-system/DEPLOYMENT.md) with step-by-step instructions for RMPH IT, user password updates, Task Scheduler automated backup configuration, downtime contingency procedures (4-hour manual slip entry rule), and 10-year audit retention compliance.
- [x] **Hospital UI/UX Overhaul & Accessibility Polish** *(Completed & Tested - 62 tests passing, 188 assertions)*
  - **Calm Hospital Palette & Light Mode:** Unified both panels (`/admin` and `/portal`) under a calm medical clinical teal theme with crisp white space, high contrast, and light mode locked (`->darkMode(false)`).
  - **Two Users, Two Feels:**
    - *Wards Portal:* Big touch-friendly buttons (`min-h-[48px]`), clear dynamic schedule banner reading from `RequestWindowService`, prominent STAT emergency toggle with bolt icon, and collapsible touch-friendly requisition rows.
    - *Central Operations:* Denser, high-utility tables with quick filters, streamlined FEFO allocation modals, and rapid delivery receiving.
  - **Delivery Receiving Overhaul:** Added live reactive summary totals (Total Batches, Total Units, and Total Delivery Value for authorized staff only) with collapsible batch rows and a mandatory confirmation modal before saving.
  - **Color-Blind Accessible Badges (Dual-Coded):** Standardized distinct icons + text labels + high-contrast colors across Request Statuses (Pending, Fulfilled, Partly Fulfilled, Rejected), Expiry warnings (Expired, Expiring Soon < 90d, OK > 90d), Stock Levels (Below Min, Stock OK), and Stock Movement Card flows.
  - **Plain Language & Friendly Empty States:** Added humanized validation error messages and clear empty state cards guiding users on what to do next. Unit costs remain strictly invisible to ward staff.
- [x] **Stage 1: Design System & Design Tokens** *(Completed & Tested - 64 tests passing, 200 assertions)*
  - **Design Tokens Created:** Primary Teal 700 (`#0F766E`), semantic soft palettes (Red 50/700, Amber 50/700, Green 50/700, Sky 50/700), Slate neutrals (50–950), Inter font family, 8px spatial grid, 8–12px border radius, and 48px minimum touch targets on ward screens.
  - **Custom Theme Stylesheet:** Created `public/css/filament/hospital-theme.css` and injected into both panels via `PanelsRenderHook::HEAD_END`.
  - **Unified Dual-Coded Badges (Real SVG Icons, Zero Emoji):** Standardized badges across request status, expiry, stock levels, and priority using real SVG Heroicons with soft backgrounds and high-contrast text. Removed all emojis from the codebase.
  - **Design System Showcase Page:** Created live interactive token showcase at `http://rmph-consignment-system.test/admin/design-system` (`App\Filament\Pages\DesignSystem`).
  - **Automated Test Coverage:** Added automated test cases in `UserExperienceAndBrandingTest.php` ensuring design system page availability and authorization rules. All 64 tests passing.
- [x] **Stage 2: Ward Portal UX & Two-Pane Request Builder** *(Completed & Tested - 69 tests passing, 240 assertions)*
  - **Persistent Bottom Navigation:** Created `resources/views/filament/portal/bottom-nav.blade.php` with 4 large touch targets (`Home`, `Catalog`, `Requests`, and prominent red `STAT`) with `min-h-[48px]`, active route states, and spacing pad (`h-20`) to prevent any content overlap.
  - **Streamlined Ward Dashboard:** Refactored `UnitInfoWidget.php` and `unit-info-widget.blade.php` to display strictly:
    1. Unit Context & Schedule Banner (live status from `RequestWindowService`).
    2. Two primary action cards: "New Requisition" (Teal) and full-width "STAT Emergency Request" (Red).
    3. Last 5 Requisitions table with dual-coded badges and friendly empty state.
  - **Two-Pane Request Builder on Tablet/Desktop:** Built `CreateRequest.php` and `create-request.blade.php`:
    - *Left Pane:* Live searchable catalog with category filter pills (All Items, Medical Supplies, Drugs and Medicines), item cards with stock on hand, and 48px `+ Add to Request Slip` / in-cart stepper controls.
    - *Right Pane:* Sticky requisition slip (cart) with large 48px `+` and `−` steppers, remove button, delivery remarks/notes input, live totals (Distinct Items, Total Units), and primary submit button.
    - *Full-Width Red STAT Card:* Activates on urgent requests with 5 preset clinical reason chips (`Acute Trauma Emergency`, `Critical Low Stock in Ward`, `STAT Bedside Procedure`, `Unexpected Surgical Complication`, `ICU/HDU Rapid Response`), free text clinical justification (min 10 chars), and patient privacy notice.
    - *Confirmation Modal Screen:* Review dialog before dispatching order to depot, summarizing priority, clinical reason, all items/quantities, and total units.
    - *Unit Cost Security:* Verified unit cost remains strictly hidden from all ward views, tables, inputs, and summaries.
  - **Automated Test Coverage:** Added automated test cases in `RequisitionPortalTest.php` and `UserExperienceAndBrandingTest.php` verifying the two-pane view, bottom navigation, Livewire interactions, STAT reason chips, and unit cost invisibility. All 69 tests passing with 240 assertions.
