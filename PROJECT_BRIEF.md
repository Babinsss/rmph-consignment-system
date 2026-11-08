# PROJECT BRIEF: Hospital Consignment Inventory System (HECS), Version 1

Roxas Memorial Provincial Hospital (RMPH), Roxas City, Capiz, Philippines

## 1. Purpose
A system that records consigned Medical Supplies and Drugs & Medicine **delivered** to the hospital and lets hospital units **request** them, with the **unit cost** of each delivered batch recorded. Nothing else.

## 2. What this system does NOT do (do not build, do not suggest)
- No accounting or finance work: no Statement of Consumed Goods (StCG), no supplier payment or liquidation, no disbursement vouchers, no discounts (Senior Citizen/PWD), no mark-ups, no selling prices.
- No billing and no patient-level use tracking. The system does not know what happens to an item after it is issued to a unit.
- No patient information of any kind (no names, no MRN).
- No General Supply (Lady Ortalez S. Luces' area is excluded).
- No EMR, no supplier login portal, no payment gateway.

## 3. Users and roles
| Role | Who | Can do |
| --- | --- | --- |
| Custodian | Aliana Marie Dula (Supplies), Kristine Mae Batan (Drugs) | Manage catalog, record deliveries (with unit cost), review and fulfill requests |
| Requesting unit staff | CSR, HDU, Gugma Dialysis Center, Pharmacy Main, Pharmacy OPD, Yakap | Browse items and stock availability, submit and track requests for their own unit only. **Cannot see unit cost.** |
| Viewer (read-only) | Chief of Hospital, Accounting, Auditor (OPEN: who exactly) | See stock, deliveries, requests and unit costs; cannot change anything |
| Administrator | RMPH IT | Manage users and roles |

Only the 6 units above may request. About 15 to 25 logins in total.

## 4. Size and setting
306 active items, about 16 consignors, about 45,000 to 65,000 units on hand, one site, hospital LAN, desktop PCs plus tablets, run on a hospital-owned server. 24/7 operation. (All figures are from the interviews; verify before relying on them.)

## 5. Must-have features (Version 1)
1. Login and roles (section 3).
2. Item catalog: code, description, category (Supply or Drug), unit of measure, consignor, minimum level.
3. Consignor list: 16 accredited consignors.
4. Record a delivery: consignor, Delivery Receipt (DR) number, date, and for each line the item, quantity, lot/batch number, expiry date, **unit cost**. Each line becomes a batch in stock.
5. Request portal for the 6 units: pick items and quantities, submit, see status (Pending, Fulfilled, Partly fulfilled, Rejected). Mon/Wed request windows shown and enforced.
6. Urgent (STAT) request: any time, outside the Mon/Wed window, with a required reason typed in. No patient information.
7. Custodian fulfills a request: system picks batches First-Expiry-First-Out (FEFO); custodian can accept or change. Stock on hand drops for those batches.
8. Stock movement history (stock card): every receive, issue and adjustment with date, user, quantity and running balance; cannot be edited or deleted, only corrected by a new adjustment with a reason.
9. Simple reports with Excel/PDF export: stock on hand, items expiring within 90 days, delivery history, request history by unit.

## 6. Later (Version 2, do NOT build now)
Barcode/QR scanning, email/SMS alerts, printable statutory forms (Bin Card RMH-PPS-F02, RAPC, GF-18 layout), return-to-supplier module, executive dashboard.

## 7. Data
Consignor; Item; Delivery (header: consignor, DR number, date, received by); Delivery line (item, quantity, lot, expiry, unit cost); Batch (from a delivery line, with quantity remaining); Unit (the 6); Request (unit, requester, date, urgent yes/no, reason, status); Request line (item, quantity requested, quantity issued); Stock movement (type RECEIVE / ISSUE / ADJUST, batch, quantity, user, date, reason); User (name, role, unit).

## 8. Rules and risks
- Unit cost is visible only to Custodians, Viewers and Administrators.
- Records are kept at least 10 years (CONFIRM with Accounting in writing).
- Runs on an RMPH server on the hospital network; daily backups (CONFIRM the actual setup with the IT administrator).
- Downtime fallback: pre-printed "DOWNTIME REQUISITION" slips; enter them within 4 hours of recovery.
- Use only made-up sample data during building and testing.

## 8b. Technology (chosen)
- Backend: Laravel (PHP). Frontend: Filament panels (Livewire + Tailwind CSS). One panel for Custodians, Viewers and Administrators; a simpler panel for the 6 requesting units.
- Database: PostgreSQL (MySQL or MariaDB acceptable if RMPH IT prefers).
- Server: Ubuntu Linux with Nginx and PHP-FPM on a hospital-owned machine, hospital network only.
- Packages: Spatie Laravel Permission (roles), Laravel Excel (exports), DomPDF (PDF).
- Verify the exact current, supported versions of Laravel and Filament before installing, and tell me what you chose.
- No heavy separate frontend framework (no React or Next.js) unless I approve it.

## 9. Open questions (ask the named person before the related slice)
1. Does a request need an approver (Head Nurse or Chief), or does the custodian simply fulfill it? (Slice 4)
2. Who exactly are the read-only Viewers? (Slice 1)
3. Because use on patients is not tracked, the system's count is "depot stock", not "stock at each unit". Is that acceptable to the Chief and Accounting? (Before Slice 6)
4. Is the Mon/Wed rule for all units, or only for some? (Slice 4)
5. Tech choice (Laravel + Filament + Tailwind): confirm the versions you plan to use exist and your IT staff can maintain them.

## 10. How we know it works
- A normal request is fulfilled in under 2 hours; an urgent request in under 15 minutes.
- Zero difference between system stock and a weekly physical count of the top 20 items.
- Near-expiry items are found by the 90-day report, not by eye.

## 11. Hard rules for the AI
- Ask me before any big decision. Work in small slices and stop for my approval after each.
- Explain in plain words. I am not a programmer.
- Build only what is in section 5. Anything in sections 2 and 6 is off limits.
- Never use real patient or hospital data.
- Stock changes must use database transactions and row locking, so two people can never be issued the same stock, and must be covered by automated tests.
- Keep PROGRESS.md updated after every slice.

## 12. Slices
1. Login and roles. 2. Catalog and consignors (with minimum levels). 3. Record deliveries and batches with unit cost. 4. Request portal for the 6 units. 5. Urgent (STAT) request. 6. Fulfill requests with FEFO and the stock movement history. 7. Reports and exports.
