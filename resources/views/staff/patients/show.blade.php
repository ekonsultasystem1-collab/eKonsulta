@extends('layouts.dashboard')

@section('title', 'Patient Details')

@section('page-title', 'Patient Details')

@section('content')

<div class="patient-details-page">

    {{-- ========================================
         PATIENT PROFILE
    ========================================= --}}

    <section class="dashboard-card patient-profile-card">

        <div class="patient-profile-header">

            <div class="patient-profile-avatar">
                &#128100;
            </div>

            <div class="patient-profile-name">

                <span class="profile-label">
                    PATIENT PROFILE
                </span>

                <h2>
                    {{ $patient->user->name ?? 'Patient' }}
                </h2>

                <p>
                    Patient No:
                    {{ $patient->patient_number ?? 'N/A' }}
                </p>

            </div>

            <div class="patient-profile-action">

                <a href="{{ route('staff.patients.index') }}">
                    Back to Patients
                </a>

            </div>

        </div>


        <div class="patient-information-grid">

            <div class="patient-info-item">
                <span>Date of Birth</span>

                <strong>
                    @if($patient->date_of_birth)
                        {{ $patient->date_of_birth->format('F j, Y') }}
                    @else
                        Not provided
                    @endif
                </strong>
            </div>


            <div class="patient-info-item">
                <span>Sex</span>

                <strong>
                    {{ $patient->sex ? ucfirst($patient->sex) : 'Not provided' }}
                </strong>
            </div>


            <div class="patient-info-item">
                <span>Contact Number</span>

                <strong>
                    {{ $patient->contact_number ?? 'Not provided' }}
                </strong>
            </div>


            <div class="patient-info-item">
                <span>Email</span>

                <strong>
                    {{ $patient->user->email ?? 'Not provided' }}
                </strong>
            </div>


            <div class="patient-info-item">
                <span>Address</span>

                <strong>
                    {{ $patient->address ?? 'Not provided' }}
                </strong>
            </div>


            <div class="patient-info-item">
                <span>Emergency Contact</span>

                <strong>
                    {{ $patient->emergency_contact_number ?? 'Not provided' }}
                </strong>
            </div>

        </div>

    </section>


    {{-- ========================================
         APPOINTMENTS
    ========================================= --}}

    <section class="dashboard-card patient-section-card">

        <div class="section-title">

            <div class="section-icon">
                &#128197;
            </div>

            <div>
                <h3>Appointments</h3>
                <p>Patient appointment history</p>
            </div>

        </div>


        @if($patient->appointments->count())

            <div class="patient-history-list">

                @foreach($patient->appointments as $appointment)

                    <div class="history-card">

                        <div class="history-date">

                            <strong>
                                {{ $appointment->appointment_date->format('M') }}
                            </strong>

                            <span>
                                {{ $appointment->appointment_date->format('d') }}
                            </span>

                        </div>


                        <div class="history-content">

                            <h4>
                                {{ $appointment->appointment_date->format('F j, Y') }}
                            </h4>


                            <div class="history-meta">

                                @if($appointment->appointment_time)

                                    <span>
                                        <strong>Time:</strong>
                                        {{ $appointment->appointment_time->format('g:i A') }}
                                    </span>

                                @endif


                                @if($appointment->doctor && $appointment->doctor->user)

                                    <span>
                                        <strong>Doctor:</strong>
                                        {{ $appointment->doctor->user->name }}
                                    </span>

                                @endif


                                @if($appointment->healthCenter)

                                    <span>
                                        <strong>Health Center:</strong>
                                        {{ $appointment->healthCenter->name }}
                                    </span>

                                @endif

                            </div>


                            @if($appointment->reason)

                                <p class="history-description">
                                    <strong>Reason:</strong>
                                    {{ $appointment->reason }}
                                </p>

                            @endif


                            <span class="status-badge">
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

                <h4>No appointments</h4>

                <p>
                    This patient has no appointment records yet.
                </p>

            </div>

        @endif

    </section>


    {{-- ========================================
         CONSULTATIONS
    ========================================= --}}

    <section class="dashboard-card patient-section-card">

        <div class="section-title">

            <div class="section-icon">
                &#129658;
            </div>

            <div>
                <h3>Consultations</h3>
                <p>Patient consultation history</p>
            </div>

        </div>


        @if($patient->consultations->count())

            <div class="patient-history-list">

                @foreach($patient->consultations as $consultation)

                    <div class="consultation-card">

                        <div class="consultation-header">

                            <div>

                                <h4>
                                    Consultation
                                </h4>

                                @if($consultation->consultation_date)

                                    <span>
                                        {{ $consultation->consultation_date->format('F j, Y g:i A') }}
                                    </span>

                                @endif

                            </div>


                            @if($consultation->doctor && $consultation->doctor->user)

                                <span class="doctor-name">
                                    {{ $consultation->doctor->user->name }}
                                </span>

                            @endif

                        </div>


                        <div class="consultation-information">

                            @if($consultation->chief_complaint)

                                <div>
                                    <span>Chief Complaint</span>

                                    <p>
                                        {{ $consultation->chief_complaint }}
                                    </p>
                                </div>

                            @endif


                            @if($consultation->symptoms)

                                <div>
                                    <span>Symptoms</span>

                                    <p>
                                        {{ $consultation->symptoms }}
                                    </p>
                                </div>

                            @endif


                            @if($consultation->diagnosis)

                                <div>
                                    <span>Diagnosis</span>

                                    <p>
                                        {{ $consultation->diagnosis }}
                                    </p>
                                </div>

                            @endif


                            @if($consultation->treatment_plan)

                                <div>
                                    <span>Treatment Plan</span>

                                    <p>
                                        {{ $consultation->treatment_plan }}
                                    </p>
                                </div>

                            @endif


                            @if($consultation->notes)

                                <div>
                                    <span>Doctor's Notes</span>

                                    <p>
                                        {{ $consultation->notes }}
                                    </p>
                                </div>

                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="empty-state">

                <div class="empty-icon">
                    &#129658;
                </div>

                <h4>No consultations</h4>

                <p>
                    This patient has no consultation records yet.
                </p>

            </div>

        @endif

    </section>


    {{-- ========================================
         PRESCRIPTIONS
    ========================================= --}}

    <section class="dashboard-card patient-section-card">

        <div class="section-title">

            <div class="section-icon">
                &#128138;
            </div>

            <div>
                <h3>Prescriptions</h3>
                <p>Patient prescription history</p>
            </div>

        </div>


        @if($patient->prescriptions->count())

            <div class="prescription-list">

                @foreach($patient->prescriptions as $prescription)

                    <div class="prescription-card">

                        <div class="prescription-header">

                            <div>

                                <span class="profile-label">
                                    PRESCRIPTION
                                </span>

                                <h4>
                                    {{ $prescription->prescription_number }}
                                </h4>

                            </div>

                            <span class="status-badge">
                                {{ ucfirst($prescription->status) }}
                            </span>

                        </div>


                        <div class="prescription-meta">

                            <strong>Date:</strong>

                            {{ $prescription->prescription_date->format('F j, Y') }}

                        </div>


                        @if($prescription->instructions)

                            <div class="prescription-instructions">

                                <strong>
                                    General Instructions
                                </strong>

                                <p>
                                    {{ $prescription->instructions }}
                                </p>

                            </div>

                        @endif


                        @if($prescription->items->count())

                            <div class="medicine-list">

                                <h4>
                                    Prescribed Medicines
                                </h4>


                                @foreach($prescription->items as $item)

                                    <div class="medicine-item">

                                        <div class="medicine-icon">
                                            &#128138;
                                        </div>


                                        <div class="medicine-information">

                                            @if($item->medicine)

                                                <h5>

                                                    {{ $item->display_name }}

                                                    @if($item->medicine->generic_name)

                                                        <span>
                                                            ({{ $item->medicine->generic_name }})
                                                        </span>

                                                    @endif

                                                </h5>


                                                @if($item->medicine->strength || $item->medicine->dosage_form)

                                                    <p>

                                                        @if($item->medicine->strength)
                                                            {{ $item->medicine->strength }}
                                                        @endif

                                                        @if($item->medicine->dosage_form)
                                                            — {{ $item->medicine->dosage_form }}
                                                        @endif

                                                    </p>

                                                @endif

                                            @endif


                                            <div class="medicine-meta">

                                                @if($item->dosage)

                                                    <span>
                                                        <strong>Dosage:</strong>
                                                        {{ $item->dosage }}
                                                    </span>

                                                @endif


                                                @if($item->frequency)

                                                    <span>
                                                        <strong>Frequency:</strong>
                                                        {{ $item->frequency }}
                                                    </span>

                                                @endif


                                                @if($item->duration)

                                                    <span>
                                                        <strong>Duration:</strong>
                                                        {{ $item->duration }}
                                                    </span>

                                                @endif


                                                @if($item->quantity)

                                                    <span>
                                                        <strong>Quantity:</strong>
                                                        {{ $item->quantity }}
                                                    </span>

                                                @endif

                                            </div>


                                            @if($item->instructions)

                                                <p class="medicine-instructions">

                                                    <strong>Instructions:</strong>
                                                    {{ $item->instructions }}

                                                </p>

                                            @endif

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        @endif

                    </div>

                @endforeach

            </div>

        @else

            <div class="empty-state">

                <div class="empty-icon">
                    &#128138;
                </div>

                <h4>No prescriptions</h4>

                <p>
                    This patient has no prescription records yet.
                </p>

            </div>

        @endif

    </section>


    {{-- BACK BUTTON --}}

    <div>

        <a
            href="{{ route('staff.patients.index') }}"
            class="primary-button"
        >
            Back to Patients
        </a>

    </div>

</div>

@endsection