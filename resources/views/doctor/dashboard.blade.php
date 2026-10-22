@extends('layouts.dashboard')

@section('title', 'Doctor Dashboard')

@section('page-title', 'Doctor Dashboard')

@section('content')

<div class="doctor-dashboard">

    <section class="welcome-banner">

        <div>
            <span class="welcome-label">
                E Konsulta Clinical Portal
            </span>

            <h1>
                Good day, {{ auth()->user()->name }}!
            </h1>

            <p>
                Manage your appointments, patients,
                consultations, and prescriptions.
            </p>
        </div>

        <div class="welcome-icon">
            &#129658;
        </div>

    </section>


    <section class="stats-grid">

      <x-stat-card
    icon="📅"
    label="Today's Appointments"
    :value="$todayAppointments->count()"
/>

<x-stat-card
    icon="👥"
    label="Patients"
    :value="$patientCount"
/>

<x-stat-card
    icon="🩺"
    label="Consultations"
    :value="$consultationCount"
/>

<x-stat-card
    icon="💊"
    label="Prescriptions"
    :value="$prescriptionCount"
/>

    </section>


    <div class="dashboard-grid">

        {{-- TODAY'S APPOINTMENTS --}}

        <section class="dashboard-card">

            <div class="card-header">

                <div>
                    <h3>Today's Appointments</h3>
                    <p>Your scheduled patient appointments</p>
                </div>

                <a href="{{ route('doctor.appointments.index') }}">
                    View All
                </a>

            </div>


            @if($todayAppointments->count())

                <div class="appointments-list">

                    @foreach($todayAppointments as $appointment)

                        <div class="appointment-preview">

                            <div class="appointment-date">

                                <strong>
                                    {{ $appointment->appointment_date->format('M') }}
                                </strong>

                                <span>
                                    {{ $appointment->appointment_date->format('d') }}
                                </span>

                            </div>


                            <div class="appointment-details">

                                <h4>
                                    {{ $appointment->appointment_date->format('l, F j, Y') }}
                                </h4>

                                <p>
                                    &#128336;
                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
                                </p>

                                @if($appointment->patient && $appointment->patient->user)

                                    <p>
                                        &#128100;
                                        {{ $appointment->patient->user->name }}
                                    </p>

                                @endif

                                @if($appointment->patient)

                                    <p>
                                        Patient No:
                                        {{ $appointment->patient->patient_number }}
                                    </p>

                                @endif

                                @if($appointment->healthCenter)

                                    <p>
                                        &#127973;
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
                        &#128197;
                    </div>

                    <h4>No appointments today</h4>

                    <p>
                        You currently have no appointments scheduled for today.
                    </p>

                    <a
                        href="{{ route('doctor.appointments.index') }}"
                        class="primary-button"
                    >
                        View All Appointments
                    </a>

                </div>

            @endif

        </section>


        {{-- QUICK ACTIONS --}}

        <section class="dashboard-card">

            <div class="card-header">

                <div>
                    <h3>Quick Actions</h3>
                    <p>Clinical tools</p>
                </div>

            </div>


            <div class="quick-actions">

                <a
                    href="{{ route('doctor.appointments.index') }}"
                    class="quick-action"
                >
                    <span>&#128197;</span>
                    <strong>Appointments</strong>
                    <small>Manage your schedule</small>
                </a>


                <a
                    href="{{ route('doctor.consultations.index') }}"
                    class="quick-action"
                >
                    <span>&#129658;</span>
                    <strong>Consultations</strong>
                    <small>View consultation records</small>
                </a>


                <a
                    href="{{ route('doctor.prescriptions.index') }}"
                    class="quick-action"
                >
                    <span>&#128138;</span>
                    <strong>Prescriptions</strong>
                    <small>Manage prescriptions</small>
                </a>


                <a
                    href="{{ route('doctor.patients.index') }}"
                    class="quick-action"
                >
                    <span>&#128101;</span>
                    <strong>Patients</strong>
                    <small>View your patients</small>
                </a>

                <a
                    href="{{ route('doctor.profile') }}"
                    class="quick-action"
                >
                    <span>&#128100;</span>
                    <strong>Profile</strong>
                    <small>Review your clinic details</small>
                </a>

            </div>

        </section>

    </div>

</div>

@endsection