# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- **Hospital Custodians (Central Supply & Drugs/Medicine)**: Manage consignment inventory, receive consignor deliveries, track batch lot numbers and expiration dates, fulfill unit requisitions, and generate stock reports.
- **Requesting Unit Staff (CSR, HDU, Gugma Dialysis, Pharmacy Main, Pharmacy OPD, Yakap)**: Browse available consigned catalog items, submit unit supply requisitions, and track status until receipt, operating under strict cost privacy (cannot view supplier unit costs).
- **Hospital Leadership / Chief of Hospital (Viewer)**: Monitor overall inventory levels, delivery history, near-expiry alerts, and stock valuation with read-only access.
- **RMPH IT Administrator**: Manage user accounts, role-based access control, system settings, and audit logs.

## Product Purpose

Roxas Memorial Provincial Hospital (RMPH) HECS manages consigned medical supplies and pharmaceuticals across hospital wards and clinical departments. It replaces ad-hoc paper requisitioning with a dual-portal workflow, ensuring clinical departments have immediate access to life-saving supplies while tracking supplier consignments, lot numbers, batch expiration dates, and consumption without exposing vendor unit pricing to ward staff.

## Positioning

A hospital-grade consignment inventory management system tailored for public healthcare facilities, balancing rapid requisitioning by clinical units with rigorous batch-level FEFO (First-Expired, First-Out) traceability and strict role-based cost confidentiality.

## Operating Context

- Used in active hospital environments: ward nursing stations (CSR, HDU), dialysis clinics, central pharmacy counters, and warehouse storage rooms.
- Accessed via desktop workstations and hospital clinical tablets (requiring high-contrast readability and minimum 48px touch targets).
- Integrated with physical delivery workflows involving delivery receipts, supplier consignor terms, lot tracking, and formal requisition slips.

## Capabilities and Constraints

- **Dual-Panel Architecture**: Operations Panel (`/admin`) for Custodians, Administrators, and Leadership; Unit Portal (`/portal`) for clinical requesting units.
- **Strict Cost Confidentiality**: Supplier unit prices and cost totals are strictly hidden from `requesting_unit` role accounts across all views, tables, and exports.
- **Batch & Expiry Management**: FEFO inventory allocation, lot number tracking, and automated near-expiry indicators (30, 60, 90 day thresholds).
- **Official Documentation**: Generates printable PDF reports (DOMPDF) and CSV exports for Stock on Hand, Deliveries, Near Expiry, and Unit Requests.
- **Tech Stack**: Laravel (v13), Filament Panels, Tailwind CSS (v4), Spatie Permission, Vite, MySQL/SQLite, and Inter font.

## Brand Commitments

- **Entity**: Roxas Memorial Provincial Hospital (RMPH).
- **Sub-Brand / System**: RMPH HECS (Hospital Enterprise Consignment System).
- **Aesthetic Tone**: Clean, calm, clinical hospital design language.
- **Palette**: Primary Teal 700 (`#0F766E`), Slate neutrals, and semantic alert tokens (Emerald, Amber, Red, Sky).
- **Accessibility Standard**: WCAG 2.2 AA compliance, dual-coded status indicators (Word + SVG Icon + Color, zero emojis).

## Evidence on Hand

- Database seeders with genuine hospital operational data:
  - Units: `CSR`, `HDU`, `DIALYSIS`, `PHARM_MAIN`, `PHARM_OPD`, `YAKAP` (`database/seeders/UnitSeeder.php`).
  - Roles & Permissions: `admin`, `custodian`, `viewer`, `requesting_unit` (`database/seeders/RoleAndPermissionSeeder.php`).
  - Consignors, medical supplies, and pharmaceutical items catalog (`database/seeders/ConsignorSeeder.php`, `database/seeders/ItemSeeder.php`).
- Implemented design system demo & tokens page at `resources/views/filament/pages/design-system.blade.php`.
- Formal PDF report templates in `resources/views/reports/`.

## Product Principles

1. **Patient Care Continuity First**: Requisitioning for clinical units must be rapid, unmistakable, and frictionless, ensuring wards never face stockouts during urgent care.
2. **Strict Financial Confidentiality**: Commercial procurement terms and supplier unit costs remain strictly within custodian and administrative boundaries.
3. **Traceability Down to the Batch**: Every movement, fulfillment, and expiration date is tied to a specific delivery lot and consignor.
4. **Clinical Clarity over Decoration**: Interfaces must be high-contrast, uncluttered, and dual-coded with clear text and standard SVG icons; no decorative emojis or ambiguous visual metaphors.
5. **Resilient to Environmental Stress**: Interfaces must work reliably under hospital lighting, on tablet touchscreens, and in high-tempo ward shifts.

## Accessibility & Inclusion

- Compliance with WCAG 2.2 AA standards.
- 48px minimum touch targets for clinical tablet interaction.
- Color alone is never used to convey state or urgency; all alerts combine color, text labels, and semantic Heroicon SVGs.
