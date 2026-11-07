<x-client.layouts.legal
    title="Privacy Notice"
    heading="Privacy <em>Notice</em>"
    intro="How NRH Intelligence collects, uses and protects personal data in KYRO, under Malaysia's Personal Data Protection Act 2010."
>

    <nav class="lg-toc" aria-label="Contents">
        <div class="lg-toc-title">Contents</div>
        <ol>
            <li><a href="#who">Who we are</a></li>
            <li><a href="#scope">Who this notice covers</a></li>
            <li><a href="#collect">Data we collect</a></li>
            <li><a href="#sources">Where it comes from</a></li>
            <li><a href="#purposes">Why we use it</a></li>
            <li><a href="#sharing">Who we share it with</a></li>
            <li><a href="#transfers">Transfers outside Malaysia</a></li>
            <li><a href="#security">How we protect it</a></li>
            <li><a href="#retention">How long we keep it</a></li>
            <li><a href="#rights">Your rights</a></li>
            <li><a href="#cookies">Cookies</a></li>
            <li><a href="#changes">Changes to this notice</a></li>
            <li><a href="#contact">Contact us</a></li>
        </ol>
    </nav>

    <h2 id="who"><span class="num">01</span>Who we are</h2>
    <p>KYRO is operated by <b>{{ config('app.company.name') }}</b> (Company No. {{ config('app.company.reg_no') }}), {{ config('app.company.address') }} ("<b>NRH Intelligence</b>", "<b>we</b>", "<b>us</b>"). We provide background screening services to companies ("<b>Customers</b>").</p>

    <h2 id="scope"><span class="num">02</span>Who this notice covers</h2>
    <ul>
        <li><b>Users:</b> people who sign in to KYRO on behalf of a Customer.</li>
        <li><b>Candidates:</b> people whom a Customer asks us to screen, usually job applicants or employees.</li>
    </ul>
    <p>For Candidate data, the Customer decides who is screened and why, and collects the Candidate's consent. We process that data to carry out the screening and also take our own responsibility for it under the PDPA. If you are a Candidate, the employer that asked you to consent should also have given you its own privacy notice.</p>

    <h2 id="collect"><span class="num">03</span>Data we collect</h2>
    <h3>About Users</h3>
    <ul>
        <li>Name, work email, phone number, job title and the Customer you belong to.</li>
        <li>Sign-in records: date and time, IP address, browser, two-factor method and failed attempts.</li>
        <li>A record of actions you take in KYRO, such as submitting requests, downloading reports and managing users.</li>
        <li>Billing details and payment slips uploaded for your Customer's account.</li>
    </ul>
    <h3>About Candidates</h3>
    <ul>
        <li>Identity details: full name, identity card or passport number, date of birth, nationality and contact details.</li>
        <li>Documents the Customer uploads, such as a resume, certificates, a copy of the identity document and the signed consent form.</li>
        <li>The results of the checks ordered, which may include employment history, education, professional qualifications, credit or financial standing, and criminal or court records.</li>
        <li>The consent record: when consent was given, the consent wording shown and the evidence provided.</li>
    </ul>
    <p>Some of this, particularly information about criminal offences and court proceedings, is <b>sensitive personal data</b> under the PDPA. We process it only with the Candidate's explicit consent.</p>

    <h2 id="sources"><span class="num">04</span>Where it comes from</h2>
    <ul>
        <li>From Users, when you register, sign in and use KYRO.</li>
        <li>From the Customer, which submits Candidate details and documents.</li>
        <li>From the sources we contact to verify information, such as former employers, universities and colleges, professional bodies, credit reporting agencies, courts and other public registries.</li>
    </ul>

    <h2 id="purposes"><span class="num">05</span>Why we use it</h2>
    <ul>
        <li>To carry out the screening the Customer ordered and the Candidate consented to, and to issue the report.</li>
        <li>To run User accounts: sign-in, two-factor verification, notifications and support.</li>
        <li>To invoice Customers and process payments.</li>
        <li>To keep KYRO secure, detect misuse and investigate incidents.</li>
        <li>To meet our legal and regulatory obligations, including tax record-keeping and lawful requests from authorities.</li>
    </ul>
    <p>We do not sell personal data and we do not use it for advertising.</p>

    <h2 id="sharing"><span class="num">06</span>Who we share it with</h2>
    <ul>
        <li><b>The Customer</b> that ordered the screening receives the Candidate's report.</li>
        <li><b>Verification sources</b> receive only the details they need to confirm a record, for example a former employer receives the Candidate's name and dates of employment.</li>
        <li><b>Service providers</b> that help us run KYRO, under contract and only for that purpose: Cloudflare (network security and delivery), Microsoft (email) and our email hosting provider.</li>
        <li><b>Authorities</b>, where the law requires it.</li>
    </ul>

    <h2 id="transfers"><span class="num">07</span>Transfers outside Malaysia</h2>
    <p>KYRO's application and database run on servers managed by NRH Intelligence. Some service providers, such as Cloudflare and Microsoft, operate global networks, so data passing through them may be processed outside Malaysia. Where that happens, we rely on providers bound by contractual data protection commitments, as the PDPA permits. A screening check in another country, such as an overseas education check, also requires sending the relevant details to a source in that country.</p>

    <h2 id="security"><span class="num">08</span>How we protect it</h2>
    <ul>
        <li>All connections to KYRO are encrypted in transit (HTTPS).</li>
        <li>Uploaded files and reports are encrypted at rest with AES-256, and identity numbers are stored encrypted.</li>
        <li>Every sign-in requires a password and a second factor, and repeated failed attempts lock the account.</li>
        <li>Each Customer can see only its own data, and access within a Customer follows the roles its managers assign.</li>
        <li>Sign-ins and key actions are logged, and staff access to Candidate data is restricted to people who need it.</li>
    </ul>
    <p>If a data breach is likely to cause significant harm, we will notify the Personal Data Protection Commissioner and affected individuals as the law requires.</p>

    <h2 id="retention"><span class="num">09</span>How long we keep it</h2>
    <table class="lg-table">
        <tr><th>Record</th><th>Kept for</th><th>Why</th></tr>
        <tr><td>Candidate data and reports</td><td>7 years from the request</td><td>To answer disputes and queries about a screening</td></tr>
        <tr><td>Consent records</td><td>10 years</td><td>Proof that the screening was authorised</td></tr>
        <tr><td>Invoices and payment records</td><td>7 years</td><td>Malaysian tax record-keeping rules</td></tr>
        <tr><td>Audit and sign-in logs</td><td>7 years</td><td>Security and regulatory inquiries</td></tr>
    </table>
    <p>When a period ends, Candidate personal data is automatically redacted. A Customer's service agreement may set a shorter period.</p>

    <h2 id="rights"><span class="num">10</span>Your rights</h2>
    <p>Under the PDPA you may:</p>
    <ul>
        <li><b>access</b> the personal data we hold about you and get a copy;</li>
        <li><b>correct</b> data that is inaccurate, incomplete or out of date;</li>
        <li><b>withdraw consent</b>, although a screening already completed cannot be undone and we must keep the records listed above; and</li>
        <li><b>limit</b> how your data is used, or object to processing likely to cause you damage or distress.</li>
    </ul>
    <p>Users can update most of their own details under Settings. Candidates may contact the employer that requested the screening, or us directly. We respond within 21 days, and may need to confirm your identity first. The PDPA allows a small fee for a copy of your data. We will tell you before charging it.</p>
    <p>If you are not satisfied with our response, you may complain to the Personal Data Protection Commissioner of Malaysia.</p>

    <h2 id="cookies"><span class="num">11</span>Cookies</h2>
    <p>KYRO uses only the cookies it needs to work:</p>
    <ul>
        <li>a <b>session cookie</b> that keeps you signed in and expires when your session ends;</li>
        <li>a <b>security token</b> that protects forms from cross-site request forgery; and</li>
        <li>a <b>remember-device cookie</b>, only if you tick "Trust this device", lasting 30 days.</li>
    </ul>
    <p>Your light or dark theme choice is saved in your browser. We do not use analytics, advertising or tracking cookies.</p>

    <h2 id="changes"><span class="num">12</span>Changes to this notice</h2>
    <p>We may update this notice. The effective date at the top shows when it last changed. If a change is significant, we will tell Customers by email or in the portal.</p>

    <h2 id="contact"><span class="num">13</span>Contact us</h2>
    <p>For privacy questions or to exercise your rights, contact:</p>
    <div class="lg-contact">
        <p><b>Data Protection Officer, {{ config('app.company.name') }}</b></p>
        <p>{{ config('app.company.address') }}</p>
        <p>Email: <a href="mailto:{{ config('app.support_email') }}">{{ config('app.support_email') }}</a> · Phone: {{ config('app.company.phone') }}</p>
    </div>

</x-client.layouts.legal>
