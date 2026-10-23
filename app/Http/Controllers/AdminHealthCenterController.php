<?php

namespace App\Http\Controllers;

use App\Models\HealthCenter;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminHealthCenterController extends Controller
{
    /**
     * Display all health centers.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);
        $search = trim($validated['search'] ?? '');

        $healthCenters = HealthCenter::with([
            'doctors.user',
            'staff.user',
        ])
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';
                $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', $term)
                        ->orWhere('address', 'like', $term)
                        ->orWhere('barangay', 'like', $term)
                        ->orWhere('contact_number', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            })
            ->orderBy('name')
            ->get();

        return view('admin.health-centers.index', compact('healthCenters', 'search'));
    }

    public function destroy(HealthCenter $healthCenter): \Illuminate\Http\RedirectResponse
    {
        DB::transaction(function () use ($healthCenter): void {
            $center = HealthCenter::query()
                ->whereKey($healthCenter->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $center->services()->detach();
            $center->status = 'inactive';
            $center->save();
            $center->delete();
        });

        return redirect()
            ->route('admin.health-centers.index')
            ->with('success', 'Health center removed from active lists and patient booking. Linked records and history were preserved.');
    }

    /**
     * Display health center details.
     */
    public function show(HealthCenter $healthCenter): View
    {
    $healthCenter->load([
    'doctors.user',
    'staff.user',
    'appointments.patient.user',
    'appointments.doctor.user',
    'services',
]);

        return view('admin.health-centers.show', compact(
            'healthCenter'
        ));
    }

    /**
     * Show the form for creating a health center.
     */
    public function create(): View
    {
        return view('admin.health-centers.create');
    }

    /**
     * Store a newly created health center.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'barangay' => [
                'required',
                'string',
                'max:255',
            ],
            'address' => [
                'required',
                'string',
                'max:255',
            ],
            'contact_number' => [
                'nullable',
                'string',
                'max:50',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'operating_hours' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $validated['status'] = 'active';

        HealthCenter::create($validated);

        return redirect()
            ->route('admin.health-centers.index')
            ->with(
                'success',
                'Health center added successfully.'
            );
    }

    /**
     * Show the form for editing a health center.
     */
    public function edit(HealthCenter $healthCenter): View
    {
        $services = Service::where('status', true)
            ->orderBy('name')
            ->get();

        $healthCenter->load('services');

        return view(
            'admin.health-centers.edit',
            compact('healthCenter', 'services')
        );
    }

    /**
     * Update an existing health center.
     */
    public function update(
        Request $request,
        HealthCenter $healthCenter
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'barangay' => [
                'required',
                'string',
                'max:255',
            ],
            'address' => [
                'required',
                'string',
                'max:255',
            ],
            'contact_number' => [
                'nullable',
                'string',
                'max:50',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'operating_hours' => [
                'nullable',
                'string',
                'max:255',
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

        $healthCenter->update([
            'name' => $validated['name'],
            'barangay' => $validated['barangay'],
            'address' => $validated['address'],
            'contact_number' => $validated['contact_number'] ?? null,
            'email' => $validated['email'] ?? null,
            'operating_hours' => $validated['operating_hours'] ?? null,
        ]);

        $healthCenter->services()->sync(
            $validated['services'] ?? []
        );

        return redirect()
            ->route(
                'admin.health-centers.show',
                $healthCenter
            )
            ->with(
                'success',
                'Health center updated successfully.'
            );
    }

    /**
     * Activate or deactivate a health center.
     */
    public function toggleStatus(
        HealthCenter $healthCenter
    ): RedirectResponse {
        $healthCenter->status = $healthCenter->status === 'active'
            ? 'inactive'
            : 'active';

        $healthCenter->save();

        $message = $healthCenter->status === 'active'
            ? 'Health center activated successfully.'
            : 'Health center deactivated successfully.';

        return redirect()
            ->route('admin.health-centers.index')
            ->with('success', $message);
    }
}