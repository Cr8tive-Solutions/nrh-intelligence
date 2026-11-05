<x-emails.layout title="Payment slip {{ $action }}" preview="A payment slip is awaiting verification">
    <div style="font-family:'Inter Tight',Helvetica,Arial,sans-serif; font-size:11px; font-weight:600; letter-spacing:2px; text-transform:uppercase; color:#059669; margin:0 0 10px;">
        Awaiting verification
    </div>
    <h1 style="margin:0 0 16px; font-family:'Inter Tight',Helvetica,Arial,sans-serif; font-size:21px; font-weight:600; color:#14201b; line-height:1.3;">
        Payment slip {{ $action }}
    </h1>
    <p style="margin:0 0 20px; font-size:15px; line-height:1.65; color:#3b4b44;">
        A payment slip has been {{ $action }} and is awaiting verification.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px; border:1px solid #e6e4d8; border-radius:8px; overflow:hidden;">
        @foreach ([
            ['Request', $reference],
            ['Customer', $customerName],
            ['Uploaded by', $uploadedBy],
            ['Uploaded at', $uploadedAt],
            ['Filename', $filename],
        ] as $i => [$label, $value])
            <tr>
                <td style="background-color:{{ $i % 2 ? '#ffffff' : '#f9faf6' }}; padding:11px 18px; font-family:'Inter Tight',Helvetica,Arial,sans-serif; font-size:12px; font-weight:600; letter-spacing:0.4px; text-transform:uppercase; color:#8a988f; width:140px; vertical-align:top; border-bottom:{{ $loop->last ? '0' : '1px solid #ebeee9' }};">
                    {{ $label }}
                </td>
                <td style="background-color:{{ $i % 2 ? '#ffffff' : '#f9faf6' }}; padding:11px 18px; font-family:'Inter Tight',Helvetica,Arial,sans-serif; font-size:14px; color:#14201b; word-break:break-word; border-bottom:{{ $loop->last ? '0' : '1px solid #ebeee9' }};">
                    {{ $value }}
                </td>
            </tr>
        @endforeach
    </table>

    <p style="margin:0; font-size:15px; line-height:1.65; color:#3b4b44;">
        Log into the admin portal to view the slip and verify payment.
    </p>
</x-emails.layout>
