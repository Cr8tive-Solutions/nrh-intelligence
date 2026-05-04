<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Current consent version
    |--------------------------------------------------------------------------
    |
    | Bump this when the standard text below changes. Both the version string
    | and the full text are stamped onto every consent record so future audits
    | can prove exactly what wording the data subject agreed to.
    |
    | Keep this in lock-step with the admin portal's
    | App\Http\Controllers\Compliance\ConsentController::CURRENT_VERSION.
    */
    'current_version' => 'v1-2026-04',

    /*
    |--------------------------------------------------------------------------
    | Standard PDPA consent text
    |--------------------------------------------------------------------------
    |
    | Shown to data subjects before submission and snapshotted into every
    | consent record. Must match the admin portal's STANDARD_TEXT verbatim
    | so both surfaces quote identical wording.
    */
    'standard_text' => <<<'TEXT'
I, the data subject, hereby give my informed consent to NRH Intelligence Sdn. Bhd. ("NRH") to collect, process, store, and verify my personal data for the purpose of background screening, identity verification, and compliance checks as requested by the engaging organisation.

I understand and agree that:

1. The personal data collected may include, where applicable, my full name, national identification or passport details, contact information, employment history, educational qualifications, criminal records, credit history, financial standing, and other information necessary to complete the requested verification.

2. NRH may share my personal data with authorised third-party verification providers, government agencies, credit bureaus, and other relevant institutions strictly for the purpose described above.

3. The results of the verification will be disclosed to the engaging organisation that has requested this screening. I understand the engaging organisation will rely on these results to make decisions affecting me.

4. NRH will retain my personal data only for the period reasonably necessary to fulfil the screening purpose and to comply with applicable laws, including the Personal Data Protection Act 2010 (Malaysia).

5. I have the right to access, correct, or request the erasure of my personal data, and to withdraw this consent in writing at any time, subject to legal and contractual obligations. I understand that withdrawing consent may halt or invalidate the screening already in progress.

6. I confirm that the information I provide is accurate to the best of my knowledge, and I authorise the verification of any information I have submitted.

By ticking the consent box and submitting this screening request, I confirm that I have read, understood, and agree to the terms above.
TEXT,
];
