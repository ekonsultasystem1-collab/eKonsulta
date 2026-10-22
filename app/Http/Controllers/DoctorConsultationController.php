<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorConsultationController extends Controller
{
    /**
 * Display a single consultation.
 */
public function show(Consultation $consultation)
{
    $doctor = Auth::user()->doctor;

    if (!$doctor) {
        abort(403, 'Doctor record not found.');
    }

    // Make sure this consultation belongs to the logged-in doctor.
    if ($consultation->doctor_id !== $doctor->id) {
        abort(403, 'You are not authorized to view this consultation.');
    }

    $consultation->load([
        'patient.user',
        'appointment.healthCenter',
        'prescriptions.items.medicine',
    ]);

    return view('doctor.consultations.show', compact(
        'doctor',
        'consultation'
    ));
}
    /**
     * Display the doctor's consultations.
     */
    public function index()
    {
        $doctor = Auth::user()->doctor;

        if (!$doctor) {
            abort(403, 'Doctor record not found.');
        }

        $consultations = Consultation::with([
            'patient.user',
            'appointment.healthCenter',
            'prescriptions.items.medicine',
        ])
            ->where('doctor_id', $doctor->id)
            ->orderByDesc('consultation_date')
            ->get();

        return view('doctor.consultations.index', compact(
            'doctor',
            'consultations'
        ));
    }

    /**
     * Show the consultation form for an appointment.
     */
    public function create(Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;

        if (!$doctor) {
            abort(403, 'Doctor record not found.');
        }

        // Make sure this appointment belongs to the logged-in doctor.
        if ($appointment->doctor_id !== $doctor->id) {
            abort(403, 'You are not authorized to access this appointment.');
        }

        // Only approved appointments can proceed to consultation.
        if ($appointment->status !== 'approved') {
            return redirect()
                ->route('doctor.appointments.index')
                ->with(
                    'error',
                    'Only approved appointments can be started for consultation.'
                );
        }

$appointment->load([
    'patient.user',
    'healthCenter',
    'patientIntake.staff.user',
]);

        return view('doctor.consultations.create', compact(
            'appointment'
        ));
    }

    /**
     * Store a new consultation.
     */
    public function store(Request $request, Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;

        if (!$doctor) {
            abort(403, 'Doctor record not found.');
        }

        // Make sure this appointment belongs to the logged-in doctor.
        if ($appointment->doctor_id !== $doctor->id) {
            abort(403, 'You are not authorized to access this appointment.');
        }

        // Only approved appointments can have a consultation.
        if ($appointment->status !== 'approved') {
            return redirect()
                ->route('doctor.appointments.index')
                ->with(
                    'error',
                    'Only approved appointments can have a consultation.'
                );
        }

        if ($appointment->consultation) {
    return redirect()
        ->route('doctor.appointments.index')
        ->with(
            'error',
            'A consultation has already been recorded for this appointment.'
        );
}

        $validated = $request->validate([
            'chief_complaint' => ['required', 'string', 'max:2000'],
            'symptoms' => ['nullable', 'string', 'max:5000'],
            'diagnosis' => ['required', 'string', 'max:5000'],
            'treatment_plan' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        

    $consultation = Consultation::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $doctor->id,
            'consultation_date' => now(),
            'chief_complaint' => $validated['chief_complaint'],
            'symptoms' => $validated['symptoms'] ?? null,
            'diagnosis' => $validated['diagnosis'],
            'treatment_plan' => $validated['treatment_plan'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Mark the appointment as completed.
       $appointment->update([
    'status' => 'completed',
]);

ActivityLog::record(
    Auth::id(),
    'Consultation Created',
    'Doctor created consultation #' . $consultation->id .
        ' for appointment #' . $appointment->id . '.'
);

return redirect()
            ->route('doctor.appointments.index')
            ->with('success', 'Consultation recorded successfully.');
    }
}