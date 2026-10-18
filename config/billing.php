<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cash-billing payment instructions
    |--------------------------------------------------------------------------
    |
    | Shown to cash-billed customers after they submit a request and on the
    | request detail page until admin marks payment received. Hardcoded for
    | now — move to a database-backed setting if you ever need per-customer
    | bank accounts or rotate these without a deploy.
    */
    'bank' => [
        'name' => env('BILLING_BANK_NAME', 'CIMB BANK BERHAD'),
        'account_holder' => env('BILLING_BANK_HOLDER', 'NRH INTELLIGENCE SDN BHD'),
        'account_number' => env('BILLING_BANK_ACCOUNT', '8011600106'),
        'swift' => env('BILLING_BANK_SWIFT', 'CIBBMYKL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Issuing-company details (printed on the invoice header + footer)
    |--------------------------------------------------------------------------
    |
    | These appear on the official invoice template. Cheques are made payable
    | to `cheque_payee`.
    */
    'company' => [
        'name' => env('BILLING_COMPANY_NAME', 'NRH INTELLIGENCE SDN BHD'),
        'reg_no' => env('BILLING_COMPANY_REG', '202101036456 (1436756D)'),
        'tax_no' => env('BILLING_COMPANY_TAX', 'C 26861781030'),
        'address' => env('BILLING_COMPANY_ADDRESS', 'No. B1-1, Jalan Selaman 1, Dataran Palma, 68000 Ampang, Selangor'),
        'phone' => env('BILLING_COMPANY_PHONE', '+60124395006'),
        'email' => env('BILLING_COMPANY_EMAIL', 'nadiaa@nrhintelligence.com'),
    ],

    'cheque_payee' => env('BILLING_CHEQUE_PAYEE', 'NRH INTELLIGENCE SDN BHD'),

    'currency' => env('BILLING_CURRENCY', 'MYR'),

    /*
     | Email used for proof-of-payment submissions. Customers should email a
     | transfer slip to this address with their request reference in the
     | subject line.
     */
    'proof_of_payment_email' => env('BILLING_POP_EMAIL', 'finance@nrhintelligence.com'),

    /*
     | Number of business days after payment is received before processing
     | begins (used in customer-facing copy).
     */
    'sla_after_payment' => '1 business day',

    /*
    |--------------------------------------------------------------------------
    | Billing-mode mapping
    |--------------------------------------------------------------------------
    |
    | The agreements.billing column is free-text in the shared schema. We
    | normalise here so any of these strings (case-insensitive) map to the
    | matching mode. Anything else falls back to credit (the safer default —
    | customer can still pay later).
    */
    'cash_aliases' => ['per_request', 'per request', 'cash', 'pay_per_use', 'pay per use', 'prepaid', 'pre-paid'],
    'credit_aliases' => ['monthly', 'credit', 'invoice', 'postpaid', 'post-paid'],

    /*
    |--------------------------------------------------------------------------
    | SST rate
    |--------------------------------------------------------------------------
    |
    | 6% Malaysian SST, applied to monthly invoice subtotals (by the admin
    | portal) AND to per-request cash totals — keep in sync with the admin
    | portal's config/billing.php.
    */
    'sst_rate' => 0.06,
];
