@extends('layouts.dashboard')

@section('title', 'Patient Dashboard')

@section('page-title', 'Patient Dashboard')

@section('content')

<div class="patient-dashboard">

    {{-- Welcome --}}
    <section class="welcome-banner">

        <div>
            <span class="welcome-label">
                E Konsulta Patient Portal
            </span>

            <h1>
                Good day, {{ auth()->user()->name }}!
            </h1>

            <p>
                Manage your appointments, consultations,
                prescriptions, and health records in one place.
            </p>

            <div class="patient-welcome-actions">
                <a href="{{ route('patient.appointments.create') }}" class="primary-button">
                    Book an Appointment
                </a>
            </div>

        </div>

        <div class="welcome-icon" aria-hidden="true">
            🩺
        </div>

    </section>


    {{-- Statistics --}}
    <section class="stats-grid" aria-label="Summary">

        <x-stat-card
            icon="📅"
            label="Upcoming Appointments"
            :value="$upcomingAppointments->count()"
            description="Scheduled visits"
        />

        <x-stat-card
            icon="🩺"
            label="Consultations"
            :value="$consultationCount"
            description="Total consultations"
        />

        <x-stat-card
            icon="💊"
            label="Prescriptions"
            :value="$prescriptionCount"
            description="Total prescriptions"
        />

        <x-stat-card
            icon="🔔"
            label="Notifications"
            :value="$notificationCount"
            description="Unread notifications"
        />

    </section>


    {{-- Main Grid --}}
    <div class="dashboard-grid">

        {{-- Upcoming Appointment --}}
        <section class="dashboard-card">

            <div class="card-header">

                <div>
                    <h3>Upcoming Appointment</h3>
                    <p>Your next scheduled visit</p>
                </div>

                <a href="{{ route('patient.appointments.index') }}">
                    View All
                </a>

            </div>


            @if($nextAppointment)

                <div class="appointment-preview">

                    <div class="appointment-date">

                        <strong>
                            {{ \Carbon\Carbon::parse($nextAppointment->appointment_date)->format('M') }}
                        </strong>

                        <span>
                            {{ \Carbon\Carbon::parse($nextAppointment->appointment_date)->format('d') }}
                        </span>

                    </div>


                    <div class="appointment-details">

                        <h4>
                            {{ \Carbon\Carbon::parse($nextAppointment->appointment_date)->format('l, F j, Y') }}
                        </h4>

                        <p>
                            🕐
                            {{ \Carbon\Carbon::parse($nextAppointment->appointment_time)->format('g:i A') }}
                        </p>


                        @if($nextAppointment->doctor && $nextAppointment->doctor->user)

                            @php
                                $doctorServiceNames = $nextAppointment->doctor->services
                                    ->pluck('name')
                                    ->filter()
                                    ->take(2)
                                    ->values()
                                    ->all();
                                $doctorServiceLabel = $doctorServiceNames
                                    ? implode(' / ', $doctorServiceNames)
                                    : ($nextAppointment->doctor->specialization ?? 'Medical Doctor');

                                if ($nextAppointment->doctor->services->count() > 2) {
                                    $doctorServiceLabel .= ' +';
                                }

                                if (mb_strlen($doctorServiceLabel) > 28) {
                                    $doctorServiceLabel = mb_substr($doctorServiceLabel, 0, 25) . '…';
                                }
                            @endphp

                            <p class="doctor-profile-line">
                                👨‍⚕️
                                {{ $nextAppointment->doctor->user->name }}
                            </p>

                            <p class="doctor-service-line">
                                🩺
                                {{ $doctorServiceLabel }}
                            </p>

                        @endif


                        @if($nextAppointment->healthCenter)

                            <p>
                                🏥
                                {{ $nextAppointment->healthCenter->name }}
                            </p>

                        @endif


                        @if($nextAppointment->reason)

                            <p>
                                <strong>Reason:</strong> {{ $nextAppointment->reason }}
                            </p>

                        @endif


                        <span class="appointment-status status-{{ strtolower($nextAppointment->status) }}">
                            {{ ucfirst($nextAppointment->status) }}
                        </span>

                    </div>

                </div>

            @else

                <div class="empty-state">

                    <div class="empty-icon">
                        📅
                    </div>

                    <h4>No upcoming appointments</h4>

                    <p>
                        You currently don't have a scheduled appointment.
                    </p>

                    <a
                        href="{{ route('patient.appointments.create') }}"
                        class="primary-button"
                    >
                        Book an Appointment
                    </a>

                </div>

            @endif

        </section>


        {{-- Quick Actions --}}
        <section class="dashboard-card">

            <div class="card-header">

                <div>
                    <h3>Quick Actions</h3>
                    <p>Common patient services</p>
                </div>

            </div>


            <div class="quick-actions">

                <a
                    href="{{ route('patient.appointments.create') }}"
                    class="quick-action"
                >

                    <span>📅</span>

                    <strong>
                        Book Appointment
                    </strong>

                    <small>
                        Schedule a consultation
                    </small>

                </a>


                <a href="{{ route('patient.medical-records.index') }}" class="quick-action">

                    <span>📋</span>

                    <strong>Medical Records</strong>

                    <small>View your health history</small>

                </a>


                <a href="{{ route('patient.prescriptions.index') }}" class="quick-action">

                    <span>💊</span>

                    <strong>
                        Prescriptions
                    </strong>

                    <small>
                        View prescribed medicines
                    </small>

                </a>


                <a href="{{ route('patient.profile') }}" class="quick-action">

                    <span>👤</span>

                    <strong>
                        My Profile
                    </strong>

                    <small>
                        Update your information
                    </small>

                </a>

            </div>

        </section>

    </div>


    {{-- Optional panels from the paper (render only if the controller supplies the data) --}}
    @if(isset($announcements) || isset($recentPrescription))

        <div class="dashboard-grid dashboard-grid--even">

            @isset($recentPrescription)
                <section class="dashboard-card">
                    <div class="card-header">
                        <div>
                            <h3>Recent Prescription</h3>
                            <p>Your latest prescribed medicine</p>
                        </div>
                        <a href="{{ route('patient.prescriptions.index') }}">View All</a>
                    </div>

                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-icon">💊</div>
                            <div>
                                <strong>{{ $recentPrescription->prescription_number }}</strong>
                                <p>{{ $recentPrescription->prescription_date->format('F j, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </section>
            @endisset

            @isset($announcements)
                <section class="dashboard-card patient-announcement-preview">
                    <div class="card-header">
                        <div>
                            <h3>Announcements</h3>
                            <p>News from your City Health Office</p>
                        </div>
                        <a href="{{ route('patient.announcements.index') }}">View All</a>
                    </div>

                    <div class="info-list announcement-preview-list">
                        @forelse($announcements as $announcement)
                            <div class="info-item announcement-preview-item">
                                <div class="info-icon">📢</div>
                                <div>
                                    <strong>
                                        <a href="{{ route('patient.announcements.show', $announcement) }}">{{ $announcement->title }}</a>
                                    </strong>
                                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($announcement->content), 100) }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No announcements right now.</p>
                        @endforelse
                    </div>
                </section>
            @endisset

        </div>

    @endif


    {{-- Health Reminder --}}
    <section class="dashboard-card health-reminder">

        <div class="reminder-icon" aria-hidden="true">
            ❤️
        </div>

        <div>

            <h3>
                Stay on top of your health
            </h3>

            <p>
                Keep your personal information and emergency
                contact details updated to help our health staff
                provide better service. Stay hydrated — drink at
                least 8 glasses of water every day.
            </p>

        </div>

    </section>

</div>

@endsection