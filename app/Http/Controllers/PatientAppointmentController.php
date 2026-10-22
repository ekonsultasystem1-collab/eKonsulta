<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\AppointmentSchedule;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientAppointmentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Appointment List
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $patient = auth()->user()->patient;

        if (!$patient) {
            abort(403, 'Patient record not found.');
        }

        $appointments = Appointment::with([
            'doctor.user',
            'healthCenter',
            'service',
            'appointmentSchedule',
        ])
            ->where('patient_id', $patient->id)
            ->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->get();

        return view(
            'patient.appointments.index',
            compact('appointments')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Show Booking Form
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $patient = auth()->user()->patient;

        if (!$patient) {
            abort(403, 'Patient record not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | Patient's Permanent Health Center
        |--------------------------------------------------------------------------
        */

        $healthCenterId = $patient->health_center_id;

        if (!$healthCenterId) {
            return redirect()
                ->route('patient.dashboard')
                ->with(
                    'error',
                    'You are not assigned to a health center. Please contact the health center staff.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Load Health Center
        |--------------------------------------------------------------------------
        */

        $healthCenter = $patient->healthCenter;

        if (!$healthCenter) {
            return redirect()
                ->route('patient.dashboard')
                ->with(
                    'error',
                    'Your assigned health center could not be found.'
                );
        }

        if ($healthCenter->trashed() || $healthCenter->status !== 'active') {
            return redirect()
                ->route('patient.dashboard')
                ->with(
                    'error',
                    'Your assigned health center is no longer available for booking. Please contact the administrator.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Services Offered By Patient's Health Center
        |--------------------------------------------------------------------------
        */

        $services = $healthCenter->services()
            ->where('services.status', true)
            ->orderBy('services.name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Available Appointment Schedules
        |--------------------------------------------------------------------------
        */

        $schedules = AppointmentSchedule::with([
            'healthCenter',
            'service',
        ])
            ->withCount([
                'appointments as active_appointments_count' => function ($query) {
                    $query->whereIn('status', [
                        'pending',
                        'approved',
                    ]);
                },
            ])
            ->where('health_center_id', $healthCenterId)
            ->whereNotNull('service_id')
            ->whereDate(
                'schedule_date',
                '>=',
                today()
            )
            ->whereHas('healthCenter', function ($query) {
                $query->where('status', 'active');
            })
            ->whereHas('service', function ($query) {
                $query->where('services.status', true);
            })
            ->orderBy('schedule_date')
            ->orderBy('appointment_time')
            ->get()
            ->filter(function ($schedule) {
                return $schedule->active_appointments_count
                    < $schedule->capacity;
            });

        /*
        |--------------------------------------------------------------------------
        | Doctors Assigned To Patient's Health Center
        |--------------------------------------------------------------------------
        |
        | We load the doctors and their services.
        | JavaScript on the booking page filters them visually.
        |
        */

        $doctors = Doctor::with([
            'user',
            'services',
        ])
            ->where('health_center_id', $healthCenterId)
            ->whereHas('user', function ($query) {
                $query->where('status', 'active');
            })
            ->orderBy('id')
            ->get();

$schedules = $schedules->filter(function ($schedule) use ($doctors) {
            $eligibleDoctors = $doctors->filter(function ($doctor) use ($schedule) {
                return $doctor->services->contains(
                    'id',
                    (int) $schedule->service_id
                );
            });

            if ($eligibleDoctors->isEmpty()) {
                return false;
            }

            return !$schedule->doctor_id || $eligibleDoctors->contains(
                'id',
                (int) $schedule->doctor_id
            );
});

        return view(
            'patient.appointments.create',
            compact(
                'healthCenter',
                'services',
                'schedules',
                'doctors'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store Appointment
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validate Form
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'service_id' => [
                'required',
                'exists:services,id',
            ],

            'appointment_schedule_id' => [
                'required',
                'exists:appointment_schedules,id',
            ],

            'doctor_id' => [
                'required',
                'exists:doctors,id',
            ],

            'reason' => [
                'required',
                'string',
                'max:1000',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $patient = Auth::user()->patient;

        if (!$patient) {
            abort(403, 'Patient record not found.');
        }

        if (!$patient->health_center_id) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'service_id' =>
                        'You are not assigned to a health center.',
                ]);
        }

        $appointment = null;

        try {

            DB::transaction(function () use (
                $validated,
                $patient,
                &$appointment
            ) {

                /*
                |--------------------------------------------------------------------------
                | Load Selected Schedule
                |--------------------------------------------------------------------------
                */

                $schedule = AppointmentSchedule::with([
                    'healthCenter',
                    'service',
                ])
                    ->lockForUpdate()
                    ->findOrFail(
                        $validated['appointment_schedule_id']
                    );


                /*
                |--------------------------------------------------------------------------
                | Schedule Must Belong To Patient's Health Center
                |--------------------------------------------------------------------------
                */

                if (
                    (int) $schedule->health_center_id !==
                    (int) $patient->health_center_id
                ) {
                    throw new \RuntimeException(
                        'The selected schedule is not available at your assigned health center.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Health Center Must Be Active
                |--------------------------------------------------------------------------
                */

                if (
                    !$schedule->healthCenter ||
                    $schedule->healthCenter->status !== 'active'
                ) {
                    throw new \RuntimeException(
                        'The selected health center is not currently active.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Schedule Must Have A Service
                |--------------------------------------------------------------------------
                */

                if (!$schedule->service) {
                    throw new \RuntimeException(
                        'The selected schedule has no valid service assigned.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Schedule Service Must Be Active
                |--------------------------------------------------------------------------
                */

                if (!$schedule->service->status) {
                    throw new \RuntimeException(
                        'The service assigned to this schedule is no longer available.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Schedule Service Must Match Selected Service
                |--------------------------------------------------------------------------
                */

                if (
                    (int) $schedule->service_id !==
                    (int) $validated['service_id']
                ) {
                    throw new \RuntimeException(
                        'The selected schedule is not available for the selected service.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Service Must Be Offered By This Health Center
                |--------------------------------------------------------------------------
                */

                $serviceExists = $schedule
                    ->healthCenter
                    ->services()
                    ->where(
                        'services.id',
                        $validated['service_id']
                    )
                    ->where(
                        'services.status',
                        true
                    )
                    ->exists();

                if (!$serviceExists) {
                    throw new \RuntimeException(
                        'The selected service is not available at your health center.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Schedule Must Still Be In The Future
                |--------------------------------------------------------------------------
                */

                $scheduleDate =
                    $schedule->schedule_date->format('Y-m-d');

                $scheduleTime =
                    $schedule->appointment_time->format('H:i:s');

                if (
                    $scheduleDate < now()->toDateString()
                    ||
                    (
                        $scheduleDate === now()->toDateString()
                        &&
                        $scheduleTime <= now()->format('H:i:s')
                    )
                ) {
                    throw new \RuntimeException(
                        'The selected appointment schedule is no longer available.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Check Schedule Capacity
                |--------------------------------------------------------------------------
                */

                $activeAppointments = $schedule
                    ->appointments()
                    ->whereIn(
                        'status',
                        [
                            'pending',
                            'approved',
                        ]
                    )
                    ->count();

                if (
                    $activeAppointments >=
                    $schedule->capacity
                ) {
                    throw new \RuntimeException(
                        'The selected appointment schedule is already full.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Load Doctor
                |--------------------------------------------------------------------------
                */

                $doctor = Doctor::findOrFail(
                    $validated['doctor_id']
                );

                $doctorIsActive = $doctor
                    ->user()
                    ->where('status', 'active')
                    ->exists();

                if (!$doctorIsActive) {
                    throw new \RuntimeException(
                        'The selected doctor is not currently available for appointments.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Doctor Must Belong To Same Health Center
                |--------------------------------------------------------------------------
                */

                if (
                    (int) $doctor->health_center_id !==
                    (int) $patient->health_center_id
                ) {
                    throw new \RuntimeException(
                        'The selected doctor is not assigned to your health center.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Doctor Must Provide Selected Service
                |--------------------------------------------------------------------------
                */

                $doctorProvidesService = $doctor
                    ->services()
                    ->where(
                        'services.id',
                        $validated['service_id']
                    )
                    ->exists();

                if (!$doctorProvidesService) {
                    throw new \RuntimeException(
                        'The selected doctor does not provide the selected service.'
                    );
                }

                if (
                    $schedule->doctor_id !== null &&
                    (int) $doctor->id !== (int) $schedule->doctor_id
                ) {
                    throw new \RuntimeException(
                        'The selected appointment schedule is assigned to a different doctor.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Prevent Duplicate Active Appointment
                |--------------------------------------------------------------------------
                */

                $alreadyBooked = $schedule
                    ->appointments()
                    ->where(
                        'patient_id',
                        $patient->id
                    )
                    ->whereIn(
                        'status',
                        [
                            'pending',
                            'approved',
                        ]
                    )
                    ->exists();

                if ($alreadyBooked) {
                    throw new \RuntimeException(
                        'You already have an active appointment for this schedule.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Create Appointment
                |--------------------------------------------------------------------------
                */

                $appointment = Appointment::create([
                    'patient_id' =>
                        $patient->id,

                    'doctor_id' =>
                        $doctor->id,

                    'health_center_id' =>
                        $patient->health_center_id,

                    'service_id' =>
                        $validated['service_id'],

                    'appointment_schedule_id' =>
                        $schedule->id,

                    'appointment_date' =>
                        $schedule->schedule_date->format('Y-m-d'),

                    'appointment_time' =>
                        $schedule->appointment_time->format('H:i:s'),

                    'reason' =>
                        $validated['reason'],

                    'status' =>
                        'pending',

                    'notes' =>
                        $validated['notes'] ?? null,
                ]);
            });

        } catch (\RuntimeException $e) {

            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'service_id' =>
                        $e->getMessage(),
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */

        $appointment->load([
            'appointmentSchedule.healthCenter',
            'service',
        ]);

        ActivityLog::record(
            Auth::id(),
            'Appointment Booked',
            'Patient booked appointment #' .
                $appointment->id .
                ' for ' .
                ($appointment->service->name ?? 'Unknown Service') .
                ' at ' .
                $appointment->appointmentSchedule->healthCenter->name .
                ' on ' .
                $appointment->appointment_date->format('F j, Y') .
                ' at ' .
                $appointment->appointment_time->format('g:i A') .
                '.'
        );


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('patient.dashboard')
            ->with(
                'success',
                'Appointment Successfully Booked!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Cancel Appointment
    |--------------------------------------------------------------------------
    */

    public function cancel(Appointment $appointment)
    {
        $patient = auth()->user()->patient;

        if (!$patient) {
            abort(403, 'Patient record not found.');
        }

        if (
            $appointment->patient_id !==
            $patient->id
        ) {
            abort(
                403,
                'You are not authorized to cancel this appointment.'
            );
        }

        if ($appointment->status !== 'pending') {
            return redirect()
                ->route('patient.appointments.index')
                ->with(
                    'error',
                    'This appointment can no longer be cancelled.'
                );
        }

        $appointment->update([
            'status' => 'cancelled',
        ]);

        ActivityLog::record(
            Auth::id(),
            'Appointment Cancelled',
            'Patient cancelled appointment #' .
                $appointment->id .
                '.'
        );

        return redirect()
            ->route('patient.appointments.index')
            ->with(
                'success',
                'Appointment cancelled successfully.'
            );
    }
}