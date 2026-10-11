<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="--ek-hero-image: url('{{ asset('images/hero-bg.jpg') }}'); --ek-wave-banner: url('{{ asset('images/wave-banner.jpg') }}');">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | E Konsulta</title>
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

        <h2>Set a new <em>password</em>.</h2>
        <p>Choose a strong password to keep your E Konsulta account protected.</p>
    </section>

    <section class="auth-panel">
        <div class="auth-card">
            <img
                class="auth-card-logo"
                src="{{ asset('images/cho-seal.jpg') }}"
                alt="City Health Office of Taguig seal"
            >

            <h1>Reset your password</h1>
            <p>Create a new password that you haven’t used for this account before.</p>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" data-password-reset-form>
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="auth-field">
                    <label for="password">New password</label>
                    <div class="app-password-field">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            autocomplete="new-password"
                            data-password-rule-input
                            aria-describedby="password-requirements"
                            required
                        >
                        <button
                            type="button"
                            class="app-password-toggle"
                            data-password-toggle
                            data-target="password"
                            data-show-label="Show new password"
                            data-hide-label="Hide new password"
                            aria-label="Show new password"
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

                <ul class="password-requirements" id="password-requirements" aria-label="Password requirements">
                    <li data-password-rule="length"><span aria-hidden="true"></span>At least 8 characters</li>
                    <li data-password-rule="uppercase"><span aria-hidden="true"></span>At least one uppercase letter</li>
                    <li data-password-rule="lowercase"><span aria-hidden="true"></span>At least one lowercase letter</li>
                    <li data-password-rule="number"><span aria-hidden="true"></span>At least one number</li>
                </ul>

                <div class="auth-field">
                    <label for="password_confirmation">Confirm new password</label>
                    <div class="app-password-field">
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            autocomplete="new-password"
                            data-password-confirmation
                            aria-describedby="password-match-status"
                            required
                        >
                        <button
                            type="button"
                            class="app-password-toggle"
                            data-password-toggle
                            data-target="password_confirmation"
                            data-show-label="Show password confirmation"
                            data-hide-label="Hide password confirmation"
                            aria-label="Show password confirmation"
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
                    <small class="password-match-status" id="password-match-status" data-password-match-status>
                        Re-enter your new password to confirm it.
                    </small>
                </div>

                <button type="submit" class="primary-button">
                    Save new password
                </button>
            </form>

            <p class="auth-switch">
                <a href="{{ route('login') }}">Back to sign in</a>
            </p>

            <p class="auth-foot">
                &copy; {{ date('Y') }} City Health Office of Taguig. All rights reserved.
            </p>
        </div>
    </section>
</main>

</body>
</html>
