<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Prescription;
use App\Models\Notification;
use Illuminate\View\View;

class PatientDashboardController extends Controller
{
    /**
     * Display the patient dashboard.
     */
    public function index(): View
    {
        $user = auth()->user();

        // Get the patient's patient record.
        $patient = $user->patient;

        // If the logged-in user does not have a patient record,
        // safely load the dashboard with empty statistics.
        if (!$patient) {
            return view('patient.dashboard', [
                'upcomingAppointments' => collect(),
                'consultationCount' => 0,
                'prescriptionCount' => 0,
                'notificationCount' => 0,
                'nextAppointment' => null,
                'announcements' => collect(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Upcoming Appointments
        |--------------------------------------------------------------------------
        */

        $upcomingAppointments = Appointment::with([
    'doctor.user',
    'healthCenter',
])
            ->where('patient_id', $patient->id)
            ->whereDate('appointment_date', '>=', now()->toDateString())
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Next Appointment
        |--------------------------------------------------------------------------
        */

        $nextAppointment = $upcomingAppointments->first();

        /*
        |--------------------------------------------------------------------------
        | Consultation Count
        |--------------------------------------------------------------------------
        */

        $consultationCount = Consultation::where(
            'patient_id',
            $patient->id
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Prescription Count
        |--------------------------------------------------------------------------
        */

        $prescriptionCount = Prescription::where(
            'patient_id',
            $patient->id
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Unread Notifications
        |--------------------------------------------------------------------------
        */

        $notificationCount = Notification::where(
            'user_id',
            $user->id
        )
            ->available()
            ->whereNull('read_at')
            ->count();

        $announcements = Announcement::visibleToUser($user)
            ->with('healthCenter')
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('patient.dashboard', compact(
            'upcomingAppointments',
            'nextAppointment',
            'consultationCount',
            'prescriptionCount',
            'notificationCount',
            'announcements'
        ));
    }
}