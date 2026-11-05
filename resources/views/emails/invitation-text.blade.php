<x-emails.text-layout>You've been invited

Hi {!! $name !!},

{!! $inviterName ? $inviterName.' has invited you' : "You've been invited" !!} to join {!! $companyName !!} on the KYRO client portal.

Set your password to activate your account by opening the link below:

{!! $url !!}

This invitation expires in {!! $expiresIn !!}. If you weren't expecting this email, you can ignore it safely.</x-emails.text-layout>
