@extends('layouts.dashboard')

@section('title', 'Consultations')

@section('page-title', 'Consultations')

@section('content')

<div class="dashboard-card">

    <div class="card-header">

        <div>
            <h3>All Consultations</h3>
            <p>View patient consultation records</p>
        </div>

        <a href="{{ route('staff.dashboard') }}">
            Back to Dashboard
        </a>

    </div>

    @if($consultations->count())

        <x-list-search target="staff-consultation-list" placeholder="Search patients, doctors, diagnoses, or dates..." label="Search consultations" />

        <div class="consultation-list" id="staff-consultation-list">

            @foreach($consultations as $consultation)

                <div class="consultation-list-card" data-search-item data-sort-timestamp="{{ $consultation->consultation_date->format('U.u') }}" data-sort-order="{{ $consultation->id }}">

                    {{-- DATE --}}

                    <div class="consultation-list-date">

                        <strong>
                            {{ $consultation->consultation_date->format('M') }}
                        </strong>

                        <span>
                            {{ $consultation->consultation_date->format('d') }}
                        </span>

                    </div>


                    {{-- CONSULTATION CONTENT --}}

                    <div class="consultation-list-content">

                        <div class="consultation-list-header">

                            <div>

                                <span class="consultation-list-label">
                                    PATIENT
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

                            <div>

                                <span class="status-badge">
                                    {{ ucfirst($consultation->status ?? 'Completed') }}
                                </span>

                            </div>

                        </div>


                        {{-- INFORMATION --}}

                        <div class="consultation-list-information">

                            @if($consultation->doctor && $consultation->doctor->user)

                                <div>

                                    <span>Doctor</span>

                                    <strong>
                                        {{ $consultation->doctor->user->name }}
                                    </strong>

                                </div>

                            @endif


                            @if($consultation->consultation_date)

                                <div>

                                    <span>Date & Time</span>

                                    <strong>
                                        {{ $consultation->consultation_date->format('F j, Y g:i A') }}
                                    </strong>

                                </div>

                            @endif


                            @if($consultation->chief_complaint)

                                <div>

                                    <span>Chief Complaint</span>

                                    <strong>
                                        {{ $consultation->chief_complaint }}
                                    </strong>

                                </div>

                            @endif


                            @if($consultation->diagnosis)

                                <div>

                                    <span>Diagnosis</span>

                                    <strong>
                                        {{ $consultation->diagnosis }}
                                    </strong>

                                </div>

                            @endif

                        </div>


                        {{-- ACTION --}}

                        <div class="consultation-list-action">

                            <a
                                href="{{ route('staff.consultations.show', $consultation) }}"
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
                Patient consultation records will appear here.
            </p>

        </div>

    @endif

</div>

@endsection