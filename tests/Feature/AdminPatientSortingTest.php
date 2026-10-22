<?php

namespace Tests\Feature;

use App\Models\HealthCenter;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminPatientSortingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_patient_directory_sorts_by_creation_date_and_keeps_search_query(): void
    {
        $admin = $this->createUser('admin@example.test', 'Admin', 'admin');
        $healthCenter = HealthCenter::create([
            'name' => 'Test Health Center',
            'barangay' => 'Test Barangay',
            'address' => 'Test Address',
            'status' => 'active',
        ]);

        $olderPatient = $this->createPatient(
            $healthCenter,
            'older@example.test',
            'Older Patient',
            'PAT-OLDER',
            '2020-01-01 09:00:00'
        );
        $newerPatient = $this->createPatient(
            $healthCenter,
            'newer@example.test',
            'Newer Patient',
            'PAT-NEWER',
            '2025-01-01 09:00:00'
        );

        $this->actingAs($admin)
            ->get(route('admin.patients.index', ['sort' => 'oldest', 'search' => 'Patient']))
            ->assertOk()
            ->assertSee('value="Patient"', false)
            ->assertSeeInOrder(['Older Patient', 'Newer Patient']);

        $this->actingAs($admin)
            ->get(route('admin.patients.index', ['sort' => 'newest']))
            ->assertOk()
            ->assertSeeInOrder(['Newer Patient', 'Older Patient']);
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
        string $patientNumber,
        string $createdAt
    ): Patient {
        $user = $this->createUser($email, $name, 'patient');
        $date = Carbon::parse($createdAt);
        $user->forceFill(['created_at' => $date, 'updated_at' => $date])->saveQuietly();

        $patient = Patient::create([
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

        $patient->forceFill(['created_at' => $date, 'updated_at' => $date])->saveQuietly();

        return $patient;
    }
}
