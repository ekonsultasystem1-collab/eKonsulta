@php
    // Visual-only helper: inline SVG icons (no backend involved).
    $svg = fn (string $paths) => '<svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';

    $icons = [
        'home'     => $svg('<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M10 21v-6h4v6"/>'),
        'calendar' => $svg('<rect x="3" y="4.5" width="18" height="16" rx="2"/><path d="M8 2.5v4M16 2.5v4M3 10h18"/>'),
        'book'     => $svg('<rect x="3" y="4.5" width="18" height="16" rx="2"/><path d="M8 2.5v4M16 2.5v4M3 10h18M12 13v5M9.5 15.5h5"/>'),
        'pill'     => $svg('<path d="M10.5 20.5a4.95 4.95 0 0 1-7-7l10-10a4.95 4.95 0 0 1 7 7z"/><path d="m8.5 8.5 7 7"/>'),
        'records'  => $svg('<rect x="5" y="3.5" width="14" height="17" rx="2"/><path d="M9 3.5h6v3H9zM9 12h6M9 16h4"/>'),
        'user'     => $svg('<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/>'),
        'bell'     => $svg('<path d="M6 8a6 6 0 0 1 12 0c0 7 3 8 3 8H3s3-1 3-8"/><path d="M10.3 20a2 2 0 0 0 3.4 0"/>'),
        'steth'    => $svg('<path d="M6 3v6a4 4 0 0 0 8 0V3"/><path d="M10 13v2a5 5 0 0 0 10 0v-2"/><circle cx="20" cy="11" r="2"/>'),
        'users'    => $svg('<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6"/><path d="M16 4.7a3.5 3.5 0 0 1 0 6.6M18 14.3c2.2.7 3.5 2.6 3.5 5.7"/>'),
        'center'   => $svg('<path d="M4 21V6l8-3 8 3v15"/><path d="M2 21h20"/><path d="M12 8v5M9.5 10.5h5"/><path d="M10 21v-4h4v4"/>'),
        'chart'    => $svg('<path d="M4 20V4M4 20h16"/><path d="M8 16v-5M12 16V8M16 16v-3"/>'),
        'log'      => $svg('<path d="M9 6h11M9 12h11M9 18h11"/><path d="M4.5 6h.01M4.5 12h.01M4.5 18h.01"/>'),
        'logout'   => $svg('<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/>'),
    ];
@endphp

<aside class="sidebar" id="sidebar" aria-label="Main navigation">

    <div class="sidebar-brand">

        <div class="brand-icon">
            <img
                src="{{ asset('images/cho-seal.jpg') }}"
                alt="City Health Office of Taguig seal"
                width="50"
                height="50"
            >
        </div>

        <div>
            <h1>E Konsulta</h1>
            <span>City Health Office &bull; Taguig</span>
        </div>

    </div>

    @php
        $quickActions = [
            'patient' => [
                ['route' => 'patient.appointments.create', 'label' => 'Book visit', 'icon' => 'book'],
                ['route' => 'patient.prescriptions.index', 'label' => 'Prescriptions', 'icon' => 'pill'],
            ],
            'doctor' => [
                ['route' => 'doctor.appointments.index', 'label' => 'Today', 'icon' => 'calendar'],
                ['route' => 'doctor.prescriptions.index', 'label' => 'Prescriptions', 'icon' => 'pill'],
            ],
            'staff' => [
                ['route' => 'staff.announcements.create', 'label' => 'Create notice', 'icon' => 'bell'],
                ['route' => 'staff.appointments.index', 'label' => 'Review requests', 'icon' => 'calendar'],
            ],
            'admin' => [
                ['route' => 'admin.announcements.create', 'label' => 'Create notice', 'icon' => 'bell'],
                ['route' => 'admin.doctors.index', 'label' => 'Manage doctors', 'icon' => 'steth'],
            ],
        ];
    @endphp

    <nav class="sidebar-navigation">

        {{-- ============================= --}}
        {{-- PATIENT NAVIGATION --}}
        {{-- ============================= --}}

        @if(auth()->user()->isPatient())

            <div class="nav-section">
                <span>Main</span>
            </div>

            <a
                href="{{ route('patient.dashboard') }}"
                class="nav-link {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}"
            >
                <span>{!! $icons['home'] !!}</span>
                <span>Dashboard</span>
            </a>


            <a
                href="{{ route('patient.appointments.create') }}"
                class="nav-link {{ request()->routeIs('patient.appointments.create') ? 'active' : '' }}"
            >
                <span>{!! $icons['book'] !!}</span>
                <span>Book Appointment</span>
            </a>

            <a
                href="{{route('patient.appointments.index') }}"
                class="nav-link {{ request()->routeIs('patient.appointments.index') && ! request()->routeIs('patient.appointments.create') ? 'active' : '' }}"
            >
                <span>{!! $icons['calendar'] !!}</span>
                <span>My Appointments</span>
            </a>

            <a
                href="{{ route('patient.prescriptions.index') }}"
                class="nav-link {{ request()->routeIs('patient.prescriptions.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['pill'] !!}</span>
                <span>Prescriptions</span>
            </a>

            <a
                href="{{ route('patient.medical-records.index') }}"
                class="nav-link {{ request()->routeIs('patient.medical-records.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['records'] !!}</span>
                <span>Medical Records</span>
            </a>


        @endif


        {{-- ============================= --}}
        {{-- DOCTOR NAVIGATION --}}
        {{-- ============================= --}}

        @if(auth()->user()->isDoctor())

            <div class="nav-section">
                <span>Main</span>
            </div>

            <a
                href="{{ route('doctor.dashboard') }}"
                class="nav-link {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}"
            >
                <span>{!! $icons['home'] !!}</span>
                <span>Dashboard</span>
            </a>

            <a
                href="{{ route('doctor.appointments.index') }}"
                class="nav-link {{ request()->routeIs('doctor.appointments.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['calendar'] !!}</span>
                <span>Appointments</span>
            </a>

            <a
                href="{{ route('doctor.consultations.index') }}"
                class="nav-link {{ request()->routeIs('doctor.consultations.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['steth'] !!}</span>
                <span>Consultations</span>
            </a>

            <a
                href="{{ route('doctor.patients.index') }}"
                class="nav-link {{ request()->routeIs('doctor.patients.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['users'] !!}</span>
                <span>Patients</span>
            </a>

            <a
                href="{{ route('doctor.prescriptions.index') }}"
                class="nav-link {{ request()->routeIs('doctor.prescriptions.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['pill'] !!}</span>
                <span>Prescriptions</span>
            </a>

        @endif


        {{-- ============================= --}}
        {{-- STAFF NAVIGATION --}}
        {{-- ============================= --}}

        @if(auth()->user()->isStaff())

            <div class="nav-section">
                <span>Main</span>
            </div>

            <a
                href="{{ route('staff.dashboard') }}"
                class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}"
            >
                <span>{!! $icons['home'] !!}</span>
                <span>Dashboard</span>
            </a>

            <a
                href="{{ route('staff.appointments.index') }}"
                class="nav-link {{ request()->routeIs('staff.appointments.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['calendar'] !!}</span>
                <span>Appointments</span>
            </a>

            <a
                href="{{ route('staff.patients.index') }}"
                class="nav-link {{ request()->routeIs('staff.patients.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['users'] !!}</span>
                <span>Patients</span>
            </a>

            <a
                href="{{ route('staff.appointment-schedules.index') }}"
                class="nav-link {{ request()->routeIs('staff.appointment-schedules.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['calendar'] !!}</span>
                <span>Appointment Schedules</span>
            </a>

            <a
                href="{{ route('staff.consultations.index') }}"
                class="nav-link {{ request()->routeIs('staff.consultations.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['steth'] !!}</span>
                <span>Consultations</span>
            </a>

            <a
                href="{{ route('staff.health-centers.index') }}"
                class="nav-link {{ request()->routeIs('staff.health-centers.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['center'] !!}</span>
                <span>Health Centers</span>
            </a>

            <a
                href="{{ route('staff.prescriptions.index') }}"
                class="nav-link {{ request()->routeIs('staff.prescriptions.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['pill'] !!}</span>
                <span>Prescriptions</span>
            </a>

        @endif


        {{-- ============================= --}}
        {{-- ADMIN NAVIGATION --}}
        {{-- ============================= --}}

        @if(auth()->user()->isAdmin())

            <div class="nav-section">
                <span>Administration</span>
            </div>

            <a
                href="{{ route('admin.dashboard') }}"
                class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
            >
                <span>{!! $icons['home'] !!}</span>
                <span>Dashboard</span>
            </a>

            <a
                href="{{ route('admin.users.index') }}"
                class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['users'] !!}</span>
                <span>Users</span>
            </a>

            <a
                href="{{ route('admin.patients.index') }}"
                class="nav-link {{ request()->routeIs('admin.patients.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['users'] !!}</span>
                <span>Patients</span>
            </a>

            <a
                href="{{ route('admin.health-centers.index') }}"
                class="nav-link {{ request()->routeIs('admin.health-centers.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['center'] !!}</span>
                <span>Health Centers</span>
            </a>

            <a
    href="{{ route('admin.services.index') }}"
    class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}"
>
    <span>{!! $icons['steth'] !!}</span>
    <span>Services</span>
</a>

            <a
                href="{{ route('admin.doctors.index') }}"
                class="nav-link {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['steth'] !!}</span>
                <span>Doctors</span>
            </a>

            <a
                href="{{ route('admin.staff.index') }}"
                class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['user'] !!}</span>
                <span>Staff</span>
            </a>

            <a
                href="{{ route('admin.announcements.index') }}"
                class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['bell'] !!}</span>
                <span>Announcements</span>
            </a>

            <a
                href="{{ route('admin.reports.index') }}"
                class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['chart'] !!}</span>
                <span>Reports</span>
            </a>

            <a
                href="{{ route('admin.activity-logs.index') }}"
                class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}"
            >
                <span>{!! $icons['log'] !!}</span>
                <span>Activity Logs</span>
            </a>

        @endif

        @if(auth()->user()->isPatient())
            <div class="nav-section nav-section--spacer">
                <span>Quick access</span>
            </div>
            @foreach($quickActions['patient'] as $action)
                <a href="{{ route($action['route']) }}" class="nav-quick-action {{ request()->routeIs(str_replace('.index', '.*', $action['route'])) ? 'is-active' : '' }}">
                    <span>{!! $icons[$action['icon']] !!}</span>
                    <span>{{ $action['label'] }}</span>
                </a>
            @endforeach
        @elseif(auth()->user()->isDoctor())
            <div class="nav-section nav-section--spacer">
                <span>Quick access</span>
            </div>
            @foreach($quickActions['doctor'] as $action)
                <a href="{{ route($action['route']) }}" class="nav-quick-action {{ request()->routeIs(str_replace('.index', '.*', $action['route'])) ? 'is-active' : '' }}">
                    <span>{!! $icons[$action['icon']] !!}</span>
                    <span>{{ $action['label'] }}</span>
                </a>
            @endforeach
        @elseif(auth()->user()->isStaff())
            <div class="nav-section nav-section--spacer">
                <span>Quick access</span>
            </div>
            @foreach($quickActions['staff'] as $action)
                <a href="{{ route($action['route']) }}" class="nav-quick-action {{ request()->routeIs(str_replace('.create', '.*', $action['route'])) || request()->routeIs(str_replace('.index', '.*', $action['route'])) ? 'is-active' : '' }}">
                    <span>{!! $icons[$action['icon']] !!}</span>
                    <span>{{ $action['label'] }}</span>
                </a>
            @endforeach
        @elseif(auth()->user()->isAdmin())
            <div class="nav-section nav-section--spacer">
                <span>Quick access</span>
            </div>
            @foreach($quickActions['admin'] as $action)
                <a href="{{ route($action['route']) }}" class="nav-quick-action {{ request()->routeIs(str_replace('.create', '.*', $action['route'])) || request()->routeIs(str_replace('.index', '.*', $action['route'])) ? 'is-active' : '' }}">
                    <span>{!! $icons[$action['icon']] !!}</span>
                    <span>{{ $action['label'] }}</span>
                </a>
            @endforeach
        @endif

    </nav>


    {{-- ============================= --}}
    {{-- BOTTOM SIDEBAR --}}
    {{-- ============================= --}}

    <div class="sidebar-bottom">

        <div class="system-status">
            <span class="status-dot"></span>
            <span>System Online</span>
        </div>

    </div>

</aside>