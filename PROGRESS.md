# NRH Intelligence — Frontend Progress Log

Last updated: 2026-05-07

---

## Status: Cash-billing payment workflow in progress (customer-side prep done)

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

## Session — 2026-05-07 (Cash-billing payment workflow)

### 1. Finance notification on slip upload (Step 0)
- **File:** `app/Http/Controllers/Client/Request/PaymentSlipController.php`
- `store()` now calls a new `notifyFinance()` method after the DB update succeeds.
- Sends `Mail::raw()` to `config('billing.proof_of_payment_email')` with: request reference, customer, uploader (name + email), timestamp, original filename.
- `Reply-To` set to the uploader so finance can hit reply with questions.
- Subject distinguishes `uploaded` vs `replaced`.
- Mail failure is logged at `payment_slip.finance_notification_failed`; the upload itself is not rolled back (slip stays in storage). Missing recipient config logs a warning and returns silently.

### 2. Payment-verified schema + customer-side UI (Step 1 — customer half)

The admin "Verify payment" UI lives in the sibling `~/Herd/nrh-admin` Laravel app (shared schema). This session shipped the schema migration and the customer-portal prep so that the moment the admin app sets `payment_verified_at`, the customer portal already reflects it.

- **Migration:** `database/migrations/2026_05_07_130127_add_payment_verified_to_screening_requests_table.php`
  - `payment_verified_at` (timestamp, nullable)
  - `payment_verified_by` (unsigned bigint, nullable, **no FK** — left to admin portal to constrain since staff `users` table lives there)
- **Model:** `App\Models\ScreeningRequest` — both columns added to `$fillable`, `payment_verified_at` cast to `datetime`, new `isPaymentVerified()` helper.
- **Status badge partial** (`resources/views/client/partials/_status-badge.blade.php`): match ladder for cash + `status === 'new'`:
  - no slip → "Awaiting payment" (red)
  - slip + unverified → "Verifying payment" (muted gold)
  - slip + verified → "Payment received" (green)
- **Request detail page** (`resources/views/client/requests/details.blade.php`): split `$isCashPaymentPending` into separate pending vs verified flags. Verified state hides the bank-details + upload block and shows a slim green confirmation card with verification timestamp and slip-download link.
- **Requests list** (`resources/views/client/requests/index.blade.php`): `$paymentState` is a 4-way `match` (`none/awaiting/verifying/verified`). Payment tab matches `awaiting OR verifying` (verified items drop out). Banner still counts only `awaiting upload` (the call-to-action). Tab counter uses a separate `$paymentTabCount` from the controller (`new` + `payment_verified_at IS NULL`).
- **Dashboard** (`resources/views/client/dashboard/index.blade.php`): recent-requests pill ladder and activity feed `$feedText` both add a "Payment received" branch.
- **Controllers:** `ViewRequestController::index` now exposes `$awaitingPaymentCount` (banner) **and** `$paymentTabCount` (tab); `DashboardController::index` uses `whereNull` instead of `where(..., null)`.

### Verification
- `vendor/bin/pint --dirty --format agent` → pass
- `php artisan test --compact` → 2 passed, 2 assertions
- Migration ran; columns confirmed via `Schema::getColumnListing("screening_requests")`.

---

## Next Up

1. **Admin verification UI in `~/Herd/nrh-admin`.** Build the endpoint that writes `payment_verified_at = now()` + `payment_verified_by = Auth::id()` from the admin app, plus a "pending verification" queue/list so finance can find uploaded slips. Without this, today's email + customer-side prep are informational only — finance still can't actually verify anything in-system.

2. **Step 2 — Customer email when admin verifies.** Observer on `ScreeningRequest::updated()` watching `payment_verified_at` transitioning null → non-null. ~30 min once the admin endpoint exists. Highest-signal customer email of the whole flow ("we received your payment, work has started").

3. **Step 3 — Convert to Laravel Notification classes (foundation for in-app bell).** Refactor existing `Mail::raw()` call sites (login OTP, invitation email, finance-on-upload, customer-on-verify) to `Notification` classes with `via: ['mail', 'database']`. Adds DB persistence for free via the standard `notifications` table — that's the prerequisite for a customer top-bar bell. Don't build the bell UI itself until at least 2–3 customer-facing notification types exist (today: zero).
