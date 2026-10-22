<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PatientAnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::visibleToUser(auth()->user())
            ->with('healthCenter')
            ->orderByDesc('published_at')
            ->get();

        return view('patient.announcements.index', compact('announcements'));
    }

    public function show(Announcement $announcement): View
    {
        if (! Announcement::currentlyVisible()->whereKey($announcement->id)->exists()) {
            abort(404);
        }

        if (! Announcement::visibleToUser(auth()->user())->whereKey($announcement->id)->exists()) {
            abort(403, 'This announcement is not available to your account.');
        }

        $announcement->load(['healthCenter', 'creator']);

        return view('patient.announcements.show', compact('announcement'));
    }

    public function notifications(): View
    {
        $notifications = Notification::available()
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        $routePrefix = match (true) {
            auth()->user()->isPatient() => 'patient',
            auth()->user()->isStaff() => 'staff',
            auth()->user()->isAdmin() => 'admin',
            default => 'patient',
        };

        return view('patient.notifications', compact('notifications', 'routePrefix'));
    }

    public function markAsRead(Notification $notification): RedirectResponse
    {
        if ((int) $notification->user_id !== (int) auth()->id()) {
            abort(403, 'You are not allowed to mark this notification as read.');
        }

        $notification->update([
            'read_at' => now(),
        ]);

        $routePrefix = match (true) {
            auth()->user()->isPatient() => 'patient',
            auth()->user()->isStaff() => 'staff',
            auth()->user()->isAdmin() => 'admin',
            default => 'patient',
        };

        return redirect()->route($routePrefix . '.notifications')->with('success', 'Notification marked as read.');
    }
}
