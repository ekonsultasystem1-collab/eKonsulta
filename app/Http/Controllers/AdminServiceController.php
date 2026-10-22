<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminServiceController extends Controller
{
    /**
     * Display all services.
     */
    public function index()
    {
        $services = Service::orderBy('name')->get();

        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the create service form.
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                'unique:services,code',
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'boolean'],
        ]);

        Service::create($validated);

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service added successfully.');
    }

    /**
     * Show the edit service form.
     */
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update an existing service.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('services', 'code')->ignore($service->id),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'boolean'],
        ]);

        $service->update($validated);

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Activate or deactivate a service.
     */
    public function toggleStatus(Service $service)
    {
        $service->update([
            'status' => ! $service->status,
        ]);

        $message = $service->status
            ? 'Service activated successfully.'
            : 'Service deactivated successfully.';

        return redirect()
            ->route('admin.services.index')
            ->with('success', $message);
    }

    /**
     * Delete a service.
     */
    public function destroy(Service $service): RedirectResponse
    {
        DB::transaction(function () use ($service): void {
            $service = Service::query()
                ->whereKey($service->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $service->healthCenters()->detach();
            $service->doctors()->detach();
            $service->status = false;
            $service->save();
            $service->delete();
        });

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service removed from active lists. Linked appointment and schedule history was preserved.');
    }
}
