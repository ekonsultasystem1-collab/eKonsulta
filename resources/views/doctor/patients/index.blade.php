@extends('layouts.dashboard')

@section('title', 'My Patients')

@section('page-title', 'My Patients')

@section('content')

<div class="dashboard-card">

    <div class="card-header">
        <div>
            <h3>My Patients</h3>
            <p>Patients who have appointments with you</p>
        </div>

        <a href="{{ route('doctor.dashboard') }}">
            Back to Dashboard
        </a>
    </div>

    @if($patients->count())

        <x-list-search
            target="doctor-patient-list"
            placeholder="Search by patient name, number, email, or phone..."
            label="Search your patients"
        />

        <div class="patient-list" id="doctor-patient-list">

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

                            @if($patient->user && $patient->user->phone)
                                <span>
                                    <strong>Phone:</strong>
                                    {{ $patient->user->phone }}
                                </span>
                            @endif

                        </div>

                    </div>

                    <div class="patient-action">

                        <a
                            href="{{ route('doctor.patients.show', $patient) }}"
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

            <h4>No patients yet</h4>

            <p>
                Patients who have appointments with you
                will appear here.
            </p>

        </div>

    @endif

</div>

@endsection