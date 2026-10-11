@extends('layouts.dashboard')

@section('title', 'Create Prescription')

@section('page-title', 'Create Prescription')

@section('content')

<div class="dashboard-card clinical-workspace prescription-create-page">

    <div class="card-header">
        <div>
            <h3>Create Prescription</h3>
            <p>Prescribe medicine for this completed consultation.</p>
        </div>

        <a href="{{ route('doctor.appointments.index') }}">
            Back to Appointments
        </a>
    </div>

    <div class="prescription-context-grid">
        <div class="appointment-details prescription-patient-summary">
            <h4>Patient Information</h4>

            @if($appointment->patient && $appointment->patient->user)
                <p>👤 {{ $appointment->patient->user->name }}</p>
            @endif

            @if($appointment->patient)
                <p>Patient No: {{ $appointment->patient->patient_number }}</p>
            @endif

            <p>📅 {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F j, Y') }}</p>
        </div>

        <div class="dashboard-card prescription-consultation-summary">
            <h4>Consultation Summary</h4>

            @if($consultation->chief_complaint)
                <p><strong>Chief Complaint:</strong> {{ $consultation->chief_complaint }}</p>
            @endif

            @if($consultation->diagnosis)
                <p><strong>Diagnosis:</strong> {{ $consultation->diagnosis }}</p>
            @endif

            @if($consultation->treatment_plan)
                <p><strong>Treatment Plan:</strong> {{ $consultation->treatment_plan }}</p>
            @endif
        </div>
    </div>

    <form class="prescription-entry-form" method="POST" action="{{ route('doctor.appointments.prescription.store', $appointment) }}">
        @csrf

        <div class="form-section prescription-form-section">
            <div class="form-section-head">
                <span class="step-number">1</span>
                <div>
                    <h4>Prescription Details</h4>
                    <p>Fill in the medication order</p>
                </div>
            </div>

            <div class="prescription-form-grid">
                <div class="form-group">
                    <label for="prescription_date">Prescription Date</label>
                    <input type="date" id="prescription_date" name="prescription_date" value="{{ old('prescription_date', now()->format('Y-m-d')) }}" required>
                    @error('prescription_date')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="medicine_id">Medicine</label>
                    <select id="medicine_id" name="medicine_id" required>
                        <option value="">-- Select Medicine --</option>
                        @foreach($medicines as $medicine)
                            <option value="{{ $medicine->id }}" {{ old('medicine_id') == $medicine->id ? 'selected' : '' }}>
                                {{ $medicine->name }} - {{ $medicine->strength }}
                            </option>
                        @endforeach
                    </select>
                    @error('medicine_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="dosage">Dosage</label>
                    <input type="text" id="dosage" name="dosage" value="{{ old('dosage') }}" placeholder="Example: 500mg" required>
                    @error('dosage')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="frequency">Frequency</label>
                    <input type="text" id="frequency" name="frequency" value="{{ old('frequency') }}" placeholder="Example: Every 8 hours" required>
                    @error('frequency')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="duration">Duration</label>
                    <input type="text" id="duration" name="duration" value="{{ old('duration') }}" placeholder="Example: 5 days" required>
                    @error('duration')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" value="{{ old('quantity') }}" min="1" required>
                    @error('quantity')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group prescription-wide-field">
                    <label for="item_instructions">Medicine Instructions</label>
                    <textarea id="item_instructions" name="item_instructions" rows="3" placeholder="Example: Take after meals.">{{ old('item_instructions') }}</textarea>
                    @error('item_instructions')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group prescription-wide-field">
                    <label for="instructions">General Prescription Instructions</label>
                    <textarea id="instructions" name="instructions" rows="4" placeholder="Additional instructions for the patient...">{{ old('instructions') }}</textarea>
                    @error('instructions')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <div class="prescription-form-actions">
            <a href="{{ route('doctor.appointments.index') }}" class="secondary-button">Cancel</a>
            <button type="submit" class="primary-button">💊 Save Prescription</button>
        </div>
    </form>
</div>

@endsection
