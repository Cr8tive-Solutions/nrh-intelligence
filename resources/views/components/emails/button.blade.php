@props([
    'url' => '#',
])

{{-- Brand CTA button. Uses a VML button for Outlook (which ignores padding on
     <a>) and a padded anchor everywhere else. --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:28px 0;">
    <tr>
        <td align="center">
            <!--[if mso]>
            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $url }}" style="height:46px;v-text-anchor:middle;width:280px;" arcsize="13%" strokecolor="#046c4e" fillcolor="#046c4e">
            <w:anchorlock/>
            <center style="color:#ffffff;font-family:Helvetica,Arial,sans-serif;font-size:15px;font-weight:600;">
            {{ $slot }}
            </center>
            </v:roundrect>
            <![endif]-->
            <!--[if !mso]><!-->
            <a href="{{ $url }}" target="_blank" style="display:inline-block; background-color:#046c4e; color:#ffffff; font-family:'Inter Tight',Helvetica,Arial,sans-serif; font-size:15px; font-weight:600; line-height:46px; text-align:center; text-decoration:none; padding:0 34px; border-radius:6px; min-width:200px; mso-hide:all;">
                {{ $slot }}
            </a>
            <!--<![endif]-->
        </td>
    </tr>
</table>
