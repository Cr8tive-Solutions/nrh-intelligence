{{-- One-time verification code, shown in a bordered emerald panel. --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:28px 0;">
    <tr>
        <td align="center" style="background-color:#ecfdf5; border:1px solid #d1fae5; border-radius:8px; padding:22px 16px;">
            <div style="font-family:'Inter Tight',Helvetica,Arial,sans-serif; font-size:11px; font-weight:600; letter-spacing:2px; text-transform:uppercase; color:#059669; margin-bottom:10px;">
                Verification code
            </div>
            <div style="font-family:'Courier New',Courier,monospace; font-size:34px; font-weight:700; letter-spacing:10px; color:#023527; line-height:1;">
                {{ $slot }}
            </div>
        </td>
    </tr>
</table>
