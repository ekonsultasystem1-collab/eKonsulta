@extends('layouts.dashboard')

@section('title', 'Appointments')

@section('page-title', 'Appointments')

@section('content')

<div class="dashboard-card appointment-list-page">

    <div class="card-header">

        <div>
            <span class="page-eyebrow">FRONT DESK</span>
            <h3>All Appointments</h3>
            <p>Manage and monitor patient appointments</p>
        </div>

        <a href="{{ route('staff.dashboard') }}">
            Back to Dashboard
        </a>

    </div>

    @if(session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    @if($appointments->count())

        <x-list-search target="staff-appointment-list" placeholder="Search patients, doctors, dates, or status..." label="Search appointments" />

        <div class="medical-records-list" id="staff-appointment-list">

            @foreach($appointments as $appointment)

                <div class="appointment-preview" data-search-item>

                    <div class="appointment-date">

                        <strong>
                            {{ $appointment->appointment_date->format('M') }}
                        </strong>

                        <span>
                            {{ $appointment->appointment_date->format('d') }}
                        </span>

                    </div>

                    <div class="appointment-details appointment-record-content">
                        <div class="appointment-record-heading">
                            <div>
                                <span class="appointment-record-eyebrow">Patient appointment</span>
                                <h4>{{ $appointment->patient?->user?->name ?? 'Patient' }}</h4>
                                <p class="appointment-record-time">
                                    {{ $appointment->appointment_date->format('F j, Y') }}
                                    · {{ $appointment->appointment_time->format('g:i A') }}
                                    @if($appointment->patient?->patient_number)
                                        · {{ $appointment->patient->patient_number }}
                                    @endif
                                </p>
                            </div>
                            <span class="appointment-status status-{{ strtolower($appointment->status) }}">
                                {{ $appointment->status === 'approved' ? 'Confirmed' : ucfirst($appointment->status) }}
                            </span>
                        </div>

                        <div class="appointment-record-facts">
                            <div>
                                <span>Assigned doctor</span>
                                <strong>{{ $appointment->doctor?->user?->name ?? 'Not assigned' }}</strong>
                            </div>
                            <div>
                                <span>Health center</span>
                                <strong>{{ $appointment->healthCenter?->name ?? 'Not available' }}</strong>
                            </div>
                            <div>
                                <span>Reason for visit</span>
                                <strong>{{ \Illuminate\Support\Str::limit($appointment->reason ?: 'Not provided', 110) }}</strong>
                            </div>
                        </div>

                        <div class="appointment-record-actions">
                            <a
                                href="{{ route('staff.appointments.show', $appointment) }}"
                                class="secondary-button"
                            >
                                View appointment
                            </a>

                            @if($appointment->status === 'pending')
                                <form
                                    method="POST"
                                    action="{{ route('staff.appointments.approve', $appointment) }}"
                                    data-confirm="This appointment will be confirmed for the patient and made available to the assigned doctor."
                                    data-confirm-title="Approve appointment?"
                                    data-confirm-ok="Approve appointment"
                                    data-confirm-cancel="Review appointment"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="primary-button">
                                        Approve Appointment
                                    </button>
                                </form>
                            @endif

                        </div>
                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="empty-state">

            <div class="empty-icon">
                📅
            </div>

            <h4>No appointments yet</h4>

            <p>
                Patient appointments will appear here.
            </p>

        </div>

    @endif

</div>

@endsection