<x-client.layouts.app pageTitle="User Management">

    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-slate-500">Team members with access to this portal</p>
        <button
            x-data
            @click="$dispatch('open-create-user')"
            class="flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Add User
        </button>
    </div>

    <div class="bg-white rounded-xl border border-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/60">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">User</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Role</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Created</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $user)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="size-8 rounded-full bg-brand-100 flex items-center justify-center text-xs font-semibold text-brand-700 shrink-0">
                                        {{ strtoupper(substr($user['name'], 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $user['name'] }}</p>
                                        <p class="text-xs text-slate-400">{{ $user['email'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="rounded-full bg-slate-100 border border-slate-200 px-2.5 py-0.5 text-xs font-medium text-slate-600">{{ $user['role'] }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-medium
                                    {{ $user['status'] === 'Active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                                    <span class="size-1.5 rounded-full {{ $user['status'] === 'Active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ $user['status'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-500">{{ $user['created_at'] }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <button class="text-xs font-medium text-brand-600 hover:text-brand-700 transition-colors">Edit</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create user modal --}}
    <div
        x-data="{ open: false }"
        @open-create-user.window="open = true"
        x-show="open"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-black/40" @click="open = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6"
             x-transition:enter="transition duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-semibold text-slate-900">Add New User</h3>
                <button @click="open = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Full Name</label>
                    <input type="text" placeholder="Ahmad bin Ali"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Email Address</label>
                    <input type="email" placeholder="ahmad@company.com"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Role</label>
                    <select class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 bg-white focus:border-brand-500 focus:outline-none focus:ring-3 focus:ring-brand-500/20 transition-colors">
                        <option>User</option>
                        <option>Admin</option>
                    </select>
                </div>
                <p class="text-xs text-slate-400">A temporary password will be sent to the user's email.</p>
                <div class="flex gap-3 pt-1">
                    <button type="button" @click="open = false"
                        class="flex-1 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Cancel</button>
                    <button type="submit"
                        class="flex-1 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition-colors">Create User</button>
                </div>
            </form>
        </div>
    </div>

</x-client.layouts.app>
