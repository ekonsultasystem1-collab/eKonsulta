@extends('layouts.dashboard')

@section('title', 'Medical Records')

@section('page-title', 'Medical Records')

@section('content')

<div class="dashboard-card">

    <div class="card-header">

        <div>
            <h3>My Medical Records</h3>
            <p>Your consultation and treatment history</p>
        </div>

        <a href="{{ route('patient.dashboard') }}">
            Back to Dashboard
        </a>

    </div>

    @if($consultations->count())

        <x-list-search target="patient-medical-record-list" placeholder="Search doctor, diagnosis, or date..." label="Search your medical records" />

        <div class="medical-records-list" id="patient-medical-record-list">

            @foreach($consultations as $consultation)

                <div class="appointment-preview" data-search-item>

                    <div class="appointment-date">

                        <strong>
                            {{ $consultation->consultation_date->format('M') }}
                        </strong>

                        <span>
                            {{ $consultation->consultation_date->format('d') }}
                        </span>

                    </div>

                    <div class="appointment-details">
                        <h4>{{ $consultation->consultation_date->format('l, F j, Y') }}</h4>

                        @if($consultation->doctor?->user)
                            <p>Dr. {{ $consultation->doctor->user->name }}</p>
                        @endif

                        @if($consultation->diagnosis)
                            <p><strong>Diagnosis:</strong> {{ \Illuminate\Support\Str::limit($consultation->diagnosis, 120) }}</p>
                        @else
                            <p>Consultation record</p>
                        @endif

                        @if($consultation->appointment?->healthCenter)
                            <p>{{ $consultation->appointment->healthCenter->name }}</p>
                        @endif

                        <div class="form-actions">
                            <a
                                href="{{ route('patient.medical-records.show', $consultation) }}"
                                class="primary-button"
                            >
                                View Full Record
                            </a>
                        </div>
                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="empty-state">

            <div class="empty-icon">
                &#128203;
            </div>

            <h4>No medical records yet</h4>

            <p>
                Your consultation and treatment records will appear here
                after you have completed a consultation.
            </p>

        </div>

    @endif

</div>

@endsection