<?php

namespace Tests\Feature;

use App\Models\HealthCenter;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPatientSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_patient_search_filters_results_and_keeps_search_value_in_form(): void
    {
        $admin = $this->createUser('admin@example.test', 'Admin', 'admin');
        $healthCenter = HealthCenter::create([
            'name' => 'Test Health Center',
            'barangay' => 'Test Barangay',
            'address' => 'Test Address',
            'status' => 'active',
        ]);

        $this->createPatient($healthCenter, 'matching@example.test', 'Matching Patient', 'PAT-MATCH');
        $this->createPatient($healthCenter, 'other@example.test', 'Other Patient', 'PAT-OTHER');

        $this->actingAs($admin)
            ->get(route('admin.patients.index', ['search' => 'Matching']))
            ->assertOk()
            ->assertSee('value="Matching"', false)
            ->assertSee('Matching Patient')
            ->assertDontSee('Other Patient');
    }

    private function createUser(string $email, string $name, string $role): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => 'password',
            'role' => $role,
        ]);
    }

    private function createPatient(
        HealthCenter $healthCenter,
        string $email,
        string $name,
        string $patientNumber
    ): Patient {
        $user = $this->createUser($email, $name, 'patient');

        return Patient::create([
            'user_id' => $user->id,
            'health_center_id' => $healthCenter->id,
            'patient_number' => $patientNumber,
            'date_of_birth' => '1990-01-01',
            'sex' => 'Female',
            'contact_number' => '09123456789',
            'address' => 'Test Address',
            'emergency_contact_name' => 'Emergency Contact',
            'emergency_contact_number' => '09987654321',
        ]);
    }
}
