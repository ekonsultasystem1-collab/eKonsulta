<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="--ek-hero-image: url('{{ asset('images/hero-bg.jpg') }}'); --ek-wave-banner: url('{{ asset('images/wave-banner.jpg') }}');">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Reset Code | E Konsulta</title>
    <link rel="icon" href="{{ asset('images/cross-badge.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/js/app.js')
</head>
<body>

<main class="auth-page">
    <section class="auth-hero" aria-label="About E Konsulta">
        <div class="auth-hero-brand">
            <img src="{{ asset('images/cho-seal.jpg') }}" alt="City Health Office of Taguig seal">
            <div>
                <strong>E Konsulta</strong>
                <span>City Health Office &bull; City of Taguig</span>
            </div>
        </div>

        <h2>Verify your <em>identity</em>.</h2>
        <p>Enter the one-time code sent to the email address you provided.</p>
    </section>

    <section class="auth-panel">
        <div class="auth-card">
            <img
                class="auth-card-logo"
                src="{{ asset('images/cho-seal.jpg') }}"
                alt="City Health Office of Taguig seal"
            >

            <h1>Enter your verification code</h1>
            <p>Enter the six-digit code from your email. It expires in 10 minutes.</p>

            @if (session('status'))
                <div class="alert alert-success" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.verify') }}">
                @csrf

                <div class="auth-field">
                    <label for="otp">Six-digit code</label>
                    <input
                        id="otp"
                        class="auth-otp-input"
                        type="text"
                        name="otp"
                        inputmode="numeric"
                        pattern="[0-9]{6}"
                        maxlength="6"
                        autocomplete="one-time-code"
                        placeholder="000000"
                        value="{{ old('otp') }}"
                        required
                        autofocus
                    >
                </div>

                <button type="submit" class="primary-button">
                    Verify code
                </button>
            </form>

            <p class="auth-switch">
                <a href="{{ route('password.request') }}">Request a new code</a>
            </p>

            <p class="auth-foot">
                &copy; {{ date('Y') }} City Health Office of Taguig. All rights reserved.
            </p>
        </div>
    </section>
</main>

</body>
</html>
