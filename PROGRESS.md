# NRH Intelligence — Frontend Progress Log

Last updated: 2026-04-21

---

## Status: Responsive design complete & built

All changes have been compiled with `npm run build`. The app is served by Laravel Herd at `https://nrh-intelligence.test`.

---

## What Was Done (Session 1–2)

### 1. Upload Documents UI (Step 3) — Screening & KYC forms
- **Files:** `resources/views/client/request/create/index.blade.php`, `due-diligence.blade.php`
- Drop zones use `x-data="{ hovered: false }"` with Alpine `:style` **object** (not string) to prevent wiping static styles
- `x-show` used instead of `x-if` for conditional visibility inside drop zones

### 2. Country Selector — Global Screening
- **File:** `resources/views/client/request/create/index.blade.php`
- Replaced grid of flag buttons with a searchable combobox (scales to all countries)
- Alpine state: `allCountries`, `countrySearch`, `countryOpen`, `selectedCountryObj`, `filteredCountries`
- Full-width layout (no max-width constraint)

### 3. KYC/KYB/KYS Screen UI (due-diligence.blade.php)
- **File:** `resources/views/client/request/create/due-diligence.blade.php`
- Added page header `<h1>Know Your <em>{{ $h1Em }}</em></h1>` — `$h1Em` resolves to Customer/Business/Supplier
- Full-width layout (removed `max-width: 960px`)
- Fixed Alpine `:style` string → object on step 2 check cards and Add/Remove buttons
- Fixed step 2 cart: `template x-if` → `x-show`
- Fixed step 3 upload zones (same pattern as index.blade.php)

### 4. Step Indicator Connector Animation Fix
- **Files:** both create form blade files
- Inner fill bar had static `style="position:absolute;inset:0;background:..."` — Alpine `:style` string was wiping those
- Fixed: `:style="{ transform: step > N ? 'scaleX(1)' : 'scaleX(0)' }"`
- Added `class="step-indicator"` to the step container wrapper in both files

### 5. Dashboard UI
- **File:** `resources/views/client/dashboard/index.blade.php`
- Simplified `<h1>` text
- Fixed adverse flags stat bug: was re-using `$stats['needs_review']` for two different stats
- Replaced confusing check-dot column with `X/Y` counter (checks passed / total)
- Wrapped table in `<div class="table-scroll">`

### 6. Browser Tab Favicon
- **Files:** `resources/views/components/client/layouts/app.blade.php`, `auth.blade.php`
- Added `<link rel="icon" type="image/png" href="{{ asset('nrh-logo.png') }}">`

### 7. Mobile Hamburger Menu Fix
- **File:** `resources/css/app.css`
- Overlay was `z-index: 30`, topbar was `z-index: 10` — hamburger was untappable under the overlay
- Fixed: `.nrh-topbar { z-index: 35 }`

### 8. Table Horizontal Scroll
- **Files:** `resources/views/client/requests/index.blade.php`, `details.blade.php`, `billing/invoices.blade.php`, `billing/transactions.blade.php`, dashboard, screening/KYC forms
- All tables wrapped with `<div class="table-scroll">...</div>`
- `.card { overflow: clip }` (not `hidden`) so the table-scroll container works inside cards

### 9. Full Responsive Design
- **File:** `resources/css/app.css`
- Three breakpoints added: tablet (≤1024px), mobile (≤768px), small mobile (≤480px)
- Built and confirmed compiling: `npm run build` ✓

Key responsive rules:
```
Tablet (≤1024px):
  - .stats → 3 columns
  - .grid → 1 column, .side-col → 2-column grid

Mobile (≤768px):
  - Topbar search hidden
  - .stats → 2 columns
  - .grid + .side-col → single column stack
  - .step-indicator → overflow-x: auto, flex-wrap: nowrap
  - [style*="grid-template-columns:1fr 300px"] etc → 1fr !important
  - [style*="position:sticky"] → static !important

Small mobile (≤480px):
  - Breadcrumbs: only last crumb shown
  - Filter chips: only active chip shown
  - Smaller padding, font sizes
```

---

## What Still Needs Testing

- [ ] Dashboard on mobile viewport — stats 2-column, table scrolls
- [ ] Screening form step indicator on mobile — scrolls horizontally
- [ ] KYC form layouts on mobile — columns stack properly
- [ ] Tables across all pages — scroll within card without layout break
- [ ] Sidebar toggle on mobile — hamburger tappable, overlay dismisses

---

## Key Files Reference

| File | Purpose |
|------|---------|
| `resources/css/app.css` | All styles + responsive breakpoints |
| `resources/views/components/client/layouts/app.blade.php` | Main layout shell (sidebar + topbar) |
| `resources/views/components/client/layouts/auth.blade.php` | Auth layout |
| `resources/views/client/layouts/_sidebar.blade.php` | Sidebar nav |
| `resources/views/client/layouts/_navbar.blade.php` | Top navbar |
| `resources/views/client/dashboard/index.blade.php` | Dashboard page |
| `resources/views/client/request/create/index.blade.php` | Screening request form (Malaysia/Global) |
| `resources/views/client/request/create/due-diligence.blade.php` | KYC/KYB/KYS form |
| `resources/views/client/requests/index.blade.php` | Requests list |
| `resources/views/client/requests/details.blade.php` | Request detail |
| `resources/views/client/billing/invoices.blade.php` | Invoices list |
| `resources/views/client/billing/transactions.blade.php` | Transactions list |

---

## Important Technical Notes

1. **Alpine `:style` must be object, not string** — string replaces the entire `style` attribute (wiping static styles). Always use `{ property: value }` syntax.
2. **`overflow: clip` vs `overflow: hidden`** — `hidden` creates a BFC blocking child scroll containers. Use `clip` on cards so `table-scroll` inside works.
3. **Tailwind v4 media queries** compile as `width<=Xpx` range syntax in output CSS — this is correct.
4. **`[style*="grid-template-columns:1fr 300px"]`** attribute selectors override inline grid layouts without editing every Blade file. Inline styles must have no spaces after colons to match.
5. **`npm run build`** must be run after any CSS change. Or use `npm run dev` / `composer run dev` for hot reload during development.

---

## Next Possible Tasks

- Further test and polish responsive behavior on real device
- Any remaining UI pages not yet audited for responsiveness
- Potential: profile page, settings page, notification UI
