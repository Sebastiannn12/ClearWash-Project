<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\User;
use App\Notifications\MembershipApplicationSubmitted;
use App\Notifications\MembershipApproved;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RenewalEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_renewal_sends_a_new_email_and_payment_receipt_for_its_own_transaction(): void
    {
        Notification::fake();
        $this->actingAs(User::factory()->create());
        $level = MembershipLevel::where('name', 'Silver')->firstOrFail();
        $this->post(route('members.store'), [
            'first_name' => 'Alex', 'last_name' => 'Reyes',
            'email' => 'alex@example.com', 'phone' => '09171234567',
            'membership_category_id' => MembershipCategory::where('name', 'Regular')->firstOrFail()->id,
            'membership_level_id' => $level->id,
            'joined_on' => now()->toDateString(),
        ])->assertRedirect()->assertSessionHasNoErrors();
        $member = Member::where('email', 'alex@example.com')->firstOrFail();
        $application = $member->membershipTransactions()->sole();
        $level = MembershipLevel::where('name', 'Silver')->firstOrFail();
        Notification::fake();

        $this->post(route('transactions.store'), [
            'member_id' => $member->id, 'membership_level_id' => $level->id,
            'type' => 'Renewal',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $renewal = $member->membershipTransactions()->where('type', 'Renewal')->sole();
        Notification::assertSentOnDemand(MembershipApplicationSubmitted::class,
            function ($notification, $channels, $notifiable) use ($renewal, $member): bool {
                $mail = $notification->toMail($notifiable);
                $this->assertSame('ClearWash Membership Renewal Received', $mail->subject);
                $html = view($mail->view, $mail->viewData)->render();
                $this->assertStringContainsString('Membership renewal received', $html);
                $this->assertStringContainsString($renewal->reference_number, $html);

                return $notification->transaction->is($renewal)
                    && $notifiable->routes['mail'] === $member->email;
            });

        $this->post(route('transactions.payments.store', $renewal), [
            'payment_reference' => 'RENEWAL-PAYMENT',
            'amount' => $renewal->amount_due, 'method' => 'Cash',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $payment = $renewal->payments()->sole();
        Notification::assertSentOnDemand(MembershipApproved::class,
            function ($notification, $channels, $notifiable) use ($renewal, $payment, $application): bool {
                $mail = $notification->toMail($notifiable);
                $this->assertSame($payment->id, $mail->viewData['payment']->id);
                $this->assertStringContainsString('/receipt/'.$payment->id.'?', $mail->viewData['receiptUrl']);
                $html = view($mail->view, $mail->viewData)->render();
                $this->assertStringContainsString($renewal->reference_number, $html);
                $this->assertStringNotContainsString($application->reference_number, $html);

                return $notification->transaction->is($renewal);
            });
    }
}
