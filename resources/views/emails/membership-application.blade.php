<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>ClearWash Membership Application Received</title></head>
<body style="margin:0;padding:24px 12px;background:#f9f4f5;color:#252328;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" style="width:100%;max-width:600px;margin:auto;border-collapse:collapse;background:#ffffff;border:1px solid #ebdde3;">
        <tr><td style="padding:28px 32px;border-bottom:3px solid #623547;"><img src="cid:clearwash-logo" width="280" alt="ClearWash logo" style="display:block;width:280px;max-width:100%;height:auto;border:0;"></td></tr>
        <tr><td style="padding:32px;">
            <h1 style="margin:0 0 24px;color:#291c24;font-size:24px;line-height:1.3;">Membership {{ strtolower($transaction->type) }} received</h1>
            <p style="margin:0 0 16px;font-size:16px;line-height:1.6;">Hello {{ $transaction->member->first_name }} {{ $transaction->member->last_name }},</p>
            <p style="margin:0 0 24px;font-size:16px;line-height:1.6;">We received your membership {{ strtolower($transaction->type) }}.</p>
            <table role="presentation" style="width:100%;border-collapse:collapse;background:#f9f4f5;">
                <tr><td style="padding:16px;border-bottom:1px solid #ebdde3;color:#623547;font-size:14px;">Reference number</td><td style="padding:16px;border-bottom:1px solid #ebdde3;text-align:right;font-weight:bold;overflow-wrap:anywhere;">{{ $transaction->reference_number }}</td></tr>
                <tr><td style="padding:16px;color:#623547;font-size:14px;">Status</td><td style="padding:16px;text-align:right;font-weight:bold;">{{ $transaction->status }}</td></tr>
            </table>
            <h2 style="margin:28px 0 12px;color:#291c24;font-size:18px;">{{ $transaction->type }} payment summary</h2>
            <table role="presentation" style="width:100%;border-collapse:collapse;">
                <tr><td style="padding:10px 0;border-bottom:1px solid #ebdde3;">Original membership fee</td><td style="padding:10px 0;border-bottom:1px solid #ebdde3;text-align:right;">₱{{ number_format((float) ($transaction->original_fee ?? $transaction->amount_due), 2) }}</td></tr>
                <tr><td style="padding:10px 0;border-bottom:1px solid #ebdde3;">Category discount ({{ number_format((float) $transaction->discount_percentage, 2) }}%)</td><td style="padding:10px 0;border-bottom:1px solid #ebdde3;text-align:right;">−₱{{ number_format((float) $transaction->discount_amount, 2) }}</td></tr>
                <tr><td style="padding:10px 0;border-bottom:1px solid #ebdde3;">Final amount due</td><td style="padding:10px 0;border-bottom:1px solid #ebdde3;text-align:right;">₱{{ number_format((float) $transaction->amount_due, 2) }}</td></tr>
                <tr><td style="padding:10px 0;border-bottom:1px solid #ebdde3;">Total paid</td><td style="padding:10px 0;border-bottom:1px solid #ebdde3;text-align:right;">₱{{ number_format($totalPaid, 2) }}</td></tr>
                <tr><td style="padding:10px 0;font-weight:bold;color:#623547;">Remaining balance</td><td style="padding:10px 0;text-align:right;font-weight:bold;color:#623547;">₱{{ number_format($remainingBalance, 2) }}</td></tr>
            </table>
            <p style="margin:24px 0 0;font-size:16px;line-height:1.6;">Keep this reference number for your records.</p>
            <p style="margin:24px 0 0;font-size:16px;">ClearWash</p>
        </td></tr>
    </table>
</body>
</html>
