<x-emails.layout title="Security alert" preview="A change was requested on your KYRO account">
    <h1 style="margin:0 0 16px; font-family:'Inter Tight',Helvetica,Arial,sans-serif; font-size:21px; font-weight:600; color:#14201b; line-height:1.3;">
        Security alert
    </h1>
    <p style="margin:0 0 16px; font-size:15px; line-height:1.65; color:#3b4b44;">
        A request was made to change the email address on your KYRO account to:
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px;">
        <tr>
            <td style="background-color:#f3f4ee; border:1px solid #e6e4d8; border-radius:8px; padding:14px 18px; font-family:'Inter Tight',Helvetica,Arial,sans-serif; font-size:15px; font-weight:600; color:#14201b; word-break:break-all;">
                {{ $newEmail }}
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px; font-size:15px; line-height:1.65; color:#3b4b44;">
        If this was you, no action is needed &mdash; just confirm the change from the link sent to the new address.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 4px;">
        <tr>
            <td style="background-color:#fdf3f2; border:1px solid #f0d5d2; border-left:4px solid #c4453a; border-radius:6px; padding:14px 18px; font-family:'Inter Tight',Helvetica,Arial,sans-serif; font-size:14px; line-height:1.6; color:#8a3a32;">
                <strong style="color:#c4453a;">If this was not you</strong>, change your password immediately and contact your administrator &mdash; your account may be at risk.
            </td>
        </tr>
    </table>
</x-emails.layout>
