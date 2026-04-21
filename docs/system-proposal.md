# NRH Intelligence — System Proposal
**Intelligence Portal Platform**
Prepared by: NRH Intelligence Sdn. Bhd.
Version: 1.0 | Date: April 2026

---

## 1. Executive Summary

NRH Intelligence is a secure, web-based intelligence portal that enables corporate clients to self-serve their background screening and due diligence requirements. The platform provides end-to-end management of screening requests, candidate tracking, billing, and reporting — replacing manual email-based workflows with a structured digital experience.

The system is split into two distinct portals:

| Portal | Audience | Purpose |
|---|---|---|
| **Client Portal** | Corporate customers and their teams | Submit requests, track progress, manage billing |
| **Admin Portal** | NRH Intelligence staff | Manage customers, process requests, issue invoices |

---

## 2. System Overview

### 2.1 Technology Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 (PHP 8.5) |
| Database | PostgreSQL (Supabase cloud-hosted) |
| Frontend | Blade, Alpine.js, Tailwind CSS v4 |
| Authentication | Session-based with email 2FA OTP |
| Hosting | Laravel Herd (development), Laravel Cloud (production) |
| Storage | Local / S3-compatible (document uploads) |

### 2.2 Architecture

```
┌─────────────────────────────────────────┐
│              NRH Intelligence           │
│                                         │
│  ┌──────────────┐  ┌──────────────────┐ │
│  │ Client Portal │  │   Admin Portal   │ │
│  │  (customers)  │  │  (NRH staff)     │ │
│  └──────┬────────┘  └────────┬─────────┘ │
│         │                    │           │
│  ┌──────▼────────────────────▼─────────┐ │
│  │          Laravel Application        │ │
│  │  Controllers │ Models │ Middleware  │ │
│  └──────────────────────┬──────────────┘ │
│                         │                │
│  ┌──────────────────────▼──────────────┐ │
│  │     PostgreSQL (Supabase)           │ │
│  │  15 tables · Cloud-hosted · Secure  │ │
│  └─────────────────────────────────────┘ │
└─────────────────────────────────────────┘
```

---

## 3. Client Portal

### 3.1 Authentication & Security

| Feature | Detail |
|---|---|
| Login | Email + password authentication |
| 2FA | 6-digit OTP sent to registered email (10-minute expiry) |
| Session | Database-backed, 2-hour lifetime |
| Roles | Admin (full access) · User (standard access) |
| Password reset | Token-based email reset flow |

### 3.2 Dashboard

The landing page after login, providing a real-time snapshot of the customer's activity.

**Displays:**
- Active screenings count (new + in progress)
- Completed screenings count
- Flagged cases requiring attention
- Service agreement expiry status
- 8 most recent screening requests with candidate counts

### 3.3 Screening Requests

#### 3.3.1 Employment Screening

A 4-step guided form for submitting employment background checks:

**Step 1 — Select Scopes**
- Country selector (Malaysia or global)
- Browse scopes grouped by **13 regulatory categories**
- Toggle individual checks into cart
- Apply pre-built packages
- Real-time cart with per-candidate cost summary

**Step 2 — Add Candidates**
- Manual entry: Name, Identity Type, Identity Number, Mobile, Remarks
- Bulk upload: CSV / Excel file (with downloadable template)
- Duplicate identity number detection

**Step 3 — Upload Documents**
- Per-candidate document upload (Consent Form, CV, supporting docs)
- Accepted: PDF, DOC, DOCX, JPG, PNG (max 5MB each)
- Upload progress tracker per candidate

**Step 4 — Review & Submit**
- Full cost breakdown (scopes × candidates)
- Confirmation before submission
- Auto-generated reference number (REQ-YYYY-NNNN format)

#### 3.3.2 Due Diligence

Three service types, each with a dedicated multi-step form:

| Type | Code | Subject | Use Case |
|---|---|---|---|
| Know Your Customer | KYC | Individual | New client/investor onboarding |
| Know Your Business | KYB | Company/Entity | M&A, vendor onboarding |
| Know Your Supplier | KYS | Vendor/Supplier | Supply chain risk assessment |

### 3.4 Malaysia Screening Scopes (42 services across 13 categories)

All Malaysia scopes are priced individually per customer agreement.

| # | Category | Services |
|---|---|---|
| 1 | Security & Integrity Check | MyKAD Verification, Crime Risk, INTERPOL, MACC, Counter-Terrorism |
| 2 | Anti-Money Laundering & CTF (AML/CTF) | MACC Listing, BNM, Security Commission, KDN |
| 3 | Securities Commission (Capital Market) | Financial Fraud, Breach of Trust, Insider Trading, Trading Violations |
| 4 | Bursa Malaysia (Corporate Enforcement) | Market Participants, Company Advisors, Directors & Individuals |
| 5 | Global Sanctions & PEP | OFAC/SDN List, UN Sanction, World Bank Sanction, PEP |
| 6 | Financial Standing & Credit Records | Credit Default, Bankruptcy, CCRIS, Academic Loan (PTPTN) |
| 7 | Legal & Civil Proceedings | Labour Court, Civil Litigation |
| 8 | Driving & Licensing Records | JPJ Offences, Licence Verification |
| 9 | International Travel Restriction | Immigration & Travel Eligibility |
| 10 | Corporate Governance & Ownership | Directorship & Shareholding, SSM Registry |
| 11 | Digital Presence & Online Risk | Social Media/Deep Web, Dark Web Intelligence |
| 12 | Academic & Qualification Verification | Malaysian/Foreign Institutions, Professional Bodies |
| 13 | Employment & Reference Verification | 1-2 Employers, 1-2 Reference Reviews |

### 3.5 Active Screenings

**List View:**
- All active requests (status: New, In Progress, Flagged)
- Candidate count per request
- Status badges with visual indicators
- Link to request detail

**Detail View:**
- Request reference, type, submission date, submitted by
- Candidate table with individual status per candidate
- Scope types assigned to each candidate
- Progress tracking

### 3.6 Track Request

A global search interface accessible from:
- The sidebar navigation
- The top navigation bar (⌘K keyboard shortcut)

**Searchable by:**
- Request reference number (e.g. REQ-2026-0101)
- Candidate full name
- Identity number (IC / Passport)

**Results show:**
- 3-step progress tracker (Received → Processing → Complete)
- Scope types assigned
- Link to full request detail

### 3.7 Candidates

**List View:**
- All candidates across all requests
- Status breakdown: Total, Active, Collecting, Under Review, Complete
- Paginated (12 per page)

**Detail View:**
- Full candidate identity details
- Verification status per scope type
- Parent screening request reference
- Submitted by

### 3.8 Reports (Completed Screenings)

- List of all completed screening requests
- Filter by date, reference
- Detail view with full candidate and scope data

### 3.9 Billing

#### Invoices
- Monthly invoice list with status (Paid / Unpaid / Overdue)
- Invoice detail with line items (scope × qty × price)
- Subtotal, tax (6% SST), and total
- PDF download

#### Transactions
- Full transaction history (Top-ups, Payments, Adjustments)
- Transaction receipt view
- Payment methods: Bank Transfer, Auto-debit, Credit Card

### 3.10 Notifications

Unified notification centre with real-time alerts for:
- Agreement expiry (60-day advance warning, 14-day critical warning)
- Unpaid and overdue invoices
- Completed screening requests
- Account top-ups and payment records
- Active screening progress updates

### 3.11 Settings

| Section | Features |
|---|---|
| **Account** | Company name, registration number, address, industry, primary contact |
| **My Profile** | Name, email address |
| **Security** | Password change with current password verification |
| **Users** | View all team members, roles and status |
| **Packages** | View pre-built screening bundles per country |
| **Agreement** | View service agreement, SLA terms, billing terms, expiry date |

---

## 4. Admin Portal

> **Status: Planned — Phase 2 Development**

The Admin Portal is the internal operations interface for NRH Intelligence staff. It provides full visibility and control over all client activity, enabling staff to manage customers, process screenings, set pricing, and generate billing.

### 4.1 Admin Authentication

| Feature | Detail |
|---|---|
| Login | Separate admin credentials (NRH staff only) |
| Roles | Super Admin · Operations · Finance · Viewer |
| 2FA | Email OTP (same as client portal) |
| Audit Logging | All actions logged with timestamp and user |

### 4.2 Dashboard (Admin)

Real-time operational overview:
- Total active screening requests across all clients
- Requests pending action (new, unassigned)
- Flagged/escalated cases
- Revenue summary (current month vs previous)
- Outstanding invoices and overdue accounts
- Recent customer activity feed

### 4.3 Customer Management

**Customer List:**
- All registered customers with status indicators
- Search by company name, registration number, email
- Filter by industry, country, status (active/inactive)
- Quick links to customer profile

**Customer Profile:**
- Company details (name, registration, address, industry)
- Primary contact information
- Agreement status and expiry
- Account balance
- Screening history summary
- Invoice and payment history
- Assigned NRH account manager

**Customer Actions:**
- Create new customer account
- Edit company details
- Activate / Deactivate account
- Set per-customer scope pricing
- Assign service agreement
- Issue manual credit/adjustment

### 4.4 Per-Customer Scope Pricing

A dedicated pricing management interface:

- View all scope types (grouped by country and category)
- Set custom price per scope for each customer
- Bulk pricing update via CSV upload
- Pricing history / audit trail
- Copy pricing from one customer to another
- Compare customer pricing across similar clients

### 4.5 Screening Request Management

**Request Queue:**
- All incoming requests across all customers
- Filter by status: New / In Progress / Flagged / Complete
- Filter by customer, type, date range
- Assign requests to operations team members

**Request Processing:**
- View full request details (customer, candidates, scopes)
- Update overall request status
- Update individual candidate status (per scope)
- Add internal notes and remarks
- Flag requests for escalation
- Attach completed reports/documents
- Notify client upon completion (automated or manual)

**Candidate Processing:**
- View each candidate's verification checklist (scope by scope)
- Mark individual scope checks as:
  - New → In Progress → Complete / Flagged
- Upload verification documents / reports
- Add investigator notes per scope check

### 4.6 Service Agreement Management

- Create new service agreements for customers
- Set agreement terms:
  - Agreement type and period (start/expiry dates)
  - SLA turnaround time
  - Billing cycle (monthly/quarterly)
  - Payment terms
  - Custom terms and conditions
- Agreement renewal workflow
- Expiry alerts for account managers

### 4.7 Invoice Management

**Invoice Generation:**
- Auto-generate monthly invoices from completed requests
- Manual invoice creation
- Line items auto-populated from:
  - Completed scope checks × candidates × agreed price
- Apply SST (6%) automatically
- Invoice numbering (INV-YYYY-NNN)

**Invoice Actions:**
- Preview before sending
- Send invoice to client (email with PDF attachment)
- Mark as paid (manual or auto-matched to transaction)
- Issue credit notes
- Void and re-issue

**Invoice Reporting:**
- Revenue by customer / period
- Outstanding receivables
- Overdue invoice ageing report
- Monthly revenue summary

### 4.8 Transaction & Account Management

- Record incoming payments (bank transfer, online)
- Apply payments to invoices
- Manual top-up credits to customer accounts
- Issue adjustments (credits / debits)
- Transaction export (CSV)
- Customer account balance management

### 4.9 Operations Reporting

| Report | Description |
|---|---|
| **Request Volume** | Requests by customer, type, period |
| **Scope Utilisation** | Most-used scope types across customers |
| **Turnaround Performance** | Average TAT vs SLA targets |
| **Revenue Summary** | Monthly/quarterly revenue per customer |
| **Candidate Pipeline** | Candidates by status across all active requests |
| **Flagged Cases** | All escalated or flagged screenings |
| **Agreement Expiry** | Upcoming renewals within 90 days |

### 4.10 User & Access Management (Admin)

- Create and manage NRH staff accounts
- Assign roles:
  - **Super Admin** — Full access including user management
  - **Operations** — Request processing and candidate management
  - **Finance** — Invoice, billing, and transaction access
  - **Viewer** — Read-only access to all data
- Activity log — all actions with timestamp

### 4.11 System Configuration

- Manage countries (add/edit/disable)
- Manage scope types (add/edit/disable per country)
- Manage identity types
- System-wide notification settings
- Email template management (invoice, request notifications)
- Maintenance mode toggle

---

## 5. Database Schema Summary

| Table | Purpose | Key Fields |
|---|---|---|
| `customers` | Corporate customer accounts | name, registration_no, balance |
| `customer_users` | Portal login accounts | email, role, status |
| `countries` | Geographic coverage | name, code, flag, region |
| `identity_types` | ID document types | name |
| `scope_types` | Screening services | category, name, turnaround, price_on_request |
| `packages` | Bundled screening sets | customer_id, country_id, name |
| `package_scope_type` | Package ↔ scope mapping | package_id, scope_type_id |
| `customer_scope_prices` | Per-customer pricing | customer_id, scope_type_id, price |
| `screening_requests` | Request headers | reference, status, type, meta |
| `request_candidates` | Candidates under screening | name, identity_number, status |
| `candidate_scope_type` | Candidate ↔ scope + status | request_candidate_id, scope_type_id, status |
| `agreements` | Service contracts | start_date, expiry_date, sla_tat, terms |
| `invoices` | Billing documents | number, status, subtotal, tax, total |
| `invoice_items` | Invoice line items | description, qty, unit_price, total |
| `transactions` | Payments & adjustments | type, amount, method, status |

---

## 6. Security & Compliance

| Aspect | Implementation |
|---|---|
| Authentication | Email + password + 2FA OTP |
| Session management | Database-backed, encrypted, 2-hour timeout |
| Data isolation | All queries scoped to authenticated customer_id |
| Password storage | Bcrypt hashing (12 rounds) |
| HTTPS | Enforced in production |
| PDPA Compliance | Data handled per Personal Data Protection Act 2010 |
| Audit trail | Admin portal — all actions logged |

---

## 7. Implementation Roadmap

### Phase 1 — Client Portal (Current)
| Module | Status |
|---|---|
| Authentication (login, 2FA, password reset) | ✅ Complete |
| Dashboard | ✅ Complete |
| Employment Screening (Malaysia + Global) | ✅ Complete |
| Due Diligence (KYC / KYB / KYS) | ✅ Complete |
| Active Screenings (list + detail) | ✅ Complete |
| Track Request (by reference, name, IC) | ✅ Complete |
| Candidates (list + detail) | ✅ Complete |
| Reports / History | ✅ Complete |
| Invoices | ✅ Complete |
| Transactions | ✅ Complete |
| Notifications | ✅ Complete |
| Settings (account, profile, security, users, packages, agreement) | ✅ Complete |
| Malaysia Scope Library (42 scopes, 13 categories) | ✅ Complete |
| Per-customer pricing | ✅ Complete |
| Global Scope Library | 🔄 In Progress |
| Email notifications (request submitted, completed) | ⏳ Planned |
| Invoice PDF download | ⏳ Planned |

### Phase 2 — Admin Portal
| Module | Status |
|---|---|
| Admin authentication & roles | ⏳ Planned |
| Customer management | ⏳ Planned |
| Per-customer scope pricing management UI | ⏳ Planned |
| Request queue & processing | ⏳ Planned |
| Candidate verification workflow | ⏳ Planned |
| Agreement management | ⏳ Planned |
| Invoice generation & management | ⏳ Planned |
| Transaction recording | ⏳ Planned |
| Operations reporting | ⏳ Planned |
| Staff user management | ⏳ Planned |
| System configuration | ⏳ Planned |

### Phase 3 — Enhancements
| Feature | Status |
|---|---|
| Client-facing PDF reports | ⏳ Planned |
| Email automation (alerts, reminders) | ⏳ Planned |
| API access for enterprise clients | ⏳ Planned |
| Mobile-responsive audit | ⏳ Planned |
| Test suite (Pest) | ⏳ Planned |

---

## 8. Screening Service Coverage

### Current Countries
| Country | Region | Scopes Available |
|---|---|---|
| 🇲🇾 Malaysia | Southeast Asia | 42 (13 categories) |
| 🇸🇬 Singapore | Southeast Asia | 4 |
| 🇮🇩 Indonesia | Southeast Asia | 3 |
| 🇹🇭 Thailand | Southeast Asia | 2 |
| 🇵🇭 Philippines | Southeast Asia | Framework only |
| 🇻🇳 Vietnam | Southeast Asia | Framework only |

> Global scope library to be added. Additional countries can be onboarded through the Admin Portal's system configuration module.

---

## 9. Glossary

| Term | Definition |
|---|---|
| **Screening Request** | A formal request submitted by a client to conduct background checks on one or more candidates |
| **Candidate** | An individual subject to background verification |
| **Scope Type** | A specific type of verification check (e.g., Criminal Record Check, CCRIS) |
| **Package** | A pre-configured bundle of scope types for a specific country |
| **Customer** | A corporate client organisation using the NRH Intelligence portal |
| **Customer User** | An individual team member within a customer organisation |
| **Reference** | Unique request identifier in REQ-YYYY-NNNN format |
| **KYC** | Know Your Customer — individual due diligence |
| **KYB** | Know Your Business — corporate entity due diligence |
| **KYS** | Know Your Supplier — vendor/supplier due diligence |
| **AML/CTF** | Anti-Money Laundering / Counter-Terrorism Financing |
| **PEP** | Politically Exposed Person |
| **SLA** | Service Level Agreement — agreed turnaround time |
| **TAT** | Turnaround Time — time from submission to report delivery |
| **MACC** | Malaysian Anti-Corruption Commission |
| **CCRIS** | Central Credit Reference Information System (Bank Negara Malaysia) |
| **SSM** | Suruhanjaya Syarikat Malaysia — Companies Commission of Malaysia |
| **JPJ** | Jabatan Pengangkutan Jalan — Road Transport Department |
| **PTPTN** | Perbadanan Tabung Pendidikan Tinggi Nasional — National Higher Education Fund |

---

*NRH Intelligence Sdn. Bhd. — Confidential System Proposal*
*"Trust in Every Hire. Secure in Every Transaction."*
