@extends('layouts.dashboard')

@section('title', 'Prescription Details')

@section('page-title', 'Prescription Details')

@section('content')

<div class="dashboard-card clinical-workspace staff-prescription-detail">

    <div class="card-header">

        <div>
            <h3>Prescription Details</h3>
            <p>Verify prescription information before releasing medicine</p>
        </div>

        <a href="{{ route('staff.prescriptions.index') }}">
            Back to Prescriptions
        </a>

    </div>


    {{-- SUCCESS / ERROR MESSAGES --}}

    @if(session('success'))

        <div class="alert alert-success" role="status">
            {{ session('success') }}
        </div>

    @endif

    @if(session('error'))

        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>

    @endif


    {{-- PATIENT INFORMATION --}}

    <div class="medical-record-section">

        <h4>Patient Information</h4>

        <p>
            <strong>Name:</strong>
            {{ $prescription->patient->user->name ?? 'Patient' }}
        </p>

        @if($prescription->patient)

            <p>
                <strong>Patient No:</strong>
                {{ $prescription->patient->patient_number }}
            </p>

        @endif

    </div>


    {{-- PRESCRIPTION INFORMATION --}}

    <div class="medical-record-section">

        <h4>Prescription Information</h4>

        <p>
            <strong>Prescription No:</strong>
            {{ $prescription->prescription_number }}
        </p>

        <p>
            <strong>Date:</strong>
            {{ $prescription->prescription_date->format('F j, Y') }}
        </p>

        @if($prescription->doctor && $prescription->doctor->user)

            <p>
                <strong>Doctor:</strong>
                {{ $prescription->doctor->user->name }}
            </p>

        @endif

        @if($prescription->consultation &&
            $prescription->consultation->appointment &&
            $prescription->consultation->appointment->healthCenter)

            <p>
                <strong>Health Center:</strong>
                {{ $prescription->consultation->appointment->healthCenter->name }}
            </p>

        @endif

        <p>
            <strong>Status:</strong>
            {{ ucfirst($prescription->status) }}
        </p>

    </div>


    {{-- CONSULTATION INFORMATION --}}

    @if($prescription->consultation)

        <div class="medical-record-section">

            <h4>Consultation</h4>

            <p>
                <strong>Chief Complaint:</strong>
                {{ $prescription->consultation->chief_complaint ?? 'N/A' }}
            </p>

            <p>
                <strong>Diagnosis:</strong>
                {{ $prescription->consultation->diagnosis ?? 'N/A' }}
            </p>

            @if($prescription->consultation->treatment_plan)

                <p>
                    <strong>Treatment Plan:</strong>
                    {{ $prescription->consultation->treatment_plan }}
                </p>

            @endif

        </div>

    @endif


    {{-- MEDICINES --}}

    <div class="medical-record-section">

        <h4>Prescribed Medicine</h4>

        @foreach($prescription->items as $item)

            <div class="appointment-preview">

                <div class="appointment-details">

                    <h4>
                        {{ $item->display_name }}

                        @if($item->medicine)

                            {{ $item->medicine->strength }}
                        @endif

                    </h4>

                    @if($item->medicine && $item->medicine->dosage_form)

                        <p>
                            <strong>Dosage Form:</strong>
                            {{ $item->medicine->dosage_form }}
                        </p>

                    @endif

                    <p>
                        <strong>Dosage:</strong>
                        {{ $item->dosage }}
                    </p>

                    <p>
                        <strong>Frequency:</strong>
                        {{ $item->frequency }}
                    </p>

                    <p>
                        <strong>Duration:</strong>
                        {{ $item->duration }}
                    </p>

                    <p>
                        <strong>Quantity:</strong>
                        {{ $item->quantity }}
                    </p>

                    @if($item->instructions)

                        <p>
                            <strong>Instructions:</strong>
                            {{ $item->instructions }}
                        </p>

                    @endif

                </div>

            </div>

        @endforeach

    </div>


    {{-- GENERAL INSTRUCTIONS --}}

    @if($prescription->instructions)

        <div class="medical-record-section">

            <h4>General Instructions</h4>

            <p>
                {{ $prescription->instructions }}
            </p>

        </div>

    @endif


    {{-- RELEASE INFORMATION --}}

    @if($prescription->released_at)

        <div class="medical-record-section">

            <h4>Release Information</h4>

            <p>
                <strong>Released At:</strong>
                {{ $prescription->released_at->format('F j, Y g:i A') }}
            </p>

            @if($prescription->releasedBy)

                <p>
                    <strong>Released By:</strong>
                    {{ $prescription->releasedBy->name }}
                </p>

            @endif

        </div>

    @endif


    {{-- RELEASE ACTION --}}

    @if($prescription->status === 'active')

        <div class="form-actions prescription-release-actions">

            <form
                action="{{ route('staff.prescriptions.release', $prescription) }}"
                method="POST"
                data-confirm="Confirm that the listed medicine has been handed to the patient."
                data-confirm-title="Release medicine?"
                data-confirm-ok="Release medicine"
                data-confirm-cancel="Review again"
                data-confirm-icon="warning"
            >

                @csrf
                @method('PATCH')

                <button
                    type="submit"
                    class="primary-button"
                >
                    Release Medicine
                </button>

            </form>

        </div>

    @else

        <div class="prescription-release-status">

            <p>
                <strong>Medicine has already been released.</strong>
            </p>

        </div>

    @endif

</div>

@endsection