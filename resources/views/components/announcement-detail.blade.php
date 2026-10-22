@props(['announcement', 'backRoute', 'editRoute' => null])

<article class="announcement-detail-page announcement-management-detail">
    <a href="{{ route($backRoute) }}" class="announcement-back-link">
        <span aria-hidden="true">←</span>
        Back to announcements
    </a>

    <header class="announcement-detail-header">
        <span class="announcement-detail-label">
            {{ $announcement->is_published ? 'Published announcement' : 'Draft announcement' }}
        </span>
        <h2>{{ $announcement->title }}</h2>
        <div class="announcement-detail-meta">
            <span class="announcement-source">
                {{ $announcement->healthCenter?->name ?? 'Global announcement' }}
            </span>
            <span class="announcement-meta-separator" aria-hidden="true"></span>
            <time datetime="{{ $announcement->published_at?->toIso8601String() }}">
                {{ $announcement->published_at?->format('F j, Y \a\t g:i A') ?? 'Not published' }}
            </time>
            @if($announcement->event_start_date || $announcement->event_end_date)
                <span class="announcement-meta-separator" aria-hidden="true"></span>
                <span>
                    Event dates:
                    {{ $announcement->event_start_date?->format('M j, Y') ?? 'Any date' }}
                    – {{ $announcement->event_end_date?->format('M j, Y') ?? 'No end date' }}
                </span>
            @endif
        </div>
    </header>

    @if($announcement->image_path)
        <figure class="announcement-detail-image">
            <button
                type="button"
                class="announcement-image-preview-trigger"
                data-image-preview="{{ asset('storage/' . $announcement->image_path) }}"
                data-image-alt="{{ $announcement->title }}"
                aria-label="Preview image for {{ $announcement->title }}"
            >
                <img src="{{ asset('storage/' . $announcement->image_path) }}" alt="{{ $announcement->title }}">
                <span class="announcement-image-preview-hint">Click to enlarge</span>
            </button>
        </figure>
    @endif

    <div class="announcement-detail-body" aria-label="Announcement content">
        {!! nl2br(e($announcement->content)) !!}
    </div>

    <footer class="announcement-detail-footer">
        <div>
            <strong>Announcement information</strong>
            <p>
                Created by {{ $announcement->creator?->name ?? 'System' }}
                · {{ $announcement->is_published ? 'Visible to its audience' : 'Only visible to administrators and staff' }}
                @if($announcement->event_end_date)
                    · Visible through {{ $announcement->event_end_date->format('F j, Y') }}
                @endif
            </p>
        </div>
        <div class="form-actions">
            <a href="{{ route($backRoute) }}" class="secondary-button">Back to list</a>
            @if($editRoute)
                <a href="{{ route($editRoute, $announcement) }}" class="primary-button">Edit announcement</a>
            @endif
        </div>
    </footer>
</article>
