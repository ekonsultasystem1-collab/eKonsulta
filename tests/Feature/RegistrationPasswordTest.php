<?php

namespace Tests\Feature;

use App\Models\HealthCenter;
use App\Models\EmailOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_form_is_split_into_clear_steps(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Registration progress')
            ->assertSee('data-register-step="1"', false)
            ->assertSee('data-register-step="2"', false)
            ->assertSee('data-register-step="3"', false)
            ->assertSee('Personal information')
            ->assertSee('Contact and health center')
            ->assertSee('Account security');
    }

    public function test_registration_rejects_passwords_that_do_not_meet_the_password_policy(): void
    {
        $healthCenter = $this->createHealthCenter();

        foreach (['lowercase1', 'UPPERCASE1', 'Lowercase', 'Ab1'] as $index => $password) {
            $this->from(route('register'))
                ->post(route('register.store'), $this->registrationData(
                    $healthCenter,
                    "weak{$index}@gmail.com",
                    $password,
                    $password
                ))
                ->assertSessionHasErrors('password');
        }
    }

    public function test_registration_requires_matching_password_confirmation(): void
    {
        $healthCenter = $this->createHealthCenter();

        $this->from(route('register'))
            ->post(route('register.store'), $this->registrationData(
                $healthCenter,
                'mismatch@gmail.com',
                'StrongPass1',
                'DifferentPass2'
            ))
            ->assertSessionHasErrors('password');

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('data-initial-step="3"', false);
    }

    public function test_registration_accepts_a_password_that_meets_the_policy(): void
    {
        Mail::fake();
        $healthCenter = $this->createHealthCenter();

        $response = $this->post(route('register.store'), $this->registrationData(
            $healthCenter,
            'valid@gmail.com',
            'StrongPass1',
            'StrongPass1'
        ));

        $response->assertRedirect(route('register.verify'));
        $this->assertDatabaseHas('email_otps', [
            'email' => 'valid@gmail.com',
        ]);
    }

    public function test_patient_may_enter_a_birth_date_exactly_110_years_ago(): void
    {
        Mail::fake();
        $healthCenter = $this->createHealthCenter();
        $data = $this->registrationData(
            $healthCenter,
            '110-years-old@gmail.com',
            'StrongPass1',
            'StrongPass1'
        );
        $data['date_of_birth'] = today()->subYears(110)->toDateString();

        $this->post(route('register.store'), $data)
            ->assertRedirect(route('register.verify'));
    }

    public function test_suffix_rejects_numbers_and_birth_date_must_be_within_the_last_110_years(): void
    {
        $healthCenter = $this->createHealthCenter();
        $data = $this->registrationData(
            $healthCenter,
            'suffix-number@gmail.com',
            'StrongPass1',
            'StrongPass1'
        );
        $data['suffix'] = 'Jr2';

        $this->from(route('register'))
            ->post(route('register.store'), $data)
            ->assertSessionHasErrors('suffix');

        foreach ([today()->addDay()->toDateString(), today()->subYears(111)->toDateString()] as $date) {
            $data['email'] = 'dob-' . str_replace('-', '', $date) . '@gmail.com';
            $data['suffix'] = 'Jr.';
            $data['date_of_birth'] = $date;

            $this->from(route('register'))
                ->post(route('register.store'), $data)
                ->assertSessionHasErrors('date_of_birth');
        }
    }

    public function test_registration_birth_date_picker_is_limited_to_today_and_110_years_ago(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('min="' . now()->subYears(110)->toDateString() . '"', false)
            ->assertSee('max="' . today()->toDateString() . '"', false);
    }

    public function test_registration_code_expires_after_five_minutes_and_resend_is_rate_limited(): void
    {
        Mail::fake();
        $healthCenter = $this->createHealthCenter();
        $data = $this->registrationData(
            $healthCenter,
            'otp-cooldown@gmail.com',
            'StrongPass1',
            'StrongPass1'
        );

        $this->post(route('register.store'), $data)
            ->assertRedirect(route('register.verify'));

        $otp = EmailOtp::where('email', $data['email'])->firstOrFail();
        $this->assertSame(now()->addMinutes(5)->timestamp, $otp->expires_at->timestamp);
        $this->get(route('register.verify'))
            ->assertOk()
            ->assertSee('data-resend-button disabled', false);

        $this->from(route('register.verify'))
            ->post(route('register.resend-otp'))
            ->assertSessionHasErrors('otp');

        $this->travel(5)->minutes();

        $this->post(route('register.resend-otp'))
            ->assertRedirect(route('register.verify'))
            ->assertSessionHas('success');

        $this->assertSame(
            now()->addMinutes(5)->timestamp,
            EmailOtp::where('email', $data['email'])->firstOrFail()->expires_at->timestamp
        );
    }

    public function test_registration_rejects_numbers_or_symbols_in_name_fields(): void
    {
        $healthCenter = $this->createHealthCenter();
        $invalidNames = [
            'first_name' => 'Test2',
            'middle_name' => 'Middle3',
            'last_name' => 'Patient!',
            'emergency_contact_name' => 'Contact4',
        ];

        foreach ($invalidNames as $field => $value) {
            $data = $this->registrationData(
                $healthCenter,
                "{$field}@gmail.com",
                'StrongPass1',
                'StrongPass1'
            );
            $data[$field] = $value;

            $this->from(route('register'))
                ->post(route('register.store'), $data)
                ->assertSessionHasErrors($field);
        }
    }

    public function test_registration_requires_phone_numbers_to_be_exactly_eleven_digits(): void
    {
        $healthCenter = $this->createHealthCenter();
        $invalidPhoneNumbers = [
            'contact_number' => '09123ABC789',
            'emergency_contact_number' => '091234567890',
        ];

        foreach ($invalidPhoneNumbers as $field => $value) {
            $data = $this->registrationData(
                $healthCenter,
                "{$field}@gmail.com",
                'StrongPass1',
                'StrongPass1'
            );
            $data[$field] = $value;

            $this->from(route('register'))
                ->post(route('register.store'), $data)
                ->assertSessionHasErrors($field);
        }
    }

    private function createHealthCenter(): HealthCenter
    {
        return HealthCenter::create([
            'name' => 'Test Health Center',
            'barangay' => 'Test Barangay',
            'address' => 'Test Address',
            'status' => 'active',
        ]);
    }

    private function registrationData(
        HealthCenter $healthCenter,
        string $email,
        string $password,
        string $confirmation
    ): array {
        return [
            'first_name' => 'Test',
            'last_name' => 'Patient',
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $confirmation,
            'date_of_birth' => '1990-01-01',
            'sex' => 'Female',
            'contact_number' => '09123456789',
            'address' => 'Test Address',
            'barangay' => $healthCenter->barangay,
            'health_center_id' => $healthCenter->id,
            'emergency_contact_name' => 'Emergency Contact',
            'emergency_contact_number' => '09987654321',
        ];
    }
}
