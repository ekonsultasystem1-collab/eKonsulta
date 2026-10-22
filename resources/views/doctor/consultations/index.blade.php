@extends('layouts.dashboard')

@section('title', 'My Consultations')

@section('page-title', 'My Consultations')

@section('content')

<div class="dashboard-card doctor-consultation-list-page">

    <div class="card-header">
        <div>
            <span class="page-eyebrow">CLINICAL RECORDS</span>
            <h3>My Consultations</h3>
            <p>Consultation records for your patients</p>
        </div>

        <a href="{{ route('doctor.dashboard') }}">
            Back to Dashboard
        </a>
    </div>

    @if($consultations->count())

        <x-list-search target="doctor-consultation-list" placeholder="Search patients, diagnoses, or dates..." label="Search your consultations" />

        <div class="consultation-list" id="doctor-consultation-list">

            @foreach($consultations as $consultation)

                <div class="consultation-list-card" data-search-item data-sort-timestamp="{{ $consultation->consultation_date->format('U.u') }}" data-sort-order="{{ $consultation->id }}">

                    <div class="consultation-list-date">
                        <strong>
                            {{ \Carbon\Carbon::parse($consultation->consultation_date)->format('M') }}
                        </strong>

                        <span>
                            {{ \Carbon\Carbon::parse($consultation->consultation_date)->format('d') }}
                        </span>
                    </div>

                    <div class="consultation-list-content">

                        <div class="consultation-list-header">

                            <div>
                                <span class="consultation-list-label">
                                    PATIENT CONSULTATION
                                </span>

                                <h4>
                                    {{ $consultation->patient->user->name ?? 'Patient' }}
                                </h4>

                                @if($consultation->patient)
                                    <p>
                                        {{ $consultation->patient->patient_number }}
                                    </p>
                                @endif
                            </div>

                        </div>

                        <div class="consultation-list-information consultation-list-summary">
                            <div>
                                <span>Date &amp; Time</span>
                                <strong>{{ $consultation->consultation_date->format('M j, Y · g:i A') }}</strong>
                            </div>

                            <div>
                                <span>Diagnosis</span>
                                <strong>
                                    {{ $consultation->diagnosis
                                        ? \Illuminate\Support\Str::limit($consultation->diagnosis, 110)
                                        : 'Not recorded' }}
                                </strong>
                            </div>

                            <div>
                                <span>Prescriptions</span>
                                <strong>{{ $consultation->prescriptions->count() }}</strong>
                            </div>
                        </div>

                        <div class="consultation-list-action">

                            <a
                                href="{{ route('doctor.consultations.show', $consultation) }}"
                                class="primary-button"
                            >
                                View Details
                            </a>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="empty-state">

            <div class="empty-icon">
                &#129658;
            </div>

            <h4>No consultations yet</h4>

            <p>
                Consultation records you create for your
                patients will appear here.
            </p>

        </div>

    @endif

</div>

@endsection