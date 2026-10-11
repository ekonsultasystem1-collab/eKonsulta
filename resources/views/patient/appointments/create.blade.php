@extends('layouts.dashboard')

@section('title', 'Book Appointment')

@section('page-title', 'Book Appointment')

@section('content')

<div class="dashboard-card">

    <div class="card-header">

        <div>
            <h3>Book an Appointment</h3>

            <p>
                Select a service, consultation schedule, and doctor.
            </p>
        </div>

        <a href="{{ route('patient.dashboard') }}">
            Back to Dashboard
        </a>

    </div>


    {{-- Validation Errors --}}
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


    <div class="reminder-box">
        <span aria-hidden="true">🕐</span>
        <span><strong>Reminder:</strong> Please arrive 15 minutes early before your appointment.</span>
    </div>


    {{-- STEP 1 — Assigned Health Center --}}
    <div class="form-section">

        <div class="form-section-head">
            <span class="step-number">1</span>
            <div>
                <h4>Health Center</h4>
                <p>Assigned from your registered barangay</p>
            </div>
        </div>

        <div class="form-group">

            <div class="center-card">
                <div class="center-icon" aria-hidden="true">🏥</div>

                <input
                    type="text"
                    value="{{ $healthCenter->name }} — {{ $healthCenter->barangay }}"
                    readonly
                    aria-label="Health Center"
                >
            </div>

            <small>
                Your health center is based on your registered barangay.
            </small>

        </div>

    </div>


    @php
        $scheduleCalendarData = $schedules->map(function ($schedule) {
            $available = max(
                $schedule->capacity - $schedule->active_appointments_count,
                0
            );

            return [
                'id' => $schedule->id,
                'serviceId' => (string) $schedule->service_id,
                'healthCenterId' => (string) $schedule->health_center_id,
                'doctorId' => $schedule->doctor_id ? (string) $schedule->doctor_id : '',
                'date' => $schedule->schedule_date->format('Y-m-d'),
                'time' => $schedule->appointment_time->format('H:i'),
                'timeLabel' => $schedule->appointment_time->format('h:i A'),
                'available' => $available,
            ];
        })->values();
    @endphp

    @if ($schedules->count() && $services->count())

        <form
            method="POST"
            action="{{ route('patient.appointments.store') }}"
        >

            @csrf

            <script type="application/json" id="appointment-schedule-data">@json($scheduleCalendarData)</script>


            {{-- STEP 2 — Service and Schedule --}}
            <div class="form-section">

                <div class="form-section-head">
                    <span class="step-number">2</span>
                    <div>
                        <h4>Service &amp; Schedule</h4>
                        <p>Choose the service, then an available date and time</p>
                    </div>
                </div>

                {{-- Service --}}
                <div class="form-group">

                    <label for="service_id">
                        Type of Service
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
                        Only services currently offered by your health center are shown.
                    </small>

                </div>


                <div class="form-group appointment-booking-schedule">
                    <span class="appointment-booking-schedule__label">Available date and time</span>

                    <div id="appointment-calendar" class="appointment-calendar">
                        <div class="appointment-calendar__header">
                            <button
                                type="button"
                                id="appointment-calendar-previous"
                                class="appointment-calendar__nav"
                                aria-label="Show previous month"
                            >
                                <span aria-hidden="true">&lsaquo;</span>
                            </button>
                            <h5 id="appointment-calendar-month" aria-live="polite"></h5>
                            <button
                                type="button"
                                id="appointment-calendar-next"
                                class="appointment-calendar__nav"
                                aria-label="Show next month"
                            >
                                <span aria-hidden="true">&rsaquo;</span>
                            </button>
                        </div>

                        <div class="appointment-calendar__weekdays" aria-hidden="true">
                            <span>Sun</span><span>Mon</span><span>Tue</span>
                            <span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
                        </div>
                        <div
                            id="appointment-calendar-grid"
                            class="appointment-calendar__grid"
                            role="group"
                            aria-label="Available appointment dates"
                        ></div>
                        <p
                            id="appointment-calendar-status"
                            class="appointment-calendar__status"
                            role="status"
                            aria-live="polite"
                        >
                            Choose a service to see available dates.
                        </p>
                    </div>

                    <fieldset
                        id="appointment-time-slots"
                        class="appointment-time-slots"
                        hidden
                    >
                        <legend id="appointment-time-slots-title">Available times</legend>
                        <div
                            id="appointment-time-slots-list"
                            class="appointment-time-slots__list"
                        ></div>
                    </fieldset>
                    <p
                        id="appointment-time-slots-empty"
                        class="appointment-time-slots__empty"
                        role="status"
                        aria-live="polite"
                    >
                        Select an available date to see its appointment times.
                    </p>
                </div>

            </div>


            {{-- STEP 3 — Doctor --}}
            <div class="form-section">

                <div class="form-section-head">
                    <span class="step-number">3</span>
                    <div>
                        <h4>Doctor</h4>
                        <p>Doctors available for your selected service and schedule</p>
                    </div>
                </div>

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
                            Select a schedule first
                        </option>

                        @foreach ($doctors as $doctor)
                            @php
                                $doctorName = $doctor->user->name ?? 'Doctor';
                                $doctorServiceNames = $doctor->services
                                    ->pluck('name')
                                    ->filter()
                                    ->map(function ($name) {
                                        return \Illuminate\Support\Str::limit(trim($name), 18, '…');
                                    })
                                    ->take(2)
                                    ->values()
                                    ->all();
                                $doctorServiceLabel = $doctorServiceNames
                                    ? implode(' / ', $doctorServiceNames)
                                    : (\Illuminate\Support\Str::limit(trim($doctor->specialization ?? 'Medical Doctor'), 18, '…'));

                                if ($doctor->services->count() > 2) {
                                    $doctorServiceLabel .= ' +';
                                }
                            @endphp

                            <option
                                value="{{ $doctor->id }}"
                                data-health-center-id="{{ $doctor->health_center_id }}"
                                data-service-ids="{{ $doctor->services->pluck('id')->implode(',') }}"
                                title="{{ $doctorName }} — {{ $doctor->services->pluck('name')->filter()->implode(' / ') ?: ($doctor->specialization ?: 'Medical Doctor') }}"
                                {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}
                            >

                                {{ $doctorName }} — {{ $doctorServiceLabel }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Selected Schedule Information --}}
                <div
                    id="schedule-info"
                    class="form-group schedule-summary"
                    style="display: none;"
                >
                    <div class="schedule-summary__header">
                        <strong>Selected appointment</strong>
                        <span class="schedule-summary__badge">Ready</span>
                    </div>

                    <div class="schedule-summary__grid">
                        <div>
                            <span>Service</span>
                            <strong id="selected-service-name">—</strong>
                        </div>
                        <div>
                            <span>Date</span>
                            <strong id="selected-date-label">—</strong>
                        </div>
                        <div>
                            <span>Time</span>
                            <strong id="selected-time-label">—</strong>
                        </div>
                        <div>
                            <span>Doctor</span>
                            <strong id="selected-doctor-name">—</strong>
                        </div>
                    </div>
                </div>

            </div>


            {{-- STEP 4 — Concern --}}
            <div class="form-section">

                <div class="form-section-head">
                    <span class="step-number">4</span>
                    <div>
                        <h4>Your Concern</h4>
                        <p>Help the doctor prepare for your visit</p>
                    </div>
                </div>

                {{-- Reason --}}
                <div class="form-group">

                    <label for="reason">
                        Reason for Consultation
                    </label>

                    <textarea
                        id="reason"
                        name="reason"
                        rows="4"
                        placeholder="Briefly describe the reason for your appointment..."
                        required
                    >{{ old('reason') }}</textarea>

                </div>


                {{-- Additional Notes --}}
                <div class="form-group">

                    <label for="notes">
                        Additional Notes
                        <span>(Optional)</span>
                    </label>

                    <textarea
                        id="notes"
                        name="notes"
                        rows="3"
                        placeholder="Any additional information for the doctor..."
                    >{{ old('notes') }}</textarea>

                </div>

            </div>


            {{-- Buttons --}}
            <div class="form-actions">

                <a
                    href="{{ route('patient.dashboard') }}"
                    class="secondary-button"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="primary-button"
                >
                    Book Appointment
                </button>

            </div>

        </form>

    @elseif (!$services->count())

        <div class="alert alert-danger">

            <strong>No services are currently available.</strong>

            <p>
                Your assigned health center has not published any available services.
            </p>

        </div>

        <div class="form-actions">

            <a
                href="{{ route('patient.dashboard') }}"
                class="secondary-button"
            >
                Back to Dashboard
            </a>

        </div>

    @else

        <div class="alert alert-danger">

            <strong>No appointment schedules are currently available.</strong>

            <p>
                Please check again later when your health center publishes new consultation schedules.
            </p>

        </div>

        <div class="form-actions">

            <a
                href="{{ route('patient.dashboard') }}"
                class="secondary-button"
            >
                Back to Dashboard
            </a>

        </div>

    @endif

</div>


{{-- JavaScript below is unchanged from your original --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const serviceSelect =
        document.getElementById('service_id');

    const scheduleSelect =
        document.getElementById('appointment_schedule_id');

    const doctorSelect =
        document.getElementById('doctor_id');

    const scheduleInfo =
        document.getElementById('schedule-info');

    const selectedServiceName =
        document.getElementById('selected-service-name');
    const selectedDateLabel =
        document.getElementById('selected-date-label');
    const selectedTimeLabel =
        document.getElementById('selected-time-label');
    const selectedDoctorName =
        document.getElementById('selected-doctor-name');

    if (
        !serviceSelect ||
        !scheduleSelect ||
        !doctorSelect
    ) {
        return;
    }

    const scheduleOptions = Array.from(
        scheduleSelect.querySelectorAll(
            'option[data-service-id]'
        )
    );

    const doctorOptions = Array.from(
        doctorSelect.querySelectorAll(
            'option[data-service-ids]'
        )
    );

    function updateSchedules() {

        const serviceId =
            serviceSelect.value;

        scheduleSelect.value = '';

        doctorSelect.value = '';
        doctorSelect.disabled = true;

        scheduleOptions.forEach(function (option) {

            const optionServiceId =
                option.dataset.serviceId;

            option.hidden =
                !serviceId ||
                optionServiceId !== serviceId;

        });

        const placeholder =
            scheduleSelect.querySelector(
                'option[value=""]'
            );

        if (placeholder) {

            placeholder.textContent =
                serviceId
                    ? 'Select an available schedule'
                    : 'Select a service first';

        }

        scheduleInfo.style.display = 'none';
        selectedServiceName.textContent = serviceSelect.selectedOptions[0]?.text.trim() || '—';
        selectedDateLabel.textContent = '—';
        selectedTimeLabel.textContent = '—';
        selectedDoctorName.textContent = '—';

        updateDoctors();

    }

    function updateDoctors() {

        const serviceId =
            serviceSelect.value;

        const selectedSchedule =
            scheduleSelect.options[
                scheduleSelect.selectedIndex
            ];

        const healthCenterId =
            selectedSchedule?.dataset.healthCenterId || '';

        doctorSelect.value = '';

        if (!serviceId || !healthCenterId) {

            doctorSelect.disabled = true;

            const placeholder =
                doctorSelect.querySelector(
                    'option[value=""]'
                );

            if (placeholder) {
                placeholder.textContent =
                    !serviceId
                        ? 'Select a service first'
                        : 'Select a schedule first';
            }

            scheduleInfo.style.display = 'none';
            selectedServiceName.textContent = serviceSelect.selectedOptions[0]?.text.trim() || '—';
            selectedDateLabel.textContent = '—';
            selectedTimeLabel.textContent = '—';
            selectedDoctorName.textContent = '—';

            return;
        }

        doctorSelect.disabled = false;

        doctorOptions.forEach(function (option) {

            const doctorHealthCenterId =
                option.dataset.healthCenterId;

            const serviceIds =
                option.dataset.serviceIds
                    ? option.dataset.serviceIds.split(',')
                    : [];

            const matchesHealthCenter =
                doctorHealthCenterId ===
                healthCenterId;

            const matchesService =
                serviceIds.includes(serviceId);

            option.hidden =
                !(
                    matchesHealthCenter &&
                    matchesService
                );

        });

        const placeholder =
            doctorSelect.querySelector(
                'option[value=""]'
            );

        if (placeholder) {
            placeholder.textContent =
                'Select a doctor';
        }

        scheduleInfo.style.display = 'block';
        selectedServiceName.textContent = serviceSelect.selectedOptions[0]?.text.trim() || '—';
        selectedDateLabel.textContent = formattedDate(selectedSchedule.date, {
            weekday: 'short',
            month: 'short',
            day: 'numeric',
            year: 'numeric',
        });
        selectedTimeLabel.textContent = selectedSchedule.timeLabel;

        const selectedDoctorOption = doctorSelect.selectedOptions[0];
        selectedDoctorName.textContent = selectedDoctorOption && selectedDoctorOption.value
            ? selectedDoctorOption.text.trim().replace(/\s*[•—]\s*.*$/, '')
            : 'Not selected';
    }

    serviceSelect.addEventListener(
        'change',
        updateSchedules
    );

    scheduleSelect.addEventListener(
        'change',
        updateDoctors
    );

    updateSchedules();

});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const serviceSelect = document.getElementById('service_id');
    const doctorSelect = document.getElementById('doctor_id');
    const calendarGrid = document.getElementById('appointment-calendar-grid');
    const calendarMonth = document.getElementById('appointment-calendar-month');
    const calendarStatus = document.getElementById('appointment-calendar-status');
    const previousMonthButton = document.getElementById('appointment-calendar-previous');
    const nextMonthButton = document.getElementById('appointment-calendar-next');
    const timeSlots = document.getElementById('appointment-time-slots');
    const timeSlotsTitle = document.getElementById('appointment-time-slots-title');
    const timeSlotsList = document.getElementById('appointment-time-slots-list');
    const timeSlotsEmpty = document.getElementById('appointment-time-slots-empty');
    const scheduleInfo = document.getElementById('schedule-info');
    const selectedServiceName = document.getElementById('selected-service-name');
    const selectedDateLabel = document.getElementById('selected-date-label');
    const selectedTimeLabel = document.getElementById('selected-time-label');
    const selectedDoctorName = document.getElementById('selected-doctor-name');
    const scheduleDataElement = document.getElementById('appointment-schedule-data');

    if (
        !serviceSelect || !doctorSelect || !calendarGrid || !calendarMonth
        || !calendarStatus || !previousMonthButton || !nextMonthButton
        || !timeSlots || !timeSlotsTitle || !timeSlotsList || !timeSlotsEmpty
        || !scheduleInfo || !scheduleDataElement
    ) {
        return;
    }

    const schedules = JSON.parse(scheduleDataElement.textContent || '[]');
    const doctorOptions = Array.from(doctorSelect.querySelectorAll('option[data-service-ids]'));
    const oldScheduleId = @json((string) old('appointment_schedule_id', ''));
    let selectedScheduleId = oldScheduleId;
    let selectedDate = '';
    let selectedDoctorId = doctorSelect.value;
    let displayedMonth = new Date();
    displayedMonth.setDate(1);

    const localDate = function (value) {
        const [year, month, day] = value.split('-').map(Number);
        return new Date(year, month - 1, day);
    };

    const dateKey = function (date) {
        return date.getFullYear() + '-'
            + String(date.getMonth() + 1).padStart(2, '0') + '-'
            + String(date.getDate()).padStart(2, '0');
    };

    const monthKey = function (date) {
        return date.getFullYear() * 12 + date.getMonth();
    };

    const serviceSchedules = function () {
        return schedules.filter(function (schedule) {
            return schedule.serviceId === serviceSelect.value && schedule.available > 0;
        });
    };

    const availableDates = function (items) {
        return Array.from(new Set(items.map(function (schedule) {
            return schedule.date;
        }))).sort();
    };

    const formattedDate = function (value, options) {
        return new Intl.DateTimeFormat(undefined, options).format(localDate(value));
    };

    function updateDoctors(resetSelection) {
        const serviceId = serviceSelect.value;
        const selectedSchedule = schedules.find(function (schedule) {
            return String(schedule.id) === selectedScheduleId
                && schedule.date === selectedDate
                && schedule.serviceId === serviceId;
        });

        if (resetSelection) {
            selectedDoctorId = '';
        }

        const placeholder = doctorSelect.querySelector('option[value=""]');
        if (!serviceId || !selectedSchedule) {
            doctorSelect.disabled = true;
            doctorSelect.value = '';
            placeholder.textContent = serviceId
                ? 'Select an appointment time first'
                : 'Select a service first';
            scheduleInfo.style.display = 'none';
            selectedServiceName.textContent = serviceSelect.selectedOptions[0]?.text.trim() || '—';
            selectedDateLabel.textContent = '—';
            selectedTimeLabel.textContent = '—';
            selectedDoctorName.textContent = '—';
            return;
        }

        let hasEligibleDoctor = false;
        doctorOptions.forEach(function (option) {
            const serviceIds = option.dataset.serviceIds
                ? option.dataset.serviceIds.split(',')
                : [];
            const matchesCenter = option.dataset.healthCenterId === selectedSchedule.healthCenterId;
            const matchesService = serviceIds.includes(serviceId);
            const matchesAssignedDoctor = !selectedSchedule.doctorId
                || option.value === selectedSchedule.doctorId;
            option.hidden = !(matchesCenter && matchesService && matchesAssignedDoctor);
            hasEligibleDoctor = hasEligibleDoctor || !option.hidden;
        });

        placeholder.textContent = hasEligibleDoctor
            ? 'Select a doctor'
            : 'No doctor is available for this schedule';
        doctorSelect.disabled = !hasEligibleDoctor;
        doctorSelect.value = doctorOptions.some(function (option) {
            return option.value === selectedDoctorId && !option.hidden;
        }) ? selectedDoctorId : '';
        selectedDoctorId = doctorSelect.value;

        selectedServiceName.textContent = serviceSelect.selectedOptions[0]?.text.trim() || '—';
        selectedDateLabel.textContent = formattedDate(selectedSchedule.date, {
            weekday: 'short',
            month: 'short',
            day: 'numeric',
            year: 'numeric',
        });
        selectedTimeLabel.textContent = selectedSchedule.timeLabel + ' (' + selectedSchedule.available + ' '
            + (selectedSchedule.available === 1 ? 'appointment available' : 'appointments available') + ')';

        const selectedDoctorOption = doctorSelect.selectedOptions[0];
        selectedDoctorName.textContent = selectedDoctorOption && selectedDoctorOption.value
            ? selectedDoctorOption.text.trim().replace(/\s*—\s*.*$/, '')
            : 'Not selected';
        scheduleInfo.style.display = 'block';
    }

    function renderTimeSlots() {
        const items = serviceSchedules()
            .filter(function (schedule) {
                return schedule.date === selectedDate;
            })
            .sort(function (first, second) {
                return first.time.localeCompare(second.time);
            });
        timeSlotsList.replaceChildren();

        if (!selectedDate || !items.length) {
            timeSlots.hidden = true;
            timeSlotsEmpty.hidden = false;
            timeSlotsEmpty.textContent = selectedDate
                ? 'No appointment times are available on this date.'
                : 'Select an available date to see its appointment times.';
            return;
        }

        timeSlots.hidden = false;
        timeSlotsEmpty.hidden = true;
        timeSlotsTitle.textContent = 'Available times for ' + formattedDate(selectedDate, {
            month: 'long',
            day: 'numeric',
            year: 'numeric',
        });

        items.forEach(function (schedule) {
            const label = document.createElement('label');
            label.className = 'appointment-time-slot';
            if (String(schedule.id) === selectedScheduleId) {
                label.classList.add('is-selected');
            }

            const input = document.createElement('input');
            input.type = 'radio';
            input.name = 'appointment_schedule_id';
            input.value = schedule.id;
            input.required = true;
            input.checked = String(schedule.id) === selectedScheduleId;

            const time = document.createElement('span');
            time.className = 'appointment-time-slot__time';
            time.textContent = schedule.timeLabel;

            const remaining = document.createElement('span');
            remaining.className = 'appointment-time-slot__remaining';
            remaining.textContent = schedule.available + ' '
                + (schedule.available === 1 ? 'appointment available' : 'appointments available');

            label.append(input, time, remaining);
            timeSlotsList.append(label);
        });
    }

    function renderCalendar() {
        const items = serviceSchedules();
        const dates = availableDates(items);
        const firstMonth = dates.length ? monthKey(localDate(dates[0])) : null;
        const lastMonth = dates.length ? monthKey(localDate(dates[dates.length - 1])) : null;
        let visibleMonth = monthKey(displayedMonth);

        if (firstMonth !== null && visibleMonth < firstMonth) {
            displayedMonth = localDate(dates[0]);
            displayedMonth.setDate(1);
            visibleMonth = monthKey(displayedMonth);
        } else if (lastMonth !== null && visibleMonth > lastMonth) {
            displayedMonth = localDate(dates[dates.length - 1]);
            displayedMonth.setDate(1);
            visibleMonth = monthKey(displayedMonth);
        }

        calendarMonth.textContent = new Intl.DateTimeFormat(undefined, {
            month: 'long',
            year: 'numeric',
        }).format(displayedMonth);
        previousMonthButton.disabled = firstMonth === null || visibleMonth <= firstMonth;
        nextMonthButton.disabled = lastMonth === null || visibleMonth >= lastMonth;
        calendarGrid.replaceChildren();

        const firstWeekday = new Date(
            displayedMonth.getFullYear(),
            displayedMonth.getMonth(),
            1
        ).getDay();
        const numberOfDays = new Date(
            displayedMonth.getFullYear(),
            displayedMonth.getMonth() + 1,
            0
        ).getDate();
        const dateSet = new Set(dates);
        const monthHasDates = dates.some(function (value) {
            const date = localDate(value);
            return date.getFullYear() === displayedMonth.getFullYear()
                && date.getMonth() === displayedMonth.getMonth();
        });

        for (let index = 0; index < firstWeekday; index += 1) {
            const spacer = document.createElement('span');
            spacer.className = 'appointment-calendar__spacer';
            spacer.setAttribute('aria-hidden', 'true');
            calendarGrid.append(spacer);
        }

        for (let day = 1; day <= numberOfDays; day += 1) {
            const date = new Date(
                displayedMonth.getFullYear(),
                displayedMonth.getMonth(),
                day
            );
            const value = dateKey(date);
            const dayItems = items.filter(function (schedule) {
                return schedule.date === value;
            });
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'appointment-calendar__day';
            button.textContent = String(day);

            if (!dateSet.has(value)) {
                button.disabled = true;
                button.classList.add('is-disabled');
                button.setAttribute('aria-label', formattedDate(value, {
                    weekday: 'long',
                    month: 'long',
                    day: 'numeric',
                }) + ', no appointments available');
            } else {
                const remaining = dayItems.reduce(function (total, schedule) {
                    return total + schedule.available;
                }, 0);
                button.dataset.date = value;
                button.setAttribute('aria-label', formattedDate(value, {
                    weekday: 'long',
                    month: 'long',
                    day: 'numeric',
                }) + ', ' + remaining + ' appointment '
                    + (remaining === 1 ? 'booking available' : 'bookings available'));
                button.setAttribute('aria-pressed', String(value === selectedDate));
                if (value === selectedDate) {
                    button.classList.add('is-selected');
                }
                button.addEventListener('click', function () {
                    selectedDate = value;
                    selectedScheduleId = '';
                    selectedDoctorId = '';
                    renderCalendar();
                    renderTimeSlots();
                    updateDoctors(true);
                });
            }

            calendarGrid.append(button);
        }

        if (!serviceSelect.value) {
            calendarStatus.textContent = 'Choose a service to see available dates.';
        } else if (!dates.length) {
            calendarStatus.textContent = 'No available dates for this service. Please choose another service.';
        } else if (!monthHasDates) {
            calendarStatus.textContent = 'No appointments are available this month. Use the month arrows to check other dates.';
        } else {
            calendarStatus.textContent = 'Choose a highlighted date. Dates without appointments are unavailable.';
        }
    }

    calendarGrid.addEventListener('keydown', function (event) {
        const activeButton = event.target.closest('button[data-date]');
        const dayStep = {
            ArrowLeft: -1,
            ArrowRight: 1,
            ArrowUp: -7,
            ArrowDown: 7,
        }[event.key];
        if (!activeButton || !dayStep) {
            return;
        }

        event.preventDefault();
        const dateSet = new Set(availableDates(serviceSchedules()));
        const target = localDate(activeButton.dataset.date);
        let targetValue = '';

        for (let attempt = 0; attempt < 60; attempt += 1) {
            target.setDate(target.getDate() + dayStep);
            const value = dateKey(target);
            if (dateSet.has(value)) {
                targetValue = value;
                break;
            }
        }

        if (!targetValue) {
            return;
        }

        displayedMonth = localDate(targetValue);
        displayedMonth.setDate(1);
        renderCalendar();
        const targetButton = calendarGrid.querySelector('button[data-date="' + targetValue + '"]');
        if (targetButton) {
            targetButton.focus();
        }
    });

    timeSlotsList.addEventListener('change', function (event) {
        const input = event.target;
        if (!(input instanceof HTMLInputElement) || input.name !== 'appointment_schedule_id') {
            return;
        }

        selectedScheduleId = input.value;
        timeSlotsList.querySelectorAll('.appointment-time-slot').forEach(function (label) {
            label.classList.toggle(
                'is-selected',
                label.querySelector('input') === input
            );
        });
        updateDoctors(true);
    });

    serviceSelect.addEventListener('change', function () {
        selectedDate = '';
        selectedScheduleId = '';
        selectedDoctorId = '';
        renderCalendar();
        renderTimeSlots();
        updateDoctors(true);
    });

    doctorSelect.addEventListener('change', function () {
        selectedDoctorId = doctorSelect.value;
        const selectedDoctorOption = doctorSelect.selectedOptions[0];
        selectedDoctorName.textContent = selectedDoctorOption && selectedDoctorOption.value
            ? selectedDoctorOption.text.trim().replace(/\s*—\s*.*$/, '')
            : 'Not selected';
    });

    previousMonthButton.addEventListener('click', function () {
        displayedMonth.setMonth(displayedMonth.getMonth() - 1);
        renderCalendar();
    });

    nextMonthButton.addEventListener('click', function () {
        displayedMonth.setMonth(displayedMonth.getMonth() + 1);
        renderCalendar();
    });

    const previousSchedule = schedules.find(function (schedule) {
        return String(schedule.id) === oldScheduleId;
    });
    if (previousSchedule && previousSchedule.serviceId === serviceSelect.value) {
        selectedDate = previousSchedule.date;
        displayedMonth = localDate(previousSchedule.date);
        displayedMonth.setDate(1);
    } else {
        selectedScheduleId = '';
    }

    renderCalendar();
    renderTimeSlots();
    updateDoctors(false);
});
</script>

@endsection