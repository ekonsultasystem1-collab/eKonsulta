@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('page-title', 'Administration Dashboard')

@section('content')

<div class="admin-dashboard">

<section class="welcome-banner">

    <div>
        <span class="welcome-label">
            E Konsulta Administration
        </span>

        <h1>
            Good day, {{ auth()->user()->name }}!
        </h1>

        <p>
            Monitor the city health system and manage
            users, health centers, and system activities.
        </p>
    </div>

    <div class="welcome-icon">
        &#9881;
    </div>

</section>


<section class="stats-grid">

    <x-stat-card
        icon="&#128101;"
        label="Total Users"
        value="{{ $totalUsers }}"
    />

    <x-stat-card
        icon="&#128101;"
        label="Patients"
        value="{{ $totalPatients }}"
    />

    <x-stat-card
        icon="&#129658;"
        label="Doctors"
        value="{{ $totalDoctors }}"
    />

    <x-stat-card
        icon="&#128100;"
        label="Staff"
        value="{{ $totalStaff }}"
    />

    <x-stat-card
        icon="&#127973;"
        label="Health Centers"
        value="{{ $totalHealthCenters }}"
    />

    <x-stat-card
        icon="&#128197;"
        label="Appointments"
        value="{{ $totalAppointments }}"
    />

    <x-stat-card
        icon="&#129658;"
        label="Consultations"
        value="{{ $totalConsultations }}"
    />

    <x-stat-card
        icon="&#128138;"
        label="Prescriptions"
        value="{{ $totalPrescriptions }}"
    />

</section>


<div class="dashboard-grid">

    {{-- Recent Activity --}}
    <section class="dashboard-card">

        <div class="card-header">

            <div>
                <h3>Recent Activity</h3>
                <p>Latest actions performed in the system</p>
            </div>

            <a href="{{ route('admin.activity-logs.index') }}">
                View All
            </a>

        </div>

        @if($recentActivityLogs->count())

            <div class="activity-list">

                @foreach($recentActivityLogs as $log)

                    <div class="activity-item">

                        <div class="activity-icon">
                            &#128221;
                        </div>

                        <div class="activity-information">

                            <strong>
                                {{ $log->action }}
                            </strong>

                            <p>
                                {{ $log->description }}
                            </p>

                            <small>
                                {{ $log->user?->name ?? 'Unknown User' }}
                                ·
                                {{ $log->created_at->format('M d, Y h:i A') }}
                            </small>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="empty-state">

                <div class="empty-icon">
                    &#128221;
                </div>

                <h4>No recent activity</h4>

                <p>
                    System activity will appear here.
                </p>

            </div>

        @endif

    </section>


    {{-- Administration --}}
    <section class="dashboard-card">

        <div class="card-header">

            <div>
                <h3>Administration</h3>
                <p>System management</p>
            </div>

        </div>

        <div class="quick-actions">

            <a
                href="{{ route('admin.users.index') }}"
                class="quick-action"
            >
                <span>&#128101;</span>
                <strong>User Management</strong>
                <small>Manage system users</small>
            </a>


            <a
                href="{{ route('admin.health-centers.index') }}"
                class="quick-action"
            >
                <span>&#127973;</span>
                <strong>Health Centers</strong>
                <small>Manage health centers</small>
            </a>


            <a
                href="{{ route('admin.doctors.index') }}"
                class="quick-action"
            >
                <span>&#129658;</span>
                <strong>Doctors</strong>
                <small>Manage doctors</small>
            </a>


            <a
                href="{{ route('admin.staff.index') }}"
                class="quick-action"
            >
                <span>&#128100;</span>
                <strong>Staff</strong>
                <small>Manage staff members</small>
            </a>

            <a
                href="{{ route('admin.announcements.create') }}"
                class="quick-action"
            >
                <span>&#128226;</span>
                <strong>Create Announcement</strong>
                <small>Share an update with patients and staff</small>
            </a>


            <a
                href="{{ route('admin.reports.index') }}"
                class="quick-action"
            >
                <span>&#128202;</span>
                <strong>Reports</strong>
                <small>View system reports</small>
            </a>


            <a
                href="{{ route('admin.activity-logs.index') }}"
                class="quick-action"
            >
                <span>&#128203;</span>
                <strong>Activity Logs</strong>
                <small>Monitor system activity</small>
            </a>

        </div>

    </section>

</div>

</div>

@endsection