@extends('layouts.dashboard')

@section('title', 'My Prescriptions')

@section('page-title', 'My Prescriptions')

@section('content')

<div class="dashboard-card doctor-prescriptions-page">

<div class="card-header">
    <div>
        <span class="page-eyebrow">MEDICATION RECORDS</span>
        <h3>My Prescriptions</h3>
        <p>Prescriptions issued to your patients</p>
    </div>

    <a href="{{ route('doctor.dashboard') }}">
        Back to Dashboard
    </a>
</div>

@if($prescriptions->count())

    <x-list-search target="doctor-prescription-list" placeholder="Search patients, prescription numbers, medicines, or status..." label="Search your prescriptions" />

    <div class="doctor-prescription-list" id="doctor-prescription-list">

        @foreach($prescriptions as $prescription)

            <div class="doctor-prescription-card" data-search-item>

                <div class="doctor-prescription-header">

                    <div class="doctor-prescription-date">

                        <div class="doctor-prescription-icon">
                            &#128138;
                        </div>

                        <div>
                            <span class="doctor-prescription-label">
                                PRESCRIPTION
                            </span>

                            <h4>
                                {{ $prescription->patient->user->name ?? 'Patient' }}
                            </h4>

                            <p>
                                {{ \Carbon\Carbon::parse($prescription->prescription_date)->format('F d, Y') }}
                            </p>
                        </div>

                    </div>

                    <span class="appointment-status status-{{ strtolower($prescription->status) }}">
                        {{ ucfirst($prescription->status) }}
                    </span>

                </div>

                <div class="doctor-prescription-information">

                    <div>
                        <span>Prescription No.</span>
                        <strong>
                            {{ $prescription->prescription_number }}
                        </strong>
                    </div>

                    <div>
                        <span>Date Issued</span>
                        <strong>
                            {{ \Carbon\Carbon::parse($prescription->prescription_date)->format('F d, Y') }}
                        </strong>
                    </div>

                </div>

                <div class="doctor-prescription-summary">
                    <span>
                        {{ $prescription->items->count() }}
                        {{ $prescription->items->count() === 1 ? 'medicine' : 'medicines' }}
                    </span>
                    @if($prescription->consultation?->diagnosis)
                        <span>Diagnosis: {{ \Illuminate\Support\Str::limit($prescription->consultation->diagnosis, 100) }}</span>
                    @endif
                </div>

                <div class="doctor-prescription-actions">

                    <a
                        href="{{ route('doctor.prescriptions.show', $prescription) }}"
                        class="btn btn-primary"
                    >
                        View Full Prescription
                    </a>

                </div>

            </div>

        @endforeach

    </div>

@else

    <div class="empty-state">

        <div class="empty-icon">
            &#128138;
        </div>

        <h4>No prescriptions yet</h4>

        <p>
            Prescriptions you create for your patients
            will appear here.
        </p>

    </div>

@endif


</div>

@endsection
