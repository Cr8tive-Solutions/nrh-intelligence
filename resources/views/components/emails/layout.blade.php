@props([
    'title' => 'KYRO',
    'preview' => null,
])

{{--
    Shared email layout for every message the portal sends.

    Email clients (especially Outlook) strip <style> blocks, ignore flexbox and
    CSS gradients, and reset fonts — so this template is deliberately built with
    nested tables and inline styles only. Colours mirror the portal brand tokens
    in resources/css/app.css (emerald + gold on paper).
--}}
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light only">
    <meta name="supported-color-schemes" content="light only">
    <title>{{ $title }}</title>
</head>
<body style="margin:0; padding:0; width:100%; background-color:#f5f3ea; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">

    {{-- Preview text (shows in the inbox list, hidden in the body) --}}
    @if ($preview)
        <div style="display:none; max-height:0; overflow:hidden; mso-hide:all; font-size:1px; line-height:1px; color:#f5f3ea; opacity:0;">
            {{ $preview }}
        </div>
    @endif

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f5f3ea;">
        <tr>
            <td align="center" style="padding:32px 16px;">

                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px; max-width:600px; margin:0 auto;">

                    {{-- Header / wordmark --}}
                    <tr>
                        <td style="background-color:#023527; border-radius:10px 10px 0 0; padding:26px 32px; border-bottom:3px solid #d4af37;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="font-family:'Inter Tight',Helvetica,Arial,sans-serif; font-size:20px; font-weight:600; letter-spacing:0.3px; color:#ffffff; line-height:1.1;">
                                        KYRO
                                        <div style="font-family:'Inter Tight',Helvetica,Arial,sans-serif; font-size:10px; font-weight:400; letter-spacing:2.6px; text-transform:uppercase; color:#8fb3a3; margin-top:6px;">
                                            by NRH Intelligence
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Body card --}}
                    <tr>
                        <td style="background-color:#ffffff; border-left:1px solid #e6e4d8; border-right:1px solid #e6e4d8; padding:36px 32px 40px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="font-family:'Inter Tight',Helvetica,Arial,sans-serif; font-size:15px; line-height:1.65; color:#3b4b44;">
                                        {{ $slot }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color:#ffffff; border:1px solid #e6e4d8; border-top:1px solid #ebeee9; border-radius:0 0 10px 10px; padding:24px 32px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="font-family:'Inter Tight',Helvetica,Arial,sans-serif; font-size:12px; line-height:1.6; color:#8a988f;">
                                        This is an automated message from the KYRO client portal.
                                        Please do not reply to this email &mdash; this inbox is not monitored.
                                        <div style="margin-top:12px; color:#b8c2bb;">
                                            &copy; {{ date('Y') }} NRH Intelligence. All rights reserved.
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
