<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\HealthCenter;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrescriptionManualMedicineTest extends TestCase
{
    use RefreshDatabase;

    public function test_prescription_form_offers_basic_and_manual_medicine_choices(): void
    {
        $records = $this->createCompletedConsultation();

        $this->actingAs($records['doctorUser'])
            ->get(route('doctor.appointments.prescription.create', $records['appointment']))
            ->assertOk()
            ->assertSee('Paracetamol - 500 mg')
            ->assertSee('Amoxicillin - 500 mg')
            ->assertSee('Ibuprofen - 200 mg')
            ->assertSee('Other - enter medicine manually');
    }

    public function test_doctor_can_prescribe_a_manually_entered_medicine_without_inventory_records(): void
    {
        $records = $this->createCompletedConsultation();

        $this->actingAs($records['doctorUser'])
            ->post(route('doctor.appointments.prescription.store', $records['appointment']), [
                'prescription_date' => today()->toDateString(),
                'medicine_choice' => 'other',
                'other_medicine_name' => 'Clinician-entered medicine',
                'dosage' => 'As prescribed',
                'frequency' => 'As prescribed',
                'duration' => 'As prescribed',
                'quantity' => 1,
                'item_instructions' => '',
                'instructions' => '',
            ])
            ->assertRedirect(route('doctor.appointments.index'))
            ->assertSessionHas('success', 'Prescription created successfully.');

        $this->assertDatabaseHas('prescription_items', [
            'medicine_id' => null,
            'medicine_name' => 'Clinician-entered medicine',
            'dosage' => 'As prescribed',
        ]);
        $this->assertDatabaseCount('medicines', 0);
    }

    private function createCompletedConsultation(): array
    {
        $healthCenter = HealthCenter::create([
            'name' => 'Test Health Center',
            'barangay' => 'Test Barangay',
            'address' => 'Test Address',
            'status' => 'active',
        ]);

        $service = Service::create([
            'name' => 'General Consultation',
            'code' => 'GENERAL',
            'status' => true,
        ]);

        $patientUser = User::create([
            'name' => 'Test Patient',
            'email' => 'patient@example.test',
            'password' => 'password',
            'role' => 'patient',
            'status' => 'active',
        ]);
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

        $doctorUser = User::create([
            'name' => 'Test Doctor',
            'email' => 'doctor@example.test',
            'password' => 'password',
            'role' => 'doctor',
            'status' => 'active',
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
            'status' => 'completed',
        ]);

        Consultation::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'consultation_date' => now(),
            'chief_complaint' => 'Routine checkup',
            'diagnosis' => 'Test diagnosis',
        ]);

        return compact('appointment', 'doctorUser');
    }
}
