@extends('layouts.dashboard')

@section('title', 'User Management')

@section('page-title', 'User Management')

@section('content')

<div class="dashboard-card">

    <div class="card-header">

        <div>
            <h3>All Users</h3>
            <p>Manage registered E Konsulta system users</p>
        </div>

        <a href="{{ route('admin.dashboard') }}">
            Back to Dashboard
        </a>

    </div>

    @if($users->count())

        <x-list-search
            target="admin-user-list"
            placeholder="Search by name, email, role, or status..."
            label="Search system users"
            :default-sort="$sort"
            :server-sort="true"
            :search-value="$search"
        />

        <div class="admin-user-list" id="admin-user-list">

       @foreach($users as $user)
    <div class="admin-user-card" data-search-item data-sort-timestamp="{{ $user->created_at?->format('U.u') ?? '' }}" data-sort-order="{{ $user->id }}">

        <div class="admin-user-avatar">
            &#128100;
        </div>

        <div class="admin-user-information">

            <span class="admin-user-label">
                {{ strtoupper($user->role) }}
            </span>

            <h4>{{ $user->name }}</h4>

            <div class="admin-user-meta">
                <span>
                    <strong>Email:</strong> {{ $user->email }}
                </span>

                <span>
                    <strong>Role:</strong> {{ ucfirst($user->role) }}
                </span>

                <span>
                    <strong>Status:</strong>
                    <span class="user-status-badge {{ $user->status === 'active' ? 'active' : 'inactive' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </span>
            </div>

        </div>

        <div class="admin-user-action">

            <a
                href="{{ route('admin.users.show', $user) }}"
                class="primary-button"
            >
                View Details
            </a>

            @if($user->id !== auth()->id())
                <form
                    method="POST"
                    action="{{ route('admin.users.toggle-status', $user) }}"
                    class="admin-user-status-form"
                >
                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="status-button {{ $user->status === 'active' ? 'deactivate' : 'activate' }}"
                    >
                        {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
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
                &#128100;
            </div>

            <h4>No users found</h4>

            <p>
                There are currently no registered users in the system.
            </p>

        </div>

    @endif

</div>

@endsection