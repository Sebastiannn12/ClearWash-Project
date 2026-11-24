<?php
namespace App\Notifications;

use App\Models\MembershipTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
        return (new MailMessage)
            ->subject('ClearWash Membership Application Received')
            ->greeting('Hello ' . $this->transaction->member->name . ',')
            ->line('We received your membership application.')
            ->line('Reference number: ' . $this->transaction->reference_number)
            ->line('Status: Pending')
            ->line('Keep this reference number for your records.')
            ->salutation('ClearWash');
    }
}
