<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\HealthCenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StaffAnnouncementController extends Controller
{
    public function index(): View
    {
        $healthCenterId = $this->currentHealthCenterId();

        $announcements = Announcement::with(['healthCenter', 'creator'])
            ->where('health_center_id', $healthCenterId)
            ->orderByDesc('created_at')
            ->get();

        return view('staff.announcements.index', compact('announcements'));
    }

    public function show(Announcement $announcement): View
    {
        $this->authorizeAnnouncement($announcement);
        $announcement->load(['healthCenter', 'creator']);

        return view('staff.announcements.show', compact('announcement'));
    }

    public function create(): View
    {
        $healthCenter = $this->currentHealthCenter();

        return view('staff.announcements.create', compact('healthCenter'));
    }

    public function store(Request $request): RedirectResponse
    {
        $healthCenterId = $this->currentHealthCenterId();

        $validated = $this->validateAnnouncementData($request);

        $announcement = Announcement::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image_path' => $this->storeImage($request),
            'health_center_id' => $healthCenterId,
            'created_by' => auth()->id(),
            'is_published' => $validated['is_published'],
            'published_at' => $validated['is_published'] ? now() : null,
            'event_start_date' => $validated['event_start_date'] ?? null,
            'event_end_date' => $validated['event_end_date'] ?? null,
        ]);

        if ($announcement->is_published) {
            $announcement->sendNotifications();
        }

        return redirect()
            ->route('staff.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function edit(Announcement $announcement): View
    {
        $this->authorizeAnnouncement($announcement);

        return view('staff.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $this->authorizeAnnouncement($announcement);

        $validated = $this->validateAnnouncementData($request, $announcement);
        $wasPublished = $announcement->is_published;

        $announcement->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'is_published' => $validated['is_published'],
            'published_at' => $validated['is_published'] ? ($announcement->published_at ?? now()) : null,
            'event_start_date' => $validated['event_start_date'] ?? null,
            'event_end_date' => $validated['event_end_date'] ?? null,
        ]);

        if ($request->hasFile('image')) {
            $announcement->update(['image_path' => $this->storeImage($request, $announcement)]);
        }

        if ($request->boolean('remove_image')) {
            if ($announcement->image_path && Storage::disk('public')->exists($announcement->image_path)) {
                Storage::disk('public')->delete($announcement->image_path);
            }

            $announcement->update(['image_path' => null]);
        }

        if ($validated['is_published'] && ! $wasPublished) {
            $announcement->sendNotifications();
        }

        return redirect()
            ->route('staff.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function toggleStatus(Announcement $announcement): RedirectResponse
    {
        $this->authorizeAnnouncement($announcement);

        if ($announcement->is_published) {
            $announcement->unpublish();

            return redirect()
                ->route('staff.announcements.index')
                ->with('success', 'Announcement unpublished successfully.');
        }

        $announcement->publish();

        return redirect()
            ->route('staff.announcements.index')
            ->with('success', 'Announcement published successfully.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $this->authorizeAnnouncement($announcement);

        if ($announcement->image_path && Storage::disk('public')->exists($announcement->image_path)) {
            Storage::disk('public')->delete($announcement->image_path);
        }

        $announcement->delete();

        return redirect()
            ->route('staff.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }

    private function validateAnnouncementData(Request $request, ?Announcement $announcement = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'event_start_date' => ['nullable', 'date_format:Y-m-d'],
            'event_end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:event_start_date'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $validated['is_published'] = (bool) $request->boolean('is_published');

        return $validated;
    }

    private function storeImage(Request $request, ?Announcement $announcement = null): ?string
    {
        if ($request->hasFile('image')) {
            if ($announcement && $announcement->image_path && Storage::disk('public')->exists($announcement->image_path)) {
                Storage::disk('public')->delete($announcement->image_path);
            }

            return $request->file('image')->store('announcements', 'public');
        }

        return $announcement?->image_path;
    }

    private function currentHealthCenterId(): int
    {
        $staff = auth()->user()->staff;

        if (! $staff || ! $staff->health_center_id) {
            abort(403, 'You are not assigned to a health center.');
        }

        return (int) $staff->health_center_id;
    }

    private function currentHealthCenter()
    {
        return HealthCenter::findOrFail($this->currentHealthCenterId());
    }

    private function authorizeAnnouncement(Announcement $announcement): void
    {
        $healthCenterId = $this->currentHealthCenterId();

        if ((int) $announcement->health_center_id !== $healthCenterId) {
            abort(403, 'You cannot modify announcements for another health center.');
        }
    }
}
