<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="--ek-hero-image: url('{{ asset('images/hero-bg.jpg') }}'); --ek-wave-banner: url('{{ asset('images/wave-banner.jpg') }}');">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') | E Konsulta</title>
    <link rel="icon" href="{{ asset('images/cross-badge.jpg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@vite('resources/js/app.js')

    @stack('styles')
</head>
<body>

@php
    $authUser = auth()->user();

    $roleLabel = 'User';
    if ($authUser->isAdmin())        { $roleLabel = 'Administrator'; }
    elseif ($authUser->isDoctor())   { $roleLabel = 'Doctor'; }
    elseif ($authUser->isStaff())    { $roleLabel = 'Health Center Staff'; }
    elseif ($authUser->isPatient())  { $roleLabel = 'Patient'; }

    // Profile link: only uses routes that already exist in your app.
    $profileUrl = null;
    if ($authUser->isPatient() && Route::has('patient.profile'))      { $profileUrl = route('patient.profile'); }
    elseif ($authUser->isStaff() && Route::has('staff.profile'))      { $profileUrl = route('staff.profile'); }
    elseif ($authUser->isDoctor() && Route::has('doctor.profile'))    { $profileUrl = route('doctor.profile'); }
    elseif ($authUser->isAdmin() && Route::has('admin.profile'))      { $profileUrl = route('admin.profile'); }
@endphp

<a class="skip-link" href="#main-content">Skip to main content</a>

<div class="app-shell">

    {{-- SIDEBAR --}}
    @include('components.sidebar')

    <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>


    <div class="app-main">

        {{-- TOP BAR --}}
        <header class="topbar">

            <button
                type="button"
                class="menu-toggle"
                id="menuToggle"
                aria-controls="sidebar"
                aria-expanded="false"
                aria-label="Open navigation menu"
            >
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>

            <div class="topbar-title">
                <small>E Konsulta</small>
                <h1>@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="topbar-actions">

                @if((Route::has('patient.notifications') || Route::has('staff.notifications') || Route::has('admin.notifications')) && ($authUser->isPatient() || $authUser->isStaff() || $authUser->isAdmin()))
                    @php
                        $notificationsRoute = match (true) {
                            $authUser->isPatient() => 'patient.notifications',
                            $authUser->isStaff() => 'staff.notifications',
                            $authUser->isAdmin() => 'admin.notifications',
                            default => null,
                        };
                    @endphp

                    @if($notificationsRoute)
                        <a href="{{ route($notificationsRoute) }}" class="icon-button" aria-label="Notifications">
                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 8 3 8H3s3-1 3-8"/><path d="M10.3 20a2 2 0 0 0 3.4 0"/></svg>
                            @if(isset($notificationCount) && $notificationCount > 0)
                                <span class="badge-count">{{ $notificationCount > 9 ? '9+' : $notificationCount }}</span>
                            @endif
                        </a>
                    @endif
                @endif

                {{-- User dropdown --}}
                <details class="user-menu" id="userMenu">

                    <summary
                        class="user-trigger"
                        id="userMenuButton"
                        aria-haspopup="true"
                        aria-controls="userDropdown"
                    >
                        <span class="avatar" aria-hidden="true">{{ strtoupper(mb_substr($authUser->name, 0, 1)) }}</span>

                        <span class="user-meta">
                            <strong>{{ $authUser->name }}</strong>
                            <small>{{ $roleLabel }}</small>
                        </span>

                        <svg class="chevron" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </summary>

                    <div class="dropdown" id="userDropdown" role="menu" aria-labelledby="userMenuButton">

                        <div class="dropdown-head">
                            <strong>{{ $authUser->name }}</strong>
                            <small>{{ $authUser->email }}</small>
                        </div>

                        @if($profileUrl)
                            <a href="{{ $profileUrl }}" role="menuitem">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                                My Profile
                            </a>
                        @endif

                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                            data-confirm="You will need to sign in again to access your account."
                            data-confirm-title="Log out of E Konsulta?"
                            data-confirm-ok="Log out"
                            data-confirm-cancel="Cancel"
                            data-confirm-icon="logout"
                        >
                            @csrf
                            <button type="submit" class="dropdown-danger" role="menuitem">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/></svg>
                                Logout
                            </button>
                        </form>

                    </div>

                </details>

            </div>

        </header>


        {{-- PAGE CONTENT --}}
        <main class="app-content{{ $authUser->isStaff() ? ' staff-app-content' : '' }}{{ $authUser->isDoctor() ? ' doctor-app-content' : '' }}{{ $authUser->isAdmin() ? ' admin-app-content' : '' }}" id="main-content">
            @yield('content')
        </main>

        <footer class="app-footer">
            &copy; {{ date('Y') }} City Health Office of Taguig &bull; E Konsulta
        </footer>

    </div>

</div>


{{-- CONFIRMATION MODAL (used by every form that asks "Are you sure?") --}}
<div class="modal-backdrop" id="confirmModal" hidden>
    <div
        class="modal"
        role="alertdialog"
        aria-modal="true"
        aria-labelledby="confirmTitle"
        aria-describedby="confirmMessage"
    >
        <div class="modal-icon" id="confirmIcon" aria-hidden="true">
            <svg class="confirmation-icon-default" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M9.8 9a2.3 2.3 0 1 1 3.9 1.7c-1.1.9-1.7 1.3-1.7 2.8"></path>
                <path d="M12 17h.01"></path>
            </svg>
            <svg class="confirmation-icon-logout" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h5"></path>
                <path d="M15 16l5-4-5-4"></path>
                <path d="M20 12H9"></path>
            </svg>
        </div>
        <h3 id="confirmTitle">Are you sure?</h3>
        <p id="confirmMessage"></p>

        <div class="modal-actions">
            <button type="button" class="secondary-button" id="confirmCancel">Cancel</button>
            <button type="button" class="primary-button" id="confirmOk">Confirm</button>
        </div>
    </div>
</div>

<dialog class="image-preview-modal" id="imagePreviewModal" aria-label="Image preview">
    <button type="button" class="image-preview-close" data-image-preview-close aria-label="Close image preview">&times;</button>
    <img id="imagePreviewModalImage" src="" alt="">
    <p id="imagePreviewModalCaption"></p>
</dialog>

@stack('scripts')

</body>
</html>