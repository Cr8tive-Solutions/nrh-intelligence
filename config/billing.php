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
        'name' => env('BILLING_BANK_NAME', 'Maybank Berhad'),
        'account_holder' => env('BILLING_BANK_HOLDER', 'NRH Intelligence Sdn. Bhd.'),
        'account_number' => env('BILLING_BANK_ACCOUNT', '5621 0123 4567'),
        'swift' => env('BILLING_BANK_SWIFT', 'MBBEMYKL'),
    ],

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
];
