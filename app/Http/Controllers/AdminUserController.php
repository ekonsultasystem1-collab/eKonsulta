<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display all system users.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);
        $search = trim($validated['search'] ?? '');

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';
                $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('role', 'like', $term)
                        ->orWhere('status', 'like', $term);
                });
            })
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function patients(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);
        $search = trim($validated['search'] ?? '');

        $patients = \App\Models\Patient::query()
            ->select([
                'id',
                'user_id',
                'patient_number',
            ])
            ->with('user:id,name,email,status,role')
            ->whereHas('user', function ($query) {
                $query->where('role', 'patient');
            })
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';
                $query->where(function ($query) use ($term) {
                    $query->where('patient_number', 'like', $term)
                        ->orWhereHas('user', function ($query) use ($term) {
                            $query->where('name', 'like', $term)
                                ->orWhere('email', 'like', $term);
                        });
                });
            })
            ->orderBy('patient_number')
            ->paginate(20)
            ->withQueryString();

        return view('admin.patients.index', compact('patients', 'search'));
    }

    /**
     * Display user details.
     */
    public function show(User $user): View
    {
        $user->load([
            'patient',
            'doctor',
            'staff',
        ]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Toggle a user's active status.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        // Prevent the administrator from disabling their own account.
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot deactivate your own administrator account.');
        }

        $user->status = $user->status === 'active'
            ? 'inactive'
            : 'active';

        $user->save();

        $message = $user->status === 'active'
            ? 'User account activated successfully.'
            : 'User account deactivated successfully.';

        return redirect()
            ->route('admin.users.index')
            ->with('success', $message);
    }
}