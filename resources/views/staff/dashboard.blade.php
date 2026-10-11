@extends('layouts.dashboard')

@section('title', 'Staff Dashboard')

@section('page-title', 'Staff Dashboard')

@section('content')

<div class="staff-dashboard">

    {{-- Welcome --}}
    <section class="welcome-banner" aria-label="Welcome">

        <div>
            <span class="welcome-label">
                E Konsulta Staff Portal
            </span>

            <h1>
                Good day, {{ auth()->user()->name }}!
            </h1>

            <p>
                Manage appointments, patients, consultations, and
                health-center services from one place.
            </p>

            <div class="staff-focus-pill" aria-label="Health center">
                <span class="staff-focus-dot" aria-hidden="true"></span>
                Health center: {{ auth()->user()->staff?->healthCenter?->name ?? 'Not assigned' }}
            </div>

            <div class="staff-welcome-actions">
                <a href="{{ route('staff.appointment-schedules.index') }}" class="primary-button">
                    Manage Schedules
                </a>
            </div>

        </div>

        <div class="welcome-icon" aria-hidden="true">
            🩺
        </div>

    </section>


    {{-- Statistics --}}
    <section class="stats-grid" aria-label="Staff summary">

        <x-stat-card
            icon="👥"
            label="Registered Patients"
            :value="$registeredPatients"
            description="Patients in the system"
        />

        <x-stat-card
            icon="📅"
            label="Today's Appointments"
            :value="$todayAppointments"
            description="Scheduled for today"
        />

        <x-stat-card
            icon="🩺"
            label="Today's Consultations"
            :value="$todayConsultations"
            description="Consultations today"
        />

        <x-stat-card
            icon="⏳"
            label="Pending Requests"
            :value="$pendingRequests"
            description="Appointments awaiting action"
        />

    </section>


    {{-- Main Grid --}}
    <div class="dashboard-grid">

        {{-- Upcoming Appointments --}}
        <section class="dashboard-card" aria-labelledby="upcoming-title">

            <div class="card-header">

                <div>
                    <h3 id="upcoming-title">
                        Upcoming Appointments
                    </h3>

                    <p>
                        Scheduled patient visits
                    </p>
                </div>

                <a href="{{ route('staff.appointments.index') }}">
                    View All
                </a>

            </div>


            @if($upcomingAppointments->isNotEmpty())

                <div class="info-list">

                    @foreach($upcomingAppointments->take(5) as $appointment)

                        <div class="info-item">

                            <div class="info-icon">
                                📅
                            </div>

                            <div>

                                <strong>
                                    {{ $appointment->patient?->user?->name ?? 'Patient' }}
                                </strong>

                                <p>
                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                    at
                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                </p>

                                @if($appointment->doctor?->user)

                                    <p>
                                        Doctor:
                                        {{ $appointment->doctor->user->name }}
                                    </p>

                                @endif

                                @if($appointment->healthCenter)

                                    <p>
                                        {{ $appointment->healthCenter->name }}
                                    </p>

                                @endif

                                <span class="appointment-status status-{{ strtolower($appointment->status) }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="empty-state">

                    <div class="empty-icon">
                        📅
                    </div>

                    <h4>
                        No upcoming appointments
                    </h4>

                    <p>
                        There are currently no upcoming appointments.
                    </p>

                    <a
                        href="{{ route('staff.appointments.index') }}"
                        class="primary-button"
                    >
                        View Appointments
                    </a>

                </div>

            @endif

        </section>


        {{-- Quick Actions --}}
        <section class="dashboard-card" aria-labelledby="quick-title">

            <div class="card-header">

                <div>
                    <h3 id="quick-title">
                        Quick Actions
                    </h3>

                    <p>
                        Common staff services
                    </p>
                </div>

            </div>


            <div class="quick-actions">

                <a
                    href="{{ route('staff.appointments.index') }}"
                    class="quick-action"
                >

                    <span aria-hidden="true">
                        📅
                    </span>

                    <strong>
                        Appointments
                    </strong>

                    <small>
                        View and manage appointments
                    </small>

                </a>


                <a
                    href="{{ route('staff.patients.index') }}"
                    class="quick-action"
                >

                    <span aria-hidden="true">
                        👥
                    </span>

                    <strong>
                        Patients
                    </strong>

                    <small>
                        View registered patients
                    </small>

                </a>


                <a
                    href="{{ route('staff.consultations.index') }}"
                    class="quick-action"
                >

                    <span aria-hidden="true">
                        🩺
                    </span>

                    <strong>
                        Consultations
                    </strong>

                    <small>
                        View patient consultations
                    </small>

                </a>


                <a
                    href="{{ route('staff.prescriptions.index') }}"
                    class="quick-action"
                >

                    <span aria-hidden="true">
                        💊
                    </span>

                    <strong>
                        Prescriptions
                    </strong>

                    <small>
                        Manage prescription releases
                    </small>

                </a>


                <a
                    href="{{ route('staff.appointment-schedules.index') }}"
                    class="quick-action"
                >

                    <span aria-hidden="true">
                        🗓️
                    </span>

                    <strong>
                        Schedules
                    </strong>

                    <small>
                        Manage appointment schedules
                    </small>

                </a>


                <a
                    href="{{ route('staff.health-centers.index') }}"
                    class="quick-action"
                >

                    <span aria-hidden="true">
                        🏥
                    </span>

                    <strong>
                        Health Centers
                    </strong>

                    <small>
                        View health-center information
                    </small>

                </a>

            </div>

        </section>

    </div>


    {{-- Today's Appointments --}}
    <section class="dashboard-card" aria-labelledby="today-title">

        <div class="card-header">

            <div>
                <h3 id="today-title">
                    Today's Appointments
                </h3>

                <p>
                    Appointments scheduled for {{ now()->format('F d, Y') }}
                </p>
            </div>

            <a href="{{ route('staff.appointments.index') }}">
                View All
            </a>

        </div>


        @if($appointments->isNotEmpty())

            <div class="info-list">

                @foreach($appointments->take(8) as $appointment)

                    <div class="info-item">

                        <div class="info-icon">
                            🕐
                        </div>

                        <div>

                            <strong>
                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                —
                                {{ $appointment->patient?->user?->name ?? 'Patient' }}
                            </strong>

                            <p>
                                Status:
                                {{ ucfirst($appointment->status) }}
                            </p>

                            @if($appointment->reason)

                                <p>
                                    {{ $appointment->reason }}
                                </p>

                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="empty-state">

                <div class="empty-icon">
                    📅
                </div>

                <h4>
                    No appointments today
                </h4>

                <p>
                    There are no appointments scheduled for today.
                </p>

            </div>

        @endif

    </section>


    {{-- Staff Reminder --}}
    <section class="dashboard-card health-reminder">

        <div class="reminder-icon" aria-hidden="true">
            🩺
        </div>

        <div>

            <h3>
                Keep patient services up to date
            </h3>

            <p>
                Review today's appointments, complete patient intake
                information, and keep consultation and prescription
                records updated for your assigned health center.
            </p>

        </div>

    </section>

</div>

@endsection