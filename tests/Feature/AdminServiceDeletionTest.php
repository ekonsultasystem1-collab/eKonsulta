<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AppointmentSchedule;
use App\Models\Doctor;
use App\Models\HealthCenter;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminServiceDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_with_an_appointment_is_removed_from_active_lists_and_history_is_preserved(): void
    {
        $admin = $this->createAdmin();
        $service = $this->createService();
        $healthCenter = $this->createHealthCenter();
        $patientUser = $this->createUser('patient@example.test', 'patient');
        $doctorUser = $this->createUser('doctor@example.test', 'doctor');
        $patient = Patient::create([
            'user_id' => $patientUser->id,
            'health_center_id' => $healthCenter->id,
            'patient_number' => 'PAT-TEST-001',
            'date_of_birth' => '1990-01-01',
            'sex' => 'Female',
            'contact_number' => '09123456789',
            'address' => 'Test Address',
            'emergency_contact_name' => 'Emergency Contact',
            'emergency_contact_number' => '09987654321',
        ]);
        $doctor = Doctor::create([
            'user_id' => $doctorUser->id,
            'health_center_id' => $healthCenter->id,
            'license_number' => 'LICENSE-TEST-001',
        ]);
        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'health_center_id' => $healthCenter->id,
            'service_id' => $service->id,
            'appointment_date' => today(),
            'appointment_time' => '09:00:00',
            'reason' => 'Routine checkup',
            'status' => 'pending',
        ]);

        $this->followingRedirects()
            ->actingAs($admin)
            ->delete(route('admin.services.destroy', $service))
            ->assertOk()
            ->assertSee('Service removed from active lists. Linked appointment and schedule history was preserved.')
            ->assertDontSee('General Consultation');

        $this->assertDatabaseHas('services', ['id' => $service->id]);
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id]);
        $this->assertNotNull($service->fresh()->deleted_at);
        $this->assertFalse($service->fresh()->status);
        $this->assertSame($service->id, $appointment->fresh()->service->id);
    }

    public function test_service_with_a_schedule_is_removed_from_active_lists_and_schedule_history_is_preserved(): void
    {
        $admin = $this->createAdmin();
        $healthCenter = $this->createHealthCenter();
        $service = $this->createService();
        $schedule = AppointmentSchedule::create([
            'health_center_id' => $healthCenter->id,
            'service_id' => $service->id,
            'schedule_date' => today(),
            'appointment_time' => '09:00:00',
            'capacity' => 1,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.services.destroy', $service))
            ->assertRedirect(route('admin.services.index'))
            ->assertSessionHas('success', 'Service removed from active lists. Linked appointment and schedule history was preserved.');

        $this->assertDatabaseHas('services', ['id' => $service->id]);
        $this->assertDatabaseHas('appointment_schedules', ['id' => $schedule->id]);
        $this->assertNotNull($service->fresh()->deleted_at);
        $this->assertSame($service->id, $schedule->fresh()->service->id);
    }

    public function test_service_without_linked_records_is_removed_from_active_lists(): void
    {
        $admin = $this->createAdmin();
        $service = $this->createService();

        $this->actingAs($admin)
            ->delete(route('admin.services.destroy', $service))
            ->assertRedirect(route('admin.services.index'))
            ->assertSessionHas('success', 'Service removed from active lists. Linked appointment and schedule history was preserved.');

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
        ]);
        $this->assertNotNull($service->fresh()->deleted_at);
    }

    private function createAdmin(): User
    {
        return $this->createUser('admin@example.test', 'admin');
    }

    private function createUser(string $email, string $role): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $email,
            'password' => 'password',
            'role' => $role,
        ]);
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

    private function createService(): Service
    {
        return Service::create([
            'name' => 'General Consultation',
            'code' => 'GENERAL',
            'status' => true,
        ]);
    }
}
