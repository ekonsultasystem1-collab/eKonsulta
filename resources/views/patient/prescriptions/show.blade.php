@extends('layouts.dashboard')

@section('title', 'My Prescription')

@section('page-title', 'My Prescription')

@section('content')

<div class="dashboard-card">

    <div class="card-header">
        <div>
            <h3>Electronic Prescription</h3>
            <p>Prescription details and document actions</p>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a
                href="{{ route('patient.prescriptions.index') }}"
                class="btn btn-secondary"
            >
                Back to Prescriptions
            </a>

            <a
                href="{{ route('patient.prescriptions.print', $prescription) }}"
                target="_blank"
                class="btn btn-secondary"
            >
                Print
            </a>

            <a
                href="{{ route('patient.prescriptions.download', $prescription) }}"
                class="btn btn-primary"
            >
                Download PDF
            </a>

        </div>
    </div>

    <div class="prescription-document">

        <div class="prescription-header">

            <h2>CITY HEALTH OFFICE</h2>
            <h3>E Konsulta</h3>
            <p>Electronic Prescription</p>

        </div>

        <div class="prescription-meta">

            <div>
                <span>Prescription No.</span>
                <strong>
                    {{ $prescription->prescription_number }}
                </strong>
            </div>

            <div>
                <span>Date Issued</span>
                <strong>
                    {{ $prescription->prescription_date->format('F d, Y') }}
                </strong>
            </div>

        </div>

        <div class="prescription-section">

            <h4>Patient Information</h4>

            <p>
                <strong>Name:</strong>
                {{ $prescription->patient->user->name ?? 'Patient' }}
            </p>

        </div>

        <div class="prescription-section">

            <h4>Medications</h4>

            @foreach($prescription->items as $item)

                <div class="prescription-medicine">

                    <h3>
                        {{ $item->display_name }}
                    </h3>

                    @if($item->medicine)
                        <p>
                            {{ $item->medicine->generic_name ?? '' }}
                            @if($item->medicine->strength)
                                {{ $item->medicine->strength }}
                            @endif
                        </p>
                    @endif

                    <div class="prescription-details">

                        <div>
                            <strong>Dosage</strong>
                            <span>{{ $item->dosage }}</span>
                        </div>

                        <div>
                            <strong>Frequency</strong>
                            <span>{{ $item->frequency }}</span>
                        </div>

                        <div>
                            <strong>Duration</strong>
                            <span>{{ $item->duration }}</span>
                        </div>

                        <div>
                            <strong>Quantity</strong>
                            <span>{{ $item->quantity }}</span>
                        </div>

                    </div>

                    @if($item->instructions)
                        <p>
                            <strong>Instructions:</strong>
                            {{ $item->instructions }}
                        </p>
                    @endif

                </div>

            @endforeach

        </div>

        @if($prescription->instructions)

            <div class="prescription-section">

                <h4>Additional Instructions</h4>

                <p>
                    {{ $prescription->instructions }}
                </p>

            </div>

        @endif

        @php
            $doctorServices = $prescription->doctor?->services?->pluck('name')->filter()->all() ?? [];
            $doctorDepartment = $doctorServices ? implode(', ', $doctorServices) : ($prescription->doctor?->specialization ?? 'Medical Doctor');
        @endphp

        <div class="prescription-signature">

            <div class="signature-line"></div>

            <strong>
                Dr. {{ $prescription->doctor->user->name ?? 'Doctor' }}
            </strong>

            <span>{{ $doctorDepartment }}</span>

            @if($prescription->doctor->license_number ?? null)
                <span>
                    License No.:
                    {{ $prescription->doctor->license_number }}
                </span>
            @endif

        </div>

    </div>

</div>

@endsection