<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\View\View;

class PatientMedicalRecordController extends Controller
{
    /**
     * Display the patient's medical records.
     */
    public function index(): View
    {
        $patient = auth()->user()->patient;

        if (!$patient) {
            abort(403, 'Patient record not found.');
        }

        $consultations = Consultation::with([
            'doctor.user',
            'appointment.healthCenter',
            'appointment.patientIntake',
            'prescriptions.items.medicine',
        ])
            ->where('patient_id', $patient->id)
            ->orderByDesc('consultation_date')
            ->get();

        return view('patient.medical-records.index', compact(
            'consultations'
        ));
    }

    /**
     * Display a single consultation record belonging to the patient.
     */
    public function show(Consultation $consultation): View
    {
        $patient = auth()->user()->patient;

        if (!$patient) {
            abort(403, 'Patient record not found.');
        }

        if ($consultation->patient_id !== $patient->id) {
            abort(403, 'You are not authorized to view this medical record.');
        }

        $consultation->load([
            'doctor.user',
            'appointment.healthCenter',
            'appointment.patientIntake',
            'prescriptions.items.medicine',
        ]);

        return view('patient.medical-records.show', compact(
            'consultation'
        ));
    }
}