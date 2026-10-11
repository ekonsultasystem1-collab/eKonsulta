@extends('layouts.dashboard')

@section('title', 'Add Service')

@section('page-title', 'Add Service')

@section('content')

<div class="dashboard-card admin-service-form-page">

    <div class="card-header">

        <div>
            <h3>Add Service</h3>
            <p>Register a healthcare service for E Konsulta</p>
        </div>

        <a href="{{ route('admin.services.index') }}">
            Back to Services
        </a>

    </div>

    <form
        method="POST"
        action="{{ route('admin.services.store') }}"
        class="admin-service-form"
    >

        @csrf

        @if($errors->any())

            <div class="form-error-box">

                <strong>Please correct the following:</strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>

        @endif

        <div class="admin-service-form-grid">

            <div class="admin-service-form-field">

                <label for="name">
                    Service Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="e.g. General Consultation"
                    required
                >

            </div>

            <div class="admin-service-form-field">

                <label for="code">
                    Service Code
                </label>

                <input
                    type="text"
                    id="code"
                    name="code"
                    value="{{ old('code') }}"
                    placeholder="e.g. GEN-CONSULT"
                    required
                >

                <small>
                    Use letters, numbers, hyphens, or underscores.
                </small>

            </div>

            <div class="admin-service-form-field full-width">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    placeholder="Describe what this healthcare service provides."
                >{{ old('description') }}</textarea>

            </div>

            <div class="admin-service-form-field">

                <label for="status">
                    Status
                </label>

                <select
                    id="status"
                    name="status"
                    required
                >

                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>

            </div>

        </div>

        <div class="admin-service-form-actions">

            <a
                href="{{ route('admin.services.index') }}"
                class="secondary-button"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="primary-button"
            >
                Add Service
            </button>

        </div>

    </form>

</div>

@endsection