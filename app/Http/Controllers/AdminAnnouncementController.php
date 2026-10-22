<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\HealthCenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminAnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::with(['healthCenter', 'creator'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function show(Announcement $announcement): View
    {
        $announcement->load(['healthCenter', 'creator']);

        return view('admin.announcements.show', compact('announcement'));
    }

    public function create(): View
    {
        $healthCenters = HealthCenter::orderBy('name')->get();

        return view('admin.announcements.create', compact('healthCenters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAnnouncementData($request);

        $announcement = Announcement::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image_path' => $this->storeImage($request),
            'health_center_id' => $validated['health_center_id'] ?? null,
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
            ->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function edit(Announcement $announcement): View
    {
        $healthCenters = HealthCenter::orderBy('name')->get();

        return view('admin.announcements.edit', compact('announcement', 'healthCenters'));
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $this->validateAnnouncementData($request, $announcement);
        $wasPublished = $announcement->is_published;

        $announcement->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'health_center_id' => $validated['health_center_id'] ?? null,
            'is_published' => $validated['is_published'],
            'published_at' => $validated['is_published'] ? ($announcement->published_at ?? now()) : null,
            'event_start_date' => $validated['event_start_date'] ?? null,
            'event_end_date' => $validated['event_end_date'] ?? null,
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $this->storeImage($request, $announcement);
            $announcement->update(['image_path' => $imagePath]);
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
            ->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function toggleStatus(Announcement $announcement): RedirectResponse
    {
        if ($announcement->is_published) {
            $announcement->unpublish();

            return redirect()
                ->route('admin.announcements.index')
                ->with('success', 'Announcement unpublished successfully.');
        }

        $announcement->publish();

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement published successfully.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        if ($announcement->image_path && Storage::disk('public')->exists($announcement->image_path)) {
            Storage::disk('public')->delete($announcement->image_path);
        }

        $announcement->delete();

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }

    private function validateAnnouncementData(Request $request, ?Announcement $announcement = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'health_center_id' => ['nullable', 'integer', 'exists:health_centers,id'],
            'event_start_date' => ['nullable', 'date_format:Y-m-d'],
            'event_end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:event_start_date'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $validated['is_published'] = (bool) ($request->boolean('is_published'));

        return $validated;
    }

    private function storeImage(Request $request, ?Announcement $announcement = null): ?string
    {
        if ($request->hasFile('image')) {
            if ($announcement && $announcement->image_path && Storage::disk('public')->exists($announcement->image_path)) {
                Storage::disk('public')->delete($announcement->image_path);
            }

            $path = $request->file('image')->store('announcements', 'public');

            return $path;
        }

        return $announcement?->image_path;
    }
}
