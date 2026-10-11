@extends('layouts.dashboard')

@section('title', 'Patient Profile')

@section('page-title', 'Patient Profile')

@section('content')

<div class="dashboard-card patient-profile-page">

    {{-- Header --}}
    <div class="card-header">
        <div>
            <h3>{{ $patient->user->name ?? 'Patient' }}</h3>
            <p>Clinical profile and medical history</p>
        </div>

        <a href="{{ route('doctor.patients.index') }}">
            Back to Patients
        </a>
    </div>


    {{-- Patient Profile --}}
    <div class="patient-profile-header">

        <div class="patient-profile-avatar">
            &#128100;
        </div>

        <div class="patient-profile-information">

            <span class="patient-profile-label">
                PATIENT PROFILE
            </span>

            <h2>
                {{ $patient->user->name ?? 'Patient' }}
            </h2>

            <div class="patient-profile-meta">

                @if($patient->patient_number)
                    <span>
                        <strong>Patient No:</strong>
                        {{ $patient->patient_number }}
                    </span>
                @endif

                @if($patient->user && $patient->user->email)
                    <span>
                        <strong>Email:</strong>
                        {{ $patient->user->email }}
                    </span>
                @endif

            </div>

        </div>

    </div>


    {{-- Appointments --}}
    <section class="patient-history-section">

        <div class="patient-section-heading">
            <div>
                <span class="patient-section-label">
                    APPOINTMENT HISTORY
                </span>

                <h3>
                    <span class="patient-section-icon">&#128197;</span>
                    Appointments
                </h3>

                <p>Scheduled and previous appointments</p>
            </div>
        </div>

        @if($patient->appointments->count())

            <div class="patient-history-list">

                @foreach($patient->appointments as $appointment)

                    <div class="patient-history-card">

                        <div class="patient-history-top">

                            <div>
                                <span class="patient-history-date">
                                    {{ $appointment->appointment_date->format('M d, Y') }}
                                </span>

                                <p class="patient-history-time">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
                                </p>
                            </div>

                            <span class="appointment-status status-{{ strtolower($appointment->status) }}">
                                {{ ucfirst($appointment->status) }}
                            </span>

                        </div>

                        <div class="patient-history-details">

                            @if($appointment->reason)
                                <div>
                                    <span>Reason</span>
                                    <strong>
                                        {{ $appointment->reason }}
                                    </strong>
                                </div>
                            @endif

                            @if($appointment->healthCenter)
                                <div>
                                    <span>Health Center</span>
                                    <strong>
                                        {{ $appointment->healthCenter->name }}
                                    </strong>
                                </div>
                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="patient-empty-state">
                <h4>No appointments found</h4>
                <p>This patient does not have any appointment records yet.</p>
            </div>

        @endif

    </section>


    {{-- Consultations --}}
    <section class="patient-history-section">

        <div class="patient-section-heading">
            <div>
                <span class="patient-section-label">
                    CLINICAL RECORDS
                </span>

                <h3>
                    <span class="patient-section-icon">&#129658;</span>
                    Consultations
                </h3>

                <p>Medical consultations and clinical findings</p>
            </div>
        </div>

        @if($patient->consultations->count())

            <div class="patient-consultation-list">

                @foreach($patient->consultations as $consultation)

                    <div class="patient-consultation-card">

                        <div class="patient-consultation-header">

                            <div>
                                <span class="patient-section-label">
                                    CONSULTATION
                                </span>

                                <h4>
                                    {{ $consultation->consultation_date
                                        ? \Carbon\Carbon::parse($consultation->consultation_date)->format('F d, Y g:i A')
                                        : 'Consultation'
                                    }}
                                </h4>
                            </div>

                        </div>

                        <div class="patient-consultation-information">

                            <div>
                                <span>Chief Complaint</span>
                                <strong>
                                    {{ $consultation->chief_complaint }}
                                </strong>
                            </div>

                            @if($consultation->symptoms)
                                <div>
                                    <span>Symptoms</span>
                                    <strong>
                                        {{ $consultation->symptoms }}
                                    </strong>
                                </div>
                            @endif

                            <div>
                                <span>Diagnosis</span>
                                <strong>
                                    {{ $consultation->diagnosis }}
                                </strong>
                            </div>

                            @if($consultation->treatment_plan)
                                <div>
                                    <span>Treatment Plan</span>
                                    <strong>
                                        {{ $consultation->treatment_plan }}
                                    </strong>
                                </div>
                            @endif

                            @if($consultation->notes)
                                <div>
                                    <span>Additional Notes</span>
                                    <strong>
                                        {{ $consultation->notes }}
                                    </strong>
                                </div>
                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="patient-empty-state">
                <h4>No consultations recorded</h4>
                <p>No clinical consultation records are available for this patient.</p>
            </div>

        @endif

    </section>


    {{-- Prescriptions --}}
    <section class="patient-history-section">

        <div class="patient-section-heading">
            <div>
                <span class="patient-section-label">
                    MEDICATION RECORDS
                </span>

                <h3>
                    <span class="patient-section-icon">&#128138;</span>
                    Prescriptions
                </h3>

                <p>Prescribed medicines and instructions</p>
            </div>
        </div>

        @if($patient->prescriptions->count())

            <div class="patient-prescription-list">

                @foreach($patient->prescriptions as $prescription)

                    <div class="patient-prescription-card">

                        <div class="patient-prescription-header">

                            <div>
                                <span class="patient-section-label">
                                    PRESCRIPTION
                                </span>

                                <h4>
                                    Prescription
                                </h4>

                                @if($prescription->created_at)
                                    <p>
                                        {{ $prescription->created_at->format('F d, Y') }}
                                    </p>
                                @endif
                            </div>

                        </div>

                        @if($prescription->items->count())

                            <div class="patient-prescription-items">

                                @foreach($prescription->items as $item)

                                    <div class="patient-prescription-item">

                                        <strong>
                                            {{ $item->medicine_name ?? $item->name ?? 'Medicine' }}
                                        </strong>

                                        <div class="patient-prescription-details">

                                            @if(!empty($item->dosage))
                                                <span>
                                                    <strong>Dosage:</strong>
                                                    {{ $item->dosage }}
                                                </span>
                                            @endif

                                            @if(!empty($item->frequency))
                                                <span>
                                                    <strong>Frequency:</strong>
                                                    {{ $item->frequency }}
                                                </span>
                                            @endif

                                            @if(!empty($item->duration))
                                                <span>
                                                    <strong>Duration:</strong>
                                                    {{ $item->duration }}
                                                </span>
                                            @endif

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        @else

                            <p class="patient-prescription-empty">
                                No prescription items recorded.
                            </p>

                        @endif

                    </div>

                @endforeach

            </div>

        @else

            <div class="patient-empty-state">
                <h4>No prescriptions recorded</h4>
                <p>No medication records are available for this patient.</p>
            </div>

        @endif

    </section>


    {{-- Back Button --}}
    <div class="patient-profile-footer">

        <a
            href="{{ route('doctor.patients.index') }}"
            class="primary-button"
        >
            ← Back to Patients
        </a>

    </div>

</div>

@endsection