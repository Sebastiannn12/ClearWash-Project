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
        $applicationSnapshot = $application->getAttributes();
        $originalCategoryId = $member->membership_category_id;
        $originalLevelId = $member->membership_level_id;
        $level = MembershipLevel::where('name', 'Gold')->firstOrFail();
        $level->update(['fee' => 1000]);
        $category = MembershipCategory::where('name', 'Student')->firstOrFail();
        $category->update(['discount_percentage' => 15]);
        Notification::fake();

        $this->post(route('transactions.store'), [
            'member_id' => $member->id, 'membership_level_id' => $level->id,
            'membership_category_id' => $category->id,
            'type' => 'Renewal',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $renewal = $member->membershipTransactions()->where('type', 'Renewal')->sole();
        $this->assertSame('1000.00', $renewal->original_fee);
        $this->assertSame('15.00', $renewal->discount_percentage);
        $this->assertSame('150.00', $renewal->discount_amount);
        $this->assertSame('850.00', $renewal->amount_due);
        $this->assertSame($originalCategoryId, $member->fresh()->membership_category_id);
        $this->assertSame($originalLevelId, $member->fresh()->membership_level_id);
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

        $this->patch(route('transactions.approve', $renewal))->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame($category->id, $member->fresh()->membership_category_id);
        $this->assertSame($level->id, $member->fresh()->membership_level_id);
        $this->assertSame($applicationSnapshot, $application->fresh()->getAttributes());
    }
}
