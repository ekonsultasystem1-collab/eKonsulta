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
            font-family: Arial, Helvetica, sans-serif;
            color: #222;
            font-size: 13px;
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
            font-size: 21px;
            letter-spacing: 0.5px;
        }

        .header h2 {
            margin: 3px 0;
            font-size: 17px;
        }

        .header p {
            margin: 4px 0 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .patient-information {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
        }

        .patient-information td {
            padding: 5px 0;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            border-bottom: 1px solid #222;
            padding-bottom: 6px;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .medicine {
            border: 1px solid #ccc;
            padding: 14px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .medicine-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .medicine-description {
            color: #555;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .medicine-details {
            width: 100%;
            border-collapse: collapse;
        }

        .medicine-details td {
            width: 50%;
            padding: 5px 8px 5px 0;
            vertical-align: top;
        }

        .medicine-instructions {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
        }

        .additional-instructions {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .additional-instructions p {
            margin: 8px 0;
        }

        .signature {
            margin-top: 75px;
            width: 300px;
            page-break-inside: avoid;
        }

        .signature-line {
            border-top: 1px solid #222;
            width: 280px;
            margin-bottom: 7px;
        }

        .signature-name {
            font-weight: bold;
            font-size: 14px;
        }

        .signature-role,
        .signature-license {
            font-size: 12px;
        }

        .footer {
            margin-top: 35px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }

        .print-button {
            display: inline-block;
            padding: 9px 18px;
            border: 1px solid #333;
            background: #fff;
            color: #222;
            cursor: pointer;
            text-decoration: none;
            border-radius: 4px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button
            type="button"
            class="print-button"
            onclick="window.print()"
        >
            Print Prescription
        </button>
    </div>

    <div class="header">
        <h1>CITY HEALTH OFFICE OF TAGUIG</h1>
        <h2>E Konsulta</h2>
        <p>Electronic Prescription</p>
    </div>

    <table class="patient-information">
        <tr>
            <td>
                <span class="label">Prescription No.:</span>
                {{ $prescription->prescription_number }}
            </td>

            <td>
                <span class="label">Date Issued:</span>
                {{ $prescription->prescription_date->format('F d, Y') }}
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <span class="label">Patient:</span>
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

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>

</body>
</html>
