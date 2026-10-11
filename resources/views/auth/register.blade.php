<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="--ek-hero-image: url('{{ asset('images/hero-bg.jpg') }}'); --ek-wave-banner: url('{{ asset('images/wave-banner.jpg') }}');">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Register | E Konsulta</title>

    <link rel="icon" href="{{ asset('images/cross-badge.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite('resources/js/app.js')

    <style>
        /* =========================================================
           e-KONSULTA REGISTRATION
           Clean / Natural / Healthcare UI
           ========================================================= */

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
        }

        body {
            font-family: var(--font, Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif);
            color: #172033;
        }


        /* =========================================================
           PAGE
           ========================================================= */

        .register-page {
            min-height: 100vh;
            min-height: 100dvh;

            width: 100%;

            display: flex;
            align-items: center;

            padding: 30px 45px;

            background:
                linear-gradient(
                    90deg,
                    rgba(4, 38, 65, .48),
                    rgba(4, 38, 65, .12)
                ),
                var(--ek-hero-image, url("/images/hero-bg.jpg"));

            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            overflow-y: auto;
        }


        /* =========================================================
           MAIN LANDSCAPE LAYOUT
           ========================================================= */

        .register-layout {
            width: min(1120px, 100%);

            margin: 0 auto;

            display: grid;

            grid-template-columns: 390px minmax(0, 1fr);

            gap: 45px;

            align-items: center;
        }


        /* =========================================================
           LEFT BRAND
           ========================================================= */

        .register-brand {
            color: #fff;

            padding: 10px 0;
        }

        .register-brand-top {
            display: flex;
            align-items: center;

            gap: 13px;

            margin-bottom: 34px;
        }

        .register-brand-logo {
            width: 60px;
            height: 60px;

            object-fit: contain;

            padding: 3px;

            background: #fff;

            border-radius: 50%;

            box-shadow:
                0 8px 25px rgba(0, 0, 0, .2);
        }

        .register-brand-name {
            display: flex;
            flex-direction: column;

            gap: 3px;
        }

        .register-brand-name strong {
            font-size: 27px;

            line-height: 1;

            font-weight: 750;

            letter-spacing: -.4px;
        }

        .register-brand-name span {
            font-size: 12px;

            color: rgba(255,255,255,.88);
        }

        .register-brand h2 {
            margin: 0;

            font-size: 42px;

            line-height: 1.08;

            font-weight: 750;

            letter-spacing: -1.2px;

            text-shadow:
                0 3px 15px rgba(0,0,0,.2);
        }

        .register-brand h2 em {
            font-style: normal;
        }

        .register-brand-description {
            margin: 18px 0 0;

            max-width: 370px;

            font-size: 14px;

            line-height: 1.65;

            color: rgba(255,255,255,.92);

            text-shadow:
                0 2px 8px rgba(0,0,0,.16);
        }


        /* =========================================================
           REGISTRATION CARD
           ========================================================= */

        .register-card {
            width: 100%;

            background: rgba(255,255,255,.96);

            border: 1px solid rgba(255,255,255,.75);

            border-radius: 18px;

            padding: 25px 28px;

            box-shadow:
                0 24px 55px rgba(0,0,0,.20),
                0 5px 18px rgba(0,0,0,.08);

            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }


        /* =========================================================
           CARD HEADER
           ========================================================= */

        .register-header {
            margin-bottom: 19px;
        }

        .register-header h1 {
            margin: 0;

            font-size: 24px;

            line-height: 1.2;

            font-weight: 750;

            color: #172033;

            letter-spacing: -.4px;
        }

        .register-header p {
            margin: 5px 0 0;

            font-size: 12px;

            color: #718096;
        }


        /* =========================================================
           ALERT
           ========================================================= */

        .register-alert {
            margin-bottom: 15px;

            padding: 10px 12px;

            border-radius: 9px;

            font-size: 12px;
        }

        .register-alert-danger {
            color: #a52626;

            background: #fff2f2;

            border: 1px solid #ffd6d6;
        }

        .register-alert-success {
            color: #267344;

            background: #effaf3;

            border: 1px solid #ccebd7;
        }

        .register-alert ul {
            margin: 5px 0 0 17px;

            padding: 0;
        }


        /* =========================================================
           FORM
           ========================================================= */

        .register-form {
            display: flex;
            flex-direction: column;

            gap: 13px;
        }

        .register-grid {
            display: grid;

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 11px 14px;
        }

        .register-full {
            grid-column: 1 / -1;
        }


        /* =========================================================
           FIELD
           ========================================================= */

        .register-field label {
            display: block;

            margin-bottom: 5px;

            font-size: 11px;

            line-height: 1.2;

            font-weight: 650;

            color: #344054;
        }

        .register-field label span {
            color: #c62828;
        }

        .register-field label small {
            color: #98a2b3;

            font-weight: 400;
        }

        .register-field input,
        .register-field select,
        .register-field textarea {
            width: 100%;

            border: 1px solid #d9dee7;

            border-radius: 8px;

            background: #fff;

            color: #172033;

            font-family: inherit;

            font-size: 12px;

            outline: none;

            transition:
                border-color .15s ease,
                box-shadow .15s ease;
        }

        .register-field input,
        .register-field select {
            height: 38px;

            padding: 0 10px;
        }

        .register-field textarea {
            min-height: 58px;

            padding: 9px 10px;

            resize: vertical;
        }

        .register-field input::placeholder,
        .register-field textarea::placeholder {
            color: #a6afbd;
        }

        .register-field input:focus,
        .register-field select:focus,
        .register-field textarea:focus {
            border-color: #0b4f8a;

            box-shadow:
                0 0 0 3px rgba(11,79,138,.08);
        }


        /* =========================================================
           PASSWORD
           ========================================================= */

        .password-field {
            position: relative;
        }

        .password-field input {
            padding-right: 50px;
        }

        .password-toggle {
            position: absolute;

            top: 50%;
            right: 8px;

            transform: translateY(-50%);

            border: 0;

            background: transparent;

            color: #0b4f8a;

            font-family: inherit;

            font-size: 10px;

            font-weight: 700;

            cursor: pointer;

            padding: 4px;
        }


        /* =========================================================
           BUTTON
           ========================================================= */

        .register-button {
            width: 100%;

            height: 42px;

            margin-top: 3px;

            border: 0;

            border-radius: 8px;

            background: #0b4f8a;

            color: #fff;

            font-family: inherit;

            font-size: 13px;

            font-weight: 700;

            cursor: pointer;

            box-shadow:
                0 6px 14px rgba(11,79,138,.18);

            transition:
                background .15s ease,
                transform .15s ease;
        }

        .register-button:hover {
            background: #083e6d;

            transform: translateY(-1px);
        }

        .register-actions {
            display: flex;
            justify-content: center;
            margin-top: 18px;
        }

        .register-cancel-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 10px 18px;
            border: 1px solid #d9e1eb;
            border-radius: 999px;
            background: #fff;
            color: #1f3350;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: background .15s ease, border-color .15s ease, transform .15s ease;
        }

        .register-cancel-button:hover {
            background: #f5f9ff;
            border-color: #bfd0ea;
            transform: translateY(-1px);
        }


        /* =========================================================
           BOTTOM LINKS
           ========================================================= */

        .register-switch {
            margin: 13px 0 0;

            text-align: center;

            font-size: 11px;

            color: #718096;
        }

        .register-switch a {
            color: #0b4f8a;

            font-weight: 700;

            text-decoration: none;
        }

        .register-switch a:hover {
            text-decoration: underline;
        }

        .register-foot {
            margin: 9px 0 0;

            text-align: center;

            font-size: 9px;

            color: #9aa3b2;
        }


        /* =========================================================
           TABLET
           ========================================================= */

        @media (max-width: 1000px) {

            .register-page {
                padding: 25px;
            }

            .register-layout {
                grid-template-columns: 1fr;

                max-width: 650px;

                gap: 25px;
            }

            .register-brand {
                text-align: center;
            }

            .register-brand-top {
                justify-content: center;

                margin-bottom: 18px;
            }

            .register-brand-description {
                margin-left: auto;
                margin-right: auto;
            }

            .register-brand h2 {
                font-size: 34px;
            }
        }


        /* =========================================================
           MOBILE
           ========================================================= */

        @media (max-width: 600px) {

            .register-page {
                padding: 15px 10px;
            }

            .register-card {
                padding: 20px 17px;

                border-radius: 15px;
            }

            .register-grid {
                grid-template-columns: 1fr;
            }

            .register-brand h2 {
                font-size: 29px;
            }

            .register-brand-name strong {
                font-size: 23px;
            }

            .register-header h1 {
                font-size: 21px;
            }
        }

        html body .register-page {
            align-items: flex-start;
            padding-top: 36px;
            padding-bottom: 36px;
            background:
                linear-gradient(135deg, rgba(13, 54, 82, .72), rgba(26, 97, 119, .5)),
                var(--ek-hero-image, url("/images/hero-bg.jpg"));
        }

        html body .register-layout {
            width: min(1080px, 100%);
            grid-template-columns: minmax(260px, .72fr) minmax(0, 1.28fr);
            gap: clamp(24px, 4vw, 48px);
            align-items: start;
        }

        html body .register-brand {
            position: sticky;
            top: 28px;
            padding-top: 32px;
        }

        html body .register-brand-top {
            margin-bottom: 26px;
        }

        html body .register-brand h2 {
            max-width: 390px;
            font-size: clamp(30px, 3.5vw, 40px);
            line-height: 1.15;
        }

        html body .register-brand-description {
            max-width: 340px;
            font-size: 14px;
            line-height: 1.75;
        }

        html body .register-card {
            padding: clamp(22px, 3vw, 34px);
            border: 1px solid rgba(230, 237, 242, .95);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 18px 48px rgba(14, 37, 54, .2);
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
        }

        html body .register-header {
            margin-bottom: 20px;
        }

        html body .register-header h1 {
            color: #1e3448;
            font-size: 25px;
            letter-spacing: -.55px;
        }

        html body .register-header p {
            margin-top: 7px;
            color: #69798a;
            font-size: 13px;
        }

        html body .register-form {
            gap: 15px;
        }

        html body .register-section-heading {
            margin-top: 6px;
            padding-top: 15px;
            border-color: #e7edf1;
        }

        html body .register-section-heading--first {
            padding-top: 0;
            border: 0;
        }

        html body .register-section-heading h2 {
            color: #2f495e;
            font-size: 13px;
            letter-spacing: 0;
        }

        html body .register-grid {
            gap: 14px;
        }

        html body .register-field label {
            margin-bottom: 6px;
            color: #34495c;
            font-size: 12px;
        }

        html body .register-field input,
        html body .register-field select,
        html body .register-field textarea {
            min-height: 42px;
            padding-right: 12px;
            padding-left: 12px;
            border-color: #d8e0e7;
            border-radius: 9px;
            color: #263a4d;
            font-size: 13px;
        }

        html body .register-field input:focus,
        html body .register-field select:focus,
        html body .register-field textarea:focus {
            border-color: #579a8b;
            box-shadow: 0 0 0 3px rgba(48, 133, 116, .12);
        }

        html body .password-field input {
            padding-right: 56px;
        }

        html body .password-toggle {
            right: 10px;
            color: #39796e;
            font-size: 11px;
        }

        .register-field .password-guidance {
            margin: 8px 0 0;
            color: #718090;
            font-size: 11px;
            line-height: 1.5;
        }

        .password-rules {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 5px 12px;
            margin: 9px 0 0;
            padding: 0;
            list-style: none;
        }

        .password-rules li {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #788694;
            font-size: 11px;
            line-height: 1.4;
            transition: color .15s ease;
        }

        .password-rules li::before {
            display: grid;
            width: 15px;
            height: 15px;
            flex: 0 0 15px;
            place-items: center;
            border: 1px solid #ccd5dc;
            border-radius: 50%;
            color: transparent;
            content: "";
            font-size: 9px;
        }

        .password-rules li.is-met {
            color: #287653;
        }

        .password-rules li.is-met::before {
            border-color: #65a889;
            background: #e8f5ee;
            color: #287653;
            content: "✓";
        }

        html body .register-field input.password-is-invalid,
        html body .register-field input.password-confirmation-mismatch {
            border-color: #cf4d55;
            background: #fffafa;
            box-shadow: 0 0 0 3px rgba(207, 77, 85, .09);
        }

        html body .register-field input.password-is-valid,
        html body .register-field input.password-confirmation-match {
            border-color: #65a889;
        }

        .password-feedback {
            min-height: 17px;
            margin: 6px 0 0;
            color: #718090;
            font-size: 11px;
            line-height: 1.5;
        }

        .password-feedback.is-error {
            color: #b4232d;
        }

        .password-feedback.is-success {
            color: #287653;
        }

        .number-feedback {
            min-height: 16px;
            margin: 5px 0 0;
            color: #718090;
            font-size: 10px;
            line-height: 1.4;
        }

        .number-feedback.is-error {
            color: #b4232d;
        }

        html body .register-field input.number-is-invalid {
            border-color: #cf4d55;
            background: #fffafa;
            box-shadow: 0 0 0 3px rgba(207, 77, 85, .09);
        }

        html body .register-alert {
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 12px;
            line-height: 1.55;
        }

        html body .register-button {
            height: 46px;
            margin-top: 3px;
            border-radius: 9px;
            background: #17645d;
            box-shadow: 0 5px 13px rgba(23, 100, 93, .17);
            font-size: 13px;
            transition: background .15s ease, box-shadow .15s ease;
        }

        html body .register-button:hover {
            background: #124f49;
            box-shadow: 0 7px 16px rgba(23, 100, 93, .2);
            transform: none;
        }

        html body .register-switch {
            font-size: 12px;
        }

        html body .register-switch a {
            color: #17645d;
        }

        @media (max-width: 1000px) {
            html body .register-page {
                padding: 25px 18px;
            }

            html body .register-layout {
                max-width: 690px;
                grid-template-columns: 1fr;
                gap: 17px;
            }

            html body .register-brand {
                position: static;
                padding: 0;
            }

            html body .register-brand-top {
                margin-bottom: 12px;
            }

            html body .register-brand h2 {
                max-width: 600px;
                margin: 0 auto;
                font-size: 29px;
            }

            html body .register-brand-description {
                max-width: 560px;
                margin: 8px auto 0;
                font-size: 12px;
            }
        }

        @media (max-width: 600px) {
            html body .register-page {
                padding: 14px 10px;
            }

            html body .register-layout {
                gap: 13px;
            }

            html body .register-brand h2 {
                font-size: 25px;
            }

            html body .register-brand-description {
                display: none;
            }

            html body .register-card {
                padding: 19px 16px;
            }

            html body .register-grid {
                grid-template-columns: minmax(0, 1fr);
                gap: 12px;
            }

            .password-rules {
                grid-template-columns: minmax(0, 1fr);
            }

            html body .register-field input,
            html body .register-field select,
            html body .register-field textarea {
                min-height: 44px;
                font-size: 16px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .password-rules li,
            html body .register-button {
                transition: none;
            }
        }

        .register-step-indicator {
            position: relative;
            margin: 0 0 20px;
            padding: 0 5px 4px;
        }

        .register-step-progress {
            position: absolute;
            top: 14px;
            right: 16.5%;
            left: 16.5%;
            height: 2px;
            overflow: hidden;
            background: #e6ecef;
        }

        .register-step-progress span {
            display: block;
            width: 0;
            height: 100%;
            background: #468b7e;
            transition: width .2s ease;
        }

        .register-step-indicator ol {
            position: relative;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .register-step-indicator li {
            display: flex;
            min-width: 0;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            color: #84919e;
            font-size: 11px;
            text-align: center;
        }

        .register-step-indicator li > span {
            display: grid;
            width: 29px;
            height: 29px;
            place-items: center;
            border: 1px solid #dce4e8;
            border-radius: 50%;
            background: #fff;
            color: #7b8895;
            font-size: 11px;
            font-weight: 700;
            transition: border-color .2s ease, background .2s ease, color .2s ease;
        }

        .register-step-indicator li strong {
            font-weight: 600;
        }

        .register-step-indicator li[aria-current="step"] {
            color: #315f58;
        }

        .register-step-indicator li[aria-current="step"] > span,
        .register-step-indicator li.is-complete > span {
            border-color: #468b7e;
            background: #468b7e;
            color: #fff;
        }

        .register-step[hidden],
        .register-step-navigation[hidden],
        .register-step-navigation button[hidden] {
            display: none !important;
        }

        .register-step {
            min-height: 330px;
            animation: register-step-enter .2s ease both;
        }

        .register-step .register-section-heading {
            margin-top: 0;
            margin-bottom: 18px;
            padding: 0;
            border: 0;
        }

        .register-step .register-section-heading h2 {
            color: #2b4053;
            font-size: 16px;
            font-weight: 700;
        }

        .register-step .register-section-heading p {
            margin: 5px 0 0;
            color: #788694;
            font-size: 12px;
            line-height: 1.5;
        }

        .register-step .register-grid {
            gap: 17px 18px;
        }

        .register-step[data-register-step="3"] > .register-grid + .register-section-heading {
            margin-top: 26px;
            padding-top: 20px;
            border-top: 1px solid #e9eef1;
        }

        .register-step-navigation {
            display: grid;
            grid-template-columns: minmax(90px, 1fr) auto minmax(150px, 1fr);
            align-items: center;
            gap: 12px;
            margin-top: 25px;
            padding-top: 17px;
            border-top: 1px solid #edf0f2;
        }

        .register-step-navigation > span {
            color: #7d8995;
            font-size: 11px;
            text-align: center;
        }

        .register-step-button {
            min-height: 44px;
            padding: 0 16px;
            border: 1px solid #d8e1e5;
            border-radius: 9px;
            background: #fff;
            color: #43596b;
            cursor: pointer;
            font: inherit;
            font-size: 13px;
            font-weight: 650;
            transition: border-color .15s ease, background .15s ease;
        }

        .register-step-button:hover {
            border-color: #b7cbc6;
            background: #f7faf9;
        }

        .register-step-button--back {
            justify-self: start;
        }

        .register-step-button--next,
        .register-step-navigation .register-button {
            grid-column: 3;
        }

        .register-step-navigation .register-button {
            height: 44px;
            margin: 0;
        }

        @keyframes register-step-enter {
            from { opacity: .65; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 600px) {
            .register-step-indicator {
                margin-bottom: 17px;
            }

            .register-step-indicator li {
                font-size: 10px;
            }

            .register-step {
                min-height: 0;
            }

            .register-step .register-grid {
                gap: 13px;
            }

            .register-step-navigation {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            .register-step-navigation > span {
                grid-column: 1 / -1;
                grid-row: 1;
            }

            .register-step-button--back {
                grid-column: 1;
                grid-row: 2;
                width: 100%;
            }

            .register-step-button--next,
            .register-step-navigation .register-button {
                grid-column: 2;
                grid-row: 2;
                width: 100%;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .register-step,
            .register-step-progress span,
            .register-step-indicator li > span,
            .register-step-button {
                animation: none;
                transition: none;
            }
        }

    </style>
</head>

<body>

<main class="register-page">

    <div class="register-layout">

        {{-- =====================================================
             LEFT: BRANDING
             ===================================================== --}}

        <section class="register-brand">

            <div class="register-brand-top">

                <img
                    class="register-brand-logo"
                    src="{{ asset('images/cho-seal.jpg') }}"
                    alt="City Health Office of Taguig seal"
                >

                <div class="register-brand-name">

                    <strong>E Konsulta</strong>

                    <span>
                        City Health Office &bull; City of Taguig
                    </span>

                </div>

            </div>


            <h2>
                Quality healthcare,
                <em>closer</em>
                to every family.
            </h2>


            <p class="register-brand-description">
                Create your E Konsulta account to book consultations,
                access prescriptions, and manage your health records
                through your local health center.
            </p>

        </section>


        {{-- =====================================================
             RIGHT: FORM
             ===================================================== --}}

        <section class="register-panel">

            <div class="register-card">

                <div class="register-header">

                    <h1>Create your account</h1>

                    <p>
                        Register as a patient to get started.
                    </p>

                </div>


                {{-- =================================================
                     VALIDATION ERRORS
                     ================================================= --}}

                @if ($errors->any())

                    <div class="register-alert register-alert-danger" role="alert">

                        <strong>
                            Please check the following:
                        </strong>

                        <ul>

                            @foreach ($errors->all() as $error)

                                <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>

                @endif


                {{-- =================================================
                     SUCCESS MESSAGE
                     ================================================= --}}

                @if (session('success'))

                    <div class="register-alert register-alert-success" role="status">

                        {{ session('success') }}

                    </div>

                @endif


                @php
                    $stepTwoFields = ['contact_number', 'barangay', 'health_center_id', 'address'];
                    $stepThreeFields = [
                        'emergency_contact_name',
                        'emergency_contact_number',
                        'email',
                        'password',
                        'password_confirmation',
                    ];
                    $initialRegistrationStep = $errors->hasAny($stepThreeFields)
                        ? 3
                        : ($errors->hasAny($stepTwoFields) ? 2 : 1);
                @endphp

                <form
                    method="POST"
                    action="{{ route('register.store') }}"
                    class="register-form"
                    data-registration-form
                    data-initial-step="{{ $initialRegistrationStep }}"
                >

                    @csrf

                    <nav class="register-step-indicator" aria-label="Registration progress">
                        <div class="register-step-progress" aria-hidden="true">
                            <span data-step-progress></span>
                        </div>
                        <ol>
                            <li data-step-indicator="1" aria-current="step">
                                <span>1</span>
                                <strong>Personal</strong>
                            </li>
                            <li data-step-indicator="2">
                                <span>2</span>
                                <strong>Contact</strong>
                            </li>
                            <li data-step-indicator="3">
                                <span>3</span>
                                <strong>Account</strong>
                            </li>
                        </ol>
                    </nav>

                    {{-- =================================================
                         NAME
                         ================================================= --}}

                    <section class="register-step" data-register-step="1" aria-labelledby="registration-step-one-title">
                    <div class="register-section-heading register-section-heading--first">
                        <h2 id="registration-step-one-title">Personal information</h2>
                        <p>Tell us a little about yourself.</p>
                    </div>

                    <div class="register-grid">

                        <div class="register-field">

                            <label for="first_name">
                                First Name <span>*</span>
                            </label>

                            <input
                                id="first_name"
                                type="text"
                                name="first_name"
                                data-name-only
                                value="{{ old('first_name') }}"
                                placeholder="First name"
                                autocomplete="given-name"
                                maxlength="100"
                                required
                            >

                        </div>


                        <div class="register-field">

                            <label for="middle_name">
                                Middle Name
                                <small>(optional)</small>
                            </label>

                            <input
                                id="middle_name"
                                type="text"
                                name="middle_name"
                                data-name-only
                                value="{{ old('middle_name') }}"
                                placeholder="Middle name"
                                autocomplete="additional-name"
                                maxlength="100"
                            >

                        </div>


                        <div class="register-field">

                            <label for="last_name">
                                Last Name <span>*</span>
                            </label>

                            <input
                                id="last_name"
                                type="text"
                                name="last_name"
                                data-name-only
                                value="{{ old('last_name') }}"
                                placeholder="Last name"
                                autocomplete="family-name"
                                maxlength="100"
                                required
                            >

                        </div>


                        <div class="register-field">

                            <label for="suffix">
                                Suffix
                                <small>(optional)</small>
                            </label>

                            <input
                                id="suffix"
                                type="text"
                                name="suffix"
                                value="{{ old('suffix') }}"
                                placeholder="Jr., Sr., III"
                            >

                        </div>


                        <div class="register-field">

                            <label for="date_of_birth">
                                Date of Birth <span>*</span>
                            </label>

                            <input
                                id="date_of_birth"
                                type="date"
                                name="date_of_birth"
                                value="{{ old('date_of_birth') }}"
                                required
                            >

                        </div>


                        <div class="register-field">

                            <label for="sex">
                                Sex <span>*</span>
                            </label>

                            <select
                                id="sex"
                                name="sex"
                                required
                            >

                                <option value="">
                                    Select sex
                                </option>

                                <option
                                    value="Male"
                                    @selected(old('sex') === 'Male')
                                >
                                    Male
                                </option>

                                <option
                                    value="Female"
                                    @selected(old('sex') === 'Female')
                                >
                                    Female
                                </option>

                            </select>

                        </div>

                    </div>
                    </section>


                    {{-- =================================================
                         CONTACT / LOCATION
                         ================================================= --}}

                    <section class="register-step" data-register-step="2" aria-labelledby="registration-step-two-title">
                    <div class="register-section-heading">
                        <h2 id="registration-step-two-title">Contact and health center</h2>
                        <p>We’ll use this to connect you with your local clinic.</p>
                    </div>

                    <div class="register-grid">

                        <div class="register-field">

                            <label for="contact_number">
                                Contact Number <span>*</span>
                            </label>

                            <input
                                id="contact_number"
                                type="tel"
                                name="contact_number"
                                data-digits-only
                                data-feedback-target="contact-number-feedback"
                                value="{{ old('contact_number') }}"
                                placeholder="09XXXXXXXXX"
                                autocomplete="tel"
                                inputmode="numeric"
                                pattern="[0-9]{11}"
                                minlength="11"
                                maxlength="11"
                                title="Enter exactly 11 digits."
                                aria-describedby="contact-number-feedback"
                                required
                            >
                            <p class="number-feedback" id="contact-number-feedback" aria-live="polite"></p>

                        </div>


                        {{-- =================================================
                             BARANGAY
                             ================================================= --}}

                        <div class="register-field">

                            <label for="barangay">
                                Barangay <span>*</span>
                            </label>

                            <select
                                id="barangay"
                                name="barangay"
                                required
                            >

                                <option value="">
                                    Select barangay
                                </option>

                                @foreach ($barangays as $barangay)

                                    <option
                                        value="{{ $barangay }}"
                                        @selected(old('barangay') === $barangay)
                                    >
                                        {{ $barangay }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- =================================================
                             HEALTH CENTER
                             ================================================= --}}

                        <div class="register-field">

                            <label for="health_center_id">
                                Health Center <span>*</span>
                            </label>

                            <select
                                id="health_center_id"
                                name="health_center_id"
                                required
                            >

                                <option value="">
                                    Select health center
                                </option>

                                @foreach ($healthCenters as $healthCenter)

                                    <option
                                        value="{{ $healthCenter->id }}"
                                        data-barangay="{{ $healthCenter->barangay }}"
                                        @selected(old('health_center_id') == $healthCenter->id)
                                    >
                                        {{ $healthCenter->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="register-field">

                            <label for="address">
                                Complete Address <span>*</span>
                            </label>

                            <input
                                id="address"
                                type="text"
                                name="address"
                                value="{{ old('address') }}"
                                placeholder="House/Unit No., Street, Barangay"
                                required
                            >

                        </div>

                    </div>
                    </section>


                    {{-- =================================================
                         EMERGENCY CONTACT
                         ================================================= --}}

                    <section class="register-step" data-register-step="3" aria-labelledby="registration-step-three-title">
                    <div class="register-section-heading">
                        <h2 id="registration-step-three-title">Emergency contact</h2>
                        <p>Add someone we can contact if needed.</p>
                    </div>

                    <div class="register-grid">

                        <div class="register-field">

                            <label for="emergency_contact_name">
                                Emergency Contact <span>*</span>
                            </label>

                            <input
                                id="emergency_contact_name"
                                type="text"
                                name="emergency_contact_name"
                                data-name-only
                                value="{{ old('emergency_contact_name') }}"
                                placeholder="Full name"
                                maxlength="255"
                                required
                            >

                        </div>


                        <div class="register-field">

                            <label for="emergency_contact_number">
                                Emergency Contact Number <span>*</span>
                            </label>

                            <input
                                id="emergency_contact_number"
                                type="tel"
                                name="emergency_contact_number"
                                data-digits-only
                                data-feedback-target="emergency-contact-number-feedback"
                                value="{{ old('emergency_contact_number') }}"
                                placeholder="09XXXXXXXXX"
                                inputmode="numeric"
                                pattern="[0-9]{11}"
                                minlength="11"
                                maxlength="11"
                                title="Enter exactly 11 digits."
                                aria-describedby="emergency-contact-number-feedback"
                                required
                            >
                            <p class="number-feedback" id="emergency-contact-number-feedback" aria-live="polite"></p>

                        </div>

                    </div>


                    {{-- =================================================
                         ACCOUNT
                         ================================================= --}}

                    <div class="register-section-heading">
                        <h2>Account security</h2>
                        <p>Set up the email and password you’ll use to sign in.</p>
                    </div>

                    <div class="register-grid">

                        <div class="register-field register-full">

                            <label for="email">
                                Email Address <span>*</span>
                            </label>

                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="you@example.com"
                                autocomplete="email"
                                required
                            >

                        </div>


                        {{-- =================================================
                             PASSWORD
                             ================================================= --}}

                        <div class="register-field">

                            <label for="password">
                                Password <span>*</span>
                            </label>

                            <div class="password-field">

                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    placeholder="Create a password"
                                    autocomplete="new-password"
                                    aria-describedby="password-guidance password-feedback"
                                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                    required
                                >

                                <button
                                    type="button"
                                    class="password-toggle"
                                    data-target="password"
                                    data-show-label="Show password"
                                    data-hide-label="Hide password"
                                    aria-label="Show password"
                                    aria-pressed="false"
                                >
                                    Show
                                </button>

                            </div>

                            <p class="password-guidance" id="password-guidance">Use at least 8 characters and include:</p>
                            <ul class="password-rules" id="password-rules" aria-live="polite">
                                <li data-password-rule="length">At least 8 characters</li>
                                <li data-password-rule="uppercase">An uppercase letter</li>
                                <li data-password-rule="lowercase">A lowercase letter</li>
                                <li data-password-rule="number">A number</li>
                            </ul>
                            <p class="password-feedback" id="password-feedback" aria-live="polite">
                                @if($errors->has('password'))
                                    {{ $errors->first('password') }}
                                @endif
                            </p>

                        </div>


                        {{-- =================================================
                             CONFIRM PASSWORD
                             ================================================= --}}

                        <div class="register-field">

                            <label for="password_confirmation">
                                Confirm Password <span>*</span>
                            </label>

                            <div class="password-field">

                                <input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    placeholder="Repeat password"
                                    autocomplete="new-password"
                                    aria-describedby="password-confirmation-feedback"
                                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                    required
                                >

                                <button
                                    type="button"
                                    class="password-toggle"
                                    data-target="password_confirmation"
                                    data-show-label="Show password confirmation"
                                    data-hide-label="Hide password confirmation"
                                    aria-label="Show password confirmation"
                                    aria-pressed="false"
                                >
                                    Show
                                </button>

                            </div>
                            <p
                                class="password-feedback"
                                id="password-confirmation-feedback"
                                aria-live="polite"
                            >@if($errors->has('password')){{ $errors->first('password') }}@endif</p>

                        </div>

                    </div>
                    </section>


                    {{-- =================================================
                         SUBMIT
                         ================================================= --}}

                    <div class="register-step-navigation" data-step-navigation hidden>
                        <button type="button" class="register-step-button register-step-button--back" data-step-previous hidden>
                            Back
                        </button>
                        <span data-step-caption>Step 1 of 3</span>
                        <button type="button" class="register-step-button register-step-button--next" data-step-next>
                            Continue
                        </button>
                        <button type="submit" class="register-button" data-registration-submit hidden>
                            Create Patient Account
                        </button>
                    </div>

                </form>


                {{-- =================================================
                     LOGIN LINK
                     ================================================= --}}

                <div class="register-actions">
                    <a href="{{ route('login') }}" class="register-cancel-button">
                        Cancel and return to login
                    </a>
                </div>

                <p class="register-switch">

                    Already have an account?

                    <a href="{{ route('login') }}">
                        Sign in
                    </a>

                </p>


                <p class="register-foot">

                    &copy; {{ date('Y') }}
                    City Health Office of Taguig.
                    All rights reserved.

                </p>

            </div>

        </section>

    </div>

</main>


<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       Password visibility
       ========================================================= */

    document
        .querySelectorAll('.password-toggle')
        .forEach(function (button) {

            button.addEventListener('click', function () {

                const input = document.getElementById(
                    button.dataset.target
                );

                if (!input) {
                    return;
                }

                if (input.type === 'password') {

                    input.type = 'text';

                    button.textContent = 'Hide';

                } else {

                    input.type = 'password';

                    button.textContent = 'Show';

                }

                const isVisible = input.type === 'text';
                button.setAttribute('aria-pressed', String(isVisible));
                button.setAttribute(
                    'aria-label',
                    isVisible
                        ? button.dataset.hideLabel
                        : button.dataset.showLabel
                );

            });

        });

    const passwordInput = document.getElementById('password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');
    const passwordFeedback = document.getElementById('password-feedback');
    const passwordConfirmationFeedback = document.getElementById('password-confirmation-feedback');
    const passwordRules = document.querySelectorAll('[data-password-rule]');
    const registrationForm = document.querySelector('.register-form');

    if (registrationForm) {
        const steps = Array.from(registrationForm.querySelectorAll('[data-register-step]'));
        const navigation = registrationForm.querySelector('[data-step-navigation]');
        const previousButton = registrationForm.querySelector('[data-step-previous]');
        const nextButton = registrationForm.querySelector('[data-step-next]');
        const submitButton = registrationForm.querySelector('[data-registration-submit]');
        const stepCaption = registrationForm.querySelector('[data-step-caption]');
        const stepProgress = registrationForm.querySelector('[data-step-progress]');
        const stepIndicators = Array.from(registrationForm.querySelectorAll('[data-step-indicator]'));
        let activeStep = Number(registrationForm.dataset.initialStep) || 1;

        const showStep = (stepNumber, focusHeading = false) => {
            activeStep = Math.min(Math.max(stepNumber, 1), steps.length);

            steps.forEach((step) => {
                step.hidden = Number(step.dataset.registerStep) !== activeStep;
            });

            stepIndicators.forEach((indicator) => {
                const number = Number(indicator.dataset.stepIndicator);
                if (number === activeStep) {
                    indicator.setAttribute('aria-current', 'step');
                } else {
                    indicator.removeAttribute('aria-current');
                }
                indicator.classList.toggle('is-complete', number < activeStep);
            });

            if (stepCaption) {
                stepCaption.textContent = `Step ${activeStep} of ${steps.length}`;
            }

            if (stepProgress) {
                stepProgress.style.width = `${((activeStep - 1) / (steps.length - 1)) * 100}%`;
            }

            if (previousButton instanceof HTMLButtonElement) {
                previousButton.hidden = activeStep === 1;
            }
            if (nextButton instanceof HTMLButtonElement) {
                nextButton.hidden = activeStep === steps.length;
            }
            if (submitButton instanceof HTMLButtonElement) {
                submitButton.hidden = activeStep !== steps.length;
            }

            if (focusHeading) {
                const heading = steps[activeStep - 1]?.querySelector('h2');
                if (heading instanceof HTMLElement) {
                    heading.setAttribute('tabindex', '-1');
                    heading.focus();
                }
            }
        };

        const validateStep = (step, reportInvalid = true) => {
            const invalidField = Array.from(step.querySelectorAll('input, select, textarea'))
                .find((field) => field instanceof HTMLInputElement
                    || field instanceof HTMLSelectElement
                    || field instanceof HTMLTextAreaElement
                    ? !field.checkValidity()
                    : false);

            if (reportInvalid && invalidField instanceof HTMLElement && 'reportValidity' in invalidField) {
                invalidField.reportValidity();
            }

            return !invalidField;
        };

        navigation.hidden = false;
        showStep(activeStep);

        nextButton?.addEventListener('click', () => {
            const currentStep = steps[activeStep - 1];
            if (currentStep && validateStep(currentStep)) {
                showStep(activeStep + 1, true);
            }
        });

        previousButton?.addEventListener('click', () => {
            showStep(activeStep - 1, true);
        });

        registrationForm.addEventListener('submit', (event) => {
            for (const step of steps) {
                if (!validateStep(step, false)) {
                    event.preventDefault();
                    showStep(Number(step.dataset.registerStep), true);
                    step.querySelector('input:invalid, select:invalid, textarea:invalid')?.reportValidity();
                    return;
                }
            }
        });

        registrationForm.addEventListener('keydown', (event) => {
            if (
                event.key === 'Enter'
                && event.target instanceof HTMLElement
                && !['TEXTAREA', 'BUTTON'].includes(event.target.tagName)
                && activeStep < steps.length
            ) {
                event.preventDefault();
                nextButton?.click();
            }
        });
    }

    document.querySelectorAll('input[data-name-only]').forEach(function (input) {
        input.addEventListener('input', function (event) {
            if (event.isComposing) {
                return;
            }

            const originalValue = input.value;
            const cursorPosition = input.selectionStart ?? originalValue.length;
            const sanitizedValue = originalValue.replace(/[^\p{L}\p{M}\s.'’-]/gu, '');

            if (sanitizedValue !== originalValue) {
                const sanitizedPrefix = originalValue
                    .slice(0, cursorPosition)
                    .replace(/[^\p{L}\p{M}\s.'’-]/gu, '');

                input.value = sanitizedValue;
                input.setSelectionRange(sanitizedPrefix.length, sanitizedPrefix.length);
            }
        });
    });

    document.querySelectorAll('input[data-digits-only]').forEach(function (input) {
        const feedback = document.getElementById(input.dataset.feedbackTarget);

        function updatePhoneNumber() {
            const digits = input.value.replace(/\D/g, '').slice(0, 11);
            input.value = digits;

            const isInvalid = digits.length > 0 && digits.length !== 11;
            input.classList.toggle('number-is-invalid', isInvalid);
            input.setAttribute('aria-invalid', String(isInvalid));
            input.setCustomValidity(
                isInvalid ? 'Enter exactly 11 digits using numbers only.' : ''
            );

            if (feedback) {
                feedback.textContent = isInvalid
                    ? `${11 - digits.length} more digit${11 - digits.length === 1 ? '' : 's'} required.`
                    : digits.length === 11
                        ? '11 digits entered.'
                        : '';
                feedback.classList.toggle('is-error', isInvalid);
            }
        }

        input.addEventListener('input', updatePhoneNumber);
        input.addEventListener('change', updatePhoneNumber);
        updatePhoneNumber();
    });

    function updatePasswordState() {
        if (!passwordInput || !passwordConfirmationInput) {
            return;
        }

        const password = passwordInput.value;
        const checks = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /\d/.test(password),
        };
        const passwordIsValid = Object.values(checks).every(Boolean);

        passwordRules.forEach(function (rule) {
            const ruleName = rule.dataset.passwordRule;
            const isMet = Boolean(ruleName && checks[ruleName]);

            rule.classList.toggle('is-met', isMet);
        });

        passwordInput.classList.toggle('password-is-invalid', password.length > 0 && !passwordIsValid);
        passwordInput.classList.toggle('password-is-valid', password.length > 0 && passwordIsValid);
        passwordInput.setAttribute('aria-invalid', String(password.length > 0 && !passwordIsValid));
        passwordInput.setCustomValidity(
            password.length > 0 && !passwordIsValid
                ? 'Use at least 8 characters, including an uppercase letter, a lowercase letter, and a number.'
                : ''
        );

        if (passwordFeedback && password.length > 0) {
            passwordFeedback.textContent = passwordIsValid
                ? 'Password meets all requirements.'
                : 'Your password does not meet all requirements yet.';
            passwordFeedback.classList.toggle('is-error', !passwordIsValid);
            passwordFeedback.classList.toggle('is-success', passwordIsValid);
        }

        const confirmation = passwordConfirmationInput.value;
        const passwordsMatch = confirmation.length > 0 && confirmation === password;
        const passwordsMismatch = confirmation.length > 0 && confirmation !== password;

        passwordConfirmationInput.classList.toggle('password-confirmation-mismatch', passwordsMismatch);
        passwordConfirmationInput.classList.toggle('password-confirmation-match', passwordsMatch);
        passwordConfirmationInput.setAttribute('aria-invalid', String(passwordsMismatch));
        passwordConfirmationInput.setCustomValidity(
            passwordsMismatch ? 'Passwords do not match.' : ''
        );

        if (passwordConfirmationFeedback && confirmation.length > 0) {
            passwordConfirmationFeedback.textContent = passwordsMatch
                ? 'Passwords match.'
                : 'Passwords do not match.';
            passwordConfirmationFeedback.classList.toggle('is-error', passwordsMismatch);
            passwordConfirmationFeedback.classList.toggle('is-success', passwordsMatch);
        } else if (passwordConfirmationFeedback) {
            passwordConfirmationFeedback.textContent = '';
            passwordConfirmationFeedback.classList.remove('is-error', 'is-success');
        }
    }

    passwordInput?.addEventListener('input', updatePasswordState);
    passwordConfirmationInput?.addEventListener('input', updatePasswordState);
    passwordInput?.addEventListener('change', updatePasswordState);
    passwordConfirmationInput?.addEventListener('change', updatePasswordState);
    registrationForm?.addEventListener('submit', updatePasswordState);
    updatePasswordState();


    /* =========================================================
       Barangay → Health Center filtering
       ========================================================= */

    const barangaySelect =
        document.getElementById('barangay');

    const healthCenterSelect =
        document.getElementById('health_center_id');


    if (barangaySelect && healthCenterSelect) {

        function filterHealthCenters() {

            const selectedBarangay =
                barangaySelect.value;


            Array.from(
                healthCenterSelect.options
            ).forEach(function (option) {

                /*
                 * Always keep the placeholder visible.
                 */
                if (!option.value) {

                    option.hidden = false;

                    return;
                }


                const optionBarangay =
                    option.dataset.barangay;


                /*
                 * Show only health centers belonging
                 * to the selected barangay.
                 */
                option.hidden =
                    selectedBarangay !== '' &&
                    optionBarangay !== selectedBarangay;

            });


            /*
             * If the currently selected health center
             * does not belong to the selected barangay,
             * clear it.
             */
            const selectedOption =
                healthCenterSelect.options[
                    healthCenterSelect.selectedIndex
                ];


            if (
                selectedOption &&
                selectedOption.value &&
                selectedOption.dataset.barangay !== selectedBarangay
            ) {

                healthCenterSelect.value = '';

            }

        }


        /*
         * Run whenever the user changes Barangay.
         */
        barangaySelect.addEventListener(
            'change',
            filterHealthCenters
        );


        /*
         * Run once when the page loads.
         *
         * This also preserves old() values after
         * a validation error.
         */
        filterHealthCenters();

    }

});
</script>


</body>
</html>