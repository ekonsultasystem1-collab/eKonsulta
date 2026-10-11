@extends('layouts.dashboard')

@section('title', 'Services')

@section('page-title', 'Services')

@section('content')

<div class="dashboard-card admin-services-page">

    <div class="card-header">

        <div>
            <h3>Services</h3>
            <p>Manage healthcare services offered through E Konsulta</p>
        </div>

        <div class="admin-service-header-actions">

            <a
                href="{{ route('admin.dashboard') }}"
                class="secondary-button"
            >
                Back to Dashboard
            </a>

            <a
                href="{{ route('admin.services.create') }}"
                class="primary-button"
            >
                Add Service
            </a>

        </div>

    </div>

    @if(session('success'))

        <div class="form-success-box">
            {{ session('success') }}
        </div>

    @endif

    @if($services->count())

        <x-list-search target="admin-service-list" placeholder="Search by service name, code, or description..." label="Search healthcare services" />

        <div class="admin-service-list" id="admin-service-list">

            @foreach($services as $service)

                <div class="admin-service-card" data-search-item>

                    <div class="admin-service-header">

                        <div class="admin-service-icon">
                            &#128138;
                        </div>

                        <div class="admin-service-title">

                            <span class="admin-service-label">
                                HEALTHCARE SERVICE
                            </span>

                            <h4>
                                {{ $service->name }}
                            </h4>

                            <p>
                                Code: {{ $service->code }}
                            </p>

                        </div>

                        <div class="admin-service-status">

                            <span class="status-badge {{ $service->status ? 'status-active' : 'status-inactive' }}">
                                {{ $service->status ? 'Active' : 'Inactive' }}
                            </span>

                        </div>

                    </div>

                    <div class="admin-service-information">

                        <div>
                            <span>CODE</span>
                            <strong>
                                {{ $service->code }}
                            </strong>
                        </div>

                        <div>
                            <span>HEALTH CENTERS</span>
                            <strong>
                                {{ $service->healthCenters->count() }}
                            </strong>
                        </div>

                        <div>
                            <span>DOCTORS</span>
                            <strong>
                                {{ $service->doctors->count() }}
                            </strong>
                        </div>

                        <div>
                            <span>STATUS</span>
                            <strong>
                                {{ $service->status ? 'Available' : 'Unavailable' }}
                            </strong>
                        </div>

                    </div>

                    @if($service->description)

                        <div class="admin-service-description">

                            <span>DESCRIPTION</span>

                            <p>
                                {{ $service->description }}
                            </p>

                        </div>

                    @endif

                    <div class="admin-service-footer">

                        <div class="admin-service-footer-actions">

                            <a
                                href="{{ route('admin.services.edit', $service) }}"
                                class="secondary-button"
                            >
                                Edit
                            </a>

                            <form
                                method="POST"
                                action="{{ route('admin.services.destroy', $service) }}"
                                onsubmit="return confirm('Are you sure you want to delete this service?');"
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="secondary-button"
                                >
                                    Delete
                                </button>

                            </form>

                        </div>

                        <form
                            method="POST"
                            action="{{ route('admin.services.toggle-status', $service) }}"
                            class="admin-service-status-form"
                        >

                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="status-button {{ $service->status ? 'deactivate' : 'activate' }}"
                            >
                                {{ $service->status ? 'Deactivate' : 'Activate' }}
                            </button>

                        </form>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="empty-state">

            <div class="empty-icon">
                &#128138;
            </div>

            <h4>No services found</h4>

            <p>
                There are currently no healthcare services registered.
            </p>

            <a
                href="{{ route('admin.services.create') }}"
                class="primary-button"
            >
                Add First Service
            </a>

        </div>

    @endif

</div>

@endsection