---
name: RMPH HECS Design System
description: Clinical, calm, and accessible design system for hospital consignment operations and ward requisitions.
colors:
  primary: "#0f766e"
  primary-hover: "#115e59"
  primary-subtle: "#f0fdfa"
  primary-border: "#ccfbf1"
  surface: "#ffffff"
  surface-muted: "#f8fafc"
  neutral-border: "#e2e8f0"
  neutral-text: "#0f172a"
  neutral-muted: "#475569"
  neutral-subtle: "#94a3b8"
  danger: "#b91c1c"
  danger-bg: "#fef2f2"
  danger-border: "#fecaca"
  warning: "#b45309"
  warning-bg: "#fffbeb"
  warning-border: "#fde68a"
  success: "#15803d"
  success-bg: "#f0fdf4"
  success-border: "#bbf7d0"
  info: "#0369a1"
  info-bg: "#f0f9ff"
  info-border: "#bae6fd"
typography:
  display:
    fontFamily: "Inter, ui-sans-serif, system-ui, -apple-system, sans-serif"
    fontSize: "1.875rem"
    fontWeight: 700
    lineHeight: 1.25
    letterSpacing: "-0.02em"
  headline:
    fontFamily: "Inter, ui-sans-serif, system-ui, -apple-system, sans-serif"
    fontSize: "1.5rem"
    fontWeight: 700
    lineHeight: 1.3
    letterSpacing: "-0.015em"
  title:
    fontFamily: "Inter, ui-sans-serif, system-ui, -apple-system, sans-serif"
    fontSize: "1.125rem"
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: "-0.01em"
  body:
    fontFamily: "Inter, ui-sans-serif, system-ui, -apple-system, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: "-0.01em"
  label:
    fontFamily: "Inter, ui-sans-serif, system-ui, -apple-system, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 600
    lineHeight: 1.0
    letterSpacing: "0.05em"
rounded:
  sm: "6px"
  md: "8px"
  lg: "12px"
  full: "9999px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "32px"
  touch: "48px"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.surface}"
    rounded: "{rounded.md}"
    padding: "8px 16px"
    height: "40px"
  button-primary-hover:
    backgroundColor: "{colors.primary-hover}"
  button-ward:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.surface}"
    rounded: "{rounded.lg}"
    padding: "12px 24px"
    height: "{spacing.touch}"
  badge-pending:
    backgroundColor: "{colors.warning-bg}"
    textColor: "{colors.warning}"
    rounded: "{rounded.md}"
    padding: "4px 10px"
  badge-fulfilled:
    backgroundColor: "{colors.success-bg}"
    textColor: "{colors.success}"
    rounded: "{rounded.md}"
    padding: "4px 10px"
  badge-danger:
    backgroundColor: "{colors.danger-bg}"
    textColor: "{colors.danger}"
    rounded: "{rounded.md}"
    padding: "4px 10px"
---

# Design System: RMPH HECS

## Overview

**Creative North Star: "The Clinical Sanctuary"**

RMPH HECS (Hospital Enterprise Consignment System) is designed for high-stakes healthcare operations at Roxas Memorial Provincial Hospital. The interface provides a calm, orderly, and deeply reassuring atmosphere that lowers cognitive load for hospital custodians and ward clinicians navigating high-stress shifts. Rather than overwhelming users with intense saturation or synthetic decorations, the system anchors itself in authoritative Hospital Alpine Teal, crisp Slate neutrals, and soft-tinted semantic alerts.

The system is engineered for dual operating environments: desktop workstations in custodian supply warehouses and administrative offices, and bedside clinical tablets in hospital wards (CSR, HDU, Dialysis Center, and Pharmacies). Clean tabular density supports custodian oversight, while generous 48px touch targets enable rapid, mistake-free tapping by clinicians wearing medical gloves.

Visual anti-references are strictly defined: this system explicitly rejects consumer SaaS tropes, including saturated violet-blue gradients, dark-mode neon glows, floating pill cards, and playful micro-illustrations. It also rejects raw, unstyled spreadsheet brutality in favor of deliberate, high-legibility clinical elegance.

**Key Characteristics:**
- **Authoritative & Calming:** Grounded in Hospital Alpine Teal 700 (#0F766E) and Slate backgrounds.
- **Dual-Coded Accessibility:** Every status combines an explicit text label with a monochrome SVG icon on a soft-tinted badge (WCAG 2.2 AA compliant).
- **Tablet & Glove-Ready:** 48px minimum touch targets across ward requisition and action flows.
- **Strict 8px Spatial Grid:** Consistent rhythmic pacing across all layouts, cards, and modal sheets.
- **Zero-Emoji Discipline:** Serious medical professionalism with zero emojis across all statuses, alerts, and navigation.

## Colors

The palette balances authoritative medical teal with high-contrast slate typography and soft-tinted, readable alert states.

### Primary
- **Hospital Alpine Teal** (#0F766E): The primary brand anchor. Used selectively on key call-to-actions, primary navigation headers, active sidebar items, and table item codes.
- **Deep Teal Hover** (#115E59): Hover and active state for primary teal controls.
- **Soft Teal Wash** (#F0FDFA): Light background wash for page banners, selected rows, and active filter tags.
- **Teal Border Accent** (#CCFBF1): Subtle borders framing primary hero sections and active controls.

### Neutral
- **Pure White Canvas** (#FFFFFF): Card surfaces, modal sheets, and active form fields.
- **Slate Table Shading** (#F8FAFC): Table headers, alternating row striping, and inactive container fills.
- **Slate Divider Line** (#E2E8F0): 1px structural container borders, horizontal rules, and table dividers.
- **Deep Charcoal Text** (#0F172A): Primary high-contrast text for headers, metrics, and data table content.
- **Slate Muted Text** (#475569): Secondary text, table headers, column descriptors, and timestamps.
- **Slate Subtle Text** (#94A3B8): Inactive placeholders, item code prefixes, and breadcrumb chevrons.

### Semantic Alerts
- **Clinical Danger Red** (Text: #B91C1C, Bg: #FEF2F2, Border: #FECACA): Expired batches, below-minimum inventory warnings, STAT emergency requisitions, and rejected items.
- **Caution Amber** (Text: #B45309, Bg: #FFFBEB, Border: #FDE68A): Near-expiry batches (<90 days), pending requisitions awaiting custodian approval, and low stock warnings.
- **Dispensed Emerald** (Text: #15803D, Bg: #F0FDF4, Border: #BBF7D0): Stock OK, fulfilled requisitions, and active consignor accounts.
- **Hospital Sky** (Text: #0369A1, Bg: #F0F9FF, Border: #BAE6FD): Partially fulfilled requisitions, batch replenishment updates, and informational callouts.

### Named Rules
**The Clinical Contrast Rule.** Alert backgrounds must always use soft 50-level tints paired with dark 700-level text and 200-level borders; high-saturation, unreadable solid color blocks for data indicators are strictly prohibited.
**The Ten Percent Rule.** Hospital Alpine Teal is reserved for primary actions, navigation indicators, and item identifiers. It must never cover more than 10% of any viewport to preserve its visual authority.

## Typography

**Display Font:** Inter (with ui-sans-serif, system-ui, -apple-system, sans-serif)  
**Body Font:** Inter (with ui-sans-serif, system-ui, -apple-system, sans-serif)  
**Label/Mono Font:** Inter with tabular figures (`font-feature-settings: "tnum"`) and monospace for batch/item codes (`ui-monospace`, `Courier New`).

**Character:** Clean, objective, high-legibility clinical typography with tight letter spacing for dense medical data scanning.

### Hierarchy
- **Display** (Bold 700, 1.875rem / 30px, line-height 1.25, tracking -0.02em): Primary dashboard page headers and summary inventory counts.
- **Headline** (Bold 700, 1.5rem / 24px, line-height 1.3, tracking -0.015em): Section titles, modal dialog headers, and panel module titles.
- **Title** (Semi-Bold 600, 1.125rem / 18px, line-height 1.4, tracking -0.01em): Table section headers, card titles, and group classifications.
- **Body** (Regular 400 & Medium 500, 0.875rem / 14px, line-height 1.5, tracking -0.01em): Table records, drug and supply descriptions, batch details, and form input values.
- **Label** (Semi-Bold 600, 0.75rem / 12px, line-height 1.0, tracking 0.05em, uppercase): Table headers, status badge text, form input labels, and batch metadata pills.

### Named Rules
**The Inter-Only Rule.** All user interface elements must use Inter with standardized weight and tracking tokens; decorative, serif, or whimsical display typefaces are strictly prohibited.
**The Tabular Figures Rule.** All item quantities, stock counts, batch numbers, and expiry day counts must render with monospace or tabular figures to preserve vertical column alignment.

## Layout

Layout follows a strict 8px spatial grid across both the Operations Panel and Unit Portal.
- **Micro Spacing:** 4px (`gap-1`, `p-1`) used exclusively for badge icon-to-label gaps and tight metadata groupings.
- **Component Padding:** 8px (`p-2`, `gap-2`) and 16px (`p-4`, `gap-4`) for buttons, form inputs, and list rows.
- **Container Padding:** 24px (`p-6`, `gap-6`) for cards, modal dialogs, and table wrappers.
- **Layout Rhythm:** 32px (`gap-8`, `mb-8`) and 48px (`gap-12`) between major dashboard sections.
- **Responsive Behavior:** 
  - Desktop (>1024px): Collapsible left sidebar navigation, multi-column inventory tables, dense filters.
  - Tablet (768px - 1024px): Expanded tap targets, horizontal scrollable tables with sticky action columns, touch-friendly filter dropdowns.
  - Mobile (<768px): Dedicated bottom navigation bar in the Unit Portal (`filament/portal/bottom-nav.blade.php`), stacked requisition items, full-width touch buttons.

### Named Rules
**The 8px Grid Rule.** All structural margins, paddings, and flex/grid gaps must be exact multiples of 8px (8, 16, 24, 32, 40, 48px), with 4px permitted only for micro badge alignment.

## Elevation & Depth

The system uses subtle tonal layering rather than dramatic drop shadows. Depth is communicated primarily through crisp 1px borders (#E2E8F0) and background tint shifts, preserving clean clinical scanning.

### Shadow Vocabulary
- **Subtle Surface** (`box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05)`): Applied to white card containers, table panels, and resting form fields.
- **Raised Action** (`box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1)`): Applied on button hover, active steppers, and floating alert banners.
- **Modal Elevation** (`box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1)`): Applied to modal dialogs, slide-over panels, and dropdown menus.

### Named Rules
**The Flat-At-Rest Rule.** All cards, tables, and rows remain flat at rest with 1px borders (#E2E8F0). Elevated drop shadows appear only in response to direct user interaction (hover, active focus, modal overlays).

## Shapes

Forms are cleanly geometric, soft, and modern, avoiding sharp harsh corners while preventing excessive capsule pill shapes.
- **Small Controls:** 6px radius (`rounded-md`) for micro-pills, badges, and code tags.
- **Interactive Controls:** 8px radius (`rounded-lg`) for standard buttons, text fields, search bars, and selects.
- **Containers & Cards:** 12px radius (`rounded-xl`) for cards, dashboard stat widgets, table enclosures, and modal dialogs.
- **Round Elements:** 9999px (`rounded-full`) reserved strictly for circular avatar badges and status indicator dots.

### Named Rules
**The Two-Tier Radius Rule.** Use 8px (`rounded-lg`) for buttons and form fields; use 12px (`rounded-xl`) for cards, tables, and modal windows. Never introduce arbitrary 4px, 16px, or 20px radii.

## Components

### Buttons
- **Shape:** 8px radius (`rounded-lg`) for standard desktop controls; 12px radius (`rounded-xl`) for ward touch buttons.
- **Primary Action (Standard 40px):** Background Teal 700 (#0F766E), text White, padding 8px 16px, height 40px, font-weight 600.
- **Ward Touch Action (48px):** Background Teal 700 (#0F766E), text White, padding 12px 24px, minimum height 48px, minimum width 48px, font-weight 700. Active state scales to 98% for physical tactile confirmation.
- **Secondary / Subdued:** Background Teal 50 (#F0FDFA), text Teal 700 (#0F766E), border Teal 200 (#CCFBF1), padding 6px 12px.
- **Hover / Focus:** Hover shifts to Teal 800 (#115E59); focus provides a 2px Teal 700 ring with 2px white offset.

### Unified Badges
- **Shape:** 8px radius (`rounded-lg`), 1px solid border matching semantic border tokens.
- **Typography:** 12px (0.75rem), font-weight 600, uppercase letter-spacing 0.05em.
- **Structure:** Inline flex, 6px gap, 14px monochrome Heroicon SVG paired with explicit text.
- **Variants:**
  - *Pending:* Soft Amber bg (#FFFBEB), Amber text (#B45309), Heroicon `clock`.
  - *Partially Fulfilled:* Soft Sky bg (#F0F9FF), Sky text (#0369A1), Heroicon `arrow-path`.
  - *Fulfilled / Stock OK:* Soft Emerald bg (#F0FDF4), Green text (#15803D), Heroicon `check-badge`.
  - *Expired / Below Min / STAT:* Soft Red bg (#FEF2F2), Red text (#B91C1C), Heroicon `x-circle` or `bolt`.

### Cards / Containers
- **Corner Style:** 12px radius (`rounded-xl`).
- **Background:** Pure White (#FFFFFF).
- **Border:** 1px solid Slate 200 (#E2E8F0).
- **Internal Padding:** 24px (`p-6`).

### Inputs / Form Fields
- **Style:** 8px radius (`rounded-lg`), 1px solid Slate 300 (#CBD5E1), background White, font size 14px, minimum height 40px (48px for ward touch inputs).
- **Focus:** 2px ring in Teal 600 (#0D9488) with 1px border shift to Teal 700 (#0F766E).
- **Error:** 1px solid Red 500 (#EF4444) with light Red 50 background wash.

### Touch Steppers (Bedside & Ward Requisitions)
- **Structure:** 3-part grouped control (Decrement button, Readonly quantity display, Increment button).
- **Touch Target:** 48px height and 48px width per button, heavy 2px border (#CBD5E1), high-contrast +/- glyphs for gloved operation.

### Data Tables
- **Header:** Slate 50 background (#F8FAFC), Slate 600 text (#475569), 12px uppercase bold, tracking 0.05em, padding 12px 16px.
- **Row Styling:** Hover background Slate 50 at 50% opacity, 1px horizontal border (#F1F5F9), padding 14px 16px.
- **Item Codes:** Monospace font, bold Teal 800 (#115E59).

### Named Rules
**The Ward Tap Target Rule.** Any interactive button, stepper, or select control rendered in the Unit Portal or ward-requisition flow must adhere to a strict 48px minimum touch target.
**The Zero-Emoji Dual-Coded Rule.** Every badge and alert must pair clear English text with an authentic SVG Heroicon on a tinted background. Emojis are strictly banned from all UI components.

## Do's and Don'ts

### Do:
- **Do** use Hospital Alpine Teal (#0F766E) as the primary brand anchor for major CTAs, navigation, and active indicators.
- **Do** enforce minimum 48px touch targets on all ward requisition controls and mobile/tablet touch interfaces.
- **Do** dual-code every badge with readable text, a standard SVG Heroicon, and semantic soft colors.
- **Do** use soft 50-level backgrounds (#FEF2F2, #FFFBEB, #F0FDF4, #F0F9FF) with high-contrast 700-level text for alert states.
- **Do** format item codes, quantities, and batch lot numbers in monospace or tabular figures.
- **Do** adhere strictly to the 8px spatial grid across all layouts, margins, and paddings.

### Don't:
- **Don't** use emojis anywhere in the user interface (no status emojis, icon substitutes, or decorative glyphs).
- **Don't** use neon, vibrant purple/blue gradients, dark-mode neon glows, or SaaS-marketing tropes.
- **Don't** rely on color alone to indicate urgent states like batch expiration or stockouts.
- **Don't** expose supplier unit costs or procurement financial values to `requesting_unit` role screens.
- **Don't** use raw unpadded table cells or unformatted system fonts.
- **Don't** mix arbitrary border radii outside the two standard tiers (8px for controls, 12px for cards).
