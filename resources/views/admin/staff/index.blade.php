@extends('layouts.dashboard')

@section('title', 'Staff Management')
@section('page-title', 'Staff Management')

@section('content')

<div class="dashboard-card">

    <div class="card-header">

        <div>
            <h3>All Staff</h3>
            <p>Manage registered E Konsulta staff members</p>
        </div>

        <div>
            <a href="{{ route('admin.dashboard') }}" class="secondary-button">
                Back to Dashboard
            </a>

            <a href="{{ route('admin.staff.create') }}" class="primary-button">
                Add Staff
            </a>
        </div>

    </div>

    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="error-message">
            {{ session('error') }}
        </div>
    @endif

    @if($staff->count())

        <x-list-search target="admin-staff-list" placeholder="Search by name, employee number, position, or health center..." label="Search staff members" />

        <div class="admin-user-list" id="admin-staff-list">

            @foreach($staff as $member)

                <div class="admin-user-card" data-search-item>

                    <div class="admin-user-avatar">
                        👤
                    </div>

                    <div class="admin-user-information">

                        <span class="admin-user-label">
                            STAFF
                        </span>

                        <h4>
                            {{ $member->user->name }}
                        </h4>

                        <div class="admin-user-meta">

                            <span>
                                <strong>Email:</strong>
                                {{ $member->user->email }}
                            </span>

                            <span>
                                <strong>Employee No.:</strong>
                                {{ $member->employee_number }}
                            </span>

                            <span>
                                <strong>Position:</strong>
                                {{ $member->position }}
                            </span>

                            <span>
                                <strong>Health Center:</strong>
                                {{ $member->healthCenter->name }}
                            </span>

                            <span>
                                <strong>Status:</strong>

                                <span class="user-status-badge {{ $member->user->status === 'active' ? 'active' : 'inactive' }}">
                                    {{ ucfirst($member->user->status) }}
                                </span>
                            </span>

                        </div>

                    </div>

                    <div class="admin-user-action">

                        <a
                            href="{{ route('admin.staff.show', $member) }}"
                            class="primary-button"
                        >
                            View Details
                        </a>

                        <a
                            href="{{ route('admin.staff.edit', $member) }}"
                            class="secondary-button"
                        >
                            Edit
                        </a>

                        @if($member->user_id !== auth()->id())

                            <form
                                method="POST"
                                action="{{ route('admin.staff.toggle-status', $member) }}"
                                class="admin-user-status-form"
                            >
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="status-button {{ $member->user->status === 'active' ? 'deactivate' : 'activate' }}"
                                >
                                    {{ $member->user->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>

                            </form>

                        @else

                            <span class="current-admin-label">
                                Current Account
                            </span>

                        @endif

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="empty-state">

            <div class="empty-icon">
                👤
            </div>

            <h4>No staff found</h4>

            <p>
                There are currently no registered staff members in the system.
            </p>

            <a
                href="{{ route('admin.staff.create') }}"
                class="primary-button"
            >
                Add Staff
            </a>

        </div>

    @endif

</div>

@endsection