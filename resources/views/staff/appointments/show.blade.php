@extends('layouts.dashboard')

@section('title', 'Appointment Details')
@section('page-title', 'Appointment Details')

@section('content')
<div class="dashboard-card appointment-detail-card">

    <div class="card-header">
        <div>
            <h3>Appointment Details</h3>
            <p>Patient appointment information</p>
        </div>
        
        <div class="header-actions">
            <a href="{{ route('staff.appointments.index') }}" class="btn btn-secondary">
                Back to Appointments
            </a>

            @if($appointment->status === 'pending')
                <form
                    method="POST"
                    action="{{ route('staff.appointments.approve', $appointment) }}"
                    data-confirm="This appointment will be confirmed for the patient and made available to the assigned doctor."
                    data-confirm-title="Approve appointment?"
                    data-confirm-ok="Approve appointment"
                >
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-primary">
                        Approve Appointment
                    </button>
                </form>
            @endif

            @if(!$appointment->patientIntake)
                <a href="{{ route('staff.appointments.intake.create', $appointment) }}" class="btn btn-primary">
                    📝 Complete Patient Intake
                </a>
            @else
                <span class="badge badge-success">
                    ✓ Intake Completed
                </span>
            @endif
        </div>
    </div>

    <div class="appointment-preview">
        <div class="appointment-date">
            <strong>📅</strong>
            <span>
                {{ $appointment->appointment_date ? $appointment->appointment_date->format('M d') : 'N/A' }}
            </span>
        </div>

        <div class="appointment-details">
            <h4>{{ $appointment->patient->user->name ?? 'Patient' }}</h4>

            @if($appointment->patient)
                <p><strong>Patient No:</strong> {{ $appointment->patient->patient_number }}</p>
            @endif

            @if($appointment->doctor && $appointment->doctor->user)
                <p><strong>Doctor:</strong> {{ $appointment->doctor->user->name }}</p>
            @endif

            @if($appointment->healthCenter)
                <p><strong>Health Center:</strong> 🏥 {{ $appointment->healthCenter->name }}</p>
            @endif

            <p>
                <strong>Date:</strong> 
                {{ $appointment->appointment_date ? $appointment->appointment_date->format('F j, Y') : 'N/A' }}
            </p>
            <p>
                <strong>Time:</strong> 
                {{ $appointment->appointment_time ? $appointment->appointment_time->format('g:i A') : 'N/A' }}
            </p>
            <p><strong>Status:</strong> {{ ucfirst($appointment->status) }}</p>

            @if($appointment->reason)
                <p><strong>Reason:</strong> {{ $appointment->reason }}</p>
            @endif

            @if($appointment->notes)
                <p><strong>Notes:</strong> {{ $appointment->notes }}</p>
            @endif
        </div>
    </div>

    {{-- CONSULTATION SECTION --}}
    @if($appointment->consultation)
        <hr>
        <h3>🩺 Consultation</h3>
        <div class="appointment-details">
            @if($appointment->consultation->consultation_date)
                <p><strong>Consultation Date:</strong> {{ $appointment->consultation->consultation_date->format('F j, Y g:i A') }}</p>
            @endif

            @if($appointment->consultation->chief_complaint)
                <p><strong>Chief Complaint:</strong> {{ $appointment->consultation->chief_complaint }}</p>
            @endif

            @if($appointment->consultation->symptoms)
                <p><strong>Symptoms:</strong> {{ $appointment->consultation->symptoms }}</p>
            @endif

            @if($appointment->consultation->diagnosis)
                <p><strong>Diagnosis:</strong> {{ $appointment->consultation->diagnosis }}</p>
            @endif

            @if($appointment->consultation->treatment_plan)
                <p><strong>Treatment Plan:</strong> {{ $appointment->consultation->treatment_plan }}</p>
            @endif

            @if($appointment->consultation->notes)
                <p><strong>Additional Notes:</strong> {{ $appointment->consultation->notes }}</p>
            @endif
        </div>
    @endif

    {{-- PRESCRIPTIONS SECTION --}}
    @if($appointment->consultation && $appointment->consultation->prescriptions->count())
        <hr>
        <h3>💊 Prescriptions</h3>
        @foreach($appointment->consultation->prescriptions as $prescription)
            <div class="prescription-details staff-appointment-prescription">
                <h4>Prescription {{ $prescription->prescription_number }}</h4>
                <p><strong>Date:</strong> {{ $prescription->prescription_date ? $prescription->prescription_date->format('F j, Y') : 'N/A' }}</p>
                <p><strong>Status:</strong> {{ ucfirst($prescription->status) }}</p>

                @if($prescription->instructions)
                    <p><strong>General Instructions:</strong> {{ $prescription->instructions }}</p>
                @endif

                @if($prescription->items && $prescription->items->count())
                    <h4 class="staff-appointment-medicine-heading">Prescribed Medicines</h4>
                    @foreach($prescription->items as $item)
                        <div class="medicine-item staff-appointment-medicine">
                            <p>
                                💊 <strong>{{ $item->display_name }}</strong>
                            </p>

                            @if($item->medicine)
                                @if($item->medicine->generic_name)
                                    <p>({{ $item->medicine->generic_name }})</p>
                                @endif

                                @if($item->medicine->strength || $item->medicine->dosage_form)
                                    <p>
                                        <strong>Medicine Config:</strong> {{ $item->medicine->strength }}
                                        @if($item->medicine->dosage_form)
                                            — {{ $item->medicine->dosage_form }}
                                        @endif
                                    </p>
                                
                                @endif
                            @endif

                            @if($item->dosage)
                                <p><strong>Dosage:</strong> {{ $item->dosage }}</p>
                            @endif

                            @if($item->frequency)
                                <p><strong>Frequency:</strong> {{ $item->frequency }}</p>
                            @endif

                            @if($item->duration)
                                <p><strong>Duration:</strong> {{ $item->duration }}</p>
                            @endif

                            @if($item->quantity)
                                <p><strong>Quantity:</strong> {{ $item->quantity }}</p>
                            @endif

                            @if($item->instructions)
                                <p><strong>Instructions:</strong> {{ $item->instructions }}</p>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        @endforeach
    @endif

    <div class="staff-appointment-footer">
        <a href="{{ route('staff.appointments.index') }}" class="btn btn-secondary">
            Back to Appointments
        </a>
    </div>

</div>
@endsection
