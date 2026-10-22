@extends('layouts.dashboard')

@section('title', 'Patients')

@section('page-title', 'Patients')

@section('content')

<div class="dashboard-card">

    <div class="card-header">

        <div>
            <h3>Registered Patients</h3>
            <p>Patients registered in the health center</p>
        </div>

        <a href="{{ route('staff.dashboard') }}">
            Back to Dashboard
        </a>

    </div>

    @if($patients->count())

        <x-list-search
            target="staff-patient-list"
            placeholder="Search by patient name, number, email, or contact..."
            label="Search registered patients"
        />

        <div class="patient-list" id="staff-patient-list">

            @foreach($patients as $patient)

                <div class="patient-card" data-search-item data-sort-timestamp="{{ optional($patient->created_at ?? $patient->user?->created_at)->format('U.u') ?? '' }}" data-sort-order="{{ $patient->id }}">

                    <div class="patient-avatar">
                        &#128100;
                    </div>

                    <div class="patient-information">

                        <h4>
                            {{ $patient->user->name ?? 'Patient' }}
                        </h4>

                        <div class="patient-meta">

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

                            @if($patient->contact_number)
                                <span>
                                    <strong>Contact:</strong>
                                    {{ $patient->contact_number }}
                                </span>
                            @endif

                        </div>

                    </div>

                    <div class="patient-action">

                        <a
                            href="{{ route('staff.patients.show', $patient) }}"
                            class="primary-button"
                        >
                            View Patient
                        </a>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="empty-state">

            <div class="empty-icon">
                &#128100;
            </div>

            <h4>No registered patients</h4>

            <p>
                Patients registered in the system will appear here.
            </p>

        </div>

    @endif

</div>

@endsection