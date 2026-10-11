<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="--ek-hero-image: url('{{ asset('images/hero-bg.jpg') }}'); --ek-wave-banner: url('{{ asset('images/wave-banner.jpg') }}');">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | E Konsulta</title>
    <link rel="icon" href="{{ asset('images/cross-badge.jpg') }}">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
   @vite('resources/js/app.js')
</head>
<body>

<main class="auth-page">

    {{-- Left: hero --}}
    <section class="auth-hero" aria-label="About E Konsulta">

        <div class="auth-hero-brand">
            <img
                src="{{ asset('images/cho-seal.jpg') }}"
                alt="City Health Office of Taguig seal"
            >
            <div>
                <strong>E Konsulta</strong>
                <span>City Health Office &bull; City of Taguig</span>
            </div>
        </div>

        <h2>Quality healthcare, <em>closer</em> to every family.</h2>

        <p>
            Book consultations at your barangay health center, view your
            prescriptions, and keep your medical records in one secure place.
        </p>

    </section>


    {{-- Right: form --}}
    <section class="auth-panel">

        <div class="auth-card">

            <img
                class="auth-card-logo"
                src="{{ asset('images/cho-seal.jpg') }}"
                alt="City Health Office of Taguig seal"
            >

            <h1>Welcome back</h1>
            <p>Sign in to your E Konsulta account.</p>

            @if (session('status'))
                <div class="alert alert-success" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <strong>We couldn't sign you in:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">

                @csrf

                <div class="auth-field">
                    <label for="email">Email address</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>

                <div class="auth-field">
                    <label for="password">Password</label>
                    <div class="app-password-field">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >
                        <button
                            type="button"
                            class="app-password-toggle"
                            data-password-toggle
                            data-target="password"
                            data-show-label="Show password"
                            data-hide-label="Hide password"
                            aria-label="Show password"
                            aria-pressed="false"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-password-icon="show">
                                <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-password-icon="hide">
                                <path d="M3 3l18 18"></path>
                                <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"></path>
                                <path d="M9.9 5.2A11 11 0 0 1 12 5c6.4 0 10 7 10 7a13.7 13.7 0 0 1-3.1 3.8"></path>
                                <path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10 10 0 0 0 4-.8"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="auth-row">
                    <label class="auth-check">
                        <input type="checkbox" name="remember" value="1">
                        <span>Remember me</span>
                    </label>

                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div>

                <button type="submit" class="primary-button">
                    Sign In
                </button>

            </form>

            @if (Route::has('register'))
                <p class="auth-switch">
                    New patient?
                    <a href="{{ route('register') }}">Create an account</a>
                </p>
            @endif

            <p class="auth-foot">
                &copy; {{ date('Y') }} City Health Office of Taguig. All rights reserved.
            </p>

        </div>

    </section>

</main>

</body>
</html>