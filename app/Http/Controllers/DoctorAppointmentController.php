<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\View\View;

class DoctorAppointmentController extends Controller
{
    /**
     * Display appointments assigned to the logged-in doctor.
     */
    public function index(): View
    {
        $doctor = auth()->user()->doctor;

        if (!$doctor) {
            abort(403, 'Doctor record not found.');
        }

        $now = now();
        $today = $now->toDateString();

        $appointments = Appointment::with([
            'patient.user',
            'healthCenter',
        ])
            ->where('doctor_id', $doctor->id)
            ->whereIn('status', ['approved', 'completed'])
            ->orderByRaw(
                'CASE WHEN status = ? AND (appointment_date > ? OR (appointment_date = ? AND appointment_time >= ?)) THEN 0 ELSE 1 END',
                ['approved', $today, $today, $now->format('H:i:s')]
            )
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        return view('doctor.appointments.index', compact(
            'appointments'
        ));
    }

}