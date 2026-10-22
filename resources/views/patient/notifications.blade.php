@extends('layouts.dashboard')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
    @php
        $unreadNotifications = $notifications->whereNull('read_at');
        $readNotifications = $notifications->whereNotNull('read_at');
        $markReadRoute = $routePrefix . '.notifications.read';
    @endphp

    <div class="notifications-page">
        <section class="notifications-hero">
            <div class="notifications-hero-copy">
                <h2>Notifications</h2>
                <p>Announcements and updates from your health center.</p>
            </div>
            @if($unreadNotifications->isNotEmpty())
                <span class="notification-count-pill">{{ $unreadNotifications->count() }} unread</span>
            @endif
        </section>

        @if($notifications->isEmpty())
            <div class="announcement-empty notifications-empty">
                <span class="announcement-empty-icon" aria-hidden="true">✓</span>
                <h3>No notifications yet</h3>
                <p>New announcements for you will appear here.</p>
            </div>
        @else
            <x-list-search target="notification-search-list" placeholder="Search notification title, message, or date..." label="Search notifications" />

            <div id="notification-search-list">
            @if($unreadNotifications->isNotEmpty())
                <section class="notification-group" data-search-group>
                    <div class="notification-group-heading">
                        <div>
                            <h3>Unread</h3>
                        </div>
                    </div>

                    <div class="notification-list">
                        @foreach($unreadNotifications as $notification)
                            @php
                                $announcementLink = $notification->announcement_id
                                    ? ($routePrefix === 'patient'
                                        ? route('patient.announcements.show', $notification->announcement_id)
                                        : route($routePrefix . '.announcements.index'))
                                    : null;
                            @endphp
                            <article class="notification-card notification-card--unread" data-search-item data-sort-timestamp="{{ $notification->created_at->format('U.u') }}">
                                <div class="notification-card-copy">
                                    <div class="notification-card-topline">
                                        <span class="notification-unread-label"><span></span>Unread</span>
                                        <time datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->format('M j, Y · g:i A') }}</time>
                                    </div>
                                    @if($announcementLink)
                                        <a href="{{ $announcementLink }}" class="notification-open-link">
                                            <h4>{{ $notification->title }}</h4>
                                            <p>{{ $notification->message }}</p>
                                        </a>
                                    @else
                                        <h4>{{ $notification->title }}</h4>
                                        <p>{{ $notification->message }}</p>
                                    @endif
                                    <div class="notification-card-actions">
                                        @if($announcementLink)
                                            <a href="{{ $announcementLink }}" class="announcement-read-link">
                                                {{ auth()->user()->isPatient() ? 'View announcement' : 'View announcements' }}
                                                <span aria-hidden="true">↗</span>
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ route($markReadRoute, $notification) }}">
                                            @csrf
                                            <button type="submit" class="notification-mark-read">
                                                <span aria-hidden="true">✓</span>
                                                Mark as read
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($readNotifications->isNotEmpty())
                <section class="notification-group notification-group--read" data-search-group>
                    <div class="notification-group-heading">
                        <div>
                            <h3>Earlier</h3>
                        </div>
                    </div>

                    <div class="notification-list">
                        @foreach($readNotifications as $notification)
                            @php
                                $announcementLink = $notification->announcement_id
                                    ? ($routePrefix === 'patient'
                                        ? route('patient.announcements.show', $notification->announcement_id)
                                        : route($routePrefix . '.announcements.index'))
                                    : null;
                            @endphp
                            <article class="notification-card notification-card--read" data-search-item data-sort-timestamp="{{ $notification->created_at->format('U.u') }}">
                                <div class="notification-card-copy">
                                    <div class="notification-card-topline">
                                        <time datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->format('M j, Y · g:i A') }}</time>
                                    </div>
                                    @if($announcementLink)
                                        <a href="{{ $announcementLink }}" class="notification-open-link">
                                            <h4>{{ $notification->title }}</h4>
                                            <p>{{ $notification->message }}</p>
                                        </a>
                                        <a href="{{ $announcementLink }}" class="announcement-read-link">
                                            {{ auth()->user()->isPatient() ? 'View announcement' : 'View announcements' }}
                                            <span aria-hidden="true">↗</span>
                                        </a>
                                    @else
                                        <h4>{{ $notification->title }}</h4>
                                        <p>{{ $notification->message }}</p>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
            </div>
        @endif
    </div>
@endsection
