<?php

namespace App\Support;

/**
 * Content source for the Customer Portal Guide (/guide).
 *
 * Kept as structured data rather than markup so the on-screen version and the
 * PDF render from one source and can never drift apart. Both views walk this
 * array through resources/views/docs/_blocks.blade.php.
 *
 * Mirrors the admin portal's App\Support\AdminGuide — same block vocabulary,
 * same renderer contract. Keep the two in step when either changes.
 *
 * Block types understood by the renderer:
 *   ['p',     'text' => string]                      paragraph (inline HTML allowed)
 *   ['sub',   'text' => string]                      sub-heading
 *   ['steps', 'items' => string[]]                   numbered procedure
 *   ['list',  'items' => string[]]                   bullet list
 *   ['table', 'head' => string[], 'rows' => array[], 'widths' => ?string[]]
 *   ['note',  'title' => string, 'text' => string]   informational callout
 *   ['warn',  'title' => string, 'text' => string]   caution callout
 *   ['links', 'items' => [['label' => string, 'route' => string]]]
 *
 * Text is authored here by developers, never by users, so the renderer prints
 * it unescaped to allow <b>, <code> and <i>.
 */
class PortalGuide
{
    /**
     * @return array<int, array{
     *     id: string, title: string, permission: ?string,
     *     summary: string, blocks: array<int, array<string, mixed>>
     * }>
     */
    public static function sections(): array
    {
        return [
            self::about(),
            self::gettingStarted(),
            self::dashboard(),
            self::newRequest(),
            self::tracking(),
            self::reports(),
            self::billing(),
            self::notifications(),
            self::settings(),
            self::roles(),
            self::troubleshooting(),
            self::reference(),
        ];
    }

    private static function about(): array
    {
        return [
            'id' => 'about',
            'title' => 'About This Guide',
            'permission' => null,
            'summary' => 'What this document covers and how to move around it.',
            'blocks' => [
                ['type' => 'p', 'text' => 'This is the user guide for the <b>NRH Intelligence customer portal</b> — where your team orders background checks, follows them while they run, downloads finished reports, and settles what you owe. It covers every item in the sidebar, the permission each one needs, and what each action actually does.'],
                ['type' => 'p', 'text' => 'Screening work is carried out by NRH staff in a separate internal system. Anything you submit here reaches them immediately, and reports they issue appear here the moment they are released — so the two sides stay in step without anyone emailing anyone.'],
                ['type' => 'sub', 'text' => 'How to use this document'],
                ['type' => 'list', 'items' => [
                    '<b>The contents list is clickable.</b> Select any entry to jump straight to that section — this works in the PDF as well as on screen.',
                    '<b>Green links open the live portal.</b> Most sections end with links that take you to the matching page, so you can follow along while you read.',
                    '<b>Each section header shows the permission it needs.</b> If you do not hold it, that page is hidden from your sidebar — see <b>Roles &amp; Permissions</b>.',
                ]],
                ['type' => 'note', 'title' => 'Reading this as a PDF', 'text' => 'Use <b>Download PDF</b> at the top of the page for a copy to keep or circulate. Internal jumps and portal links stay live in the PDF. If you print it, the reference tables in the last section are the part worth keeping at your desk.'],
                ['type' => 'warn', 'title' => 'Where this guide and the portal disagree', 'text' => 'The portal is correct. This guide is generated from the running application, but configuration such as prices, packages and roles is set per customer — check <b>What’s New</b> if something looks different from how it is described here.'],
            ],
        ];
    }

    private static function gettingStarted(): array
    {
        return [
            'id' => 'getting-started',
            'title' => 'Getting Started',
            'permission' => null,
            'summary' => 'Activating your account, signing in, and finding your way around.',
            'blocks' => [
                ['type' => 'sub', 'text' => 'Who can have an account'],
                ['type' => 'p', 'text' => 'The portal is invitation-only — there is no public sign-up. Accounts are created either by NRH when your company is onboarded, or by an Owner on your own account inviting a colleague. Every account belongs to exactly one company, and you only ever see that company’s requests, candidates, reports and invoices.'],

                ['type' => 'sub', 'text' => 'Accepting your invitation'],
                ['type' => 'steps', 'items' => [
                    'Open the email titled <b>“You’re invited to … on NRH Intelligence”</b> and click the activation link.',
                    'Choose a password of at least eight characters and confirm it.',
                    'You are signed in and taken to the dashboard. Your account switches from <code>inactive</code> to <code>active</code>.',
                ]],
                ['type' => 'warn', 'title' => 'Invitations expire', 'text' => 'An activation link is valid for <b>14 days</b>, and issuing a new one invalidates the last. After that the link shows an “invalid invitation” page — any Owner can resend it from <b>Settings → Users</b>, which resets the 14-day window. Resends are limited to one per minute.'],

                ['type' => 'sub', 'text' => 'Signing in and two-factor verification'],
                ['type' => 'p', 'text' => 'Sign in with your email address and password. Because screening data is personal data, the portal then emails you a <b>six-digit verification code</b> and asks you to enter it. This cannot be switched off.'],
                ['type' => 'table', 'head' => ['', 'Behaviour'], 'widths' => ['26%', '74%'], 'rows' => [
                    ['Code format', 'Six digits, numeric only.'],
                    ['Valid for', '10 minutes from the moment it is sent. An older code is rejected even if it arrives late.'],
                    ['Didn’t arrive?', 'Use <b>Resend code</b> on the verification screen — limited to three per minute.'],
                    ['Attempts', 'Sign-in and code submissions are limited to five per minute. Repeated wrong guesses pause you briefly.'],
                    ['Remember me', 'Keeps you signed in on that device between visits. Leave it off on shared machines.'],
                ]],
                ['type' => 'note', 'title' => 'Deactivated accounts cannot sign in', 'text' => 'Only accounts marked <code>active</code> can sign in. If your password is right but sign-in still fails, ask an Owner to check your status in <b>Settings → Users</b>.'],

                ['type' => 'sub', 'text' => 'Forgotten password'],
                ['type' => 'p', 'text' => 'Use <b>Forgot password</b> on the sign-in screen. For privacy the portal always answers the same way — “if that email exists, a reset link has been sent” — whether or not the address is registered. If you are already signed in and simply want to change your password, use <b>Settings → Security</b> instead; that route asks for your current password first.'],

                ['type' => 'sub', 'text' => 'The workspace at a glance'],
                ['type' => 'p', 'text' => 'The left sidebar is the whole map of the portal, grouped into four bands. A band only appears if you hold at least one permission inside it.'],
                ['type' => 'table', 'head' => ['Band', 'Contains'], 'widths' => ['22%', '78%'], 'rows' => [
                    ['<b>Workspace</b>', 'Dashboard, Candidates, Requests (Malaysia / Global), Active Requests, Track Request, Reports'],
                    ['<b>Billing</b>', 'Transactions, Invoices'],
                    ['<b>System</b>', 'What’s New, User Guide (this document)'],
                    ['<b>Settings</b>', 'Account, Users, Packages, Audit Log, Security, Agreement'],
                ]],
                ['type' => 'p', 'text' => 'At the foot of the sidebar sit a <b>light / dark mode</b> toggle and your own name — click it for <b>My Profile</b>, <b>Notifications</b> and <b>Sign Out</b>. On narrow screens the sidebar collapses behind a menu button in the top bar.'],
            ],
        ];
    }

    private static function dashboard(): array
    {
        return [
            'id' => 'dashboard',
            'title' => 'Your Dashboard',
            'permission' => 'view-dashboard',
            'summary' => 'The landing page after sign-in: where your account stands right now.',
            'blocks' => [
                ['type' => 'p', 'text' => 'The tiles across the top count <i>all</i> of your company’s requests, not just recent ones. They are worth reading precisely, because several of them overlap.'],
                ['type' => 'table', 'head' => ['Tile', 'Counts'], 'widths' => ['24%', '76%'], 'rows' => [
                    ['<b>In progress</b>', 'Requests that are either <code>new</code> or <code>in_progress</code> — everything submitted but not yet finished.'],
                    ['<b>Cleared</b>', 'Requests whose status is <code>complete</code>.'],
                    ['<b>Needs review</b>', 'Requests containing at least one <b>flagged</b> candidate — a check surfaced something needing your attention. Open the request to see which.'],
                    ['<b>Total</b>', 'Every request your company has ever submitted.'],
                    ['<b>Awaiting payment</b>', 'Per-request (cash) accounts only: new requests with no payment slip uploaded yet. Monthly-billed accounts never see this tile.'],
                ]],
                ['type' => 'sub', 'text' => 'Recent requests and agreement expiry'],
                ['type' => 'p', 'text' => 'Below the tiles sit your eight most recently created requests with candidate counts and status — click any row to open it. The dashboard also shows when your service agreement expires and how many days remain. Sixty days out an expiry warning starts appearing in <b>Notifications</b>; inside fourteen days it escalates. Renewal is handled by your account manager, not in the portal.'],
                ['type' => 'links', 'items' => [
                    ['label' => 'Open the Dashboard', 'route' => 'client.dashboard'],
                ]],
            ],
        ];
    }

    private static function newRequest(): array
    {
        return [
            'id' => 'new-request',
            'title' => 'Submitting a Screening Request',
            'permission' => 'create-requests',
            'summary' => 'The four-step wizard, and what each step will refuse to let you skip.',
            'blocks' => [
                ['type' => 'sub', 'text' => 'Malaysia or Global'],
                ['type' => 'p', 'text' => 'Both entry points run the same wizard; the only difference is the country. <b>Malaysia</b> locks the country and shows the full Malaysian scope library straight away. <b>Global</b> asks you to choose a country first, then shows the scopes available there. Pick whichever matches where the candidate’s history needs verifying — not where your office is. Checks in two countries for the same person are two separate requests.'],

                ['type' => 'sub', 'text' => 'Step 1 — Select scopes'],
                ['type' => 'p', 'text' => 'A <b>scope</b> is one individual check — an employment verification, an education verification, a criminal record check — each with its own price and turnaround. Browse the library and add scopes to the basket; the running total updates as you go. Whatever you select applies to <i>every</i> candidate in the request, so if two people need different checks they belong in different requests.'],
                ['type' => 'p', 'text' => 'If NRH has set up <b>packages</b> for your company they appear as one-click bundles at the top of the step. Prices shown are your company’s prices — an agreed per-customer rate overrides the standard rate automatically.'],
                ['type' => 'note', 'title' => 'Price on request', 'text' => 'Scopes marked <b>price on request</b> with no agreed rate for your company are hidden from the library, and cannot be ordered even by pasting a link — the portal refuses the submission and names the scope. Contact your account manager to have a price set first.'],
                ['type' => 'note', 'title' => 'If you cannot see any prices', 'text' => 'Prices are gated by <code>view-prices</code>. The <b>HR</b> role can order checks without seeing what they cost — that is deliberate, not a fault.'],

                ['type' => 'sub', 'text' => 'Step 2 — Add candidates'],
                ['type' => 'p', 'text' => 'Add one row per person. You can add as many as you like to a single request; they all receive the same set of scopes.'],
                ['type' => 'table', 'head' => ['Field', 'Required', 'Notes'], 'widths' => ['24%', '16%', '60%'], 'rows' => [
                    ['<b>Full name</b>', 'Yes', 'As it appears on the identity document.'],
                    ['<b>Identity type</b>', 'Yes', 'NRIC, passport, and so on.'],
                    ['<b>Identity number</b>', 'Yes', 'Stored encrypted. Type it exactly — see the search note under <b>Tracking</b>.'],
                    ['<b>Nationality</b>', 'No', 'Helpful for global checks.'],
                    ['<b>Date of birth</b>', 'No', 'Must be in the past.'],
                    ['<b>Mobile</b>', 'No', 'Used if our team needs to reach the candidate for consent.'],
                    ['<b>Remarks</b>', 'No', 'Anything the verification team should know.'],
                ]],
                ['type' => 'warn', 'title' => 'Accuracy pays for itself', 'text' => 'A mistyped identity number is the most common cause of a request coming back <b>Rejected</b> for insufficient documents — and a rejected request never starts its turnaround clock. Check the number against the document before moving on.'],

                ['type' => 'sub', 'text' => 'Step 3 — Upload documents and consent'],
                ['type' => 'p', 'text' => 'What this step asks for depends entirely on the scopes you chose. Each scope declares which documents it needs, and the wizard asks for the combined set, once per candidate.'],
                ['type' => 'table', 'head' => ['Document', 'Asked for when'], 'widths' => ['30%', '70%'], 'rows' => [
                    ['Signed consent form', 'A scope requires documented PDPA consent from the candidate rather than a tick-box.'],
                    ['NRIC / ID copy', 'Identity-dependent checks.'],
                    ['Resume / CV', 'Employment history verification.'],
                    ['Certificate copy', 'Education and qualification verification.'],
                ]],
                ['type' => 'table', 'head' => ['Upload rules', ''], 'widths' => ['30%', '70%'], 'rows' => [
                    ['Accepted formats', 'PDF, DOC, DOCX, JPG, JPEG, PNG'],
                    ['Maximum size', '10&nbsp;MB per file'],
                    ['Completeness', 'Every required document must be present for every candidate'],
                ]],
                ['type' => 'p', 'text' => 'Consent is never optional, but it is captured one of two ways and never both. <b>If any selected scope requires documents</b>, you upload the signed consent form and consent is recorded against that evidence. <b>If none does</b>, the wizard shows a single PDPA consent declaration you must tick before it will submit.'],
                ['type' => 'note', 'title' => 'Consent is stamped, not just ticked', 'text' => 'Either way the portal records the consent wording in force at the time — currently version <code>'.config('consent.current_version').'</code> — against every candidate, with the date and the submitting user, so the basis for processing is provable later. A submission with a document missing is rejected with a message naming the candidate number and the missing file.'],

                ['type' => 'sub', 'text' => 'Step 4 — Review and submit'],
                ['type' => 'p', 'text' => 'The final step lists the scopes, the candidates and — if you can see prices — the total. Read it once more: <b>a request cannot be edited or cancelled from the portal after submission</b>, though candidates can sometimes be added to one.'],

                ['type' => 'sub', 'text' => 'What happens immediately after you submit'],
                ['type' => 'table', 'head' => ['Your account', 'What happens'], 'widths' => ['26%', '74%'], 'rows' => [
                    ['<b>Monthly (credit)</b>', 'The request moves to <b>In progress</b> straight away and you land on a confirmation page. Nothing is required from you. The cost joins your next monthly invoice.'],
                    ['<b>Per-request (cash)</b>', 'The request is created as <b>New</b> and you land on its detail page with payment instructions. <b>Work does not begin until payment is verified.</b>'],
                ]],

                ['type' => 'sub', 'text' => 'Adding a candidate to an existing request'],
                ['type' => 'p', 'text' => 'On the request detail page, an <b>Add candidate</b> control appears when all of the following are true:'],
                ['type' => 'list', 'items' => [
                    'your account is <b>monthly (credit)</b> billed — per-request accounts cannot, because the request has already been paid for;',
                    'the request has not yet been rolled into an invoice;',
                    'the request is not <b>Complete</b>, <b>Updated</b> or <b>Rejected</b>;',
                    'you hold <code>create-requests</code>.',
                ]],
                ['type' => 'p', 'text' => 'The new candidate can only be given scopes that already exist on the request. If you need a different set of checks, submit a new request instead.'],
                ['type' => 'links', 'items' => [
                    ['label' => 'Malaysia Screening', 'route' => 'client.request.malaysia'],
                    ['label' => 'Global Screening', 'route' => 'client.request.global'],
                ]],
            ],
        ];
    }

    private static function tracking(): array
    {
        return [
            'id' => 'tracking',
            'title' => 'Tracking Work in Progress',
            'permission' => 'view-requests',
            'summary' => 'Four views onto live work, and what the status colours mean.',
            'blocks' => [
                ['type' => 'sub', 'text' => 'Active Requests'],
                ['type' => 'p', 'text' => 'Every request that has not yet finished, newest first, with reference, candidate count and status. Finished work moves to <b>Reports</b> and leaves this list. Per-request (cash) accounts get one extra affordance: a banner counting requests that still need a payment slip, and a tab that additionally includes slips uploaded but not yet verified — so the list answers “what is holding up my screening?” without opening anything.'],

                ['type' => 'sub', 'text' => 'Inside a request'],
                ['type' => 'table', 'head' => ['Block', 'What it tells you'], 'widths' => ['24%', '76%'], 'rows' => [
                    ['<b>Status banner</b>', 'One sentence on where things stand and what, if anything, is needed from you.'],
                    ['<b>Progress tracker</b>', 'Three steps for monthly accounts (Submitted → Checking → Done), or four for per-request accounts, which insert <b>Payment</b> in second place. The last step shows the estimated completion date until the work finishes, then the actual date.'],
                    ['<b>Payment details</b>', 'Per-request accounts with payment outstanding: bank details, your payment reference, and the slip upload. Disappears once payment is confirmed.'],
                    ['<b>Request details</b>', 'Reference, who submitted it, when, and the billing status.'],
                    ['<b>What we’re checking</b>', 'The scopes on this request, each with its own progress against its target turnaround.'],
                    ['<b>Reports</b>', 'Every current report version available to download. Superseded versions are not listed.'],
                    ['<b>Candidates</b>', 'Each person on the request with their own status. Click through for per-scope detail.'],
                ]],
                ['type' => 'note', 'title' => 'Turnaround is counted in business hours', 'text' => 'Each scope has a target measured in <b>business hours</b>, not calendar hours — weekends and non-working hours do not count against it. The clock starts when work actually starts: after payment is verified on a cash account, and after any rejection for missing documents has been resolved. A scope past its target is marked as over.'],

                ['type' => 'sub', 'text' => 'Track Request — searching'],
                ['type' => 'table', 'head' => ['Search by', 'Behaviour'], 'widths' => ['26%', '74%'], 'rows' => [
                    ['<b>Request reference</b>', 'Partial match. A reference such as <code>REP-…</code> also pulls up the request itself, not only its candidates.'],
                    ['<b>Candidate name</b>', 'Partial match, case-insensitive.'],
                    ['<b>Identity number</b>', '<b>Exact, complete number only.</b>'],
                ]],
                ['type' => 'warn', 'title' => 'Why partial ID numbers find nothing', 'text' => 'Identity numbers are encrypted at rest, which is what makes partial matching impossible — the portal can confirm a full number matches but cannot scan inside the stored value. Enter the complete number, or search by name instead.'],

                ['type' => 'sub', 'text' => 'The Candidates directory'],
                ['type' => 'p', 'text' => 'Every person your company has ever submitted, across all requests, twelve to a page and newest first, with counts by stage across the top. Use this when you are chasing a person rather than a request. Opening a candidate shows their details, which request they belong to, and the state of each individual scope.'],

                ['type' => 'sub', 'text' => 'Status legend'],
                ['type' => 'table', 'head' => ['Status', 'Meaning'], 'widths' => ['26%', '74%'], 'rows' => [
                    ['<b>New</b>', 'Submitted and with NRH. On a per-request account this also means payment is outstanding, and the label is replaced by the payment state below.'],
                    ['<b>In Progress</b>', 'Verification is under way and the turnaround clock is running.'],
                    ['<b>Preliminary Report</b>', 'An early report has been issued while some checks continue. The full report follows.'],
                    ['<b>Flagged</b>', 'A check surfaced something needing your attention. Open the request to see which candidate and scope.'],
                    ['<b>Rejected</b>', 'Insufficient documents — the request was not started and no turnaround applies. Fix the problem and submit again.'],
                    ['<b>Complete</b>', 'All checks finished and the report is available.'],
                    ['<b>Updated</b>', 'A revised report has been issued after completion, superseding the earlier one.'],
                ]],
                ['type' => 'table', 'head' => ['Payment state (cash accounts)', 'Meaning'], 'widths' => ['26%', '74%'], 'rows' => [
                    ['<b>Awaiting payment</b>', 'No payment slip uploaded yet. Nothing is running.'],
                    ['<b>Verifying payment</b>', 'Slip received; our finance team is confirming the transfer.'],
                    ['<b>Payment received</b>', 'Confirmed. Processing begins within '.config('billing.sla_after_payment', '1 business day').'.'],
                ]],
                ['type' => 'links', 'items' => [
                    ['label' => 'Active Requests', 'route' => 'client.requests.index'],
                    ['label' => 'Track Request', 'route' => 'client.requests.track'],
                    ['label' => 'Candidates', 'route' => 'client.candidates'],
                ]],
            ],
        ];
    }

    private static function reports(): array
    {
        return [
            'id' => 'reports',
            'title' => 'Reports',
            'permission' => 'view-reports',
            'summary' => 'What you receive, when, and where to find it afterwards.',
            'blocks' => [
                ['type' => 'table', 'head' => ['Type', 'When it is issued'], 'widths' => ['22%', '78%'], 'rows' => [
                    ['<b>Preliminary</b>', 'While verification continues, when enough is confirmed to be useful — typically so hiring can proceed on the fast checks while slower ones run. Marked clearly as preliminary; a full report always follows.'],
                    ['<b>Full</b>', 'On completion of every scope on the request. This is the definitive document.'],
                    ['<b>Updated</b>', 'A re-issued full report, produced when something material changes after completion — a correction, or a check that came back late. It supersedes the earlier version.'],
                ]],
                ['type' => 'p', 'text' => 'Reports are versioned, and the portal only ever lists <b>current</b> versions: once a report is superseded the older file disappears from the list, so nobody circulates a stale document by accident. If you need a superseded version for an audit, ask your account manager.'],

                ['type' => 'sub', 'text' => 'Downloading a report'],
                ['type' => 'p', 'text' => 'Reports appear in the <b>Reports</b> block on the request detail page, and against the request under <b>Reports</b> in the sidebar. Files are PDFs named after the request, the report type and the version — for example <code>REP-2026-0184-full-v2.pdf</code>.'],
                ['type' => 'note', 'title' => 'Downloads are logged', 'text' => 'Every report download is written to the audit log with the user and the time. Screening reports contain personal data, and being able to show who took a copy is part of handling it properly.'],
                ['type' => 'warn', 'title' => '“Report file is no longer available”', 'text' => 'The record exists but the file could not be retrieved. This is a server-side condition, not something retrying will fix — contact support with the request reference.'],

                ['type' => 'sub', 'text' => 'Where finished work lives'],
                ['type' => 'p', 'text' => 'Once a request completes it leaves <b>Active Requests</b> and appears under <b>Reports</b>, permanently. This is your archive — search it when you need a report months later.'],
                ['type' => 'links', 'items' => [
                    ['label' => 'Reports', 'route' => 'client.history.index'],
                ]],
            ],
        ];
    }

    private static function billing(): array
    {
        $bank = config('billing.bank');
        $sla = config('billing.sla_after_payment', '1 business day');
        $sst = rtrim(rtrim(number_format((float) config('billing.sst_rate', 0.06) * 100, 2), '0'), '.');
        $email = config('billing.proof_of_payment_email');

        return [
            'id' => 'billing',
            'title' => 'Billing &amp; Payments',
            'permission' => 'view-billing',
            'summary' => 'Two modes, two very different rhythms. Find yours first.',
            'blocks' => [
                ['type' => 'sub', 'text' => 'Which billing mode am I on?'],
                ['type' => 'p', 'text' => 'Your service agreement sets it, and it applies to the whole company. The quickest test is submit-time behaviour: if a new request lands you on a page asking you to transfer money you are <b>per-request (cash)</b>; if it goes straight to <b>In progress</b> you are <b>monthly (credit)</b>. You can also see the agreement itself under <b>Settings → Agreement</b>.'],
                ['type' => 'table', 'head' => ['', 'Per-request (cash)', 'Monthly (credit)'], 'widths' => ['24%', '38%', '38%'], 'rows' => [
                    ['<b>Pay</b>', 'Before each request starts', 'Against a monthly invoice, after the fact'],
                    ['<b>Proof of payment</b>', 'Bank slip, uploaded per request', 'Receipt, uploaded against the invoice'],
                    ['<b>Work begins</b>', 'Within '.$sla.' of payment being verified', 'Immediately on submission'],
                    ['<b>Add candidates later</b>', 'No', 'Yes, until the request is invoiced'],
                ]],

                ['type' => 'sub', 'text' => 'Per-request (cash) accounts'],
                ['type' => 'p', 'text' => 'After submitting you land on the request’s detail page with a <b>Payment details</b> block showing the amount, our bank account, and the reference to quote.'],
                ['type' => 'table', 'head' => ['Detail', 'Value'], 'widths' => ['26%', '74%'], 'rows' => [
                    ['Bank', e($bank['name'] ?? '')],
                    ['Account holder', e($bank['account_holder'] ?? '')],
                    ['Account number', '<code>'.e($bank['account_number'] ?? '').'</code>'],
                    ['SWIFT', '<code>'.e($bank['swift'] ?? '').'</code>'],
                    ['Currency', e((string) config('billing.currency', 'MYR'))],
                    ['Payment reference', '<b>Your request number.</b> This is what lets finance match your transfer without chasing you.'],
                ]],
                ['type' => 'warn', 'title' => 'Always check the bank details in the portal', 'text' => 'Treat the <b>Payment details</b> block on the live request page as authoritative, and never act on bank details sent to you by email.'],
                ['type' => 'steps', 'items' => [
                    'Transfer the exact amount shown, quoting the request number as the reference.',
                    'On the request page, choose your bank-transfer confirmation under <b>Upload your payment slip</b> — PDF, JPG or PNG, up to 5&nbsp;MB.',
                    'Upload. Finance is notified automatically and the status becomes <b>Verifying payment</b>.',
                    'Once verified the status becomes <b>Payment received</b> and processing begins within '.$sla.'.',
                ]],
                ['type' => 'p', 'text' => 'Uploaded the wrong file? While payment is still unverified you can view, remove or replace the slip from the same block. If uploading fails entirely, email the slip to <code>'.e((string) $email).'</code> with the request number in the subject line.'],
                ['type' => 'note', 'title' => 'When the upload is refused', 'text' => 'Slips are only accepted while a request is still <b>New</b> and un-invoiced. If the request has moved on, payment has already been handled; if it carries an invoice number, pay through the invoice instead. Uploading and removing slips also requires a billing or pricing permission.'],

                ['type' => 'sub', 'text' => 'Monthly (credit) accounts'],
                ['type' => 'p', 'text' => 'Requests run immediately and are gathered into a monthly invoice. The invoice list shows number, issue date, due date, total and status — unpaid, overdue or paid. Opening one shows the line items, the amounts and any receipts already submitted. Malaysian SST at '.$sst.'% is applied to invoice subtotals.'],
                ['type' => 'p', 'text' => 'Use <b>Download</b> on the invoice page for a PDF named after the invoice number. Both opening the detail page and downloading require <code>download-invoices</code> — roles without it see the list but not the documents.'],
                ['type' => 'table', 'head' => ['Receipt field', 'Notes'], 'widths' => ['26%', '74%'], 'rows' => [
                    ['<b>Receipt file</b>', 'Required. PDF, JPG or PNG, up to 5&nbsp;MB.'],
                    ['<b>Amount paid</b>', 'Optional. Useful for part-payments.'],
                    ['<b>Paid on</b>', 'Optional. Cannot be a future date.'],
                    ['<b>Reference</b>', 'Optional. Your bank’s transaction reference.'],
                    ['<b>Notes</b>', 'Optional. Anything finance should know.'],
                ]],
                ['type' => 'p', 'text' => 'The receipt is queued as <b>pending</b> until finance verifies it, and you are notified either way. Invoices already marked paid will not accept a receipt. Uploading requires <code>manage-billing</code>.'],

                ['type' => 'sub', 'text' => 'Transactions and receipts'],
                ['type' => 'p', 'text' => 'A running ledger of payments recorded against your account, with type, amount, method and reference. Each row can be opened as a receipt and downloaded as a PDF — again subject to <code>download-invoices</code>.'],
                ['type' => 'links', 'items' => [
                    ['label' => 'Invoices', 'route' => 'client.billing.invoices'],
                    ['label' => 'Transactions', 'route' => 'client.billing.transactions'],
                    ['label' => 'Agreement', 'route' => 'client.settings.agreement'],
                ]],
            ],
        ];
    }

    private static function notifications(): array
    {
        return [
            'id' => 'notifications',
            'title' => 'Notifications &amp; Product Updates',
            'permission' => null,
            'summary' => 'What the portal tells you without being asked.',
            'blocks' => [
                ['type' => 'p', 'text' => 'Notifications are assembled live from the state of your account rather than stored as messages, so the list always reflects reality right now. Reach them from your name at the foot of the sidebar.'],
                ['type' => 'table', 'head' => ['Notification', 'Trigger'], 'widths' => ['32%', '68%'], 'rows' => [
                    ['Agreement expiring', 'Within 60 days of expiry; escalates inside 14 days, and again once expired.'],
                    ['Invoice unpaid or overdue', 'Any invoice not settled, with the amount and due date.'],
                    ['Request completed', 'Your three most recent completed requests. Links to the report.'],
                    ['Preliminary report ready', 'A preliminary report has been issued. Full report to follow.'],
                    ['Report updated', 'A revised report has superseded an earlier one.'],
                    ['Request in progress', 'Your three most recent active requests and their current status.'],
                    ['Payment slip verified / rejected', 'Finance actioned a receipt in the last 30 days. A rejection tells you to upload a new slip.'],
                    ['Transaction recorded', 'Your three most recent payments, linking to the receipt.'],
                ]],
                ['type' => 'sub', 'text' => 'What’s New'],
                ['type' => 'p', 'text' => 'Release notes for the portal itself, grouped by date, newest first. Worth a glance when something looks different from how this guide describes it.'],
                ['type' => 'links', 'items' => [
                    ['label' => 'Notifications', 'route' => 'client.notifications'],
                    ['label' => 'What’s New', 'route' => 'client.updates'],
                ]],
            ],
        ];
    }

    private static function settings(): array
    {
        return [
            'id' => 'settings',
            'title' => 'Settings &amp; Administration',
            'permission' => 'varies — see each item',
            'summary' => 'Your own account, your company’s record, and the controls Owners hold.',
            'blocks' => [
                ['type' => 'sub', 'text' => 'My profile'],
                ['type' => 'p', 'text' => 'Your own name, contact details and profile picture. Always available whatever your role. Removing your picture falls back to your initials.'],

                ['type' => 'sub', 'text' => 'Account details — <code>manage-settings</code>'],
                ['type' => 'p', 'text' => 'Your company’s record: name, registration number, address, country, industry, and the primary contact’s name, email and phone. Company name and contact name and email are required. These details appear on your invoices, so keep the registration number and address current.'],

                ['type' => 'sub', 'text' => 'Security — <code>manage-settings</code>'],
                ['type' => 'p', 'text' => 'Change your password. You must enter your current password, and the new one must be at least eight characters and typed twice. Two-factor verification by email is always on and cannot be disabled.'],

                ['type' => 'sub', 'text' => 'Users and invitations — <code>manage-team</code>'],
                ['type' => 'table', 'head' => ['Action', 'What it does'], 'widths' => ['26%', '74%'], 'rows' => [
                    ['<b>Invite a user</b>', 'Enter name, email and role. An activation email goes out and the account sits <code>inactive</code> until accepted. Email addresses must be unique across the whole platform.'],
                    ['<b>Resend invitation</b>', 'Sends a fresh link and resets the 14-day expiry. One resend per minute; unavailable once the user is active.'],
                    ['<b>Edit a user</b>', 'Change name, email, role, and active / inactive status.'],
                    ['<b>Grant extra permissions</b>', 'Beyond the role, grant individual permissions to one person — useful for an HR user who also needs to see prices, without promoting them to Admin.'],
                    ['<b>Deactivate</b>', 'Set status to inactive. The account keeps its history but cannot sign in. Prefer this to deletion when someone leaves.'],
                    ['<b>Remove</b>', 'Deletes the user. You cannot remove your own account.'],
                ]],
                ['type' => 'warn', 'title' => 'Keep more than one Owner', 'text' => 'Only Owners can manage the team and billing. If your single Owner leaves and is removed, nobody left on the account can invite a replacement — you would need NRH to intervene. Two Owners costs nothing and avoids that entirely.'],

                ['type' => 'sub', 'text' => 'Packages — <code>manage-packages</code>'],
                ['type' => 'p', 'text' => 'The scope bundles configured for your company, with the scopes each contains and the country it applies to. Read-only — packages are defined by NRH. To have one added or changed, speak to your account manager.'],

                ['type' => 'sub', 'text' => 'Agreement — <code>manage-settings</code>'],
                ['type' => 'p', 'text' => 'Your service agreement: billing mode, start and expiry dates, and days remaining. Read-only; renewals go through your account manager.'],

                ['type' => 'sub', 'text' => 'Audit log — <code>view-audit-log</code>'],
                ['type' => 'p', 'text' => 'A record of what your team has done in the portal, 25 entries to a page, newest first. It covers sign-ins and failed sign-in attempts, two-factor events, report downloads, user invitations and changes, and changes to requests and candidates. Filter by event type, log category, user and date range; filters survive paging, so a filtered view can be shared as a link.'],
                ['type' => 'note', 'title' => 'Scope of the log', 'text' => 'It shows actions taken by users on your own account. Actions taken by NRH staff live in our internal system and can be produced on request for a formal audit.'],
                ['type' => 'links', 'items' => [
                    ['label' => 'My Profile', 'route' => 'client.settings.profile'],
                    ['label' => 'Account', 'route' => 'client.settings.account'],
                    ['label' => 'Security', 'route' => 'client.settings.security'],
                    ['label' => 'Users', 'route' => 'client.settings.users'],
                    ['label' => 'Packages', 'route' => 'client.settings.packages'],
                    ['label' => 'Audit Log', 'route' => 'client.settings.audit-log'],
                ]],
            ],
        ];
    }

    private static function roles(): array
    {
        return [
            'id' => 'roles',
            'title' => 'Roles &amp; Permissions',
            'permission' => null,
            'summary' => 'What each role can reach, and how to widen one person’s access.',
            'blocks' => [
                ['type' => 'p', 'text' => 'Access is decided by <b>permissions</b>. Your role carries a default set, and an Owner can grant individual permissions on top of that for your account specifically. The portal hides whatever you do not hold — so parts of this guide may describe menu items you never see.'],
                ['type' => 'table', 'head' => ['Role', 'Suits'], 'widths' => ['18%', '82%'], 'rows' => [
                    ['<code>Owner</code>', 'The person accountable for the account. Everything, including team management and billing. Keep at least two.'],
                    ['<code>Admin</code>', 'Day-to-day lead. Everything except managing the team and uploading payments — still sees prices and downloads invoices.'],
                    ['<code>HR</code>', 'Submits and tracks screening, reads reports, sees that invoices exist. <b>Cannot see prices</b> or download invoices — the usual choice for recruiters.'],
                    ['<code>Accounts</code>', 'Finance. Full visibility of billing, prices, invoices and the audit log, but <b>cannot submit requests</b>.'],
                ]],
                ['type' => 'sub', 'text' => 'Permission grid'],
                ['type' => 'table', 'head' => ['Permission', 'Grants', 'Owner', 'Admin', 'HR', 'Accounts'], 'widths' => ['22%', '42%', '9%', '9%', '9%', '9%'], 'rows' => self::permissionRows()],
                ['type' => 'note', 'title' => 'Permissions on top of a role', 'text' => 'An Owner can grant an individual permission to one user without changing their role, from <b>Settings → Users</b>. Reach for that before promoting someone: giving one recruiter <code>view-prices</code> is a far smaller change than making them an Admin.'],
            ],
        ];
    }

    /**
     * The seeded role → permission grid.
     *
     * Mirrors database/migrations/2026_05_06_011804_add_hr_accounts_roles_and_pricing_permission.php.
     * Held here rather than read from the database so the guide renders identically
     * everywhere and needs no query; if a role is re-scoped, update both.
     *
     * @return array<int, array<int, string>>
     */
    private static function permissionRows(): array
    {
        $yes = '<b>&#10003;</b>';
        $no = '&middot;';

        $grid = [
            ['view-dashboard', 'See the dashboard', true, true, true, true],
            ['view-candidates', 'Browse the candidates directory', true, true, true, true],
            ['view-requests', 'See and track active requests', true, true, true, true],
            ['create-requests', 'Submit new screening requests', true, true, true, false],
            ['view-reports', 'Open and download reports', true, true, true, true],
            ['view-billing', 'See invoices and transactions', true, true, true, true],
            ['manage-billing', 'Upload payment receipts and slips', true, false, false, true],
            ['view-prices', 'See prices and payment details', true, true, false, true],
            ['download-invoices', 'Download invoice and receipt PDFs', true, true, false, true],
            ['manage-settings', 'Edit account, security and agreement', true, true, false, false],
            ['manage-team', 'Invite and manage users', true, false, false, false],
            ['manage-packages', 'View configured packages', true, true, false, false],
            ['view-audit-log', 'Read the audit log', true, true, false, true],
        ];

        return array_map(fn (array $row) => [
            '<code>'.$row[0].'</code>',
            $row[1],
            $row[2] ? $yes : $no,
            $row[3] ? $yes : $no,
            $row[4] ? $yes : $no,
            $row[5] ? $yes : $no,
        ], $grid);
    }

    private static function troubleshooting(): array
    {
        return [
            'id' => 'troubleshooting',
            'title' => 'Troubleshooting &amp; FAQ',
            'permission' => null,
            'summary' => 'The things people actually get stuck on.',
            'blocks' => [
                ['type' => 'table', 'head' => ['Symptom', 'What is going on'], 'widths' => ['32%', '68%'], 'rows' => [
                    ['<b>A menu item other people talk about isn’t in my sidebar</b>', 'The sidebar only renders what your permissions cover. Check the grid under <b>Roles &amp; Permissions</b>, then ask an Owner to grant the specific permission in <b>Settings → Users</b>.'],
                    ['<b>I can’t see any prices when building a request</b>', 'Prices need <code>view-prices</code>, which the HR role deliberately lacks. You can still submit the request; someone with the permission can confirm the cost.'],
                    ['<b>A check I need isn’t in the scope list</b>', 'Scopes priced “on request” with no rate agreed for your company are hidden, and the portal refuses them even by direct link. Ask your account manager to set a price first.'],
                    ['<b>My verification code never arrives</b>', 'Check spam, then use <b>Resend code</b> — three per minute. The code is valid for ten minutes; an older one is rejected even if it arrives late. If nothing comes through at all, confirm the email address on your account with an Owner.'],
                    ['<b>My invitation link says it’s invalid</b>', 'Invitations expire after 14 days, and each new one invalidates the last. Ask an Owner to resend it and use the newest email.'],
                    ['<b>I searched an IC number and got nothing</b>', 'Identity numbers must be entered in full — they are encrypted, so partial matching is impossible. Search by name instead.'],
                    ['<b>The portal won’t let me upload a payment slip</b>', 'Slips are only accepted on per-request (cash) accounts, while the request is still <b>New</b> and not attached to an invoice. If it carries an invoice number, pay through the invoice. You also need a billing or pricing permission.'],
                    ['<b>My request has sat at “New” for days</b>', 'On a per-request account, <b>New</b> means payment is outstanding — nothing runs until it is verified. Open the request and check the payment block.'],
                    ['<b>A report download says the file is no longer available</b>', 'A server-side condition, not something retrying will fix. Contact support with the request reference.'],
                    ['<b>Can I cancel or edit a request after submitting?</b>', 'Not from the portal — contact your account manager as early as possible. On monthly accounts you <i>can</i> add candidates to a request that is not yet invoiced or finished.'],
                    ['<b>A report I downloaded last month has changed</b>', 'An updated report supersedes the earlier version, and superseded versions are hidden so nobody circulates a stale document. Download the current version and replace your copy.'],
                    ['<b>Who can see that I downloaded a report?</b>', 'Every download is logged with the user and time, visible to anyone on your account holding <code>view-audit-log</code>.'],
                ]],
            ],
        ];
    }

    private static function reference(): array
    {
        $sst = rtrim(rtrim(number_format((float) config('billing.sst_rate', 0.06) * 100, 2), '0'), '.');

        return [
            'id' => 'reference',
            'title' => 'Reference &amp; Glossary',
            'permission' => null,
            'summary' => 'Status values, formats and terminology — the desk-copy section.',
            'blocks' => [
                ['type' => 'sub', 'text' => 'Status values'],
                ['type' => 'table', 'head' => ['Where', 'Allowed values'], 'widths' => ['30%', '70%'], 'rows' => [
                    ['Screening request', '<code>new</code> · <code>in_progress</code> · <code>rejected</code> · <code>complete</code> · <code>updated</code>'],
                    ['Candidate', '<code>new</code> · <code>in_progress</code> · <code>flagged</code> · <code>complete</code>'],
                    ['Invoice', '<code>unpaid</code> · <code>paid</code> · <code>overdue</code>'],
                    ['Payment receipt', '<code>pending</code> · <code>verified</code> · <code>rejected</code>'],
                    ['Your account', '<code>active</code> · <code>inactive</code>'],
                ]],
                ['type' => 'sub', 'text' => 'Formats and limits'],
                ['type' => 'table', 'head' => ['Item', 'Value'], 'widths' => ['30%', '70%'], 'rows' => [
                    ['Request reference', '<code>REP-YYYY-NNNN</code>'],
                    ['Invoice number', '<code>INV-YYYY-NNN</code>'],
                    ['Candidate documents', 'PDF, DOC, DOCX, JPG, PNG — 10&nbsp;MB each'],
                    ['Payment slips and receipts', 'PDF, JPG, PNG — 5&nbsp;MB each'],
                    ['Tax', $sst.'% SST on invoice subtotals and cash request totals'],
                    ['Verification code', '6 digits, valid 10 minutes'],
                    ['Invitation link', 'Valid 14 days'],
                    ['Agreement warning', '60 days to expiry (warning), 14 days (critical)'],
                    ['Consent version', '<code>'.config('consent.current_version').'</code>'],
                ]],
                ['type' => 'sub', 'text' => 'Terminology'],
                ['type' => 'table', 'head' => ['Term', 'Means'], 'widths' => ['24%', '76%'], 'rows' => [
                    ['<b>Scope</b>', 'One individual check — an employment verification, a criminal record check — with its own price and turnaround.'],
                    ['<b>Package</b>', 'A named bundle of scopes configured for your company, addable in one click.'],
                    ['<b>Request</b>', 'One order: a set of scopes applied to one or more candidates, identified by a reference.'],
                    ['<b>Candidate</b>', 'One person being screened within a request.'],
                    ['<b>TAT</b>', 'Turnaround time — the target for completing a scope, counted in business hours only.'],
                    ['<b>SLA</b>', 'The commitment your agreement sets against that target. A scope past target is marked as over.'],
                    ['<b>PDPA</b>', 'Malaysia’s Personal Data Protection Act. Consent under it is recorded for every candidate.'],
                    ['<b>Preliminary report</b>', 'An early report issued while some checks continue.'],
                    ['<b>Supersede</b>', 'Issue a corrected report version that formally replaces an earlier one. The old version is hidden.'],
                    ['<b>SST</b>', 'Malaysian Sales and Service Tax, applied to invoice subtotals.'],
                    ['<b>Cash / per-request</b>', 'Billing mode where each request is paid before it starts.'],
                    ['<b>Credit / monthly</b>', 'Billing mode where requests run immediately and are invoiced monthly.'],
                ]],
                ['type' => 'sub', 'text' => 'Getting help'],
                ['type' => 'table', 'head' => ['Topic', 'Who to ask'], 'widths' => ['32%', '68%'], 'rows' => [
                    ['Payments and invoices', '<code>'.e((string) config('billing.proof_of_payment_email')).'</code> — quote the request or invoice number in the subject line.'],
                    ['Pricing, packages, agreements, renewals', 'Your account manager. These are all set outside the portal.'],
                    ['A specific request or report', 'Your account manager, quoting the request reference.'],
                    ['Access and permissions', 'An Owner on your own account, in <b>Settings → Users</b>.'],
                ]],
                ['type' => 'note', 'title' => 'Before you write in', 'text' => 'Include the <b>request reference</b> (or invoice number), what you expected, and what the portal actually showed. That one line saves a round trip almost every time.'],
            ],
        ];
    }
}
