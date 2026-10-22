@extends('layouts.dashboard')

@section('title', 'My Prescriptions')

@section('page-title', 'Prescriptions')

@section('content')

<div class="dashboard-card">

    <div class="card-header">

        <div>
            <h3>My Prescriptions</h3>
            <p>Browse prescriptions issued during your consultations.</p>
        </div>

        <a href="{{ route('patient.dashboard') }}">
            Back to Dashboard
        </a>

    </div>


    @if($prescriptions->count())

        <x-list-search target="patient-prescription-list" placeholder="Search prescription number, doctor, date, or status..." label="Search your prescriptions" />

        <div class="medical-records-list" id="patient-prescription-list">

            @foreach($prescriptions as $prescription)

                <div class="appointment-preview" data-search-item data-sort-timestamp="{{ $prescription->prescription_date->format('U.u') }}" data-sort-order="{{ $prescription->id }}">

                    <div class="appointment-date">

                        <strong>
                            {{ $prescription->prescription_date->format('M') }}
                        </strong>

                        <span>
                            {{ $prescription->prescription_date->format('d') }}
                        </span>

                    </div>


                    <div class="appointment-details">
                        <h4>Prescription {{ $prescription->prescription_number }}</h4>
                        <p>{{ $prescription->prescription_date->format('l, F j, Y') }}</p>

                        @if($prescription->doctor?->user)
                            <p>Issued by Dr. {{ $prescription->doctor->user->name }}</p>
                        @endif

                        <span class="appointment-status status-{{ strtolower($prescription->status) }}">
                            {{ ucfirst($prescription->status) }}
                        </span>

                        <div class="form-actions">
                            <a
                                href="{{ route('patient.prescriptions.show', $prescription) }}"
                                class="primary-button"
                            >
                                View Prescription
                            </a>
                        </div>
                    </div>

                </div>

            @endforeach

        </div>


    @else

        <div class="empty-state">

            <div class="empty-icon">
                💊
            </div>

            <h4>No prescriptions yet</h4>

            <p>
                Your prescriptions will appear here after your doctor
                prescribes medicine during a consultation.
            </p>

        </div>

    @endif

</div>

@endsection