@extends('layouts.dashboard')

@section('title', 'Doctor Management')
@section('page-title', 'Doctor Management')

@section('content')

<div class="dashboard-card">

    <div class="card-header">
        <div>
            <h3>All Doctors</h3>
            <p>Manage registered E Konsulta doctors</p>
        </div>

        <div class="admin-doctor-header-actions">
            <a href="{{ route('admin.dashboard') }}">
                Back to Dashboard
            </a>

            <a href="{{ route('admin.doctors.create') }}" class="primary-button">
                Add Doctor
            </a>
        </div>
    </div>

    @if($doctors->count())

        <x-list-search target="admin-doctor-list" placeholder="Search by name, license, specialty, or health center..." label="Search doctors" />

        <div class="admin-doctor-list" id="admin-doctor-list">

            @foreach($doctors as $doctor)

                <div class="admin-doctor-card" data-search-item>

                    <div class="admin-doctor-avatar">
                        &#128104;&#8205;&#9877;
                    </div>

                    <div class="admin-doctor-information">

                        <span class="admin-doctor-label">
                            DOCTOR
                        </span>

                        <h4>
                            {{ $doctor->user->name }}
                        </h4>

                        <div class="admin-doctor-meta">

                            <span>
                                <strong>Email:</strong>
                                {{ $doctor->user->email }}
                            </span>

                            <span>
                                <strong>License:</strong>
                                {{ $doctor->license_number }}
                            </span>

                            <span>
                                <strong>Specialization:</strong>
                                {{ $doctor->specialization ?: 'Not specified' }}
                            </span>

                            <span>
                                <strong>Health Center:</strong>
                                {{ $doctor->healthCenter->name }}
                            </span>

                            <span>
                                <strong>Status:</strong>

                                <span class="user-status-badge {{ $doctor->user->status === 'active' ? 'active' : 'inactive' }}">
                                    {{ ucfirst($doctor->user->status) }}
                                </span>
                            </span>

                        </div>

                    </div>

                    <div class="admin-doctor-action">

                        <a
                            href="{{ route('admin.doctors.show', $doctor) }}"
                            class="primary-button"
                        >
                            View Details
                        </a>

                        <a
                            href="{{ route('admin.doctors.edit', $doctor) }}"
                            class="secondary-button"
                        >
                            Edit
                        </a>

                        <form
                            method="POST"
                            action="{{ route('admin.doctors.toggle-status', $doctor) }}"
                            class="admin-doctor-status-form"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="status-button {{ $doctor->user->status === 'active' ? 'deactivate' : 'activate' }}"
                            >
                                {{ $doctor->user->status === 'active' ? 'Deactivate' : 'Activate' }}
                            </button>

                        </form>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="empty-state">

            <div class="empty-icon">
                &#128104;&#8205;&#9877;
            </div>

            <h4>No doctors found</h4>

            <p>
                There are currently no registered doctors in the system.
            </p>

        </div>

    @endif

</div>

@endsection