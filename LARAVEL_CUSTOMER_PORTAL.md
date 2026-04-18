# LARAVEL_CUSTOMER_PORTAL.md

Laravel implementation plan for the NRH INTELLIGENCE Customer (Client) Portal — the customer-facing side of the background check/verification platform.

---

## Stack

- **Framework:** Laravel 11.x
- **Auth:** Custom session-based auth (mirrors existing CI3 behavior)
- **Database:** MySQL
- **Frontend:** Blade + DataTables.js + Chart.js + Alpine.js (or Livewire)
- **PDF:** `barryvdh/laravel-dompdf` or `mpdf/mpdf`
- **Excel:** `maatwebsite/excel`
- **Queue:** Laravel Queue (mail notifications)
- **Mail:** Laravel Mail (SMTP)
- **Payment:** Cash (direct bank transfer) + Monthly Billing — no online payment gateway
- **File Storage:** `storage/app/files/` (symlinked to `public/files/`)

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Client/
│   │       ├── Auth/
│   │       │   ├── LoginController.php
│   │       │   └── RegistrationController.php
│   │       ├── DashboardController.php
│   │       ├── Request/
│   │       │   ├── CreateRequestController.php
│   │       │   ├── ViewRequestController.php
│   │       │   ├── SearchRequestController.php
│   │       │   ├── TrackRequestController.php
│   │       │   └── OldRequestController.php
│   │       ├── Billing/
│   │       │   ├── TransactionController.php
│   │       │   └── InvoiceController.php
│   │       ├── Scope/
│   │       │   └── MapsController.php
│   │       └── Settings/
│   │           ├── AccountController.php
│   │           ├── UserController.php
│   │           ├── PackageController.php
│   │           ├── SecurityController.php
│   │           ├── AgreementController.php
│   │           └── InvoiceSettingsController.php
│   ├── Middleware/
│   │   ├── ClientAuthenticated.php     # Guard all client routes
│   │   ├── CheckAgreement.php          # Redirect if agreement expired
│   │   └── CheckBalance.php            # Warn if balance low (optional)
│   └── Requests/                       # Form Request validation classes
│       ├── RegisterRequest.php
│       ├── CreateCandidateRequest.php
│       └── TopupRequest.php
├── Models/
│   ├── Customer.php
│   ├── CustomerUser.php
│   ├── CustomerAgreement.php
│   ├── BgRequest.php                   # Avoid conflict with Laravel's Request
│   ├── CandidateRequest.php
│   ├── CandidateFile.php
│   ├── CandidateReport.php
│   ├── TemporaryCandidate.php
│   ├── TemporaryFile.php
│   ├── SearchScope.php
│   ├── ScopeCountry.php
│   ├── Package.php
│   ├── PackageScope.php
│   ├── Preferred.php
│   ├── PreferredScope.php
│   ├── Balance.php
│   ├── Transaction.php
│   ├── Invoice.php
│   ├── InvoiceItem.php
│   ├── Country.php
│   └── AppLogger.php
├── Services/
│   ├── Client/
│   │   ├── CartService.php             # Replaces CI3 Cart library
│   │   ├── RequestSubmissionService.php
│   │   ├── DashboardService.php
│   │   └── FileUploadService.php
│   ├── EmailService.php
│   └── HashidsService.php
└── Mail/
    ├── ClientTwoFactorMail.php
    ├── ClientPasswordResetMail.php
    ├── RegistrationReceivedMail.php
    ├── RegistrationApprovedMail.php
    ├── RequestCompletedMail.php
    └── InvoiceIssuedMail.php
```

---

## Routing

All customer routes are under the root prefix (no path prefix, mirrors existing `/login`, `/dashboard` etc.).

```php
// routes/client.php  (loaded from RouteServiceProvider)
Route::name('client.')->group(function () {

    // Auth (unauthenticated)
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'submit'])->name('login.submit');
    Route::get('/verification', [LoginController::class, 'verification'])->name('verification');
    Route::post('/verification', [LoginController::class, 'verifyCode'])->name('verification.submit');
    Route::get('/forgot-password', [LoginController::class, 'forgot'])->name('forgot');
    Route::post('/forgot-password', [LoginController::class, 'sendReset'])->name('forgot.submit');
    Route::get('/reset-password/{token}', [LoginController::class, 'reset'])->name('reset');
    Route::post('/reset-password', [LoginController::class, 'processReset'])->name('reset.process');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Registration
    Route::get('/register', [RegistrationController::class, 'index'])->name('register');
    Route::post('/register', [RegistrationController::class, 'submit'])->name('register.submit');
    Route::get('/register/success', [RegistrationController::class, 'success'])->name('register.success');

    // Authenticated routes
    Route::middleware(['client.auth', 'check.agreement'])->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/filter', [DashboardController::class, 'filter'])->name('dashboard.filter');

        // Request — Create
        Route::prefix('request')->name('request.')->group(function () {
            Route::get('/new', [CreateRequestController::class, 'index'])->name('new');
            Route::get('/recent', [CreateRequestController::class, 'getRecent'])->name('recent');
            Route::get('/scopes', [CreateRequestController::class, 'getScopes'])->name('scopes');
            Route::post('/cart/add', [CreateRequestController::class, 'addToCart'])->name('cart.add');
            Route::post('/cart/remove', [CreateRequestController::class, 'removeFromCart'])->name('cart.remove');
            Route::post('/cart/clear', [CreateRequestController::class, 'clearCart'])->name('cart.clear');
            Route::post('/cart/add-package', [CreateRequestController::class, 'addPackage'])->name('cart.add-package');
            Route::post('/cart/remove-package', [CreateRequestController::class, 'removePackage'])->name('cart.remove-package');
            Route::post('/preferred/add', [CreateRequestController::class, 'addPreferred'])->name('preferred.add');
            Route::post('/preferred/remove', [CreateRequestController::class, 'removePreferred'])->name('preferred.remove');
            Route::post('/preferred/save', [CreateRequestController::class, 'savePreferred'])->name('preferred.save');
            Route::post('/candidate', [CreateRequestController::class, 'newCandidate'])->name('candidate.store');
            Route::delete('/candidate/{id}', [CreateRequestController::class, 'removeCandidate'])->name('candidate.remove');
            Route::get('/candidates', [CreateRequestController::class, 'candidates'])->name('candidates');    // DataTable AJAX
            Route::post('/candidate/validate', [CreateRequestController::class, 'validateCandidate'])->name('candidate.validate');
            Route::post('/upload-document', [CreateRequestController::class, 'uploadDocument'])->name('upload.document');
            Route::post('/upload-file', [CreateRequestController::class, 'uploadFile'])->name('upload.file');
            Route::post('/submit', [CreateRequestController::class, 'submit'])->name('submit');
            Route::get('/success', [CreateRequestController::class, 'successful'])->name('success');
        });

        // Request — View (active)
        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('/', [ViewRequestController::class, 'index'])->name('index');
            Route::get('/list', [ViewRequestController::class, 'list'])->name('list');          // DataTable AJAX
            Route::get('/search', [SearchRequestController::class, 'index'])->name('search');
            Route::get('/track', [TrackRequestController::class, 'index'])->name('track');
            Route::post('/track/candidate', [TrackRequestController::class, 'candidate'])->name('track.candidate');
            Route::get('/{id}', [ViewRequestController::class, 'details'])->name('details');
            Route::get('/{id}/report', [ViewRequestController::class, 'report'])->name('report');
            Route::get('/{id}/report/separate', [ViewRequestController::class, 'separate'])->name('report.separate');
            Route::get('/{requestId}/candidate/{candidateId}', [ViewRequestController::class, 'individual'])->name('individual');
        });

        // Old / Completed Requests
        Route::prefix('history')->name('history.')->group(function () {
            Route::get('/', [OldRequestController::class, 'index'])->name('index');
            Route::post('/search', [OldRequestController::class, 'search'])->name('search');
            Route::get('/{id}', [OldRequestController::class, 'details'])->name('details');
            Route::get('/{id}/basic', [OldRequestController::class, 'basic'])->name('basic');
            Route::get('/{requestId}/candidate/{candidateId}', [OldRequestController::class, 'single'])->name('single');
            Route::get('/{id}/download', [OldRequestController::class, 'download'])->name('download');
        });

        // Billing
        Route::prefix('billing')->name('billing.')->group(function () {
            Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
            Route::get('/transactions/list', [TransactionController::class, 'list'])->name('transactions.list');  // DataTable AJAX
            Route::get('/transactions/{id}/receipt', [TransactionController::class, 'receipt'])->name('transactions.receipt');
            Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices');
            Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
            Route::get('/invoices/{id}/download', [InvoiceController::class, 'download'])->name('invoices.download');
        });

        // Scope Maps
        Route::get('/maps', [MapsController::class, 'index'])->name('maps');
        Route::get('/maps/{countryId}', [MapsController::class, 'countries'])->name('maps.countries');

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/account', [AccountController::class, 'index'])->name('account');
            Route::post('/account', [AccountController::class, 'update'])->name('account.update');
            Route::get('/account/resources', [AccountController::class, 'resources'])->name('account.resources');
            Route::get('/users', [Settings\UserController::class, 'index'])->name('users');
            Route::get('/users/create', [Settings\UserController::class, 'create'])->name('users.create');
            Route::post('/users', [Settings\UserController::class, 'store'])->name('users.store');
            Route::get('/users/{id}', [Settings\UserController::class, 'details'])->name('users.details');
            Route::post('/users/{id}', [Settings\UserController::class, 'update'])->name('users.update');
            Route::get('/packages', [PackageController::class, 'index'])->name('packages');
            Route::get('/packages/create', [PackageController::class, 'create'])->name('packages.create');
            Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
            Route::get('/packages/{id}', [PackageController::class, 'details'])->name('packages.details');
            Route::post('/packages/{id}', [PackageController::class, 'update'])->name('packages.update');
            Route::get('/security', [SecurityController::class, 'index'])->name('security');
            Route::post('/security', [SecurityController::class, 'update'])->name('security.update');
            Route::get('/agreement', [AgreementController::class, 'index'])->name('agreement');
            Route::get('/invoice', [InvoiceSettingsController::class, 'index'])->name('invoice');
        });
    });
});
```

---

## Authentication

### Session-Based (not Sanctum)

```php
// app/Http/Middleware/ClientAuthenticated.php
public function handle(Request $request, Closure $next)
{
    if (!session()->has('client_user_id')) {
        return redirect()->route('client.login');
    }
    return $next($request);
}
```

Session stores: `client_user_id`, `client_customer_id`, `client_user_role_id`

### Two-Factor Authentication (2FA)
1. Validate email + password
2. Generate 6-digit code → store in `authentications` table with 30-min expiry
3. Send `ClientTwoFactorMail`
4. Redirect to `/verification` → user enters code
5. On match + not expired → write full session

### Password Reset
1. Submit email → check `customer_users` table → generate `password_resets` token
2. Send `ClientPasswordResetMail` with link (30-min expiry)
3. `GET /reset-password/{token}` validates token → show form
4. On submit → update `password` (bcrypt) → invalidate token → redirect to login

### Registration Flow
1. Guest submits registration form
2. Insert into `registrations` table with status `Pending`
3. Send `RegistrationReceivedMail` to applicant
4. Admin approves → creates `customers` + `customer_users` records → send `RegistrationApprovedMail` with credentials

---

## Middleware

| Middleware | Purpose |
|---|---|
| `ClientAuthenticated` | Guard all client routes |
| `CheckAgreement` | If `customer_agreements.expiry_date` is past → redirect to agreement page |

---

## Key Models & Relationships

```php
// CustomerUser (authenticated user)
CustomerUser::belongsTo(Customer::class)
CustomerUser::belongsTo(UserRole::class)
CustomerUser::hasMany(BgRequest::class)

// Customer
Customer::hasOne(Balance::class)
Customer::hasMany(CustomerUser::class)
Customer::hasMany(BgRequest::class)
Customer::hasMany(Invoice::class)
Customer::hasMany(Transaction::class)
Customer::hasOne(CustomerAgreement::class)
Customer::belongsTo(Country::class)
Customer::belongsToMany(SearchScope::class, 'scope_customers')

// BgRequest
BgRequest::belongsTo(CustomerUser::class)
BgRequest::belongsTo(Customer::class)
BgRequest::hasMany(CandidateRequest::class)

// CandidateRequest
CandidateRequest::belongsTo(BgRequest::class)
CandidateRequest::hasMany(CandidateFile::class)
CandidateRequest::hasMany(CandidateReport::class)
CandidateRequest::belongsTo(IdentityType::class)

// Package (client-saved favorites — maps to `preferred` table)
Preferred::belongsTo(Customer::class)
Preferred::belongsToMany(SearchScope::class, 'preferred_scopes')

// Package (admin-defined bundles)
Package::belongsTo(Country::class)
Package::belongsToMany(SearchScope::class, 'package_scopes')

// Balance (read-only for client — updated by admin only)
Balance::belongsTo(Customer::class)
// Fields: amount, currency_id, updated_at

// Invoice
Invoice::belongsTo(Customer::class)
Invoice::hasMany(InvoiceItem::class)
InvoiceItem::belongsTo(BgRequest::class)
```

---

## Cart Service

Replaces CodeIgniter's `Cart` library using Laravel session.

```php
// app/Services/Client/CartService.php
class CartService
{
    private string $key = 'client_cart';

    public function add(array $scope): void
    {
        $cart = session($this->key, []);
        $cart[$scope['id']] = $scope;   // keyed by SearchScopeId
        session([$this->key => $cart]);
    }

    public function remove(int $scopeId): void { ... }
    public function clear(): void { session()->forget($this->key); }
    public function contents(): array { return session($this->key, []); }
    public function total(): float { ... }
    public function addPackage(Package $package): void { ... }
}
```

---

## Modules

### Dashboard
- Query counts per `request_status_id` (New=1, Pending=2, Complete=3) scoped to `customer_id`
- AJAX `POST /dashboard/filter` returns JSON for Chart.js
- Date filter: daily (hours), weekly (days), monthly (days), custom range
- Display: balance remaining, last topup date, agreement expiry

### Create Request (Critical Flow)

**Step 1 — Country & Scope Selection**
- Load available scopes filtered by: `scope_countries` (country enabled) + `scope_customers` (customer override)
- `CartService::add()` / `remove()` stored in session
- Add admin-defined packages: load `packages` where `countries_id = customer.country_id`

**Step 2 — Add Candidates**
- Each candidate stored in `temporary_candidates` (session user context)
- Fields: name, identity type, identity number, mobile, remarks
- Duplicate check: query `candidate_requests` for same identity number under customer
- DataTable AJAX for listing added candidates

**Step 3 — Upload Documents**
- File types per scope requirements: Consent (1), CV (2), Extra (3)
- Upload to `storage/app/files/{type}/{customer_id}/{user_id}/{Y-m-d}/`
- Store reference in `temporary_files` table
- Accepted: PDF, DOC, DOCX, JPG, PNG; max size configured in `filesystems.php`

**Step 4 — Validate & Submit**
- Check all required documents uploaded per scope rules
- On success:
  - Insert `bg_requests` record
  - Move each `temporary_candidate` → `candidate_requests`
  - Move each `temporary_file` → `candidate_files`
  - Insert `usages` record (amount stored for monthly billing)
  - Log to `app_loggers`
  - Clear cart + temporary records
  - Redirect to `/request/success`

### View Requests (Active)
- DataTable with server-side processing: columns RequestId, Candidates, Status, Date, Actions
- Scoped to `customer_id` from session
- Status badge colors: New=blue, Pending=amber, Complete=green
- Report: generate PDF via DomPDF per request

### Track Requests
- Search by candidate name or identity number
- Returns matching `candidate_requests` with current `request_status_id`
- Real-time status visualization (step indicator)

### Old / Completed Requests
- Query requests where `request_status_id = 3 (Complete)`
- Download: ZIP of all `candidate_files` for a request
- Old/archive: use `->on('legacy')` Eloquent connection for `voss_old_current` DB

### Billing — Transactions
- Read-only view of all payment transactions for the customer
- DataTable: date, reference, amount, payment method, recorded by
- Payment methods: `cash` (direct bank transfer), `monthly_billing`
- Transactions are recorded by admin; client views only
- Receipt: downloadable PDF of individual transaction

### Billing — Invoices
- Monthly invoices issued by admin at the end of each billing cycle
- List: filter by status (Unpaid/Paid)
- View: itemised list of requests completed in the period + unit prices + total
- Download: PDF invoice
- Client is notified by email when a new invoice is issued (`InvoiceIssuedMail`)

### Scope Maps
- Display `countries` with `scope_countries` count
- Click country → list available `search_scopes` with description and turnaround time

### Settings — Users (Team Management)
- CRUD for `customer_users` scoped to session `customer_id`
- On creation: generate password → send `CustomerUserCreatedMail` with credentials
- Roles: customer-level roles only (not admin roles)

### Settings — Packages (Favorites)
- CRUD for `preferred` (customer-saved scope bundles)
- Assign `search_scopes` via `preferred_scopes` pivot
- Used in "Create Request" → "My Favorites" tab

### Settings — Security
- Current password required to set new password
- `Hash::check($current, $user->password)` → `Hash::make($new)`

### Settings — Agreement
- Read-only view of `customer_agreements` for customer
- Fields: agreement type, start date, expiry date, SLA/TAT commitments
- `CheckAgreement` middleware redirects here if expired

---

## File Upload Service

```php
// app/Services/Client/FileUploadService.php
class FileUploadService
{
    public function store(UploadedFile $file, int $customerId, int $userId, int $fileTypeId): string
    {
        $folder = "files/{$fileTypeId}/{$customerId}/{$userId}/" . now()->format('Y-m-d');
        $path = $file->store($folder, 'local');
        return $path;
    }

    public function download(string $path): StreamedResponse
    {
        return Storage::download($path);
    }
}
```

File types: `1 = Consent`, `2 = CV`, `3 = Extra`

---

## Request Submission Service

```php
// app/Services/Client/RequestSubmissionService.php
class RequestSubmissionService
{
    public function submit(CustomerUser $user, array $cartContents): BgRequest
    {
        return DB::transaction(function () use ($user, $cartContents) {
            $request = BgRequest::create([...]);
            $candidates = TemporaryCandidate::where('user_id', $user->id)->get();

            foreach ($candidates as $candidate) {
                $cr = CandidateRequest::create([..., 'bg_request_id' => $request->id]);
                TemporaryFile::where('temp_candidate_id', $candidate->id)
                    ->each(fn($f) => CandidateFile::create([..., 'candidate_request_id' => $cr->id]));
                $candidate->delete();
            }

            // Usage recorded for monthly billing purposes
            $totalCost = collect($cartContents)->sum('price') * $candidates->count();
            Usage::create(['bg_request_id' => $request->id, 'amount' => $totalCost]);
            AppLogger::create(['bg_request_id' => $request->id, 'created_by' => $user->id]);

            return $request;
        });
    }
}
```

---

## Mail Classes

```
app/Mail/
├── ClientTwoFactorMail.php            # 6-digit 2FA code
├── ClientPasswordResetMail.php        # Password reset link
├── RegistrationReceivedMail.php       # On registration submit
├── RegistrationApprovedMail.php       # Credentials on approval
├── RequestCompletedMail.php           # When request status → Complete
└── InvoiceIssuedMail.php              # When admin issues a monthly invoice
```

All templates in `resources/views/emails/client/`.

---

## View Layout (Blade)

```
resources/views/client/
├── layouts/
│   ├── app.blade.php           # Authenticated layout (navbar, sidebar, footer)
│   ├── auth.blade.php          # Login/register layout
│   ├── _navbar.blade.php
│   ├── _sidebar.blade.php
│   └── _footer.blade.php
├── auth/
│   ├── login.blade.php
│   ├── verification.blade.php
│   ├── forgot.blade.php
│   ├── reset.blade.php
│   └── register.blade.php
├── dashboard/
│   └── index.blade.php
├── request/
│   ├── create/
│   │   └── index.blade.php     # Multi-step create form
│   ├── index.blade.php         # Active requests list
│   ├── details.blade.php
│   ├── track.blade.php
│   └── old/
│       ├── index.blade.php
│       └── details.blade.php
├── billing/
│   ├── transactions.blade.php
│   └── invoices/
├── scope/
│   └── maps.blade.php
└── settings/
    ├── account.blade.php
    ├── users/
    ├── packages/
    ├── security.blade.php
    └── agreement.blade.php
```

---

## Database Migrations (Key Tables)

These are shared with the management portal. Customer portal-specific additions:

```
database/migrations/
├── create_temporary_candidates_table.php
│   # id, customer_user_id, customer_id, name, identity_type_id, identity_number, mobile, remarks
├── create_temporary_files_table.php
│   # id, temporary_candidate_id, file_type_id, file_name, original_file_name, path
├── create_candidate_files_table.php
│   # id, candidate_request_id, file_type_id, file_name, original_file_name, path
├── create_usages_table.php
│   # id, bg_request_id, customer_id, amount, created_at
├── create_preferred_table.php
│   # id, customer_id, name, country_id, created_by
├── create_preferred_scopes_table.php
│   # id, preferred_id, search_scope_id
└── create_registrations_table.php
    # id, company_name, email, address, status_id, created_at
```

---

## Environment Variables

```env
# Client session
CLIENT_SESSION_KEY=client_user_id

# Legacy DB (for old/archive requests)
LEGACY_DB_HOST=103.215.139.52
LEGACY_DB_DATABASE=voss_old_current
LEGACY_DB_USERNAME=
LEGACY_DB_PASSWORD=

# File Storage
FILESYSTEM_DISK=local
# Files stored at storage/app/files/

# Mail
MAIL_FROM_ADDRESS=no-reply@nrh-intelligence.com
MAIL_FROM_NAME="NRH INTELLIGENCE"

# Hashids (URL-safe IDs)
HASHIDS_SALT=
HASHIDS_MIN_LENGTH=10
```

---

## Shared Laravel Setup Notes

- **Route files:** `routes/admin.php` + `routes/client.php`, both registered in `bootstrap/app.php` via `withRouting()`
- **Config:** `config/hashids.php` for ID encoding
- **Hashids:** Use `vinkla/hashids` package; wrap in `HashidsService` for encode/decode in controllers
- **CSRF:** Automatic via Laravel's `VerifyCsrfToken` middleware; AJAX requests include `X-CSRF-TOKEN` header
- **DataTables responses:** Return `['data' => [...], 'recordsTotal' => n, 'recordsFiltered' => n]` JSON
- **Soft Deletes:** Add `SoftDeletes` trait + `deleted_at` column to Customer, CustomerUser, BgRequest, Package
- **Audit fields:** Use model `creating`/`updating` observers or `CreatedBy`/`UpdatedBy` columns filled from session
