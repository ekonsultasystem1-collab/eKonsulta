<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\HealthCenter;
use App\Models\Service;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminDoctorController extends Controller
{
    /**
     * Display all doctors.
     */
    public function index(): View
    {
        $doctors = Doctor::with([
            'user',
            'healthCenter',
        ])
            ->orderBy('id')
            ->get();

        return view('admin.doctors.index', compact('doctors'));
    }

    /**
     * Display doctor details.
     */
    public function show(Doctor $doctor): View
    {
        $doctor->load([
            'user',
            'healthCenter',
            'services',
            'appointments.patient.user',
            'consultations.patient.user',
            'prescriptions',
        ]);

        return view('admin.doctors.show', compact('doctor'));
    }

    /**
     * Show the create doctor form.
     */
    public function create(): View
    {
        $healthCenters = HealthCenter::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view(
            'admin.doctors.create',
            compact('healthCenters')
        );
    }

    /**
     * Store a new doctor and user account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],

            'health_center_id' => [
                'required',
                'exists:health_centers,id',
            ],

            'license_number' => [
                'required',
                'string',
                'max:100',
                'unique:doctors,license_number',
            ],

            'specialization' => [
                'nullable',
                'string',
                'max:255',
            ],

            'contact_number' => [
                'nullable',
                'string',
                'max:30',
            ],
        ]);

        $doctor = DB::transaction(function () use ($validated) {

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'doctor',
            ]);

            return Doctor::create([
                'user_id' => $user->id,
                'health_center_id' => $validated['health_center_id'],
                'license_number' => $validated['license_number'],
                'specialization' => $validated['specialization'] ?? null,
                'contact_number' => $validated['contact_number'] ?? null,
            ]);
        });

        ActivityLog::record(
            auth()->id(),
            'Doctor Created',
            'Administrator created doctor account: ' .
                $doctor->user->name . '.'
        );

        return redirect()
            ->route('admin.doctors.show', $doctor)
            ->with('success', 'Doctor account created successfully.');
    }

    /**
     * Show the edit doctor form.
     */
    public function edit(Doctor $doctor): View
    {
        $doctor->load([
            'user',
            'services',
            'healthCenter.services',
        ]);

        /*
         * Only show active services offered by the doctor's
         * assigned health center.
         */
        $services = $doctor->healthCenter
            ? $doctor->healthCenter->services()
                ->where('services.status', true)
                ->orderBy('services.name')
                ->get()
            : collect();

        $healthCenters = HealthCenter::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view(
            'admin.doctors.edit',
            compact(
                'doctor',
                'healthCenters',
                'services'
            )
        );
    }

    /**
     * Update doctor information and service assignments.
     */
    public function update(
        Request $request,
        Doctor $doctor
    ): RedirectResponse {
        $doctor->load('user');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $doctor->user->id,
            ],

            'health_center_id' => [
                'required',
                'exists:health_centers,id',
            ],

            'license_number' => [
                'required',
                'string',
                'max:100',
                'unique:doctors,license_number,' . $doctor->id,
            ],

            'specialization' => [
                'nullable',
                'string',
                'max:255',
            ],

            'contact_number' => [
                'nullable',
                'string',
                'max:30',
            ],

            'services' => [
                'nullable',
                'array',
            ],

            'services.*' => [
                'integer',
                'exists:services,id',
            ],
        ]);

        DB::transaction(function () use (
            $doctor,
            $validated
        ) {
            $doctor->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            $doctor->update([
                'health_center_id' => $validated['health_center_id'],
                'license_number' => $validated['license_number'],
                'specialization' => $validated['specialization'] ?? null,
                'contact_number' => $validated['contact_number'] ?? null,
            ]);

            /*
             * Only services offered by the doctor's selected
             * health center can be assigned.
             */
            $allowedServiceIds = HealthCenter::find(
                $validated['health_center_id']
            )
                ->services()
                ->where('services.status', true)
                ->pluck('services.id')
                ->toArray();

            $selectedServiceIds = array_values(
                array_intersect(
                    $validated['services'] ?? [],
                    $allowedServiceIds
                )
            );

            $doctor->services()->sync($selectedServiceIds);
        });

        ActivityLog::record(
            auth()->id(),
            'Doctor Updated',
            'Administrator updated doctor account: ' .
                $doctor->user->name . '.'
        );

        return redirect()
            ->route('admin.doctors.show', $doctor)
            ->with('success', 'Doctor information updated successfully.');
    }

    /**
     * Toggle a doctor's account status.
     */
    public function toggleStatus(
        Doctor $doctor,
        Request $request
    ): RedirectResponse {
        $doctor->load('user');

        $doctor->user->status = $doctor->user->status === 'active'
            ? 'inactive'
            : 'active';

        $doctor->user->save();

        $message = $doctor->user->status === 'active'
            ? 'Doctor account activated successfully.'
            : 'Doctor account deactivated successfully.';

        ActivityLog::record(
            auth()->id(),
            $doctor->user->status === 'active'
                ? 'Doctor Activated'
                : 'Doctor Deactivated',
            'Administrator ' .
                ($doctor->user->status === 'active'
                    ? 'activated'
                    : 'deactivated') .
                ' doctor account: ' .
                $doctor->user->name . '.'
        );

        return redirect()
            ->route('admin.doctors.index')
            ->with('success', $message);
    }
}