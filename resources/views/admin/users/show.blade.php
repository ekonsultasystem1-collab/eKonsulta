@extends('layouts.dashboard')

@section('title', 'User Details')

@section('page-title', 'User Details')

@section('content')

<section class="welcome-banner">

    <div>
        <span class="welcome-label">
            E Konsulta Administration
        </span>

        <h1>
            User Details
        </h1>

        <p>
            View account and profile information.
        </p>
    </div>

    <div class="welcome-icon">
        👤
    </div>

</section>


<section class="dashboard-card">

    <div class="card-header">

        <div>
            <h3>{{ $user->name }}</h3>
            <p>User account information</p>
        </div>

        <a href="{{ route('admin.users.index') }}">
            Back to Users
        </a>

    </div>


    <div class="appointment-details">

        <p>
            <strong>Name:</strong>
            {{ $user->name }}
        </p>

        <p>
            <strong>Email:</strong>
            {{ $user->email }}
        </p>

        <p>
            <strong>Role:</strong>
            {{ ucfirst($user->role) }}
        </p>

        <p>
            <strong>Registered:</strong>
            {{ $user->created_at->format('F j, Y') }}
        </p>

    </div>

</section>


@if($user->patient)

<section class="dashboard-card">

    <div class="card-header">

        <div>
            <h3>Patient Profile</h3>
            <p>Patient information</p>
        </div>

    </div>

    <div class="appointment-details">

        <p>
            <strong>Patient No:</strong>
            {{ $user->patient->patient_number }}
        </p>

        @if($user->patient->date_of_birth)
            <p>
                <strong>Date of Birth:</strong>
                {{ $user->patient->date_of_birth->format('F j, Y') }}
            </p>
        @endif

        @if($user->patient->sex)
            <p>
                <strong>Sex:</strong>
                {{ $user->patient->sex }}
            </p>
        @endif

        @if($user->patient->contact_number)
            <p>
                <strong>Contact:</strong>
                {{ $user->patient->contact_number }}
            </p>
        @endif

        @if($user->patient->address)
            <p>
                <strong>Address:</strong>
                {{ $user->patient->address }}
            </p>
        @endif

    </div>

</section>

@endif


@if($user->doctor)

<section class="dashboard-card">

    <div class="card-header">

        <div>
            <h3>Doctor Profile</h3>
            <p>Doctor information</p>
        </div>

    </div>

    <div class="appointment-details">

        @if($user->doctor->specialization)
            <p>
                <strong>Specialization:</strong>
                {{ $user->doctor->specialization }}
            </p>
        @endif

    </div>

</section>

@endif


@if($user->staff)

<section class="dashboard-card">

    <div class="card-header">

        <div>
            <h3>Staff Profile</h3>
            <p>Staff information</p>
        </div>

    </div>

    <div class="appointment-details">

        @if($user->staff->position)
            <p>
                <strong>Position:</strong>
                {{ $user->staff->position }}
            </p>
        @endif

    </div>

</section>

@endif


@endsection