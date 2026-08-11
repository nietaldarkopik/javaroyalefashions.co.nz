<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Invoice {{ $order->order_number }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #212529; background:#f4f4f4; padding:24px;">
<table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden;">
    <tr>
        <td style="background:#0d1117;color:#fff;padding:24px;">
            <h2 style="margin:0;">{{ config('app.name') }}</h2>
            <p style="margin:4px 0 0;color:#9aa0a6;">Order Invoice</p>
        </td>
    </tr>
    <tr>
        <td style="padding:24px;">
            <p>Hi {{ $order->customer_name }},</p>
            <p>Thank you for your order! Here's your invoice for reference.</p>

            <table width="100%" style="margin:16px 0; font-size:14px;">
                <tr><td><strong>Order Number:</strong></td><td>{{ $order->order_number }}</td></tr>
                <tr><td><strong>Order Date:</strong></td><td>{{ $order->created_at->format('d M Y, H:i') }}</td></tr>
                <tr><td><strong>Payment Method:</strong></td><td>Bank Transfer</td></tr>
            </table>

            <table width="100%" cellpadding="8" style="border-collapse:collapse; font-size:14px;">
                <thead>
                    <tr style="background:#f1f3f5;">
                        <th align="left">Item</th>
                        <th align="center">Qty</th>
                        <th align="right">Price</th>
                        <th align="right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                    <tr style="border-bottom:1px solid #eee;">
                        <td>{{ $item->product_name }}@if ($item->variant_label)<br><small>{{ $item->variant_label }}</small>@endif</td>
                        <td align="center">{{ $item->quantity }}</td>
                        <td align="right">${{ number_format($item->unit_price, 2) }}</td>
                        <td align="right">${{ number_format($item->line_total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <table width="100%" style="margin-top:12px; font-size:14px;">
                <tr><td align="right" width="80%">Subtotal</td><td align="right">${{ number_format($order->subtotal, 2) }}</td></tr>
                <tr><td align="right">Shipping ({{ $order->shipping_area->label() }})</td><td align="right">${{ number_format($order->shipping_cost, 2) }}</td></tr>
                <tr style="font-weight:bold; font-size:16px;"><td align="right">Total</td><td align="right">${{ number_format($order->grand_total, 2) }} {{ $order->currency }}</td></tr>
            </table>

            <h4 style="margin-top:24px;">Shipping To</h4>
            <p style="font-size:14px;">
                @foreach ($order->shippingAddressLines() as $line)
                    {{ $line }}<br>
                @endforeach
            </p>

            <p style="margin-top:24px;font-size:13px;color:#6c757d;">
                Payment is by manual bank transfer. Please check your order confirmation page for our bank
                account details and to upload your proof of payment if you haven't already.
            </p>
        </td>
    </tr>
    <tr>
        <td style="padding:16px 24px;background:#f8f9fa;font-size:12px;color:#868e96;text-align:center;">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </td>
    </tr>
</table>
</body>
</html>
