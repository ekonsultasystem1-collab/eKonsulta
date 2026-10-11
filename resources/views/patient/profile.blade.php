@extends('layouts.dashboard')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
    <div class="dashboard-card patient-account-profile-page">
        <div class="card-header">
            <div>
                <h3>My Profile</h3>
                <p>Keep your contact and emergency information up to date.</p>
            </div>
            <a href="{{ route('patient.dashboard') }}" class="secondary-button">Back to Dashboard</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger patient-account-error-summary" role="alert" aria-labelledby="profile-error-heading">
                <strong id="profile-error-heading">Please correct the following:</strong>
                <ul>
                    @foreach($errors->getMessages() as $field => $messages)
                        <li><a href="#{{ $field }}">{{ $messages[0] }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($patient)
            <section class="patient-account-record-card" aria-labelledby="patient-account-record-heading">
                <div>
                    <span class="patient-account-eyebrow">PATIENT RECORD</span>
                    <h4 id="patient-account-record-heading">Your registered details</h4>
                    <p>These details help staff identify your patient record.</p>
                </div>
                <dl class="patient-account-facts">
                    <div>
                        <dt>Patient number</dt>
                        <dd>{{ $patient->patient_number }}</dd>
                    </div>
                    <div>
                        <dt>Date of birth</dt>
                        <dd>{{ $patient->date_of_birth?->format('F j, Y') ?? 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt>Sex</dt>
                        <dd>{{ $patient->sex ?? 'Not provided' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="patient-account-edit" aria-labelledby="patient-account-edit-heading">
                <div class="patient-account-section-heading">
                    <h3 id="patient-account-edit-heading">Contact and account information</h3>
                    <p>Update the details the health center can use to contact you.</p>
                </div>

                <form action="{{ route('patient.profile.update') }}" method="POST" class="patient-account-form">
                    @csrf
                    @method('PATCH')

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="name">Full name <span aria-hidden="true">*</span></label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                autocomplete="name"
                                maxlength="255"
                                required
                                @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                            >
                            @error('name')
                                <small class="patient-account-field-error" id="name-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="email">Email address <span aria-hidden="true">*</span></label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                autocomplete="email"
                                maxlength="255"
                                required
                                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                            >
                            @error('email')
                                <small class="patient-account-field-error" id="email-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="contact_number">Contact number</label>
                            <input
                                type="tel"
                                id="contact_number"
                                name="contact_number"
                                value="{{ old('contact_number', $patient->contact_number) }}"
                                autocomplete="tel"
                                inputmode="tel"
                                maxlength="30"
                                @error('contact_number') aria-invalid="true" aria-describedby="contact-number-error" @enderror
                            >
                            @error('contact_number')
                                <small class="patient-account-field-error" id="contact-number-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="address">Home address</label>
                            <textarea
                                id="address"
                                name="address"
                                rows="3"
                                autocomplete="street-address"
                                maxlength="500"
                                @error('address') aria-invalid="true" aria-describedby="address-error" @enderror
                            >{{ old('address', $patient->address) }}</textarea>
                            @error('address')
                                <small class="patient-account-field-error" id="address-error">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <fieldset class="patient-account-emergency">
                        <legend>Emergency contact <span>(optional)</span></legend>
                        <p>Add someone the health center can contact in an emergency.</p>
                        <div class="form-grid">
                            <div class="form-field">
                                <label for="emergency_contact_name">Contact name</label>
                                <input
                                    type="text"
                                    id="emergency_contact_name"
                                    name="emergency_contact_name"
                                    value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}"
                                    autocomplete="off"
                                    maxlength="255"
                                    @error('emergency_contact_name') aria-invalid="true" aria-describedby="emergency-contact-name-error" @enderror
                                >
                                @error('emergency_contact_name')
                                    <small class="patient-account-field-error" id="emergency-contact-name-error">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-field">
                                <label for="emergency_contact_number">Contact number</label>
                                <input
                                    type="tel"
                                    id="emergency_contact_number"
                                    name="emergency_contact_number"
                                    value="{{ old('emergency_contact_number', $patient->emergency_contact_number) }}"
                                    autocomplete="off"
                                    inputmode="tel"
                                    maxlength="30"
                                    @error('emergency_contact_number') aria-invalid="true" aria-describedby="emergency-contact-number-error" @enderror
                                >
                                @error('emergency_contact_number')
                                    <small class="patient-account-field-error" id="emergency-contact-number-error">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </fieldset>

                    <div class="patient-account-form-actions">
                        <button type="submit" class="primary-button">Save profile</button>
                    </div>
                </form>
            </section>
        @else
            <div class="empty-state" role="status">
                <h4>Patient record not found</h4>
                <p>Your patient profile has not been set up yet. Contact your health center for assistance.</p>
            </div>
        @endif
    </div>
@endsection
