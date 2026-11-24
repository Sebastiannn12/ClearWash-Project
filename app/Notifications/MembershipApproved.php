<?php

namespace App\Notifications;

use App\Models\MembershipTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Symfony\Component\Mime\Email;

class MembershipApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public MembershipTransaction $transaction, public int $paidInCents, public ?int $paymentId = null, public string $eventStatus = 'Approved')
    {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $transaction = $this->transaction->load(['member', 'payments']);
        $payment = $this->paymentId ? $transaction->payments->firstWhere('id', $this->paymentId) : $transaction->payments->sortByDesc('id')->first();
        $receiptUrl = $payment ? URL::temporarySignedRoute('payments.public-receipt', now()->addDays(30), ['payment' => $payment->id]) : null;

        return (new MailMessage)
            ->subject($this->eventStatus === 'Approved' ? 'ClearWash Membership Approved' : 'ClearWash Payment Confirmation')
            ->view('emails.membership-approved', ['transaction' => $transaction, 'payment' => $payment, 'paidInCents' => $this->paidInCents, 'receiptUrl' => $receiptUrl, 'eventStatus' => $this->eventStatus])
            ->withSymfonyMessage(fn (Email $message) => $message->embedFromPath(public_path('clearwash-email-logo.png'), 'clearwash-logo', 'image/png'));
    }
}
