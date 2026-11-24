<?php

namespace App\Notifications;

use App\Models\MembershipTransaction;
use App\TransactionType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Symfony\Component\Mime\Email;

class MembershipApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public MembershipTransaction $transaction
    ) {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $totalPaid = (float) $this->transaction->payments()->sum('amount');
        $remainingBalance = max(0, (float) $this->transaction->amount_due - $totalPaid);

        return (new MailMessage)
            ->subject($this->transaction->type === TransactionType::Renewal->value
                ? 'ClearWash Membership Renewal Received'
                : 'ClearWash Membership Application Received')
            ->view('emails.membership-application', [
                'transaction' => $this->transaction,
                'totalPaid' => $totalPaid,
                'remainingBalance' => $remainingBalance,
            ])
            ->withSymfonyMessage(fn (Email $message) => $message->embedFromPath(public_path('clearwash-email-logo.png'), 'clearwash-logo', 'image/png'));
    }
}
