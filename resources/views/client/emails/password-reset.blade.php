<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <title>Reset your NRH Intelligence password</title>
    <!--[if mso]><style>table,td,div,p,a{font-family:Arial,sans-serif !important;}</style><![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#f1f4f3;-webkit-font-smoothing:antialiased;">
    <!-- Preheader (hidden) -->
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;mso-hide:all;">
        Use the button inside to set a new password. This link expires in {{ $expireMinutes }} minutes.
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f4f3;">
        <tr>
            <td align="center" style="padding:32px 16px;">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;">

                    <!-- Brand bar -->
                    <tr>
                        <td style="background-color:#0f2a22;border-radius:12px 12px 0 0;padding:22px 32px;">
                            <span style="font-family:Arial,Helvetica,sans-serif;font-size:17px;font-weight:700;letter-spacing:0.5px;color:#ffffff;">
                                NRH <span style="color:#34d399;">Intelligence</span>
                            </span>
                        </td>
                    </tr>

                    <!-- Card body -->
                    <tr>
                        <td style="background-color:#ffffff;padding:36px 32px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:700;letter-spacing:1.5px;color:#059669;text-transform:uppercase;padding-bottom:10px;">
                                        Account Recovery
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-family:Arial,Helvetica,sans-serif;font-size:22px;font-weight:700;color:#111827;line-height:1.3;padding-bottom:16px;">
                                        Reset your password
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-family:Arial,Helvetica,sans-serif;font-size:15px;color:#374151;line-height:1.6;padding-bottom:12px;">
                                        Hi {{ $name }},
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-family:Arial,Helvetica,sans-serif;font-size:15px;color:#374151;line-height:1.6;padding-bottom:24px;">
                                        We received a request to reset the password for your NRH Intelligence
                                        portal account. Click the button below to choose a new password.
                                    </td>
                                </tr>

                                <!-- CTA button (bulletproof) -->
                                <tr>
                                    <td align="center" style="padding-bottom:24px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center" bgcolor="#059669" style="border-radius:8px;">
                                                    <a href="{{ $url }}" target="_blank"
                                                       style="display:inline-block;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;padding:14px 36px;border-radius:8px;background-color:#059669;">
                                                        Reset password
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#6b7280;line-height:1.6;padding-bottom:18px;">
                                        This link expires in <strong style="color:#374151;">{{ $expireMinutes }} minutes</strong>.
                                        If you didn&rsquo;t request a password reset, you can safely ignore this
                                        email &mdash; your password won&rsquo;t change.
                                    </td>
                                </tr>

                                <!-- Fallback link -->
                                <tr>
                                    <td style="border-top:1px solid #e5e7eb;padding-top:18px;">
                                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#9ca3af;line-height:1.5;padding-bottom:6px;">
                                            If the button doesn&rsquo;t work, copy and paste this link into your browser:
                                        </div>
                                        <a href="{{ $url }}" target="_blank"
                                           style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#059669;word-break:break-all;line-height:1.5;">{{ $url }}</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#ffffff;border-radius:0 0 12px 12px;border-top:1px solid #f1f4f3;padding:20px 32px 26px;">
                            <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#9ca3af;line-height:1.6;">
                                This is an automated message from NRH Intelligence. Please do not reply to this email.
                            </div>
                            <div style="font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#c4cbc8;line-height:1.6;padding-top:8px;">
                                &copy; {{ date('Y') }} NRH Intelligence Sdn. Bhd. All rights reserved.
                            </div>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>
</html>
