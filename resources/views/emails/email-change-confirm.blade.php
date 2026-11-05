<x-emails.layout title="Confirm your new email" preview="Confirm your new KYRO email address">
    <h1 style="margin:0 0 16px; font-family:'Inter Tight',Helvetica,Arial,sans-serif; font-size:21px; font-weight:600; color:#14201b; line-height:1.3;">
        Confirm your new email address
    </h1>
    <p style="margin:0 0 16px; font-size:15px; line-height:1.65; color:#3b4b44;">
        A request was made to change the email address on your KYRO account to <strong style="color:#14201b;">this address</strong>. Confirm the change to make it your new sign-in email.
    </p>

    <x-emails.button :url="$url">Confirm email change</x-emails.button>

    <p style="margin:0 0 16px; font-size:13px; line-height:1.6; color:#8a988f;">
        This link expires in one hour. If you didn&rsquo;t request this change, you can ignore this email and your address will stay the same.
    </p>

    <p style="margin:0; font-size:12px; line-height:1.5; color:#b8c2bb; word-break:break-all;">
        If the button doesn&rsquo;t work, copy and paste this link into your browser:<br>
        <a href="{{ $url }}" style="color:#046c4e; text-decoration:underline;">{{ $url }}</a>
    </p>
</x-emails.layout>
