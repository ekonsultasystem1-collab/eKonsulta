@extends('layouts.dashboard')

@section('title', 'Consultation Details')

@section('page-title', 'Consultation Details')

@section('content')

<div class="dashboard-card consultation-detail-page">

    {{-- PAGE HEADER --}}
    <div class="card-header">

        <div>
            <h3>Consultation Details</h3>
            <p>Patient consultation information</p>
        </div>

        <a href="{{ route('staff.consultations.index') }}">
            Back to Consultations
        </a>

    </div>


    <div class="consultation-details-page">

        {{-- PATIENT HEADER --}}
        <div class="consultation-patient-header">

            <div class="consultation-patient-avatar">
                &#128100;
            </div>

            <div class="consultation-patient-name">

                <span class="profile-label">
                    PATIENT
                </span>

                <h2>
                    {{ $consultation->patient->user->name ?? 'Patient' }}
                </h2>

                @if($consultation->patient)
                    <p>
                        {{ $consultation->patient->patient_number }}
                    </p>
                @endif

            </div>

        </div>


        {{-- PATIENT INFORMATION --}}
        <div class="consultation-patient-information">

            @if($consultation->doctor && $consultation->doctor->user)

                <div class="consultation-info-item">

                    <span>Doctor</span>

                    <strong>
                        {{ $consultation->doctor->user->name }}
                    </strong>

                </div>

            @endif


            @if($consultation->appointment && $consultation->appointment->healthCenter)

                <div class="consultation-info-item">

                    <span>Health Center</span>

                    <strong>
                        &#127979;
                        {{ $consultation->appointment->healthCenter->name }}
                    </strong>

                </div>

            @endif


            @if($consultation->consultation_date)

                <div class="consultation-info-item">

                    <span>Consultation Date</span>

                    <strong>
                        {{ $consultation->consultation_date->format('F j, Y g:i A') }}
                    </strong>

                </div>

            @endif

        </div>


        {{-- CONSULTATION SECTION --}}
        <div class="medical-section">

            <div class="medical-section-header">

                <div class="medical-section-icon">
                    &#129658;
                </div>

                <div>
                    <h3>Consultation</h3>

                    <p>
                        Clinical assessment and treatment information
                    </p>
                </div>

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

                    <div class="consultation-notes">

                        <span>Doctor's Notes</span>

                        <p>
                            {{ $consultation->notes }}
                        </p>

                    </div>

                @endif

            </div>

        </div>


        {{-- PRESCRIPTIONS --}}
        <div class="medical-section">

            <div class="medical-section-header">

                <div class="medical-section-icon prescription-section-icon">
                    &#128138;
                </div>

                <div>
                    <h3>Prescriptions</h3>

                    <p>
                        Medication issued for this consultation
                    </p>
                </div>

            </div>


            @if($consultation->prescriptions->count())

                <div class="staff-prescription-list">

                    @foreach($consultation->prescriptions as $prescription)

                        <div class="staff-prescription-card">

                            {{-- PRESCRIPTION HEADER --}}
                            <div class="staff-prescription-header">

                                <div>

                                    <span class="profile-label">
                                        PRESCRIPTION
                                    </span>

                                    <h4>
                                        {{ $prescription->prescription_number }}
                                    </h4>

                                </div>


                                <span class="status-badge prescription-status-{{ $prescription->status }}">
                                    {{ ucfirst($prescription->status) }}
                                </span>

                            </div>


                            {{-- PRESCRIPTION INFORMATION --}}
                            <div class="staff-prescription-information">

                                @if($prescription->prescription_date)

                                    <div>

                                        <span>Date</span>

                                        <strong>
                                            {{ $prescription->prescription_date->format('F j, Y') }}
                                        </strong>

                                    </div>

                                @endif


                                @if($prescription->instructions)

                                    <div>

                                        <span>General Instructions</span>

                                        <strong>
                                            {{ $prescription->instructions }}
                                        </strong>

                                    </div>

                                @endif

                            </div>


                            {{-- MEDICINES --}}
                            @if($prescription->items->count())

                                <div class="prescribed-medicines">

                                    <div class="prescribed-medicines-title">
                                        Prescribed Medicines
                                    </div>


                                    @foreach($prescription->items as $item)

                                        @if($item->medicine)

                                            <div class="staff-medicine-card">

                                                {{-- MEDICINE HEADER --}}
                                                <div class="staff-medicine-main">

                                                    <div class="medicine-icon">
                                                        &#128138;
                                                    </div>

                                                    <div class="medicine-information">

                                                        <h4>
                                                            {{ $item->display_name }}
                                                        </h4>

                                                        @if($item->medicine->generic_name)

                                                            <p>
                                                                {{ $item->medicine->generic_name }}
                                                            </p>

                                                        @endif

                                                    </div>

                                                </div>


                                                {{-- MEDICINE DETAILS --}}
                                                <div class="staff-medicine-details">

                                                    @if($item->medicine->strength || $item->medicine->dosage_form)

                                                        <div>

                                                            <span>Medicine</span>

                                                            <strong>

                                                                @if($item->medicine->strength)
                                                                    {{ $item->medicine->strength }}
                                                                @endif

                                                                @if($item->medicine->dosage_form)
                                                                    &mdash; {{ $item->medicine->dosage_form }}
                                                                @endif

                                                            </strong>

                                                        </div>

                                                    @endif


                                                    @if($item->dosage)

                                                        <div>

                                                            <span>Dosage</span>

                                                            <strong>
                                                                {{ $item->dosage }}
                                                            </strong>

                                                        </div>

                                                    @endif


                                                    @if($item->frequency)

                                                        <div>

                                                            <span>Frequency</span>

                                                            <strong>
                                                                {{ $item->frequency }}
                                                            </strong>

                                                        </div>

                                                    @endif


                                                    @if($item->duration)

                                                        <div>

                                                            <span>Duration</span>

                                                            <strong>
                                                                {{ $item->duration }}
                                                            </strong>

                                                        </div>

                                                    @endif


                                                    @if($item->quantity)

                                                        <div>

                                                            <span>Quantity</span>

                                                            <strong>
                                                                {{ $item->quantity }}
                                                            </strong>

                                                        </div>

                                                    @endif

                                                </div>


                                                {{-- MEDICINE INSTRUCTIONS --}}
                                                @if($item->instructions)

                                                    <div class="staff-medicine-instructions">

                                                        <span>Instructions</span>

                                                        <p>
                                                            {{ $item->instructions }}
                                                        </p>

                                                    </div>

                                                @endif

                                            </div>

                                        @endif

                                    @endforeach

                                </div>

                            @endif

                        </div>

                    @endforeach

                </div>

            @else

                <div class="empty-state">

                    <h4>No prescriptions</h4>

                    <p>
                        No prescriptions were issued for this consultation.
                    </p>

                </div>

            @endif

        </div>


        {{-- FOOTER --}}
        <div class="consultation-details-footer">

            <a
                href="{{ route('staff.consultations.index') }}"
                class="primary-button"
            >
                Back to Consultations
            </a>

        </div>

    </div>

</div>

@endsection