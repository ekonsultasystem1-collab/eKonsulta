@extends('layouts.dashboard')

@section('title', 'Health Centers')

@section('page-title', 'Health Centers')

@section('content')

<div class="dashboard-card health-centers-page">

    <div class="card-header">

        <div>
            <h3>Health Centers</h3>
            <p>View registered health centers and their information</p>
        </div>

        <a href="{{ route('staff.dashboard') }}">
            Back to Dashboard
        </a>

    </div>

    @if($healthCenters->count())

        <x-list-search target="staff-health-center-list" placeholder="Search health center, address, or contact..." label="Search health centers" />

        <div class="health-center-list" id="staff-health-center-list">

            @foreach($healthCenters as $healthCenter)

                <div class="health-center-card" data-search-item data-sort-date="{{ $healthCenter->created_at?->toIso8601String() ?? '' }}">

                    <div class="health-center-header">

                        <div class="health-center-icon">
                            &#127973;
                        </div>

                        <div class="health-center-title">

                            <span class="health-center-label">
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

                        <div class="health-center-status">
                            <span class="status-badge">
                                {{ ucfirst($healthCenter->status) }}
                            </span>
                        </div>

                    </div>


                    <div class="health-center-information">

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

                        @if($healthCenter->operating_hours)
                            <div>
                                <span>OPERATING HOURS</span>
                                <strong>
                                    {{ $healthCenter->operating_hours }}
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


                    <div class="health-center-footer">

                        <a
                            href="{{ route('staff.health-centers.show', $healthCenter) }}"
                            class="primary-button"
                        >
                            View Details
                        </a>

                    </div>

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
                Registered health centers will appear here.
            </p>

        </div>

    @endif

</div>

@endsection