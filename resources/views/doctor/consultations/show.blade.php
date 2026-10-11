@extends('layouts.dashboard')

@section('title', 'Consultation Details')

@section('page-title', 'Consultation Details')

@section('content')

    <div class="dashboard-card consultation-detail-page doctor-consultation-detail">

        <div class="card-header">

            <div>
                <h3>Consultation Details</h3>
                <p>Clinical consultation record</p>
            </div>

            <a href="{{ route('doctor.consultations.index') }}">
                Back to Consultations
            </a>

        </div>


        {{-- Patient Information --}}
        <div class="appointment-preview consultation-detail-patient">

            <div class="appointment-date">

                <strong>👤</strong>

            </div>

            <div class="appointment-details">

                <h4>
                    {{ $consultation->patient->user->name ?? 'Patient' }}
                </h4>

                @if($consultation->patient)

                    <p>
                        <strong>Patient No:</strong>
                        {{ $consultation->patient->patient_number }}
                    </p>

                @endif

                @if($consultation->patient->user->email ?? false)

                    <p>
                        <strong>Email:</strong>
                        {{ $consultation->patient->user->email }}
                    </p>

                @endif

            </div>

        </div>


        {{-- Consultation Information --}}
        <section class="consultation-detail-section">

            <div class="consultation-detail-section-heading">
                <span aria-hidden="true">01</span>
                <div>
                    <h3>Consultation Information</h3>
                    <p>Visit details and care location</p>
                </div>
            </div>

            <div class="consultation-detail-facts">
                <div>
                    <span>Date and time</span>
                    <strong>{{ \Carbon\Carbon::parse($consultation->consultation_date)->format('F d, Y g:i A') }}</strong>
                </div>

                @if($consultation->appointment?->healthCenter)
                    <div>
                        <span>Health center</span>
                        <strong>{{ $consultation->appointment->healthCenter->name }}</strong>
                    </div>
                @endif
            </div>

        </section>

        <section class="consultation-detail-section">
            <div class="consultation-detail-section-heading">
                <span aria-hidden="true">02</span>
                <div>
                    <h3>Clinical Findings</h3>
                    <p>Assessment recorded during the consultation</p>
                </div>
            </div>

            <div class="consultation-detail-findings">
                @if($consultation->chief_complaint)
                    <div>
                        <span>Chief complaint</span>
                        <p>{{ $consultation->chief_complaint }}</p>
                    </div>
                @endif

                @if($consultation->symptoms)
                    <div>
                        <span>Symptoms</span>
                        <p>{{ $consultation->symptoms }}</p>
                    </div>
                @endif

                @if($consultation->diagnosis)
                    <div>
                        <span>Diagnosis</span>
                        <p>{{ $consultation->diagnosis }}</p>
                    </div>
                @endif

                @if($consultation->treatment_plan)
                    <div>
                        <span>Treatment plan</span>
                        <p>{{ $consultation->treatment_plan }}</p>
                    </div>
                @endif

                @if($consultation->notes)
                    <div>
                        <span>Additional notes</span>
                        <p>{{ $consultation->notes }}</p>
                    </div>
                @endif

                @if(!$consultation->chief_complaint && !$consultation->symptoms && !$consultation->diagnosis && !$consultation->treatment_plan && !$consultation->notes)
                    <p class="text-muted">No clinical findings were recorded.</p>
                @endif
            </div>
        </section>

        <section class="consultation-detail-section">
            <div class="consultation-detail-section-heading">
                <span aria-hidden="true">03</span>
                <div>
                    <h3>Prescriptions</h3>
                    <p>Medication orders linked to this consultation</p>
                </div>
            </div>

            @forelse($consultation->prescriptions as $prescription)
                <article class="consultation-prescription-card">
                    <div>
                        <span class="consultation-list-label">PRESCRIPTION</span>
                        <h4>{{ $prescription->prescription_number }}</h4>
                        <p>{{ $prescription->prescription_date->format('F j, Y') }} · {{ $prescription->items->count() }} {{ $prescription->items->count() === 1 ? 'medicine' : 'medicines' }}</p>
                    </div>
                    <a href="{{ route('doctor.prescriptions.show', $prescription) }}" class="secondary-button">
                        View Prescription
                    </a>
                </article>
            @empty
                <div class="empty-state consultation-prescriptions-empty">
                    <h4>No prescriptions issued</h4>
                    <p>No medication orders are linked to this consultation.</p>
                </div>
            @endforelse
        </section>

        <div class="form-actions consultation-detail-actions">
            <a href="{{ route('doctor.consultations.index') }}" class="secondary-button">
                Back to Consultations
            </a>
        </div>

    </div>

@endsection