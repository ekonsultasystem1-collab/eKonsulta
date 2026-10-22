@extends('layouts.dashboard')

@section('title', 'Doctor Appointments')

@section('page-title', 'Appointments')

@section('content')

<div class="dashboard-card">

    <div class="card-header">
        <div>
            <h3>Patient Appointments</h3>
            <p>Manage appointments assigned to you</p>
        </div>

        <a href="{{ route('doctor.dashboard') }}">
            Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($appointments->count())

        <x-list-search target="doctor-appointment-list" placeholder="Search patients, patient numbers, dates, or status..." label="Search appointments" />

        <div class="doctor-appointments-list" id="doctor-appointment-list">

            @foreach($appointments as $appointment)

                <div class="doctor-appointment-card" data-search-item data-sort-timestamp="{{ $appointment->appointment_date->format('U.u') }}" data-sort-order="{{ $appointment->id }}">

                    <div class="doctor-appointment-date">
                        <strong>
                            {{ $appointment->appointment_date->format('M') }}
                        </strong>

                        <span>
                            {{ $appointment->appointment_date->format('d') }}
                        </span>
                    </div>

                    <div class="doctor-appointment-content">

                        <div class="doctor-appointment-header">

                            <div>
                                <span class="doctor-appointment-label">
                                    PATIENT APPOINTMENT
                                </span>

                                <h4>
                                    {{ $appointment->patient->user->name ?? 'Patient' }}
                                </h4>

                                <p>
                                    {{ $appointment->patient->patient_number ?? 'N/A' }}
                                </p>
                            </div>

                            <span class="appointment-status status-{{ strtolower($appointment->status) }}">
                                {{ ucfirst($appointment->status) }}
                            </span>

                        </div>

                        <div class="doctor-appointment-information">

                            <div>
                                <span>Date & Time</span>
                                <strong>
                                    {{ $appointment->appointment_date->format('F j, Y') }}
                                    —
                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
                                </strong>
                            </div>

                            <div>
                                <span>Health Center</span>
                                <strong>
                                    {{ $appointment->healthCenter->name ?? 'Not available' }}
                                </strong>
                            </div>

                            <div>
                                <span>Reason</span>
                                <strong>
                                    {{ $appointment->reason ?? 'No reason provided' }}
                                </strong>
                            </div>

                            <div>
                                <span>Patient Number</span>
                                <strong>
                                    {{ $appointment->patient->patient_number ?? 'N/A' }}
                                </strong>
                            </div>

                        </div>

                        {{-- ACTIONS --}}

                        @if($appointment->status === 'approved')

                            <div class="doctor-appointment-actions">

                                <a
                                    href="{{ route('doctor.appointments.consultation.create', $appointment) }}"
                                    class="primary-button"
                                >
                                    Start Consultation
                                </a>

                            </div>

   @elseif($appointment->status === 'completed')

    @if($appointment->consultation && !$appointment->consultation->prescriptions->count())

        <div class="doctor-appointment-actions">

            <a
                href="{{ route('doctor.appointments.prescription.create', $appointment) }}"
                class="primary-button"
            >
                Create Prescription
            </a>

        </div>

    @endif

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

            <h4>No appointments</h4>

            <p>
                You currently have no appointments assigned to you.
            </p>

        </div>

    @endif

</div>

@endsection