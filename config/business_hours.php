<?php

return [
    'working_days' => [1, 2, 3, 4, 5],
    'start_hour' => 9,
    'end_hour' => 18,
    'timezone' => env('BUSINESS_HOURS_TZ', env('APP_TIMEZONE', 'Asia/Kuala_Lumpur')),
];
