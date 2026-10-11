@extends('layouts.dashboard')

@section('title', 'Reports')

@section('page-title', 'Reports')

@section('content')

<div class="dashboard-card">

    <div class="card-header">

        <div>
            <h3>System Reports</h3>
            <p>Overview of E Konsulta system statistics</p>
        </div>

        <a
            href="{{ route('admin.dashboard') }}"
            class="secondary-button"
        >
            Back to Dashboard
        </a>

    </div>


    {{-- ============================= --}}
    {{-- SYSTEM OVERVIEW --}}
    {{-- ============================= --}}

    <div class="admin-report-section">

        <div class="admin-report-section-header">
            <span class="admin-report-icon">📊</span>

            <div>
                <h4>System Overview</h4>
                <p>Current records across the E Konsulta system</p>
            </div>
        </div>


        <div class="admin-report-grid">

            <div class="admin-report-card">
                <span class="admin-report-card-icon">👥</span>
                <span class="admin-report-card-label">TOTAL USERS</span>
                <strong>{{ $totalUsers }}</strong>
            </div>

            <div class="admin-report-card">
                <span class="admin-report-card-icon">🧑</span>
                <span class="admin-report-card-label">PATIENTS</span>
                <strong>{{ $totalPatients }}</strong>
            </div>

            <div class="admin-report-card">
                <span class="admin-report-card-icon">🩺</span>
                <span class="admin-report-card-label">DOCTORS</span>
                <strong>{{ $totalDoctors }}</strong>
            </div>

            <div class="admin-report-card">
                <span class="admin-report-card-icon">👤</span>
                <span class="admin-report-card-label">STAFF</span>
                <strong>{{ $totalStaff }}</strong>
            </div>

            <div class="admin-report-card">
                <span class="admin-report-card-icon">🏥</span>
                <span class="admin-report-card-label">HEALTH CENTERS</span>
                <strong>{{ $totalHealthCenters }}</strong>
            </div>

            <div class="admin-report-card">
                <span class="admin-report-card-icon">📅</span>
                <span class="admin-report-card-label">APPOINTMENTS</span>
                <strong>{{ $totalAppointments }}</strong>
            </div>

            <div class="admin-report-card">
                <span class="admin-report-card-icon">🩺</span>
                <span class="admin-report-card-label">CONSULTATIONS</span>
                <strong>{{ $totalConsultations }}</strong>
            </div>

            <div class="admin-report-card">
                <span class="admin-report-card-icon">💊</span>
                <span class="admin-report-card-label">PRESCRIPTIONS</span>
                <strong>{{ $totalPrescriptions }}</strong>
            </div>

        </div>

    </div>


    {{-- ============================= --}}
    {{-- USER STATUS --}}
    {{-- ============================= --}}

    <div class="admin-report-section">

        <div class="admin-report-section-header">
            <span class="admin-report-icon">👥</span>

            <div>
                <h4>User Accounts</h4>
                <p>Current user account status</p>
            </div>
        </div>


        <div class="admin-report-summary">

            <div>
                <span>ACTIVE USERS</span>
                <strong>{{ $activeUsers }}</strong>
            </div>

            <div>
                <span>INACTIVE USERS</span>
                <strong>{{ $inactiveUsers }}</strong>
            </div>

        </div>

    </div>


    {{-- ============================= --}}
    {{-- HEALTH CENTERS --}}
    {{-- ============================= --}}

    <div class="admin-report-section">

        <div class="admin-report-section-header">
            <span class="admin-report-icon">🏥</span>

            <div>
                <h4>Health Centers</h4>
                <p>Health center availability status</p>
            </div>
        </div>


        <div class="admin-report-summary">

            <div>
                <span>ACTIVE HEALTH CENTERS</span>
                <strong>{{ $activeHealthCenters }}</strong>
            </div>

            <div>
                <span>INACTIVE HEALTH CENTERS</span>
                <strong>{{ $inactiveHealthCenters }}</strong>
            </div>

        </div>

    </div>


    {{-- ============================= --}}
    {{-- APPOINTMENTS --}}
    {{-- ============================= --}}

    <div class="admin-report-section">

        <div class="admin-report-section-header">
            <span class="admin-report-icon">📅</span>

            <div>
                <h4>Appointment Status</h4>
                <p>Breakdown of recorded appointments</p>
            </div>
        </div>


        <div class="admin-report-summary">

            <div>
                <span>PENDING</span>
                <strong>{{ $pendingAppointments }}</strong>
            </div>

            <div>
                <span>APPROVED</span>
                <strong>{{ $approvedAppointments }}</strong>
            </div>

            <div>
                <span>COMPLETED</span>
                <strong>{{ $completedAppointments }}</strong>
            </div>

            <div>
                <span>CANCELLED</span>
                <strong>{{ $cancelledAppointments }}</strong>
            </div>

        </div>

    </div>

</div>

@endsection