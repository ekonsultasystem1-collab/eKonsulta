<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        E Konsulta Prescription - {{ $prescription->prescription_number }}
    </title>

    <style>
        @page {
            size: A4;
            margin: 18mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #222;
            font-size: 11px;
            line-height: 1.5;
            margin: 0;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
        }

        .header h2 {
            margin: 3px 0;
            font-size: 16px;
        }

        .header p {
            margin: 4px 0 0;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .patient-information {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .patient-information td {
            padding: 5px 0;
            vertical-align: top;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            border-bottom: 1px solid #222;
            padding-bottom: 6px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .medicine {
            border: 1px solid #ccc;
            padding: 12px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .medicine-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .medicine-description {
            color: #555;
            font-size: 10px;
            margin-bottom: 8px;
        }

        .medicine-details {
            width: 100%;
            border-collapse: collapse;
        }

        .medicine-details td {
            width: 50%;
            padding: 4px 8px 4px 0;
            vertical-align: top;
        }

        .medicine-instructions {
            margin-top: 8px;
            padding-top: 7px;
            border-top: 1px solid #ddd;
        }

        .additional-instructions {
            margin-top: 18px;
            page-break-inside: avoid;
        }

        .signature {
            margin-top: 70px;
            width: 300px;
            page-break-inside: avoid;
        }

        .signature-line {
            border-top: 1px solid #222;
            width: 270px;
            margin-bottom: 6px;
        }

        .signature-name {
            font-weight: bold;
            font-size: 12px;
        }

        .signature-role,
        .signature-license {
            font-size: 10px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 8px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>CITY HEALTH OFFICE OF TAGUIG</h1>
        <h2>E Konsulta</h2>
        <p>Electronic Prescription</p>
    </div>

    <table class="patient-information">

        <tr>
            <td>
                <strong>Prescription No.:</strong>
                {{ $prescription->prescription_number }}
            </td>

            <td>
                <strong>Date Issued:</strong>
                {{ $prescription->prescription_date->format('F d, Y') }}
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <strong>Patient:</strong>
                {{ $prescription->patient->user->name ?? 'Patient' }}
            </td>
        </tr>

    </table>

    <div class="section-title">
        Prescribed Medicines
    </div>

    @forelse($prescription->items as $item)

        <div class="medicine">

            <div class="medicine-name">
                {{ $item->display_name }}
            </div>

            @if($item->medicine)

                <div class="medicine-description">

                    @if($item->medicine->generic_name)
                        {{ $item->medicine->generic_name }}
                    @endif

                    @if($item->medicine->strength)

                        @if($item->medicine->generic_name)
                            &mdash;
                        @endif

                        {{ $item->medicine->strength }}

                    @endif

                </div>

            @endif

            <table class="medicine-details">

                <tr>
                    <td>
                        <strong>Dosage:</strong>
                        {{ $item->dosage }}
                    </td>

                    <td>
                        <strong>Frequency:</strong>
                        {{ $item->frequency }}
                    </td>
                </tr>

                <tr>
                    <td>
                        <strong>Duration:</strong>
                        {{ $item->duration }}
                    </td>

                    <td>
                        <strong>Quantity:</strong>
                        {{ $item->quantity }}
                    </td>
                </tr>

            </table>

            @if($item->instructions)

                <div class="medicine-instructions">
                    <strong>Medicine Instructions:</strong><br>
                    {{ $item->instructions }}
                </div>

            @endif

        </div>

    @empty

        <p>No medicines were prescribed.</p>

    @endforelse

    @if($prescription->instructions)

        <div class="additional-instructions">

            <div class="section-title">
                Additional Instructions
            </div>

            <p>
                {{ $prescription->instructions }}
            </p>

        </div>

    @endif

    @php
        $doctorServices = $prescription->doctor?->services?->pluck('name')->filter()->all() ?? [];
        $doctorDepartment = $doctorServices ? implode(', ', $doctorServices) : ($prescription->doctor?->specialization ?? 'Medical Doctor');
    @endphp

    <div class="signature">

        <div class="signature-line"></div>

        <div class="signature-name">
            Dr. {{ $prescription->doctor->user->name ?? 'Doctor' }}
        </div>

        <div class="signature-role">
            {{ $doctorDepartment }}
        </div>

        @if($prescription->doctor->license_number ?? null)

            <div class="signature-license">
                License No.:
                {{ $prescription->doctor->license_number }}
            </div>

        @endif

    </div>

    <div class="footer">
        City Health Office of Taguig • E Konsulta
    </div>

</body>
</html>
