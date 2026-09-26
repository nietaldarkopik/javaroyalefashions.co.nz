<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Review your products</title>
</head>
<body style="font-family: Arial, sans-serif; color: #121212; background:#f4f4f4; padding:24px; margin:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden;">
    <tr>
        <td style="background:#121212;color:#fff;padding:24px;">
            <h2 style="margin:0;">{{ $siteName }}</h2>
            <p style="margin:4px 0 0;color:#bdbdbd;">How was your purchase?</p>
        </td>
    </tr>
    <tr>
        <td style="padding:24px;">
            <p>Hi {{ $order->customer_name }},</p>
            <p>Thank you for shopping with us. We'd love to hear what you think of the products from your order
                <strong>{{ $order->order_number }}</strong>.</p>
            <p>You can review every product from this purchase in one place &mdash; rate as many or as few as you like.</p>

            <table width="100%" cellpadding="8" style="border-collapse:collapse; font-size:14px; margin:16px 0;">
                @foreach ($order->reviewableItems() as $line)
                <tr style="border-bottom:1px solid #eee;">
                    <td>
                        {{ $line['item']->product_name }}
                        @if ($line['variant_labels']->isNotEmpty())
                        <br><small style="color:#6c757d;">{{ $line['variant_labels']->implode(', ') }}</small>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>

            <table cellpadding="0" cellspacing="0" style="margin:24px auto;">
                <tr>
                    <td style="background:#A8532E;border-radius:999px;">
                        <a href="{{ $reviewUrl }}" style="display:inline-block;padding:14px 32px;color:#fff;text-decoration:none;font-weight:bold;font-size:14px;letter-spacing:0.04em;text-transform:uppercase;">Review Your Products</a>
                    </td>
                </tr>
            </table>

            <p style="font-size:13px;color:#6c757d;">
                This link is personal to your order &mdash; please don't forward it.
                It expires on {{ $expiresAt->format('j F Y') }}.
            </p>
            <p style="font-size:12px;color:#868e96;word-break:break-all;">
                If the button doesn't work, copy this address into your browser:<br>{{ $reviewUrl }}
            </p>
        </td>
    </tr>
    <tr>
        <td style="padding:16px 24px;background:#f8f9fa;font-size:12px;color:#868e96;text-align:center;">
            &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
        </td>
    </tr>
</table>
</body>
</html>
