<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Medicine;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class DoctorPrescriptionController extends Controller
{
    /**
     * Display prescriptions created by the logged-in doctor.
     */
    public function index(): View
    {
        $doctor = auth()->user()->doctor;

        if (!$doctor) {
            abort(403, 'Doctor record not found.');
        }

        $prescriptions = Prescription::where('doctor_id', $doctor->id)
            ->with([
                'patient.user',
                'consultation',
                'items.medicine',
            ])
            ->latest('prescription_date')
            ->get();

        return view('doctor.prescriptions.index', compact('prescriptions'));
    }

/**
 * Display a single prescription.
 */
public function show(Prescription $prescription): View
{
    $doctor = auth()->user()->doctor;

    if (!$doctor) {
        abort(403, 'Doctor record not found.');
    }

    if ($prescription->doctor_id !== $doctor->id) {
        abort(403, 'You are not authorized to view this prescription.');
    }

    $prescription->load([
        'patient.user',
        'doctor.user',
        'items.medicine',
        'consultation',
    ]);

    return view('doctor.prescriptions.show', compact('prescription'));
}

/**
 * Download prescription as PDF.
 */
public function download(Prescription $prescription)
{
    $doctor = auth()->user()->doctor;

    if (!$doctor) {
        abort(403, 'Doctor record not found.');
    }

    if ($prescription->doctor_id !== $doctor->id) {
        abort(403, 'You are not authorized to download this prescription.');
    }

    $prescription->load([
        'patient.user',
        'doctor.user',
        'items.medicine',
        'consultation',
    ]);

    $pdf = Pdf::loadView(
        'doctor.prescriptions.pdf',
        compact('prescription')
    );

    $filename = $prescription->prescription_number . '.pdf';

    return $pdf->download($filename);
}

/**
 * Print-friendly prescription page.
 */
public function print(Prescription $prescription): View
{
    $doctor = auth()->user()->doctor;

    if (!$doctor) {
        abort(403, 'Doctor record not found.');
    }

    if ($prescription->doctor_id !== $doctor->id) {
        abort(403, 'You are not authorized to print this prescription.');
    }

    $prescription->load([
        'patient.user',
        'doctor.user',
        'items.medicine',
        'consultation',
    ]);

    return view('doctor.prescriptions.print', compact('prescription'));
}

    /**
     * Show the prescription form for a completed consultation.
     */
    public function create(Appointment $appointment): View
    {
        $doctor = auth()->user()->doctor;

        if (!$doctor) {
            abort(403, 'Doctor record not found.');
        }

        // Make sure this appointment belongs to the logged-in doctor.
        if ($appointment->doctor_id !== $doctor->id) {
            abort(403, 'You are not authorized to create a prescription for this appointment.');
        }

        // A prescription can only be created after the consultation is completed.
        if ($appointment->status !== 'completed') {
            abort(403, 'A prescription can only be created for a completed consultation.');
        }

        $consultation = $appointment->consultation;

        if (!$consultation) {
            abort(404, 'Consultation record not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | Medicines
        |--------------------------------------------------------------------------
        |
        | Medicines are displayed as available prescription choices.
        | The system does not automatically track or manage physical
        | medicine inventory.
        |
        */

        $medicines = Medicine::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('doctor.prescriptions.create', compact(
            'appointment',
            'consultation',
            'medicines'
        ));
    }

    /**
     * Store a new prescription.
     */
    public function store(Request $request, Appointment $appointment)
    {
        $doctor = auth()->user()->doctor;

        if (!$doctor) {
            abort(403, 'Doctor record not found.');
        }

        if ($appointment->doctor_id !== $doctor->id) {
            abort(403, 'You are not authorized to create a prescription for this appointment.');
        }

        if ($appointment->status !== 'completed') {
            abort(403, 'A prescription can only be created for a completed consultation.');
        }

        $consultation = $appointment->consultation;

        if (!$consultation) {
            abort(404, 'Consultation record not found.');
        }

        $validated = $request->validate([
            'prescription_date' => ['required', 'date'],
            'instructions' => ['nullable', 'string', 'max:2000'],
            'medicine_id' => ['required', 'exists:medicines,id'],
            'dosage' => ['required', 'string', 'max:255'],
            'frequency' => ['required', 'string', 'max:255'],
            'duration' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'item_instructions' => ['nullable', 'string', 'max:1000'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Medicine
        |--------------------------------------------------------------------------
        |
        | The selected medicine must exist and be active.
        | Physical medicine stock is not checked or modified here.
        |
        */

        $medicine = Medicine::where('id', $validated['medicine_id'])
            ->where('status', 'active')
            ->firstOrFail();

        $prescription = null;

        DB::transaction(function () use (
            $validated,
            $consultation,
            $appointment,
            $doctor,
            $medicine,
            &$prescription
        ) {
            $prescription = Prescription::create([
                'consultation_id' => $consultation->id,
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $doctor->id,
                'prescription_number' => 'RX-' . now()->format('YmdHis'),
                'prescription_date' => $validated['prescription_date'],
                'instructions' => $validated['instructions'] ?? null,
                'status' => 'active',
            ]);

            $prescription->items()->create([
                'medicine_id' => $medicine->id,
                'dosage' => $validated['dosage'],
                'frequency' => $validated['frequency'],
                'duration' => $validated['duration'],
                'quantity' => $validated['quantity'],
                'instructions' => $validated['item_instructions'] ?? null,
            ]);
        });

        ActivityLog::record(
            auth()->id(),
            'Prescription Created',
            'Doctor created prescription ' . $prescription->prescription_number .
                ' for patient #' . $appointment->patient_id . '.',
            $request->ip()
        );

        return redirect()
            ->route('doctor.appointments.index')
            ->with('success', 'Prescription created successfully.');
    }
}