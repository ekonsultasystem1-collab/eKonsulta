<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AppointmentSchedule;
use App\Models\Doctor;
use App\Models\HealthCenter;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentApprovalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_sees_confirmation_after_booking_an_appointment(): void
    {
        $healthCenter = $this->createHealthCenter('Booking Health Center');
        $service = Service::create([
            'name' => 'General Consultation',
            'code' => 'BOOK-GENERAL',
            'status' => true,
        ]);
        $healthCenter->services()->attach($service);

        $patientUser = $this->createUser('Booking Patient', 'patient', 'booking-patient@example.test');
        $patient = Patient::create([
            'user_id' => $patientUser->id,
            'health_center_id' => $healthCenter->id,
            'patient_number' => 'PAT-BOOK-001',
            'date_of_birth' => '1990-01-01',
            'sex' => 'Female',
            'contact_number' => '09123456789',
            'address' => 'Test Address',
            'emergency_contact_name' => 'Emergency Contact',
            'emergency_contact_number' => '09987654321',
        ]);
        $doctorUser = $this->createUser('Booking Doctor', 'doctor', 'booking-doctor@example.test');
        $doctor = Doctor::create([
            'user_id' => $doctorUser->id,
            'health_center_id' => $healthCenter->id,
            'license_number' => 'LICENSE-BOOK-001',
        ]);
        $doctor->services()->attach($service);
        $schedule = AppointmentSchedule::create([
            'health_center_id' => $healthCenter->id,
            'service_id' => $service->id,
            'doctor_id' => $doctor->id,
            'schedule_date' => today()->addDay(),
            'appointment_time' => '09:00:00',
            'capacity' => 1,
        ]);

        $this->actingAs($patientUser)
            ->post(route('patient.appointments.store'), [
                'service_id' => $service->id,
                'appointment_schedule_id' => $schedule->id,
                'doctor_id' => $doctor->id,
                'reason' => 'Routine consultation',
            ])
            ->assertRedirect(route('patient.dashboard'))
            ->assertSessionHas('success', 'Appointment Successfully Booked!');

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->id,
            'appointment_schedule_id' => $schedule->id,
            'status' => 'pending',
        ]);
    }

    public function test_staff_approval_confirms_appointment_for_patient_and_doctor(): void
    {
        $records = $this->createAppointmentRecords();

        $this->actingAs($records['staffUser'])
            ->patch(route('staff.appointments.approve', $records['appointment']))
            ->assertRedirect(route('staff.appointments.index'))
            ->assertSessionHas('success', 'Appointment confirmed for the patient.');

        $this->assertDatabaseHas('appointments', [
            'id' => $records['appointment']->id,
            'status' => 'approved',
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $records['staffUser']->id,
            'action' => 'Appointment Approved',
        ]);

        $this->actingAs($records['patientUser'])
            ->get(route('patient.appointments.index'))
            ->assertOk()
            ->assertSee('Confirmed');

        $this->actingAs($records['doctorUser'])
            ->get(route('doctor.appointments.index'))
            ->assertOk()
            ->assertSee('Patient Name')
            ->assertSee('Start Consultation')
            ->assertDontSee('Approve Appointment');
    }

    public function test_doctor_cannot_see_appointments_awaiting_staff_approval(): void
    {
        $records = $this->createAppointmentRecords();
        $records['appointment']->update([
            'status' => 'approved',
        ]);
        $unapprovedAppointment = $this->createAppointment(
            $records,
            'pending',
            'Awaiting Approval Patient'
        );

        $this->actingAs($records['doctorUser'])
            ->get(route('doctor.appointments.index'))
            ->assertOk()
            ->assertSee('Patient Name')
            ->assertDontSee('Awaiting Approval Patient');

        $this->actingAs($records['doctorUser'])
            ->get(route('doctor.patients.index'))
            ->assertOk()
            ->assertDontSee('Awaiting Approval Patient');

        $this->actingAs($records['doctorUser'])
            ->get(route('doctor.patients.show', $unapprovedAppointment->patient_id))
            ->assertForbidden();

        $this->actingAs($records['doctorUser'])
            ->get(route('doctor.appointments.consultation.create', $unapprovedAppointment))
            ->assertRedirect(route('doctor.appointments.index'))
            ->assertSessionHas('error', 'Only approved appointments can be started for consultation.');

        $this->assertSame('pending', $unapprovedAppointment->fresh()->status);
    }

    public function test_staff_cannot_approve_appointments_from_another_health_center(): void
    {
        $records = $this->createAppointmentRecords();
        $otherHealthCenter = $this->createHealthCenter('Other Health Center');
        $records['appointment']->update([
            'health_center_id' => $otherHealthCenter->id,
        ]);

        $this->actingAs($records['staffUser'])
            ->patch(route('staff.appointments.approve', $records['appointment']))
            ->assertForbidden();

        $this->assertSame('pending', $records['appointment']->fresh()->status);
    }

    public function test_only_pending_appointments_can_be_approved_by_staff(): void
    {
        $records = $this->createAppointmentRecords();
        $records['appointment']->update([
            'status' => 'approved',
        ]);

        $this->actingAs($records['staffUser'])
            ->patch(route('staff.appointments.approve', $records['appointment']))
            ->assertRedirect(route('staff.appointments.index'))
            ->assertSessionHas('error', 'Only pending appointments can be approved.');

        $this->assertSame('approved', $records['appointment']->fresh()->status);
        $this->assertDatabaseCount('activity_logs', 0);
    }

    private function createAppointmentRecords(): array
    {
        $healthCenter = $this->createHealthCenter('Test Health Center');
        $service = Service::create([
            'name' => 'General Consultation',
            'code' => 'GENERAL',
            'status' => true,
        ]);

        $patientUser = $this->createUser('Patient Name', 'patient', 'patient@example.test');
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

        $doctorUser = $this->createUser('Doctor Name', 'doctor', 'doctor@example.test');
        $doctor = Doctor::create([
            'user_id' => $doctorUser->id,
            'health_center_id' => $healthCenter->id,
            'license_number' => 'LICENSE-TEST-001',
        ]);

        $staffUser = $this->createUser('Staff Name', 'staff', 'staff@example.test');
        Staff::create([
            'user_id' => $staffUser->id,
            'health_center_id' => $healthCenter->id,
            'employee_number' => 'EMP-TEST-001',
            'position' => 'Health Center Staff',
        ]);

        return [
            'healthCenter' => $healthCenter,
            'patientUser' => $patientUser,
            'doctorUser' => $doctorUser,
            'staffUser' => $staffUser,
            'appointment' => Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'health_center_id' => $healthCenter->id,
                'service_id' => $service->id,
                'appointment_date' => today()->addDay(),
                'appointment_time' => '09:00:00',
                'reason' => 'Routine checkup',
                'status' => 'pending',
            ]),
        ];
    }

    private function createAppointment(
        array $records,
        string $status,
        string $patientName
    ): Appointment {
        $patientUser = $this->createUser(
            $patientName,
            'patient',
            str_replace(' ', '.', strtolower($patientName)) . '@example.test'
        );

        $patient = Patient::create([
            'user_id' => $patientUser->id,
            'health_center_id' => $records['healthCenter']->id,
            'patient_number' => 'PAT-TEST-002',
            'date_of_birth' => '1990-01-01',
            'sex' => 'Female',
            'contact_number' => '09123456789',
            'address' => 'Test Address',
            'emergency_contact_name' => 'Emergency Contact',
            'emergency_contact_number' => '09987654321',
        ]);

        return Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $records['appointment']->doctor_id,
            'health_center_id' => $records['healthCenter']->id,
            'service_id' => $records['appointment']->service_id,
            'appointment_date' => today()->addDay(),
            'appointment_time' => '10:00:00',
            'reason' => 'Routine checkup',
            'status' => $status,
        ]);
    }

    private function createHealthCenter(string $name): HealthCenter
    {
        return HealthCenter::create([
            'name' => $name,
            'barangay' => 'Test Barangay',
            'address' => 'Test Address',
            'status' => 'active',
        ]);
    }

    private function createUser(string $name, string $role, string $email): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => 'password',
            'role' => $role,
            'status' => 'active',
        ]);
    }
}
