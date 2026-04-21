<x-client.layouts.app pageTitle="My Profile">

    <div class="page-head">
        <div>
            <h1>My <em>Profile</em></h1>
            <div class="sub">Your personal account details</div>
        </div>
    </div>

    <div style="max-width:560px;display:flex;flex-direction:column;gap:16px;">

        {{-- Avatar + role card --}}
        <div class="card" style="padding:20px 24px;">
            <div style="display:flex;align-items:center;gap:16px;">
                <div style="position:relative;flex-shrink:0;" x-data="avatarPreview()">
                    {{-- Avatar display --}}
                    <div style="width:64px;height:64px;border-radius:50%;overflow:hidden;box-shadow:inset 0 0 0 2px rgba(212,175,55,0.4);">
                        <template x-if="preview">
                            <img :src="preview" style="width:100%;height:100%;object-fit:cover;" alt="Avatar">
                        </template>
                        <template x-if="!preview">
                            @if ($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" style="width:100%;height:100%;object-fit:cover;" alt="Avatar">
                            @else
                                <div style="width:100%;height:100%;background:var(--emerald-700);color:var(--gold-400);display:grid;place-items:center;font-size:20px;font-weight:600;font-family:var(--font-mono);">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                            @endif
                        </template>
                    </div>

                    {{-- Upload trigger --}}
                    <label for="avatar-input"
                           style="position:absolute;bottom:0;right:0;width:22px;height:22px;border-radius:50%;background:var(--emerald-700);color:white;display:grid;place-items:center;cursor:pointer;box-shadow:0 0 0 2px var(--card);"
                           title="Change photo">
                        <svg style="width:11px;height:11px;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"/>
                        </svg>
                    </label>
                    <input id="avatar-input" type="file" accept="image/jpeg,image/png,image/webp" style="display:none;" @change="onFileChange">
                </div>

                <div>
                    <p style="font-size:16px;font-weight:600;color:var(--ink-900);margin:0;">{{ $user->name }}</p>
                    <p style="font-size:12px;color:var(--ink-500);margin:3px 0 0;">{{ $user->email }}</p>
                    <div style="display:flex;gap:8px;margin-top:8px;">
                        <span class="pill pill-pending"><span class="dot"></span>{{ ucfirst($user->role) }}</span>
                        <span class="pill {{ $user->status === 'active' ? 'pill-clear' : 'pill-pending' }}"><span class="dot"></span>{{ ucfirst($user->status) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit form --}}
        <div class="card">
            <div class="card-head">
                <h3>Personal Details</h3>
            </div>
            {{-- Remove avatar form (outside main form to avoid nesting) --}}
        @if ($user->avatar)
            <form id="remove-avatar-form" method="POST" action="{{ route('client.settings.profile.avatar.remove') }}">
                @csrf
                @method('DELETE')
            </form>
        @endif

        <form method="POST" action="{{ route('client.settings.profile.update') }}" enctype="multipart/form-data" class="form-body" x-data="avatarPreview()">
                @csrf

                {{-- Hidden file input submitted with form --}}
                <input id="avatar-form-input" type="file" name="avatar" accept="image/jpeg,image/png,image/webp" style="display:none;" @change="onFileChange">

                {{-- Avatar picker inside form --}}
                <div class="field">
                    <label>Profile Photo</label>
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:52px;height:52px;border-radius:50%;overflow:hidden;flex-shrink:0;border:1px solid var(--line);">
                            <template x-if="preview">
                                <img :src="preview" style="width:100%;height:100%;object-fit:cover;" alt="">
                            </template>
                            <template x-if="!preview">
                                @if ($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
                                @else
                                    <div style="width:100%;height:100%;background:var(--emerald-700);color:var(--gold-400);display:grid;place-items:center;font-size:16px;font-weight:700;font-family:var(--font-mono);">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                @endif
                            </template>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:6px;">
                            <label for="avatar-form-input" class="btn btn-ghost" style="cursor:pointer;font-size:12px;padding:6px 14px;">
                                <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                                Choose photo
                            </label>
                            <p style="font-size:11px;color:var(--ink-400);margin:0;" x-text="fileName || 'JPG, PNG or WebP · Max 2MB'"></p>
                            @if ($user->avatar)
                                <button type="submit" form="remove-avatar-form"
                                    style="font-size:11px;color:#ef4444;background:none;border:none;cursor:pointer;padding:0;font-family:var(--font-ui);"
                                    onmouseover="this.style.textDecoration='underline'"
                                    onmouseout="this.style.textDecoration='none'">
                                    Remove photo
                                </button>
                            @endif
                        </div>
                    </div>
                    @error('avatar') <p class="hint">{{ $message }}</p> @enderror
                </div>

                <div class="field">
                    <label>Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"/>
                    @error('name') <p class="hint">{{ $message }}</p> @enderror
                </div>

                <div class="field">
                    <label>Work Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"/>
                    @error('email') <p class="hint">{{ $message }}</p> @enderror
                </div>

                <div class="field-row field-row-2">
                    <div class="field">
                        <label>Role</label>
                        <div class="readonly">{{ ucfirst($user->role) }}</div>
                    </div>
                    <div class="field">
                        <label>Status</label>
                        <div class="readonly">{{ ucfirst($user->status) }}</div>
                    </div>
                </div>

                <div class="field">
                    <label>Member Since</label>
                    <div class="readonly">{{ $user->created_at->format('d M Y') }}</div>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>

        {{-- Quick links --}}
        <div class="card" style="padding:16px 20px;">
            <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.12em;color:var(--ink-500);margin:0 0 12px;">Related Settings</p>
            <div style="display:flex;flex-direction:column;gap:2px;">
                @foreach ([
                    [route('client.settings.security'), 'Change Password', 'M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.169.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z'],
                    [route('client.settings.account'), 'Company Account', 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21'],
                ] as [$href, $label, $icon])
                    <a href="{{ $href }}"
                       style="display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:var(--radius);font-size:13px;font-weight:500;color:var(--ink-700);text-decoration:none;transition:background 120ms;"
                       onmouseover="this.style.background='rgba(5,150,105,0.05)';this.style.color='var(--ink-900)'"
                       onmouseout="this.style.background='';this.style.color='var(--ink-700)'">
                        <svg style="width:15px;height:15px;color:var(--emerald-600);flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                        </svg>
                        {{ $label }}
                        <svg style="width:12px;height:12px;margin-left:auto;color:var(--ink-300);" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </a>
                @endforeach
            </div>
        </div>

    </div>

@push('scripts')
<script>
function avatarPreview() {
    return {
        preview: null,
        fileName: '',
        onFileChange(e) {
            const file = e.target.files[0];
            if (!file) { return; }
            this.fileName = file.name;
            const reader = new FileReader();
            reader.onload = (ev) => { this.preview = ev.target.result; };
            reader.readAsDataURL(file);
        },
    };
}
</script>
@endpush

</x-client.layouts.app>
