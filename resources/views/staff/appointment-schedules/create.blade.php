@extends('layouts.dashboard')

@section('title', 'Create Appointment Schedule')

@section('page-title', 'Create Appointment Schedule')

@section('content')

<div class="dashboard-card clinical-workspace schedule-create-page">

<div class="card-header">

    <div>
        <span class="page-eyebrow">SCHEDULING</span>
        <h3>Create Appointment Schedules</h3>

        <p>
            Generate multiple appointment time slots for your assigned health center.
        </p>
    </div>

    <a href="{{ route('staff.appointment-schedules.index') }}" class="secondary-button">
        Back to Schedules
    </a>

</div>


{{-- Errors --}}

@if ($errors->any())

    <div class="alert alert-danger" role="alert">

        <strong>Please correct the following:</strong>

        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>

    </div>

@endif


{{-- Success --}}

@if (session('success'))

    <div class="alert alert-success" role="status">
        {{ session('success') }}
    </div>

@endif


<form
    method="POST"
    action="{{ route('staff.appointment-schedules.store') }}"
>

    @csrf


    {{-- ============================================================
         SECTION 1: HEALTH CENTER & SERVICE
    ============================================================= --}}

    <div class="form-section">

        <div class="form-section-head">

            <span class="step-number">1</span>

            <div>
                <h4>Health Center &amp; Service</h4>

                <p>
                    Schedules are created for your assigned health center
                </p>
            </div>

        </div>


        {{-- Health Center --}}

        <div class="form-group">

            <label for="health_center">
                Health Center
            </label>

            <input
                type="text"
                id="health_center"
                value="{{ $staff->healthCenter->barangay ?? '' }} / {{ $staff->healthCenter->name ?? '' }}"
                readonly
            >

            <small>
                This schedule will automatically be created for your assigned health center.
            </small>

        </div>


        {{-- Service --}}

        <div class="form-group">

            <label for="service_id">
                Service
            </label>

            <select
                id="service_id"
                name="service_id"
                required
            >

                <option value="">
                    Select a service
                </option>

                @foreach ($services as $service)

                    <option
                        value="{{ $service->id }}"
                        {{ old('service_id') == $service->id ? 'selected' : '' }}
                    >
                        {{ $service->name }}
                    </option>

                @endforeach

            </select>

            <small>
                Select the service that this schedule will provide.
            </small>

            @error('service_id')

                <div class="field-error">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- Doctor --}}

        <div class="form-group">

            <label for="doctor_id">
                Doctor
            </label>

            <select
                id="doctor_id"
                name="doctor_id"
                required
                disabled
            >

                <option value="">
                    Select a service first
                </option>

            </select>

            <small>
                Only doctors assigned to the selected service at your health center will appear.
            </small>

            @error('doctor_id')

                <div class="field-error">
                    {{ $message }}
                </div>

            @enderror

        </div>

    </div>


    {{-- ============================================================
         SECTION 2: DATE & TIME
    ============================================================= --}}

    <div class="form-section">

        <div class="form-section-head">

            <span class="step-number">2</span>

            <div>
                <h4>Date &amp; Time</h4>

                <p>
                    Set the day and the operating hours
                </p>
            </div>

        </div>


        {{-- Date --}}

        <div class="form-group">

            <label for="schedule_date">
                Schedule Date
            </label>

            <input
                type="date"
                id="schedule_date"
                name="schedule_date"
                value="{{ old('schedule_date') }}"
                min="{{ today()->format('Y-m-d') }}"
                required
            >

            @error('schedule_date')

                <div class="field-error">
                    {{ $message }}
                </div>

            @enderror

        </div>


        <div class="form-row">


            {{-- Start Time --}}

            <div class="form-group">

                <label for="start_time">
                    Start Time
                </label>

                <input
                    type="time"
                    id="start_time"
                    name="start_time"
                    value="{{ old('start_time', '08:00') }}"
                    required
                >

                @error('start_time')

                    <div class="field-error">
                        {{ $message }}
                    </div>

                @enderror

            </div>


            {{-- End Time --}}

            <div class="form-group">

                <label for="end_time">
                    End Time
                </label>

                <input
                    type="time"
                    id="end_time"
                    name="end_time"
                    value="{{ old('end_time', '17:00') }}"
                    required
                >

                @error('end_time')

                    <div class="field-error">
                        {{ $message }}
                    </div>

                @enderror

            </div>

        </div>

    </div>


    {{-- ============================================================
         SECTION 3: SLOTS & CAPACITY
    ============================================================= --}}

    <div class="form-section">

        <div class="form-section-head">

            <span class="step-number">3</span>

            <div>
                <h4>Slots &amp; Capacity</h4>

                <p>
                    How the day is divided and how many patients each slot accepts
                </p>
            </div>

        </div>


        <div class="form-row">


            {{-- Interval --}}

            <div class="form-group">

                <label for="interval">
                    Appointment Interval
                </label>

                <select
                    id="interval"
                    name="interval"
                    required
                >

                    <option
                        value="60"
                        {{ old('interval', '60') == '60' ? 'selected' : '' }}
                    >
                        Every 1 hour
                    </option>

                    <option
                        value="30"
                        {{ old('interval') == '30' ? 'selected' : '' }}
                    >
                        Every 30 minutes
                    </option>

                </select>

                <small>
                    Example: 1-hour interval creates 8:00 AM, 9:00 AM, 10:00 AM, etc.
                </small>

                @error('interval')

                    <div class="field-error">
                        {{ $message }}
                    </div>

                @enderror

            </div>


            {{-- Capacity --}}

            <div class="form-group">

                <label for="capacity">
                    Appointment Capacity
                </label>

                <input
                    type="number"
                    id="capacity"
                    name="capacity"
                    value="{{ old('capacity', 10) }}"
                    min="1"
                    max="100"
                    required
                >

                <small>
                    Maximum number of patients who can book each time slot.
                </small>

                @error('capacity')

                    <div class="field-error">
                        {{ $message }}
                    </div>

                @enderror

            </div>

        </div>


        {{-- Lunch Break --}}

        <div class="form-group">

            <label>

                <input
                    type="checkbox"
                    id="include_lunch_break"
                    name="include_lunch_break"
                    value="1"
                    {{ old('include_lunch_break') ? 'checked' : '' }}
                >

                Skip 12:00 PM – 1:00 PM lunch break

            </label>

        </div>

    </div>


    {{-- ============================================================
         SCHEDULE PREVIEW
    ============================================================= --}}

    <div
        class="form-group preview-panel"
        id="schedule-preview"
        role="status"
        aria-live="polite"
    >

        <strong>
            Schedule Preview
        </strong>

        <p
            id="preview-text"
            style="margin: 6px 0 0;"
        >
            Select your schedule details to see the generated time slots.
        </p>

        <ul id="preview-list"></ul>

    </div>


    {{-- ============================================================
         BUTTONS
    ============================================================= --}}

    <div class="form-actions">

        <a
            href="{{ route('staff.appointment-schedules.index') }}"
            class="secondary-button"
        >
            Cancel
        </a>

        <button
            type="submit"
            class="primary-button"
        >
            Create Schedules
        </button>

    </div>

</form>

</div>

{{-- ================================================================
JAVASCRIPT
================================================================= --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Service → Doctor
    |--------------------------------------------------------------------------
    */

    const serviceSelect =
        document.getElementById('service_id');

    const doctorSelect =
        document.getElementById('doctor_id');

    const servicesData =
        @json($servicesData);

    const oldDoctorId =
        @json(old('doctor_id'));


    function updateDoctors() {

        const serviceId =
            serviceSelect.value;

        doctorSelect.innerHTML = '';


        /*
        |--------------------------------------------------------------------------
        | No service selected
        |--------------------------------------------------------------------------
        */

        if (
            !serviceId ||
            !servicesData[serviceId]
        ) {

            doctorSelect.disabled = true;

            const option =
                document.createElement('option');

            option.value = '';

            option.textContent =
                'Select a service first';

            doctorSelect.appendChild(option);

            return;
        }


        const doctors =
            servicesData[serviceId];


        /*
        |--------------------------------------------------------------------------
        | No doctors available
        |--------------------------------------------------------------------------
        */

        if (!doctors.length) {

            doctorSelect.disabled = true;

            const option =
                document.createElement('option');

            option.value = '';

            option.textContent =
                'No doctors available for this service';

            doctorSelect.appendChild(option);

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Doctors available
        |--------------------------------------------------------------------------
        */

        doctorSelect.disabled = false;


        const defaultOption =
            document.createElement('option');

        defaultOption.value = '';

        defaultOption.textContent =
            'Select a doctor';

        doctorSelect.appendChild(defaultOption);


        doctors.forEach(function (doctor) {

            const option =
                document.createElement('option');

            option.value =
                doctor.id;

            option.textContent =
                doctor.name;


            if (
                String(oldDoctorId) ===
                String(doctor.id)
            ) {
                option.selected = true;
            }


            doctorSelect.appendChild(option);

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Service Change
    |--------------------------------------------------------------------------
    */

    serviceSelect.addEventListener(
        'change',
        function () {
            updateDoctors();
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Load Doctors Initially
    |--------------------------------------------------------------------------
    */

    updateDoctors();


    /*
    |--------------------------------------------------------------------------
    | Schedule Preview
    |--------------------------------------------------------------------------
    */

    const startTime =
        document.getElementById('start_time');

    const endTime =
        document.getElementById('end_time');

    const interval =
        document.getElementById('interval');

    const includeLunch =
        document.getElementById('include_lunch_break');

    const preview =
        document.getElementById('schedule-preview');

    const previewText =
        document.getElementById('preview-text');

    const previewList =
        document.getElementById('preview-list');


    function updatePreview() {

        if (
            !startTime ||
            !endTime ||
            !interval ||
            !preview
        ) {
            return;
        }


        const start =
            startTime.value;

        const end =
            endTime.value;

        const intervalValue =
            parseInt(interval.value);


        if (
            !start ||
            !end ||
            !intervalValue
        ) {

            if (previewText) {
                previewText.textContent =
                    'No schedule preview available.';
            }

            if (previewList) {
                previewList.innerHTML = '';
            }

            return;
        }


        const startParts =
            start.split(':');

        const endParts =
            end.split(':');


        let current =
            new Date();

        current.setHours(
            parseInt(startParts[0]),
            parseInt(startParts[1]),
            0,
            0
        );


        const endDate =
            new Date();

        endDate.setHours(
            parseInt(endParts[0]),
            parseInt(endParts[1]),
            0,
            0
        );


        const slots = [];


        while (current < endDate) {

            const hours =
                current.getHours();

            const minutes =
                current.getMinutes();


            /*
            |--------------------------------------------------------------------------
            | Skip lunch
            |--------------------------------------------------------------------------
            */

            if (
                !includeLunch.checked ||
                hours !== 12
            ) {

                const formattedHours =
                    String(hours).padStart(2, '0');

                const formattedMinutes =
                    String(minutes).padStart(2, '0');


                slots.push(
                    `${formattedHours}:${formattedMinutes}`
                );

            }


            current.setMinutes(
                current.getMinutes() +
                intervalValue
            );

        }


        /*
        |--------------------------------------------------------------------------
        | No slots
        |--------------------------------------------------------------------------
        */

        if (!slots.length) {

            if (previewText) {
                previewText.textContent =
                    'No slots generated.';
            }

            if (previewList) {
                previewList.innerHTML = '';
            }

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Preview text
        |--------------------------------------------------------------------------
        */

        if (previewText) {

            previewText.textContent =
                `${slots.length} slot(s) will be created.`;

        }


        /*
        |--------------------------------------------------------------------------
        | Preview list
        |--------------------------------------------------------------------------
        */

        if (previewList) {

            previewList.innerHTML = '';

            slots.forEach(function (slot) {

                const item =
                    document.createElement('li');

                item.textContent =
                    slot;

                previewList.appendChild(item);

            });

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Preview Events
    |--------------------------------------------------------------------------
    */

    if (startTime) {

        startTime.addEventListener(
            'change',
            updatePreview
        );

    }


    if (endTime) {

        endTime.addEventListener(
            'change',
            updatePreview
        );

    }


    if (interval) {

        interval.addEventListener(
            'change',
            updatePreview
        );

    }


    if (includeLunch) {

        includeLunch.addEventListener(
            'change',
            updatePreview
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Initial Preview
    |--------------------------------------------------------------------------
    */

    updatePreview();

});
</script>

@endsection
