@extends('layouts.dashboard')

@section('title', 'Announcements')
@section('page-title', 'Announcements')

@section('content')
    <div class="patient-announcements-page">
        <section class="announcement-hero">
            <div class="announcement-hero-copy">
                <h2>Announcements</h2>
                <p>Health updates and important notices from your City Health Office and local health center.</p>
            </div>
            <div class="announcement-hero-count">
                <strong>{{ $announcements->count() }}</strong>
                <span>{{ \Illuminate\Support\Str::plural('announcement', $announcements->count()) }}</span>
            </div>
        </section>

        <div class="announcement-feed-heading">
            <div>
                <h3>Latest updates</h3>
            </div>
        </div>

        @if($announcements->isEmpty())
            <div class="announcement-empty">
                <span class="announcement-empty-icon" aria-hidden="true">✦</span>
                <h3>No announcements yet</h3>
                <p>New updates from your health center will appear here.</p>
            </div>
        @else
            <x-list-search target="patient-announcement-list" placeholder="Search by announcement title, health center, or topic..." label="Search announcements" />

            <div class="announcement-feed" id="patient-announcement-list">
                @foreach($announcements as $announcement)
                    <article class="announcement-card" data-search-item data-sort-timestamp="{{ ($announcement->published_at ?? $announcement->created_at)?->format('U.u') ?? '' }}" data-sort-order="{{ $announcement->id }}">
                        @if($announcement->image_path)
                            <button
                                type="button"
                                class="announcement-card-visual announcement-image-preview-trigger"
                                data-image-preview="{{ asset('storage/' . $announcement->image_path) }}"
                                data-image-alt="{{ $announcement->title }}"
                                aria-label="Preview image for {{ $announcement->title }}"
                            >
                                <img src="{{ asset('storage/' . $announcement->image_path) }}" alt="">
                            </button>
                        @else
                            <a class="announcement-card-visual" href="{{ route('patient.announcements.show', $announcement) }}" aria-label="Read {{ $announcement->title }}">
                                <span class="announcement-visual-mark" aria-hidden="true">✚</span>
                            </a>
                        @endif

                        <div class="announcement-card-content">
                            <div class="announcement-card-meta">
                                <span class="announcement-source">
                                    {{ $announcement->healthCenter?->name ?? 'City Health Office' }}
                                </span>
                                <span class="announcement-date">{{ $announcement->published_at?->format('M j, Y') ?? 'Recently' }}</span>
                                @if($announcement->event_end_date)
                                    <span class="announcement-date">Until {{ $announcement->event_end_date->format('M j, Y') }}</span>
                                @endif
                            </div>

                            <a class="announcement-card-open" href="{{ route('patient.announcements.show', $announcement) }}">
                                <h3>{{ $announcement->title }}</h3>
                                <p class="announcement-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($announcement->content), 180) }}</p>
                            </a>

                            <a href="{{ route('patient.announcements.show', $announcement) }}" class="announcement-read-link">
                                View announcement <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
@endsection
