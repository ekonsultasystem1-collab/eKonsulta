@extends('layouts.dashboard')

@section('title', 'Announcements')
@section('page-title', 'Announcements')

@section('content')
    <div class="announcement-management-page">
        <section class="announcement-management-hero">
            <div>
                <h2>Announcements</h2>
                <p>Manage updates for {{ auth()->user()->staff?->healthCenter?->name ?? 'your health center' }}.</p>
            </div>
            <a href="{{ route('staff.announcements.create') }}" class="primary-button">
                Create announcement
            </a>
        </section>

        @if(session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif

        <nav class="announcement-page-navigation" aria-label="Announcement navigation">
            <a href="{{ route('staff.dashboard') }}" class="secondary-button">Back to Dashboard</a>
        </nav>

        <div class="announcement-management-heading">
            <div>
                <h3>All announcements</h3>
            </div>
        </div>

        @if($announcements->isEmpty())
            <div class="announcement-empty">
                <span class="announcement-empty-icon" aria-hidden="true">✦</span>
                <h3>No announcements yet</h3>
                <p>Publish a notice to keep patients informed about services and health center news.</p>
                <a href="{{ route('staff.announcements.create') }}" class="primary-button">Create announcement</a>
            </div>
        @else
            <x-list-search target="staff-announcement-list" placeholder="Search announcement title, status, or content..." label="Search announcements" />

            <div class="announcement-management-list" id="staff-announcement-list">
                @foreach($announcements as $announcement)
                    <article class="announcement-management-card" data-search-item data-sort-timestamp="{{ ($announcement->published_at ?? $announcement->created_at)?->format('U.u') ?? '' }}" data-sort-order="{{ $announcement->id }}">
                        <div class="announcement-management-thumb">
                            @if($announcement->image_path)
                                <button
                                    type="button"
                                    class="announcement-image-preview-trigger"
                                    data-image-preview="{{ asset('storage/' . $announcement->image_path) }}"
                                    data-image-alt="{{ $announcement->title }}"
                                    aria-label="Preview image for {{ $announcement->title }}"
                                >
                                    <img src="{{ asset('storage/' . $announcement->image_path) }}" alt="">
                                </button>
                            @else
                                <span aria-hidden="true">✚</span>
                            @endif
                        </div>
                        <div class="announcement-management-copy">
                            <div class="announcement-management-meta">
                                <span class="announcement-status {{ $announcement->is_published ? 'announcement-status--published' : 'announcement-status--draft' }}">
                                    <span></span>{{ $announcement->is_published ? 'Published' : 'Draft' }}
                                </span>
                                <span>{{ $announcement->published_at?->format('M j, Y') ?? 'Not published' }}</span>
                                @if($announcement->event_end_date)
                                    <span>Ends {{ $announcement->event_end_date->format('M j, Y') }}</span>
                                @endif
                            </div>
                            <h4>{{ $announcement->title }}</h4>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($announcement->content), 150) }}</p>
                            <small>Created by {{ $announcement->creator?->name ?? 'System' }}</small>
                            <a class="announcement-view-link" href="{{ route('staff.announcements.show', $announcement) }}">
                                View full announcement
                            </a>
                        </div>
                        <div class="announcement-management-actions">
                            <a href="{{ route('staff.announcements.show', $announcement) }}" class="secondary-button secondary-button--small">View</a>
                            <a href="{{ route('staff.announcements.edit', $announcement) }}" class="secondary-button secondary-button--small">Edit</a>
                            <form method="POST" action="{{ route('staff.announcements.toggle-status', $announcement) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="announcement-action-button">
                                    {{ $announcement->is_published ? 'Unpublish' : 'Publish' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('staff.announcements.destroy', $announcement) }}" data-confirm="This announcement will be permanently deleted." data-confirm-title="Delete announcement?" data-confirm-ok="Delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="announcement-delete-button">Delete</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
@endsection
