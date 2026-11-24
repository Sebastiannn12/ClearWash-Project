<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>ClearWash Membership Approved</title></head>
<body style="margin:0;padding:24px 12px;background:#f9f4f5;color:#252328;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" style="width:100%;max-width:600px;margin:auto;border-collapse:collapse;background:#fff;border:1px solid #ebdde3;">
<tr><td style="padding:28px 32px;border-bottom:3px solid #623547;"><img src="cid:clearwash-logo" width="64" alt="ClearWash logo" style="vertical-align:middle;border:0;"> <span style="font-size:22px;font-weight:bold;color:#623547;vertical-align:middle;">ClearWash</span></td></tr>
<tr><td style="padding:32px;"><h1 style="margin:0 0 24px;color:#291c24;font-size:24px;">{{ $eventStatus === 'Approved' ? 'Membership approved' : 'Payment confirmation' }}</h1>
<p style="font-size:16px;line-height:1.6;">Hello {{ $transaction->member->full_name }},</p><p style="font-size:16px;line-height:1.6;">{{ $eventStatus === 'Approved' ? 'Your membership transaction has been approved.' : 'We received your payment.' }}</p>
<table role="presentation" style="width:100%;border-collapse:collapse;background:#f9f4f5;">
@foreach (['Membership number' => $transaction->member->membership_number, 'Category' => $transaction->category_name, 'Level' => $transaction->level_name, 'Transaction reference' => $transaction->reference_number, 'Status' => $eventStatus, 'Original fee' => '₱'.number_format((float) ($transaction->original_fee ?? $transaction->amount_due), 2), 'Discount' => number_format((float) $transaction->discount_percentage, 2).'% · ₱'.number_format((float) $transaction->discount_amount, 2), 'Amount due' => '₱'.number_format((float) $transaction->amount_due, 2), 'Total paid' => '₱'.number_format($paidInCents / 100, 2), 'Remaining balance' => '₱'.number_format(max(0, (float) $transaction->amount_due - $paidInCents / 100), 2)] as $label => $value)
<tr><td style="padding:12px;border-bottom:1px solid #ebdde3;color:#623547;">{{ $label }}</td><td style="padding:12px;border-bottom:1px solid #ebdde3;text-align:right;overflow-wrap:anywhere;">{{ $value }}</td></tr>
@endforeach
@if ($payment)<tr><td style="padding:12px;color:#623547;">Latest payment</td><td style="padding:12px;text-align:right;">{{ $payment->payment_reference }} · {{ $payment->method }} · ₱{{ number_format((float) $payment->amount, 2) }}</td></tr>@endif
</table>
@if ($receiptUrl)<p style="margin:28px 0;"><a href="{{ $receiptUrl }}" style="display:inline-block;padding:12px 20px;background:#623547;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;">View printable receipt</a></p><p style="font-size:13px;color:#625660;">This receipt link expires in 30 days.</p>@endif
<p style="margin:24px 0 0;">ClearWash</p></td></tr></table></body></html>
