<x-client.layouts.legal
    title="Terms of Use"
    heading="Terms of <em>Use</em>"
    intro="These terms apply to every company and person who uses the KYRO client portal to order and receive background screening."
>

    <nav class="lg-toc" aria-label="Contents">
        <div class="lg-toc-title">Contents</div>
        <ol>
            <li><a href="#about">About these terms</a></li>
            <li><a href="#accounts">Accounts and access</a></li>
            <li><a href="#consent">Candidate consent</a></li>
            <li><a href="#use">Acceptable use</a></li>
            <li><a href="#reports">Screening reports</a></li>
            <li><a href="#fees">Fees and payment</a></li>
            <li><a href="#data">Personal data</a></li>
            <li><a href="#service">Availability and changes</a></li>
            <li><a href="#ip">Intellectual property</a></li>
            <li><a href="#suspension">Suspension and termination</a></li>
            <li><a href="#liability">Liability</a></li>
            <li><a href="#changes">Changes to these terms</a></li>
            <li><a href="#law">Governing law</a></li>
            <li><a href="#contact">Contact us</a></li>
        </ol>
    </nav>

    <h2 id="about"><span class="num">01</span>About these terms</h2>
    <p>KYRO is a background screening portal operated by <b>{{ config('app.company.name') }}</b> (Company No. {{ config('app.company.reg_no') }}), a company incorporated in Malaysia ("<b>NRH Intelligence</b>", "<b>we</b>", "<b>us</b>").</p>
    <p>These terms govern your use of the KYRO client portal. "<b>Customer</b>" means the company that holds a KYRO account. "<b>User</b>" or "<b>you</b>" means any person who signs in on the Customer's behalf.</p>
    <p>The Customer's signed service agreement with NRH Intelligence also applies. If the service agreement and these terms conflict, the service agreement takes priority.</p>

    <h2 id="accounts"><span class="num">02</span>Accounts and access</h2>
    <ul>
        <li>KYRO accounts are by invitation only. A Customer's account is opened after we approve its registration, and its managers can then invite their own colleagues.</li>
        <li>Each User must have their own account. Do not share passwords or verification codes.</li>
        <li>Every sign-in requires a second factor (an emailed code or an authenticator app). Keep the device or mailbox that receives your codes secure.</li>
        <li>The Customer is responsible for everything done under its Users' accounts, for keeping its list of Users up to date and for removing access promptly when someone leaves.</li>
        <li>Tell us straight away at <a href="mailto:{{ config('app.support_email') }}">{{ config('app.support_email') }}</a> if you suspect someone else has used your account.</li>
    </ul>

    <h2 id="consent"><span class="num">03</span>Candidate consent</h2>
    <p>Background screening involves personal data, and often sensitive personal data, about the people being screened ("<b>Candidates</b>"). Before submitting a Candidate to KYRO, the Customer must:</p>
    <ul>
        <li>have a lawful purpose for the screening, such as a recruitment or employment decision;</li>
        <li>obtain the Candidate's informed and explicit consent to the checks being ordered, in line with the Personal Data Protection Act 2010 (PDPA);</li>
        <li>give the Candidate the notices the PDPA requires, including that their data will be shared with NRH Intelligence for screening; and</li>
        <li>make sure the details submitted are accurate and complete.</li>
    </ul>
    <p>KYRO records the consent confirmation or the signed consent form you provide with each request. We may pause or decline a request if consent is missing or unclear.</p>

    <h2 id="use"><span class="num">04</span>Acceptable use</h2>
    <p>You must not:</p>
    <ul>
        <li>screen anyone without their consent, or for a purpose unrelated to the reason you gave them;</li>
        <li>use KYRO or its reports to discriminate unlawfully against any person;</li>
        <li>submit false or misleading information, or information about a person you are not entitled to screen;</li>
        <li>try to access another Customer's data, probe or test the portal's security, or interfere with its operation;</li>
        <li>use automated tools to scrape or bulk-download data from the portal; or</li>
        <li>resell or provide KYRO's services or reports to anyone outside your organisation.</li>
    </ul>

    <h2 id="reports"><span class="num">05</span>Screening reports</h2>
    <ul>
        <li>Reports are confidential. Use them only for the purpose the Candidate consented to, and share them only with people in your organisation who need them for that decision.</li>
        <li>Reports are compiled from information held by third parties such as former employers, education institutions, registries and public sources. We take care to verify what we report, but we cannot guarantee that third-party records are complete, current or free of error.</li>
        <li>Turnaround times shown in KYRO are estimates. They depend on how quickly third-party sources respond and exclude weekends and public holidays.</li>
        <li>The hiring or engagement decision is yours. A report is one input into that decision and is not advice to hire or reject anyone.</li>
        <li>If a Candidate disputes something in a report, let us know and we will look into it and issue a corrected report where needed.</li>
    </ul>

    <h2 id="fees"><span class="num">06</span>Fees and payment</h2>
    <ul>
        <li>Prices are those in your service agreement or shown in KYRO when you place a request, in Malaysian Ringgit (MYR).</li>
        <li><b>Cash customers</b> pay before work starts, by uploading proof of payment against each request.</li>
        <li><b>Credit customers</b> are invoiced monthly and pay within the term in their service agreement. Overdue invoices may lead us to pause new requests.</li>
        <li>A check that has already started or been completed is not refundable merely because the result is unfavourable or the Candidate withdraws.</li>
    </ul>

    <h2 id="data"><span class="num">07</span>Personal data</h2>
    <p>How we collect, use and protect personal data is explained in our <a href="{{ route('client.privacy') }}">Privacy Notice</a>. Both the Customer and NRH Intelligence must comply with the PDPA in relation to Candidate data. Neither of us may use that data for any purpose other than the screening the Candidate agreed to.</p>

    <h2 id="service"><span class="num">08</span>Availability and changes</h2>
    <p>We aim to keep KYRO available at all times, but it may be unavailable during maintenance, upgrades or events outside our control. We may improve or change features from time to time. If a change materially reduces the service you have paid for, we will tell you in advance.</p>

    <h2 id="ip"><span class="num">09</span>Intellectual property</h2>
    <p>The KYRO portal, its design, software and content belong to NRH Intelligence. Under these terms you may use the portal, and the reports issued to you, for your internal screening purposes only. Information you submit about your organisation and Candidates remains yours.</p>

    <h2 id="suspension"><span class="num">10</span>Suspension and termination</h2>
    <p>We may suspend or close a User or Customer account if these terms or the service agreement are breached, if we suspect misuse or a security risk, or if required by law. Where we can, we will tell you first and give you a chance to fix the problem.</p>
    <p>When a Customer account closes, we keep records for the periods described in our <a href="{{ route('client.privacy') }}#retention">Privacy Notice</a>, then delete or anonymise them.</p>

    <h2 id="liability"><span class="num">11</span>Liability</h2>
    <p>Nothing in these terms limits liability that cannot be limited under Malaysian law. Otherwise, NRH Intelligence's liability is as set out in the Customer's service agreement. Where the service agreement is silent:</p>
    <ul>
        <li>we are not liable for indirect or consequential loss, or for loss of profit, business or goodwill; and</li>
        <li>our total liability for any claim is limited to the fees the Customer paid for the request that gave rise to it.</li>
    </ul>

    <h2 id="changes"><span class="num">12</span>Changes to these terms</h2>
    <p>We may update these terms. The effective date at the top of this page shows when they last changed. If a change is significant, we will notify Customers by email or in the portal before it takes effect. Continuing to use KYRO after that date means you accept the updated terms.</p>

    <h2 id="law"><span class="num">13</span>Governing law</h2>
    <p>These terms are governed by the laws of Malaysia. Any dispute will be subject to the exclusive jurisdiction of the courts of Malaysia.</p>

    <h2 id="contact"><span class="num">14</span>Contact us</h2>
    <div class="lg-contact">
        <p><b>{{ config('app.company.name') }}</b> ({{ config('app.company.reg_no') }})</p>
        <p>{{ config('app.company.address') }}</p>
        <p>Email: <a href="mailto:{{ config('app.support_email') }}">{{ config('app.support_email') }}</a> · Phone: {{ config('app.company.phone') }}</p>
    </div>

</x-client.layouts.legal>
