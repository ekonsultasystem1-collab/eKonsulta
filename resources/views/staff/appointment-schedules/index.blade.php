@extends('layouts.dashboard')

@section('title', 'Appointment Schedules')

@section('page-title', 'Appointment Schedules')

@section('content')

@php
    $upcomingSchedules = $schedules->filter(function ($schedule) {
        if ($schedule->schedule_date->isFuture()) {
            return true;
        }

        return $schedule->schedule_date->isToday()
            && $schedule->appointment_time->format('H:i') >= now()->format('H:i');
    });
    $pastSchedules = $schedules->filter(
        fn ($schedule) => !$schedule->schedule_date->isFuture()
            && !(
                $schedule->schedule_date->isToday()
                && $schedule->appointment_time->format('H:i') >= now()->format('H:i')
            )
    )->sortByDesc(fn ($schedule) => $schedule->schedule_date->format('Y-m-d') . ' ' . $schedule->appointment_time->format('H:i'));
@endphp

<div class="page-header">
    <div>
        <span class="page-eyebrow">STAFF WORKSPACE</span>
        <h1>Appointment Schedules</h1>
        <p>Create and review patient booking slots for your health center.</p>
    </div>

    <a href="{{ route('staff.appointment-schedules.create') }}" class="primary-button schedule-create-action">
        <span aria-hidden="true">+</span> Create schedules
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success" role="status">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" role="alert">
        {{ session('error') }}
    </div>
@endif

<section class="schedule-overview" aria-label="Schedule overview">
    <div>
        <span>Total schedules</span>
        <strong>{{ $schedules->count() }}</strong>
    </div>
    <div>
        <span>Upcoming schedules</span>
        <strong>{{ $upcomingSchedules->count() }}</strong>
    </div>
    <div>
        <span>Active bookings</span>
        <strong>{{ $schedules->sum('active_appointments_count') }}</strong>
    </div>
</section>

<div class="card schedule-list-card">
    <div class="card-header">
        <div>
            <h2>Manage booking slots</h2>
            <p>Review upcoming slots first. Past slots remain below for reference.</p>
        </div>
    </div>

    <div class="card-body">
        @if($schedules->count())
            <div class="schedule-list-guidance">
                <span class="schedule-guidance-icon" aria-hidden="true">i</span>
                <p><strong>Availability</strong> shows remaining places. Slots with active bookings are protected from deletion.</p>
            </div>

            <x-list-search target="staff-schedule-list" placeholder="Search date, service, doctor, or health center..." label="Search appointment schedules" />

            <div class="schedule-groups" id="staff-schedule-list">
                @if($upcomingSchedules->isNotEmpty())
                    <section class="schedule-group" data-search-group aria-labelledby="upcoming-schedules-heading">
                        <div class="schedule-group-heading">
                            <div>
                                <h3 id="upcoming-schedules-heading">Upcoming schedules</h3>
                                <p>Slots taking place later today or on a future date.</p>
                            </div>
                            <span>{{ $upcomingSchedules->count() }}</span>
                        </div>
                        <div class="schedule-card-list">
                            @foreach($upcomingSchedules as $schedule)
                    @php
                        $booked = (int) $schedule->active_appointments_count;
                        $available = max($schedule->capacity - $booked, 0);
                        $occupancy = $schedule->capacity > 0
                            ? min(($booked / $schedule->capacity) * 100, 100)
                            : 0;
                    @endphp

                    <article class="schedule-card" data-search-item>
                        <div class="schedule-card-date">
                            <span>{{ $schedule->schedule_date->format('D') }}</span>
                            <strong>{{ $schedule->schedule_date->format('d') }}</strong>
                            <span>{{ $schedule->schedule_date->format('M Y') }}</span>
                        </div>

                        <div class="schedule-card-main">
                            <div class="schedule-card-heading">
                                <div>
                                    <span class="schedule-card-eyebrow">{{ $schedule->healthCenter->name }}</span>
                                    <h3>{{ $schedule->appointment_time->format('h:i A') }}</h3>
                                    <div class="schedule-card-details">
                                        <span><strong>Service</strong> {{ $schedule->service->name ?? 'Not specified' }}</span>
                                        <span><strong>Doctor</strong> {{ $schedule->doctor?->user?->name ?? 'Not assigned' }}</span>
                                    </div>
                                </div>
                                <span class="schedule-availability {{ $available === 0 ? 'is-full' : 'is-open' }}">
                                    {{ $available === 0 ? 'Fully booked' : $available . ' ' . \Illuminate\Support\Str::plural('place', $available) . ' left' }}
                                </span>
                            </div>

                            <div class="schedule-capacity">
                                <div class="schedule-capacity-label">
                                    <span>Booked places</span>
                                    <strong>{{ $booked }} / {{ $schedule->capacity }}</strong>
                                </div>
                                <div class="schedule-capacity-track" role="progressbar" aria-label="Booked places for {{ $schedule->service->name ?? 'schedule' }} on {{ $schedule->schedule_date->format('F j, Y') }}" aria-valuemin="0" aria-valuemax="{{ $schedule->capacity }}" aria-valuenow="{{ min($booked, $schedule->capacity) }}">
                                    <span style="width: {{ $occupancy }}%"></span>
                                </div>
                            </div>
                        </div>

                        <div class="schedule-card-action">
                            @if($booked === 0)
                                <form
                                    method="POST"
                                    action="{{ route('staff.appointment-schedules.destroy', $schedule) }}"
                                    data-confirm="This empty schedule slot will be removed and can no longer be booked."
                                    data-confirm-title="Delete this schedule?"
                                    data-confirm-ok="Delete schedule"
                                    data-confirm-cancel="Keep schedule"
                                    data-confirm-icon="warning"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="schedule-delete-button">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M10 11v6m4-6v6M5 7l1 13h12l1-13M9 7V4h6v3"/></svg>
                                        <span>Delete schedule</span>
                                    </button>
                                </form>
                            @else
                                <span class="schedule-protected-label" title="A schedule with active bookings cannot be deleted">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                                    Bookings protect this slot
                                </span>
                            @endif
                        </div>
                    </article>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if($pastSchedules->isNotEmpty())
                    <section class="schedule-group schedule-group-past" data-search-group aria-labelledby="past-schedules-heading">
                        <div class="schedule-group-heading">
                            <div>
                                <h3 id="past-schedules-heading">Past schedules</h3>
                                <p>Previous slots are kept here for reference.</p>
                            </div>
                            <span>{{ $pastSchedules->count() }}</span>
                        </div>
                        <div class="schedule-card-list">
                            @foreach($pastSchedules as $schedule)
                                @php
                                    $booked = (int) $schedule->active_appointments_count;
                                    $available = max($schedule->capacity - $booked, 0);
                                    $occupancy = $schedule->capacity > 0
                                        ? min(($booked / $schedule->capacity) * 100, 100)
                                        : 0;
                                @endphp

                                <article class="schedule-card schedule-card-past" data-search-item>
                                    <div class="schedule-card-date">
                                        <span>{{ $schedule->schedule_date->format('D') }}</span>
                                        <strong>{{ $schedule->schedule_date->format('d') }}</strong>
                                        <span>{{ $schedule->schedule_date->format('M Y') }}</span>
                                    </div>

                                    <div class="schedule-card-main">
                                        <div class="schedule-card-heading">
                                            <div>
                                                <span class="schedule-card-eyebrow">{{ $schedule->healthCenter->name }}</span>
                                                <h3>{{ $schedule->appointment_time->format('h:i A') }}</h3>
                                                <div class="schedule-card-details">
                                                    <span><strong>Service</strong> {{ $schedule->service->name ?? 'Not specified' }}</span>
                                                    <span><strong>Doctor</strong> {{ $schedule->doctor?->user?->name ?? 'Not assigned' }}</span>
                                                </div>
                                            </div>
                                            <span class="schedule-availability is-past">Past slot</span>
                                        </div>

                                        <div class="schedule-capacity">
                                            <div class="schedule-capacity-label">
                                                <span>Active bookings</span>
                                                <strong>{{ $booked }} / {{ $schedule->capacity }}</strong>
                                            </div>
                                            <div class="schedule-capacity-track" role="progressbar" aria-label="Active bookings for {{ $schedule->service->name ?? 'schedule' }} on {{ $schedule->schedule_date->format('F j, Y') }}" aria-valuemin="0" aria-valuemax="{{ $schedule->capacity }}" aria-valuenow="{{ min($booked, $schedule->capacity) }}">
                                                <span style="width: {{ $occupancy }}%"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="schedule-card-action">
                                        @if($booked === 0)
                                            <form
                                                method="POST"
                                                action="{{ route('staff.appointment-schedules.destroy', $schedule) }}"
                                                data-confirm="This past schedule slot will be permanently removed."
                                                data-confirm-title="Delete this past schedule?"
                                                data-confirm-ok="Delete schedule"
                                                data-confirm-cancel="Keep schedule"
                                                data-confirm-icon="warning"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="schedule-delete-button">
                                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M10 11v6m4-6v6M5 7l1 13h12l1-13M9 7V4h6v3"/></svg>
                                                    <span>Delete schedule</span>
                                                </button>
                                            </form>
                                        @else
                                            <span class="schedule-protected-label" title="A schedule with active bookings cannot be deleted">
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                                                Bookings protect this slot
                                            </span>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon" aria-hidden="true">📅</div>
                <h3>No appointment schedules yet</h3>
                <p>Create an appointment schedule so patients can book available consultation slots.</p>
                <a href="{{ route('staff.appointment-schedules.create') }}" class="btn btn-primary">
                    Create First Schedule
                </a>
            </div>
        @endif
    </div>
</div>

@endsection
