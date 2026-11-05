<x-emails.layout title="Reset your password" preview="Reset your KYRO password">
    <div style="font-family:'Inter Tight',Helvetica,Arial,sans-serif; font-size:11px; font-weight:600; letter-spacing:2px; text-transform:uppercase; color:#059669; margin:0 0 10px;">
        Account recovery
    </div>
    <h1 style="margin:0 0 16px; font-family:'Inter Tight',Helvetica,Arial,sans-serif; font-size:21px; font-weight:600; color:#14201b; line-height:1.3;">
        Reset your password
    </h1>
    <p style="margin:0 0 16px; font-size:15px; line-height:1.65; color:#3b4b44;">
        Hi {{ $name }},
    </p>
    <p style="margin:0 0 16px; font-size:15px; line-height:1.65; color:#3b4b44;">
        We received a request to reset the password for your KYRO portal account. Click the button below to choose a new password.
    </p>

    <x-emails.button :url="$url">Reset password</x-emails.button>

    <p style="margin:0 0 16px; font-size:13px; line-height:1.6; color:#8a988f;">
        This link expires in {{ $expireMinutes }} minutes. If you didn&rsquo;t request a password reset, you can safely ignore this email &mdash; your password won&rsquo;t change.
    </p>

    <p style="margin:0; font-size:12px; line-height:1.5; color:#b8c2bb; word-break:break-all;">
        If the button doesn&rsquo;t work, copy and paste this link into your browser:<br>
        <a href="{{ $url }}" style="color:#046c4e; text-decoration:underline;">{{ $url }}</a>
    </p>
</x-emails.layout>
