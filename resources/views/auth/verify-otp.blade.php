<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="--ek-hero-image: url('{{ asset('images/hero-bg.jpg') }}'); --ek-wave-banner: url('{{ asset('images/wave-banner.jpg') }}');">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Verify Email - E Konsulta</title>

    <link rel="icon" href="{{ asset('images/cross-badge.jpg') }}">

    @vite('resources/js/app.js')
</head>

<body>

<div class="ek-auth">

    {{-- Brand panel --}}
    <aside class="ek-auth-brand">

        <div class="ek-brand-content">

            <img
                class="ek-seal"
                src="{{ asset('images/cho-seal.jpg') }}"
                alt="City Health Office of Taguig seal"
            >

            <h1 class="ek-wordmark">
                <span>e-<span class="ek-wordmark-red" style="display:inline;">Konsulta</span></span>
            </h1>

            <p class="ek-brand-sub">
                City Health System — online consultation booking and
                digital health records for Taguig residents.
            </p>

            <p class="ek-tagline">
                <span>Mabilis. Maayos.</span>
                <span>Malapit sa iyo.</span>
            </p>

        </div>

    </aside>


    {{-- Form panel --}}
    <main class="ek-auth-panel ek-auth-panel--center">

        <div class="ek-card">

            <h2>Verify Your Email</h2>

            <p class="ek-card-intro">
                We sent a 6-digit verification code to your email address.
                Please enter the code below to complete your registration.
            </p>

            @if (session('success'))
                <div class="ek-alert ek-alert--success" role="status">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="ek-alert" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('register.verify.submit') }}"
                class="ek-form"
            >
                @csrf

                <div class="ek-field">
                    <label for="otp">
                        Verification Code
                    </label>

                    <input
                        id="otp"
                        class="ek-input ek-input--otp"
                        type="text"
                        name="otp"
                        inputmode="numeric"
                        maxlength="6"
                        pattern="[0-9]{6}"
                        autocomplete="one-time-code"
                        placeholder="••••••"
                        value="{{ old('otp') }}"
                        required
                        autofocus
                    >
                </div>

                <button type="submit" class="ek-btn">
                    Verify Email
                </button>
            </form>

            <form
                method="POST"
                action="{{ route('register.resend-otp') }}"
                class="ek-form"
                style="margin-top: 12px;"
                data-resend-form
            >
                @csrf

                <p data-resend-countdown data-countdown-until="{{ $resendAvailableAt }}" role="status" aria-live="polite"></p>
                <button type="submit" class="ek-btn ek-btn--ghost" data-resend-button {{ $resendAvailableAt > now()->timestamp ? 'disabled' : '' }}>
                    Resend Code
                </button>
            </form>

            <p class="ek-foot">
                <a href="{{ route('register') }}" class="ek-link">
                    ← Back to Registration
                </a>
            </p>

        </div>

    </main>

</div>

</body>
</html>