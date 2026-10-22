@extends('layouts.dashboard')

@section('title', 'Patients')

@section('content')
    <div class="admin-patient-page">
        <header class="admin-patient-page__header">
            <div>
                <p class="admin-patient-page__eyebrow">Admin directory</p>
                <h1>Patients</h1>
                <p>Search patient accounts using a name, email, or patient number.</p>
            </div>
        </header>

        <section class="admin-patient-panel" aria-labelledby="patient-directory-heading">
            <h2 id="patient-directory-heading" class="sr-only">Patient directory</h2>

            <div class="admin-patient-search">
                <x-list-search
                    target="admin-patient-list"
                    placeholder="Search by patient name, email, or patient number..."
                    label="Search patient directory"
                />
            </div>

            @if ($patients->count())
                <div class="admin-patient-table-wrap">
                    <table class="admin-patient-table">
                        <thead>
                            <tr>
                                <th scope="col">Patient</th>
                                <th scope="col">Email</th>
                                <th scope="col">Account status</th>
                            </tr>
                        </thead>
                        <tbody id="admin-patient-list">
                            @foreach ($patients as $patient)
                                <tr
                                    data-search-item
                                    data-sort-timestamp="{{ optional($patient->created_at ?? $patient->user?->created_at)->format('U.u') ?? '' }}"
                                    data-sort-order="{{ $patient->id }}"
                                >
                                    <th scope="row">
                                        <span class="admin-patient-table__name">{{ $patient->user->name }}</span>
                                        <span class="admin-patient-table__number">{{ $patient->patient_number }}</span>
                                    </th>
                                    <td>{{ $patient->user->email }}</td>
                                    <td>
                                        <span class="admin-patient-status admin-patient-status--{{ $patient->user->status }}">
                                            {{ ucfirst($patient->user->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <nav class="admin-patient-pagination" aria-label="Patient list pages">
                    {{ $patients->links() }}
                </nav>
            @else
                <div class="admin-patient-empty" role="status">
                    <h2>No patients found</h2>
                    <p>
                        {{ $search !== ''
                            ? 'Try another name, email, or patient number.'
                            : 'Patient accounts will appear here when they are registered.' }}
                    </p>
                </div>
            @endif
        </section>
    </div>
@endsection
