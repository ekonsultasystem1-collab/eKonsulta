@extends('layouts.dashboard')

@section('title', 'Add Staff')
@section('page-title', 'Add Staff')

@section('content')

<div class="dashboard-card">

    <div class="card-header">

        <div>
            <h3>Add Staff</h3>
            <p>Create a new staff account for the E Konsulta system</p>
        </div>

        <a
            href="{{ route('admin.staff.index') }}"
            class="secondary-button"
        >
            Back to Staff
        </a>

    </div>

    @if($errors->any())
        <div class="error-message">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('admin.staff.store') }}"
    >

        @csrf

        <div class="form-group">
            <label for="name">Staff Name</label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                required
            >
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>

            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>

            <div class="app-password-field">
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                >
                <button
                    type="button"
                    class="app-password-toggle"
                    data-password-toggle
                    data-target="password"
                    data-show-label="Show password"
                    data-hide-label="Hide password"
                    aria-label="Show password"
                    aria-pressed="false"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-password-icon="show">
                        <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-password-icon="hide">
                        <path d="M3 3l18 18"></path>
                        <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"></path>
                        <path d="M9.9 5.2A11 11 0 0 1 12 5c6.4 0 10 7 10 7a13.7 13.7 0 0 1-3.1 3.8"></path>
                        <path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10 10 0 0 0 4-.8"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="form-group">
            <label for="password_confirmation">
                Confirm Password
            </label>

            <div class="app-password-field">
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                >
                <button
                    type="button"
                    class="app-password-toggle"
                    data-password-toggle
                    data-target="password_confirmation"
                    data-show-label="Show password confirmation"
                    data-hide-label="Hide password confirmation"
                    aria-label="Show password confirmation"
                    aria-pressed="false"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-password-icon="show">
                        <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-password-icon="hide">
                        <path d="M3 3l18 18"></path>
                        <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"></path>
                        <path d="M9.9 5.2A11 11 0 0 1 12 5c6.4 0 10 7 10 7a13.7 13.7 0 0 1-3.1 3.8"></path>
                        <path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10 10 0 0 0 4-.8"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="form-group">
            <label for="health_center_id">Health Center</label>

            <select
                id="health_center_id"
                name="health_center_id"
                required
            >
                <option value="">Select health center</option>

                @foreach($healthCenters as $healthCenter)
                    <option
                        value="{{ $healthCenter->id }}"
                        {{ old('health_center_id') == $healthCenter->id ? 'selected' : '' }}
                    >
                        {{ $healthCenter->name }}
                    </option>
                @endforeach

            </select>
        </div>

        <div class="form-group">
            <label for="employee_number">
                Employee Number
            </label>

            <input
                type="text"
                id="employee_number"
                name="employee_number"
                value="{{ old('employee_number') }}"
                required
            >
        </div>

        <div class="form-group">
            <label for="position">Position</label>

            <input
                type="text"
                id="position"
                name="position"
                value="{{ old('position') }}"
                placeholder="e.g. Health Center Staff"
                required
            >
        </div>

        <div class="form-group">
            <label for="contact_number">
                Contact Number
            </label>

            <input
                type="text"
                id="contact_number"
                name="contact_number"
                value="{{ old('contact_number') }}"
            >
        </div>

        <div class="form-actions">

            <a
                href="{{ route('admin.staff.index') }}"
                class="secondary-button"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="primary-button"
            >
                Create Staff
            </button>

        </div>

    </form>

</div>

@endsection