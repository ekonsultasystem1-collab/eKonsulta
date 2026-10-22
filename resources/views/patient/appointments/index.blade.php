@extends('layouts.dashboard')

@section('title', 'My Appointments')

@section('page-title', 'My Appointments')

@section('content')

    {{-- Flash Messages --}}

    @if(session('success'))
        <div class="alert alert-success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif


    {{-- Page Header --}}

    <section class="dashboard-card appointment-list-page">

        <div class="card-header">

            <div>
                <span class="page-eyebrow">CARE SCHEDULE</span>
                <h3>My Appointments</h3>

                <p>
                    View and manage your scheduled consultations.
                </p>
            </div>

            <a
                href="{{ route('patient.appointments.create') }}"
                class="primary-button"
            >
                📅 Book Appointment
            </a>

        </div>


        {{-- Appointments --}}

        @if($appointments->count())

            <x-list-search target="patient-appointment-list" placeholder="Search doctor, health center, date, or status..." label="Search your appointments" />

            <div class="appointments-list" id="patient-appointment-list">

                @foreach($appointments as $appointment)

                    <div class="appointment-preview" data-search-item data-sort-timestamp="{{ $appointment->appointment_date->format('U.u') }}" data-sort-order="{{ $appointment->id }}">

                        {{-- Date --}}

                        <div class="appointment-date">

                            <strong>
                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M') }}
                            </strong>

                            <span>
                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d') }}
                            </span>

                        </div>


                        {{-- Appointment Details --}}

                        <div class="appointment-details appointment-record-content">
                            <div class="appointment-record-heading">
                                <div>
                                    <span class="appointment-record-eyebrow">Appointment</span>
                                    <h4>{{ $appointment->appointment_date->format('l, F j, Y') }}</h4>
                                    <p class="appointment-record-time">{{ $appointment->appointment_time->format('g:i A') }}</p>
                                </div>
                                <span class="appointment-status status-{{ strtolower($appointment->status) }}">
                                    {{ $appointment->status === 'approved' ? 'Confirmed' : ($appointment->status === 'pending' ? 'Awaiting confirmation' : ucfirst($appointment->status)) }}
                                </span>
                            </div>

                            <div class="appointment-record-facts">
                                <div>
                                    <span>Doctor</span>
                                    <strong>{{ $appointment->doctor?->user?->name ?? 'To be assigned' }}</strong>
                                    @php
                                        $doctorServiceNames = $appointment->doctor?->services
                                            ? $appointment->doctor->services->pluck('name')->filter()->take(2)->values()->all()
                                            : [];
                                        $doctorServiceLabel = $doctorServiceNames
                                            ? implode(' / ', $doctorServiceNames)
                                            : ($appointment->doctor?->specialization ?? 'Medical Doctor');

                                        if ($appointment->doctor?->services && $appointment->doctor->services->count() > 2) {
                                            $doctorServiceLabel .= ' +';
                                        }

                                        if (mb_strlen($doctorServiceLabel) > 28) {
                                            $doctorServiceLabel = mb_substr($doctorServiceLabel, 0, 25) . '…';
                                        }
                                    @endphp
                                    <small>{{ $doctorServiceLabel }}</small>
                                </div>
                                <div>
                                    <span>Health center</span>
                                    <strong>{{ $appointment->healthCenter?->name ?? 'Not available' }}</strong>
                                </div>
                                @if($appointment->reason)
                                    <div>
                                        <span>Reason for visit</span>
                                        <strong>{{ \Illuminate\Support\Str::limit($appointment->reason, 110) }}</strong>
                                    </div>
                                @endif
                            </div>

                            @if($appointment->status === 'pending')
                                <div class="appointment-record-actions">
                                    <form
                                        method="POST"
                                        action="{{ route('patient.appointments.cancel', $appointment) }}"
                                        data-confirm="Your pending appointment request will be cancelled."
                                        data-confirm-title="Cancel this appointment?"
                                        data-confirm-ok="Cancel appointment"
                                        data-confirm-cancel="Keep appointment"
                                        data-confirm-icon="warning"
                                    >
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="secondary-button appointment-cancel-button">
                                            Cancel appointment
                                        </button>
                                    </form>
                                </div>
                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            {{-- No Appointments --}}

            <div class="empty-state">

                <div class="empty-icon">
                    📅
                </div>

                <h4>No appointments found</h4>

                <p>
                    You currently don't have any appointments.
                </p>

                <a
                    href="{{ route('patient.appointments.create') }}"
                    class="primary-button"
                >
                    Book an Appointment
                </a>

            </div>

        @endif

    </section>

@endsection