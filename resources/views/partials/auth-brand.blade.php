{{-- NEW FILE: shared brand panel used by login + register. Purely visual. --}}
<aside class="ek-auth-brand" aria-label="E Konsulta">

    <div class="ek-brand-content">

        <img
            class="ek-seal"
            src="{{ asset('images/cho-seal.jpg') }}"
            alt="City Health Office of Taguig seal"
        >

        <h1 class="ek-wordmark">
            <span>TAGUIG</span>
            <span class="ek-wordmark-red">E Konsulta</span>
        </h1>

        <p class="ek-brand-sub">
            Patient Appointment and E-Prescription System for Taguig Health Centers
        </p>

        <p class="ek-tagline">
            <span>Better Access.</span>
            <span>Better Care.</span>
            <span>Better Taguig.</span>
        </p>

    </div>

    <svg class="ek-skyline" viewBox="0 0 640 280" preserveAspectRatio="xMidYMax slice" aria-hidden="true">
        <g fill="#C9DAF2">
            <rect x="20" y="120" width="46" height="130"/>
            <rect x="74" y="90" width="38" height="160"/>
            <rect x="120" y="140" width="52" height="110"/>
            <rect x="430" y="110" width="44" height="140"/>
            <rect x="482" y="70" width="40" height="180"/>
            <rect x="530" y="130" width="56" height="120"/>
            <rect x="594" y="100" width="36" height="150"/>
        </g>
        <path d="M190 200 Q290 130 390 200" fill="none" stroke="#9DB9E0" stroke-width="6"/>
        <rect x="180" y="200" width="220" height="8" fill="#9DB9E0"/>
        <path d="M0 214 C160 168 340 262 640 190 V280 H0Z" fill="#0A3D8F"/>
        <path d="M0 240 C180 204 370 280 640 226 V280 H0Z" fill="#D91E25"/>
    </svg>

</aside>