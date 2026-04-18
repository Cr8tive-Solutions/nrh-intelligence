<aside
    id="sidebar"
    class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-slate-900 transition-transform duration-300 lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    {{-- Logo --}}
    <div class="flex h-16 shrink-0 items-center gap-3 px-6 border-b border-slate-800">
        <div class="size-8 rounded-lg bg-brand-600 flex items-center justify-center shrink-0">
            <svg class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.955 11.955 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
            </svg>
        </div>
        <span class="text-base font-semibold text-white tracking-tight">NRH INTELLIGENCE</span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">

        @php
            $navItem = fn(string $route, string $label, string $icon, string $match = '') =>
                ['route' => $route, 'label' => $label, 'icon' => $icon, 'match' => $match ?: $route];
            $items = [
                $navItem('client.dashboard', 'Dashboard', 'dashboard'),
                $navItem('client.request.new', 'New Request', 'new-request', 'client.request'),
                $navItem('client.requests.index', 'Active Requests', 'requests', 'client.requests'),
                $navItem('client.history.index', 'History', 'history', 'client.history'),
            ];
        @endphp

        {{-- Main Nav --}}
        @foreach ($items as $item)
            <a href="{{ route($item['route']) }}"
               class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
                      {{ request()->routeIs($item['match'] . '*')
                            ? 'bg-brand-600 text-white'
                            : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                @include('client.layouts._sidebar-icon', ['icon' => $item['icon']])
                {{ $item['label'] }}
            </a>
        @endforeach

        {{-- Billing Section --}}
        <div class="pt-4">
            <p class="px-3 mb-1 text-xs font-semibold uppercase tracking-wider text-slate-600">Billing</p>
            <a href="{{ route('client.billing.transactions') }}"
               class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
                      {{ request()->routeIs('client.billing.transactions*') ? 'bg-brand-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                @include('client.layouts._sidebar-icon', ['icon' => 'transactions'])
                Transactions
            </a>
            <a href="{{ route('client.billing.invoices') }}"
               class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
                      {{ request()->routeIs('client.billing.invoices*') ? 'bg-brand-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                @include('client.layouts._sidebar-icon', ['icon' => 'invoices'])
                Invoices
            </a>
        </div>

        {{-- Scope --}}
        <div class="pt-4">
            <p class="px-3 mb-1 text-xs font-semibold uppercase tracking-wider text-slate-600">Scope</p>
            <a href="{{ route('client.maps') }}"
               class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
                      {{ request()->routeIs('client.maps*') ? 'bg-brand-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                @include('client.layouts._sidebar-icon', ['icon' => 'maps'])
                Scope Maps
            </a>
        </div>

        {{-- Settings --}}
        <div class="pt-4">
            <p class="px-3 mb-1 text-xs font-semibold uppercase tracking-wider text-slate-600">Settings</p>
            @foreach ([
                ['client.settings.account', 'Account', 'account', 'client.settings.account'],
                ['client.settings.users', 'Users', 'users', 'client.settings.users'],
                ['client.settings.packages', 'Packages', 'packages', 'client.settings.packages'],
                ['client.settings.security', 'Security', 'security', 'client.settings.security'],
                ['client.settings.agreement', 'Agreement', 'agreement', 'client.settings.agreement'],
            ] as [$route, $label, $icon, $match])
                <a href="{{ route($route) }}"
                   class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
                          {{ request()->routeIs($match . '*') ? 'bg-brand-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    @include('client.layouts._sidebar-icon', ['icon' => $icon])
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </nav>

    {{-- Next Invoice Reminder --}}
    <div class="shrink-0 px-3 pb-4">
        <div class="rounded-xl bg-slate-800 px-4 py-3">
            <p class="text-xs text-slate-500 font-medium">Next Invoice</p>
            <p class="mt-0.5 text-sm font-semibold text-white">End of {{ now()->format('F Y') }}</p>
            <a href="{{ route('client.billing.invoices') }}" class="mt-2 flex items-center gap-1 text-xs text-brand-400 hover:text-brand-300 font-medium transition-colors">
                View invoices
                <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>
    </div>
</aside>
