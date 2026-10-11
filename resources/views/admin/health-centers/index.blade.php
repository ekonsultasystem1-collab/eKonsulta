@extends('layouts.dashboard')

@section('title', 'Health Centers')

@section('page-title', 'Health Centers')

@section('content')

<div class="dashboard-card admin-health-centers-page">

 <div class="card-header">

    <div>
        <h3>Health Centers</h3>
        <p>Manage registered health centers and their information</p>
    </div>

    <div class="admin-health-center-header-actions">

        <a
            href="{{ route('admin.dashboard') }}"
            class="secondary-button"
        >
            Back to Dashboard
        </a>

        <a
            href="{{ route('admin.health-centers.create') }}"
            class="primary-button"
        >
            Add Health Center
        </a>

    </div>

</div>

    @if ($errors->has('health_center'))
        <div class="health-center-feedback health-center-feedback--error" role="alert">
            {{ $errors->first('health_center') }}
        </div>
    @endif

    @if (session('success'))
        <div class="health-center-feedback health-center-feedback--success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if($healthCenters->count())

        <x-list-search target="admin-health-center-list" placeholder="Search by health center, address, or contact..." label="Search health centers" />

        <div class="admin-health-center-list" id="admin-health-center-list">

            @foreach($healthCenters as $healthCenter)

                <div class="admin-health-center-card" data-search-item>

                    <div class="admin-health-center-header">

                        <div class="admin-health-center-icon">
                            &#127973;
                        </div>

                        <div class="admin-health-center-title">

                            <span class="admin-health-center-label">
                                HEALTH CENTER
                            </span>

                            <h4>
                                {{ $healthCenter->name }}
                            </h4>

                            @if($healthCenter->address)
                                <p>
                                    {{ $healthCenter->address }}
                                </p>
                            @endif

                        </div>

                        <div class="admin-health-center-status">

                            <span class="status-badge {{ $healthCenter->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                {{ ucfirst($healthCenter->status) }}
                            </span>

                        </div>

                    </div>


                    <div class="admin-health-center-information">

                        @if($healthCenter->contact_number)
                            <div>
                                <span>CONTACT</span>
                                <strong>
                                    {{ $healthCenter->contact_number }}
                                </strong>
                            </div>
                        @endif

                        @if($healthCenter->email)
                            <div>
                                <span>EMAIL</span>
                                <strong>
                                    {{ $healthCenter->email }}
                                </strong>
                            </div>
                        @endif

                        <div>
                            <span>DOCTORS</span>
                            <strong>
                                {{ $healthCenter->doctors->count() }}
                            </strong>
                        </div>

                        <div>
                            <span>STAFF</span>
                            <strong>
                                {{ $healthCenter->staff->count() }}
                            </strong>
                        </div>

                    </div>


      <div class="admin-health-center-footer">

    <div class="admin-health-center-footer-actions">

        <a
            href="{{ route('admin.health-centers.show', $healthCenter) }}"
            class="primary-button"
        >
            View Details
        </a>

        <a
            href="{{ route('admin.health-centers.edit', $healthCenter) }}"
            class="secondary-button"
        >
            Edit
        </a>

    </div>

    <form
        method="POST"
        action="{{ route('admin.health-centers.toggle-status', $healthCenter) }}"
        class="admin-health-center-status-form"
        data-confirm="Changing this health center's status may affect services and appointment booking."
        data-confirm-title="{{ $healthCenter->status === 'active' ? 'Deactivate health center?' : 'Activate health center?' }}"
        data-confirm-ok="{{ $healthCenter->status === 'active' ? 'Deactivate' : 'Activate' }}"
        data-confirm-cancel="Cancel"
        data-confirm-icon="warning"
    >

        @csrf
        @method('PATCH')

        <button
            type="submit"
            class="status-button {{ $healthCenter->status === 'active' ? 'deactivate' : 'activate' }}"
        >
            {{ $healthCenter->status === 'active' ? 'Deactivate' : 'Activate' }}
        </button>
    </form>

    <form
        method="POST"
        action="{{ route('admin.health-centers.destroy', $healthCenter) }}"
        data-confirm-title="Delete this health center?"
        data-confirm="Deletion is only allowed when no records, services, or announcements are linked. Otherwise, deactivate the center instead."
        data-confirm-ok="Delete center"
        data-confirm-cancel="Cancel"
        data-confirm-icon="warning"
    >
        @csrf
        @method('DELETE')

        <button type="submit" class="health-center-delete-button">
            Delete
        </button>
    </form>
</div>

            @endforeach

        </div>

    @else

        <div class="empty-state">

            <div class="empty-icon">
                &#127973;
            </div>

            <h4>No health centers found</h4>

            <p>
                There are currently no registered health centers.
            </p>

        </div>

    @endif

</div>

@endsection