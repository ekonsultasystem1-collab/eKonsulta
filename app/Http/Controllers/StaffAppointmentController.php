<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StaffAppointmentController extends Controller
{
    /**
     * Display appointments for the staff member's assigned
     * health center.
     */
    public function index(): View
    {
        $staff = auth()->user()->staff;

        if (!$staff) {
            abort(403, 'Staff record not found.');
        }

        $appointments = Appointment::with([
            'patient.user',
            'doctor.user',
            'healthCenter',
            'consultation',
            'patientIntake',
        ])
            ->where(
                'health_center_id',
                $staff->health_center_id
            )
            ->orderByDesc('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        return view(
            'staff.appointments.index',
            compact('appointments')
        );
    }


    /**
     * Display appointment details.
     */
    public function show(Appointment $appointment): View
    {
        $staff = auth()->user()->staff;

        if (!$staff) {
            abort(403, 'Staff record not found.');
        }

        /*
         * Prevent staff from viewing another
         * health center's appointment.
         */
        if (
            $appointment->health_center_id
            !== $staff->health_center_id
        ) {
            abort(
                403,
                'You are not authorized to view this appointment.'
            );
        }

        $appointment->load([
            'patient.user',
            'doctor.user',
            'healthCenter',
            'consultation',
            'consultation.prescriptions.items.medicine',
            'patientIntake.staff.user',
        ]);

        return view(
            'staff.appointments.show',
            compact('appointment')
        );
    }

    public function approve(Appointment $appointment): RedirectResponse
    {
        $staff = auth()->user()->staff;

        if (!$staff) {
            abort(403, 'Staff record not found.');
        }

        $approved = DB::transaction(function () use ($appointment, $staff): bool {
            $lockedAppointment = Appointment::whereKey($appointment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedAppointment->health_center_id !== $staff->health_center_id) {
                abort(403, 'You are not authorized to approve this appointment.');
            }

            if ($lockedAppointment->status !== 'pending') {
                return false;
            }

            $lockedAppointment->update([
                'status' => 'approved',
            ]);

            ActivityLog::record(
                auth()->id(),
                'Appointment Approved',
                'Staff approved appointment #' . $lockedAppointment->id . '.'
            );

            return true;
        });

        if (!$approved) {
            return redirect()
                ->route('staff.appointments.index')
                ->with('error', 'Only pending appointments can be approved.');
        }

        return redirect()
            ->route('staff.appointments.index')
            ->with('success', 'Appointment confirmed for the patient.');
    }
}