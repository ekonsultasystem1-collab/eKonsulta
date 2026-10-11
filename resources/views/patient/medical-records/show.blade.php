@extends('layouts.dashboard')

@section('title', 'Medical Record')

@section('page-title', 'Medical Record')

@section('content')

<div class="dashboard-card">
    <div class="card-header">
        <div>
            <h3>Consultation Record</h3>
            <p>Full details from your completed consultation.</p>
        </div>

        <a href="{{ route('patient.medical-records.index') }}" class="secondary-button">
            Back to Medical Records
        </a>
    </div>

    <section class="prescription-context-grid" aria-label="Consultation overview">
        <div class="prescription-patient-summary">
            <h4>Visit Information</h4>
            <p><strong>Date:</strong> {{ $consultation->consultation_date->format('F j, Y') }}</p>
            <p><strong>Doctor:</strong> {{ $consultation->doctor?->user?->name ?? 'Not recorded' }}</p>
            @if($consultation->doctor?->specialization)
                <p><strong>Specialization:</strong> {{ $consultation->doctor->specialization }}</p>
            @endif
            @if($consultation->appointment?->healthCenter)
                <p><strong>Health center:</strong> {{ $consultation->appointment->healthCenter->name }}</p>
            @endif
        </div>

        <div class="prescription-consultation-summary">
            <h4>Clinical Notes</h4>
            @foreach([
                'Chief complaint' => $consultation->chief_complaint,
                'Symptoms' => $consultation->symptoms,
                'Diagnosis' => $consultation->diagnosis,
                'Treatment plan' => $consultation->treatment_plan,
                "Doctor's notes" => $consultation->notes,
            ] as $label => $value)
                @if($value)
                    <p><strong>{{ $label }}:</strong> {{ $value }}</p>
                @endif
            @endforeach
            @if(!$consultation->chief_complaint && !$consultation->symptoms && !$consultation->diagnosis && !$consultation->treatment_plan && !$consultation->notes)
                <p>No clinical notes were recorded for this consultation.</p>
            @endif
        </div>
    </section>

    @if($consultation->appointment?->patientIntake)
        @php
            $intake = $consultation->appointment->patientIntake;
            $height = (float) $intake->height;
            $weight = (float) $intake->weight;
            $bmi = $height > 0 && $weight > 0 ? $weight / (($height / 100) ** 2) : null;
        @endphp

        <section class="dashboard-card">
            <div class="card-header">
                <div>
                    <h3>Patient Intake</h3>
                    <p>Measurements and health information recorded before the visit.</p>
                </div>
            </div>

            <div class="detail-grid">
                @if($intake->height !== null)
                    <p><strong>Height:</strong> {{ number_format((float) $intake->height, 2) }} cm</p>
                @endif
                @if($intake->weight !== null)
                    <p><strong>Weight:</strong> {{ number_format((float) $intake->weight, 2) }} kg</p>
                @endif
                @if($bmi !== null)
                    <p><strong>BMI:</strong> {{ number_format($bmi, 2) }}</p>
                @endif
                <p><strong>Diabetes:</strong> {{ $intake->has_diabetes ? 'Yes' : 'No' }}</p>
                <p><strong>Hypertension:</strong> {{ $intake->has_hypertension ? 'Yes' : 'No' }}</p>
            </div>

            <hr>
            <div class="detail-grid">
                <p><strong>Allergies:</strong> {{ $intake->allergies ?: 'None reported' }}</p>
                <p><strong>Comorbidities:</strong> {{ $intake->comorbidities ?: 'None reported' }}</p>
                <p><strong>Maintenance medications:</strong> {{ $intake->maintenance_medications ?: 'None reported' }}</p>
                <p><strong>Other medical information:</strong> {{ $intake->other_medical_information ?: 'None recorded' }}</p>
            </div>
        </section>
    @endif

    <section class="dashboard-card">
        <div class="card-header">
            <div>
                <h3>Prescriptions</h3>
                <p>Prescriptions associated with this consultation.</p>
            </div>
        </div>

        @forelse($consultation->prescriptions as $prescription)
            <div class="appointment-preview">
                <div class="appointment-details">
                    <h4>{{ $prescription->prescription_number }}</h4>
                    <p>{{ $prescription->prescription_date->format('F j, Y') }}</p>
                    <span class="appointment-status status-{{ strtolower($prescription->status) }}">
                        {{ ucfirst($prescription->status) }}
                    </span>
                    <div class="form-actions">
                        <a href="{{ route('patient.prescriptions.show', $prescription) }}" class="primary-button">
                            View Prescription
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p>No prescriptions were recorded for this consultation.</p>
        @endforelse
    </section>
</div>

@endsection
