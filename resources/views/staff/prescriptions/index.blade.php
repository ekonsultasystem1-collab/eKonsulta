@extends('layouts.dashboard')

@section('title', 'Prescriptions')

@section('page-title', 'Prescriptions')

@section('content')

<div class="dashboard-card">

    <div class="card-header">

        <div>
            <h3>Medicine Release</h3>
            <p>Review prescriptions and release medicines to patients</p>
        </div>

        <a href="{{ route('staff.dashboard') }}">
            Back to Dashboard
        </a>

    </div>

    @if($prescriptions->count())

        <x-list-search target="staff-prescription-list" placeholder="Search patients, doctors, prescription numbers, or status..." label="Search prescriptions" />

        <div class="medical-records-list" id="staff-prescription-list">

            @foreach($prescriptions as $prescription)

                <div class="appointment-preview" data-search-item data-sort-timestamp="{{ $prescription->prescription_date->format('U.u') }}" data-sort-order="{{ $prescription->id }}">

                    <div class="appointment-date">

                        <strong>
                            RX
                        </strong>

                        <span>
                            {{ $prescription->id }}
                        </span>

                    </div>

                    <div class="appointment-details">

                        <h4>
                            {{ $prescription->patient->user->name ?? 'Patient' }}
                        </h4>

                        @if($prescription->patient)

                            <p>
                                <strong>Patient No:</strong>
                                {{ $prescription->patient->patient_number }}
                            </p>

                        @endif

                        @if($prescription->doctor && $prescription->doctor->user)

                            <p>
                                <strong>Doctor:</strong>
                                {{ $prescription->doctor->user->name }}
                            </p>

                        @endif

                        <p>
                            <strong>Prescription No:</strong>
                            {{ $prescription->prescription_number }}
                        </p>

                        <p>
                            <strong>Date:</strong>
                            {{ $prescription->prescription_date->format('F j, Y') }}
                        </p>

                        <p>
                            <strong>Medicine:</strong>
                            {{ $prescription->items->count() }}
                            {{ $prescription->items->count() === 1 ? 'item' : 'items' }}
                        </p>

                        <p>
                            <strong>Status:</strong>
                            {{ ucfirst($prescription->status) }}
                        </p>

                        @if($prescription->released_at)

                            <p>
                                <strong>Released:</strong>
                                {{ $prescription->released_at->format('F j, Y g:i A') }}
                            </p>

                            @if($prescription->releasedBy)

                                <p>
                                    <strong>Released By:</strong>
                                    {{ $prescription->releasedBy->name }}
                                </p>

                            @endif

                        @endif


                        {{-- ACTIONS --}}

                        <div style="margin-top: 15px;">

                            <a
                                href="{{ route('staff.prescriptions.show', $prescription) }}"
                                class="primary-button"
                            >
                                View Prescription
                            </a>

                        </div>

                    </div>

                </div>

                <hr>

            @endforeach

        </div>

    @else

        <div class="empty-state">

            <div class="empty-icon">
                💊
            </div>

            <h4>No prescriptions yet</h4>

            <p>
                Doctor-issued prescriptions will appear here.
            </p>

        </div>

    @endif

</div>

@endsection