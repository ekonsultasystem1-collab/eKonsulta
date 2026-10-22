<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\PasswordResetCodeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_links_to_the_password_reset_request_form(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('password.request'));

        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Forgot your password?')
            ->assertSee(route('password.email'));
    }

    public function test_reset_request_emails_a_six_digit_code_and_shows_code_entry(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect(route('password.otp'))
            ->assertSessionHas('status');

        Notification::assertSentTo($user, PasswordResetCodeNotification::class, function (
            PasswordResetCodeNotification $notification
        ) use ($user): bool {
            $message = $notification->toMail($user);

            return preg_match('/^\d{6}$/', $notification->code) === 1
                && $message->actionText === null
                && in_array('This code expires in 5 minutes and can only be used once.', $message->introLines, true);
        });

        $this->get(route('password.otp'))
            ->assertOk()
            ->assertSee('Enter your verification code')
            ->assertSee('expires in 5 minutes')
            ->assertSee('data-resend-button disabled', false)
            ->assertSee(route('password.verify'));
    }

    public function test_reset_request_does_not_disclose_unknown_email_addresses(): void
    {
        Notification::fake();
        $existingUser = User::factory()->create();

        $existingResponse = $this->post(route('password.email'), [
            'email' => $existingUser->email,
        ]);
        $unknownResponse = $this->post(route('password.email'), [
            'email' => 'not-registered@example.com',
        ]);

        $existingResponse->assertSessionHas('status');
        $unknownResponse->assertSessionHas('status', $existingResponse->getSession()->get('status'));
        Notification::assertSentTo($existingUser, PasswordResetCodeNotification::class);
        Notification::assertCount(1);
    }

    public function test_user_can_verify_code_and_reset_password(): void
    {
        Notification::fake();
        $user = User::factory()->create(['password' => 'OldPassword1']);
        $code = $this->requestCode($user);

        $this->get(route('password.reset'))
            ->assertRedirect(route('password.request'));

        $this->post(route('password.verify'), ['otp' => $code])
            ->assertRedirect(route('password.reset'));

        $this->get(route('password.reset'))
            ->assertOk()
            ->assertSee('Reset your password')
            ->assertSee('At least one uppercase letter')
            ->assertSee('At least one number');

        $this->post(route('password.update'), [
            'email' => $user->email,
            'password' => 'NewStrongPass1',
            'password_confirmation' => 'NewStrongPass1',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->assertTrue(password_verify('NewStrongPass1', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
        $this->get(route('password.reset'))->assertRedirect(route('password.request'));
    }

    public function test_reset_rejects_reusing_the_current_password(): void
    {
        Notification::fake();
        $user = User::factory()->create(['password' => 'CurrentStrong1']);
        $code = $this->requestCode($user);

        $this->post(route('password.verify'), ['otp' => $code])
            ->assertRedirect(route('password.reset'));

        $this->from(route('password.reset'))
            ->post(route('password.update'), [
                'email' => $user->email,
                'password' => 'CurrentStrong1',
                'password_confirmation' => 'CurrentStrong1',
            ])
            ->assertSessionHasErrors([
                'password' => 'Choose a new password that is different from your current password.',
            ]);

        $this->assertTrue(password_verify('CurrentStrong1', $user->fresh()->password));

        $this->post(route('password.update'), [
            'email' => $user->email,
            'password' => 'BrandNewPass2',
            'password_confirmation' => 'BrandNewPass2',
        ])
            ->assertRedirect(route('login'));
    }

    public function test_reset_enforces_password_rules_and_confirmation(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $code = $this->requestCode($user);

        $this->post(route('password.verify'), ['otp' => $code])
            ->assertRedirect(route('password.reset'));

        $this->from(route('password.reset'))
            ->post(route('password.update'), [
                'email' => $user->email,
                'password' => 'lowercase1',
                'password_confirmation' => 'lowercase1',
            ])
            ->assertSessionHasErrors('password');

        $this->from(route('password.reset'))
            ->post(route('password.update'), [
                'email' => $user->email,
                'password' => 'StrongPass1',
                'password_confirmation' => 'DifferentPass2',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_invalid_code_is_rejected_and_limited_attempts_block_verification(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $code = $this->requestCode($user);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->from(route('password.otp'))
                ->post(route('password.verify'), ['otp' => '000000'])
                ->assertSessionHasErrors('otp');
        }

        $this->travel(61)->seconds();

        $this->from(route('password.otp'))
            ->post(route('password.verify'), ['otp' => $code])
            ->assertSessionHasErrors('otp');

        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_expired_code_cannot_verify(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $code = $this->requestCode($user);

        $this->travel(11)->minutes();

        $this->from(route('password.otp'))
            ->post(route('password.verify'), ['otp' => $code])
            ->assertSessionHasErrors('otp');

        $this->travelBack();
    }

    public function test_reset_code_can_be_resent_after_five_minute_countdown(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect(route('password.otp'));

        $this->from(route('password.otp'))
            ->post(route('password.resend'))
            ->assertSessionHasErrors('otp');

        $this->travel(5)->minutes();

        $this->post(route('password.resend'))
            ->assertRedirect(route('password.otp'))
            ->assertSessionHas('status');

        Notification::assertSentToTimes($user, PasswordResetCodeNotification::class, 2);
    }

    private function requestCode(User $user): string
    {
        $this->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect(route('password.otp'));

        $code = null;
        Notification::assertSentTo($user, PasswordResetCodeNotification::class, function (
            PasswordResetCodeNotification $notification
        ) use (&$code): bool {
            $code = $notification->code;

            return true;
        });

        return $code;
    }
}
