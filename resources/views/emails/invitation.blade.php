<x-emails.layout title="You've been invited" preview="You've been invited to join {{ $companyName }} on KYRO">
    <h1 style="margin:0 0 16px; font-family:'Inter Tight',Helvetica,Arial,sans-serif; font-size:21px; font-weight:600; color:#14201b; line-height:1.3;">
        You&rsquo;ve been invited
    </h1>
    <p style="margin:0 0 16px; font-size:15px; line-height:1.65; color:#3b4b44;">
        Hi {{ $name }},
    </p>
    <p style="margin:0 0 16px; font-size:15px; line-height:1.65; color:#3b4b44;">
        @if ($inviterName)
            <strong style="color:#14201b;">{{ $inviterName }}</strong> has invited you
        @else
            You&rsquo;ve been invited
        @endif
        to join <strong style="color:#14201b;">{{ $companyName }}</strong> on the KYRO client portal. Set your password to activate your account and get started.
    </p>

    <x-emails.button :url="$url">Activate your account</x-emails.button>

    <p style="margin:0 0 16px; font-size:13px; line-height:1.6; color:#8a988f;">
        This invitation expires in {{ $expiresIn }}. If you weren&rsquo;t expecting this email, you can ignore it safely.
    </p>

    <p style="margin:0; font-size:12px; line-height:1.5; color:#b8c2bb; word-break:break-all;">
        If the button doesn&rsquo;t work, copy and paste this link into your browser:<br>
        <a href="{{ $url }}" style="color:#046c4e; text-decoration:underline;">{{ $url }}</a>
    </p>
</x-emails.layout>
