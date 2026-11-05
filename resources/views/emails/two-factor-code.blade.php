<x-emails.layout title="Your verification code" preview="Your KYRO verification code">
    <h1 style="margin:0 0 16px; font-family:'Inter Tight',Helvetica,Arial,sans-serif; font-size:21px; font-weight:600; color:#14201b; line-height:1.3;">
        Verify your sign-in
    </h1>
    <p style="margin:0 0 4px; font-size:15px; line-height:1.65; color:#3b4b44;">
        Use the code below to finish signing in to your KYRO account.
    </p>

    <x-emails.code>{{ $code }}</x-emails.code>

    <p style="margin:0; font-size:13px; line-height:1.6; color:#8a988f;">
        This code expires in {{ $expireMinutes }} minutes. If you didn&rsquo;t try to sign in, you can ignore this email &mdash; your account is still secure.
    </p>
</x-emails.layout>
