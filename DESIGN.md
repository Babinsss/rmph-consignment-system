# RMPH Consignment System — Design System & Governance

## Visual Philosophy
- **Domain**: Healthcare clinical operations, hospital logistics, audit compliance.
- **Tone**: Authoritative, precise, high-legibility, calm clinical efficiency.
- **Anti-patterns Refused**: No generic gradient text, no nested container cards, no emoji icon clutter, no undersized text below 12px, no low-contrast badges (<4.5:1).

## Typography
- **Primary Face**: `Instrument Sans`, sans-serif (bundled locally with Vite, optimal optical geometry and high x-height for clinical charts).
- **Fallbacks**: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif.
- **Size Scale**:
  - `caption`: 12px (0.75rem), line-height: 1.25, tracking: +0.02em (never below 11px)
  - `body-sm`: 13px–14px (0.875rem), line-height: 1.5
  - `body`: 15px–16px (1rem), line-height: 1.6
  - `heading-sm`: 18px–20px (1.25rem), weight: 700
  - `heading-md`: 24px–28px (1.75rem), weight: 800
  - `heading-lg`: 36px–48px (2.5rem–3rem), weight: 900, tracking: -0.02em
- **Rules**:
  - `text-wrap: balance` on headings
  - `text-wrap: pretty` on paragraphs to prevent orphan stragglers

## Color System
- **Clinical Primary**: Deep Teal `#0f766e` (`teal-700` light) / `#14b8a6` (`teal-400` dark)
- **Clinical Accent**: Emerald `#047857` (`emerald-700` light) / `#34d399` (`emerald-400` dark)
- **Neutral Dark / Slate**: `#0f172a` (`slate-900`), `#1e293b` (`slate-800`), `#334155` (`slate-700`)
- **Neutral Light**: `#ffffff`, `#f8fafc` (`slate-50`), `#f1f5f9` (`slate-100`)
- **Borders & Dividers**: `#e2e8f0` (`slate-200`) light / `#334155` (`slate-700`) dark
- **Role Accents**:
  - Hospital Administration: `#4f46e5` (`indigo-600`)
  - Pharmacy Stocks: `#0284c7` (`sky-600`)
  - Medical Supplies: `#0d9488` (`teal-600`)
  - Ward Requesters: `#7c3aed` (`purple-600`)
- **Contrast Guarantee**: All text on interactive elements and badges meets minimum WCAG AA 4.5:1 contrast.

## Layout & Responsiveness
- **Touch Targets**: Minimum 44x44px for touch interactions on mobile.
- **Breakpoints**: Mobile (<640px), Tablet (640px-1023px), Desktop (≥1024px).
- **Grids**: Fluid responsive grids collapsing gracefully to single-column on mobile.
