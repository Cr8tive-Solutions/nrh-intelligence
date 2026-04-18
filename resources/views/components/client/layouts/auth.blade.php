<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Client Portal' }} — NRH INTELLIGENCE</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 antialiased" x-data>

    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">

        {{-- Logo --}}
        <div class="mb-8 text-center">
            <a href="/">
                <div class="inline-flex items-center gap-2">
                    <div class="size-9 rounded-lg bg-brand-600 flex items-center justify-center">
                        <svg class="size-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.955 11.955 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-slate-900 tracking-tight">NRH INTELLIGENCE</span>
                </div>
            </a>
            <p class="mt-2 text-sm text-slate-500">Background Verification Platform</p>
        </div>

        {{-- Card --}}
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 px-8 py-8">
                {{ $slot }}
            </div>
        </div>

        {{-- Footer --}}
        <p class="mt-6 text-xs text-slate-400">
            &copy; {{ date('Y') }} NRH INTELLIGENCE. All rights reserved.
        </p>
    </div>

</body>
</html>
