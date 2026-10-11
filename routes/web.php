<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\PatientAppointmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientMedicalRecordController;
use App\Http\Controllers\DoctorDashboardController;
use App\Http\Controllers\DoctorAppointmentController;
use App\Http\Controllers\DoctorConsultationController;
use App\Http\Controllers\DoctorPrescriptionController;
use App\Http\Controllers\DoctorProfileController;
use App\Http\Controllers\PatientPrescriptionController;
use App\Http\Controllers\DoctorPatientController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\StaffPatientController;
use App\Http\Controllers\StaffAppointmentController;
use App\Http\Controllers\StaffConsultationController;
use App\Http\Controllers\StaffHealthCenterController;
use App\Http\Controllers\PatientProfileController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminHealthCenterController;
use App\Http\Controllers\StaffProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\StaffPrescriptionController;
use App\Http\Controllers\StaffAppointmentScheduleController;
use App\Http\Controllers\AdminActivityLogController;
use App\Http\Controllers\AdminAnnouncementController;
use App\Http\Controllers\AdminDoctorController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminReportController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\PatientAnnouncementController;
use App\Http\Controllers\StaffAnnouncementController;
use App\Http\Controllers\StaffPatientIntakeController;
use App\Http\Controllers\AdminServiceController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/


Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.store');

Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->middleware('throttle:6,1')
    ->name('password.email');

Route::get('/forgot-password/verify', [PasswordResetController::class, 'showOtpForm'])
    ->name('password.otp');

Route::post('/forgot-password/verify', [PasswordResetController::class, 'verifyOtp'])
    ->middleware('throttle:6,1')
    ->name('password.verify');

Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('throttle:6,1')
    ->name('password.update');

Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.store');

Route::get('/register/verify', [AuthController::class, 'showVerifyOtp'])
    ->name('register.verify');

Route::post('/register/verify', [AuthController::class, 'verifyOtp'])
    ->name('register.verify.submit');

Route::post('/register/resend-otp', [AuthController::class, 'resendOtp'])
    ->name('register.resend-otp');

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:patient'])
    ->group(function () {
        Route::get('/patient/notifications', [PatientAnnouncementController::class, 'notifications'])
            ->name('patient.notifications');

        Route::post('/patient/notifications/{notification}/read', [PatientAnnouncementController::class, 'markAsRead'])
            ->name('patient.notifications.read');

        Route::get('/patient/announcements', [PatientAnnouncementController::class, 'index'])
            ->name('patient.announcements.index');

        Route::get('/patient/announcements/{announcement}', [PatientAnnouncementController::class, 'show'])
            ->name('patient.announcements.show');
    });

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {



   Route::get('/dashboard', [AdminDashboardController::class, 'index'])
    ->name('dashboard');

    Route::get('/notifications', [PatientAnnouncementController::class, 'notifications'])
        ->name('notifications');

    Route::post('/notifications/{notification}/read', [PatientAnnouncementController::class, 'markAsRead'])
        ->name('notifications.read');

Route::get('/users', [AdminUserController::class, 'index'])
    ->name('users.index');

    Route::get('/patients', [AdminUserController::class, 'patients'])
        ->name('patients.index');

    Route::patch('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])
        ->name('users.toggle-status');

Route::get('/users/{user}', [AdminUserController::class, 'show'])
    ->name('users.show');

Route::get('/health-centers', [AdminHealthCenterController::class, 'index'])
    ->name('health-centers.index');

Route::get('/health-centers/create', [AdminHealthCenterController::class, 'create'])
    ->name('health-centers.create');

Route::post('/health-centers', [AdminHealthCenterController::class, 'store'])
    ->name('health-centers.store');

Route::get('/health-centers/{healthCenter}', [AdminHealthCenterController::class, 'show'])
    ->name('health-centers.show');

Route::get('/health-centers/{healthCenter}/edit', [AdminHealthCenterController::class, 'edit'])
    ->name('health-centers.edit');

Route::put('/health-centers/{healthCenter}', [AdminHealthCenterController::class, 'update'])
    ->name('health-centers.update');

Route::patch('/health-centers/{healthCenter}/toggle-status', [AdminHealthCenterController::class, 'toggleStatus'])
    ->name('health-centers.toggle-status');

Route::delete('/health-centers/{healthCenter}', [AdminHealthCenterController::class, 'destroy'])
    ->name('health-centers.destroy');

    Route::get('/doctors', [AdminDoctorController::class, 'index'])
    ->name('doctors.index');

Route::get('/doctors/create', [AdminDoctorController::class, 'create'])
    ->name('doctors.create');

Route::post('/doctors', [AdminDoctorController::class, 'store'])
    ->name('doctors.store');

Route::get('/doctors/{doctor}', [AdminDoctorController::class, 'show'])
    ->name('doctors.show');

Route::get('/doctors/{doctor}/edit', [AdminDoctorController::class, 'edit'])
    ->name('doctors.edit');

Route::put('/doctors/{doctor}', [AdminDoctorController::class, 'update'])
    ->name('doctors.update');

Route::patch('/doctors/{doctor}/toggle-status', [AdminDoctorController::class, 'toggleStatus'])
    ->name('doctors.toggle-status');

    Route::get('/activity-logs', [AdminActivityLogController::class, 'index'])
    ->name('activity-logs.index');

    Route::get('/profile', [AdminProfileController::class, 'show'])
    ->name('profile');

    Route::get('/reports', [AdminReportController::class, 'index'])
    ->name('reports.index');

    Route::get('/staff', [AdminStaffController::class, 'index'])
    ->name('staff.index');

Route::get('/staff/create', [AdminStaffController::class, 'create'])
    ->name('staff.create');

Route::post('/staff', [AdminStaffController::class, 'store'])
    ->name('staff.store');

Route::get('/staff/{staff}', [AdminStaffController::class, 'show'])
    ->name('staff.show');

Route::get('/staff/{staff}/edit', [AdminStaffController::class, 'edit'])
    ->name('staff.edit');

Route::put('/staff/{staff}', [AdminStaffController::class, 'update'])
    ->name('staff.update');

Route::patch('/staff/{staff}/toggle-status', [AdminStaffController::class, 'toggleStatus'])
    ->name('staff.toggle-status');

    // Services
    Route::get('/services', [AdminServiceController::class, 'index'])
        ->name('services.index');

    Route::get('/services/create', [AdminServiceController::class, 'create'])
        ->name('services.create');

    Route::post('/services', [AdminServiceController::class, 'store'])
        ->name('services.store');

    Route::get('/services/{service}/edit', [AdminServiceController::class, 'edit'])
        ->name('services.edit');

    Route::put('/services/{service}', [AdminServiceController::class, 'update'])
        ->name('services.update');

    Route::patch('/services/{service}/toggle-status', [AdminServiceController::class, 'toggleStatus'])
        ->name('services.toggle-status');

    Route::delete('/services/{service}', [AdminServiceController::class, 'destroy'])
        ->name('services.destroy');

    Route::get('/announcements', [AdminAnnouncementController::class, 'index'])
        ->name('announcements.index');

    Route::get('/announcements/create', [AdminAnnouncementController::class, 'create'])
        ->name('announcements.create');

    Route::post('/announcements', [AdminAnnouncementController::class, 'store'])
        ->name('announcements.store');

    Route::get('/announcements/{announcement}', [AdminAnnouncementController::class, 'show'])
        ->name('announcements.show');

    Route::get('/announcements/{announcement}/edit', [AdminAnnouncementController::class, 'edit'])
        ->name('announcements.edit');

    Route::put('/announcements/{announcement}', [AdminAnnouncementController::class, 'update'])
        ->name('announcements.update');

    Route::patch('/announcements/{announcement}/toggle-status', [AdminAnnouncementController::class, 'toggleStatus'])
        ->name('announcements.toggle-status');

    Route::delete('/announcements/{announcement}', [AdminAnnouncementController::class, 'destroy'])
        ->name('announcements.destroy');

    });


/*
|--------------------------------------------------------------------------
| Doctor Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:doctor'])
    ->prefix('doctor')
    ->name('doctor.')
    ->group(function () {

        Route::get('/dashboard', [DoctorDashboardController::class, 'index'])
    ->name('dashboard');

    Route::get('/consultations', [DoctorConsultationController::class, 'index'])
    ->name('consultations.index');

    Route::get('/consultations/{consultation}', [DoctorConsultationController::class, 'show'])
    ->name('consultations.show');

        Route::get('/appointments/{appointment}/consultation/create', [DoctorConsultationController::class, 'create'])
    ->name('appointments.consultation.create');

        Route::post('/appointments/{appointment}/consultation', [DoctorConsultationController::class, 'store'])
    ->name('appointments.consultation.store');
Route::get('/appointments/{appointment}/prescription/create', [DoctorPrescriptionController::class, 'create'])
->name('appointments.prescription.create');

Route::post('/appointments/{appointment}/prescription', [DoctorPrescriptionController::class, 'store'])
->name('appointments.prescription.store');

Route::get('/patients', [DoctorPatientController::class, 'index'])
    ->name('patients.index');

    Route::get('/patients/{patient}', [DoctorPatientController::class, 'show'])
    ->name('patients.show');

    Route::get('/prescriptions', [DoctorPrescriptionController::class, 'index'])
    ->name('prescriptions.index');

    Route::get('/prescriptions/{prescription}', [DoctorPrescriptionController::class, 'show'])
    ->name('prescriptions.show');

Route::get('/prescriptions/{prescription}/download', [DoctorPrescriptionController::class, 'download'])
    ->name('prescriptions.download');

Route::get('/prescriptions/{prescription}/print', [DoctorPrescriptionController::class, 'print'])
    ->name('prescriptions.print');
        /*
        |--------------------------------------------------------------------------
        | Doctor Appointments
        |--------------------------------------------------------------------------
        */

        Route::get('/appointments', [DoctorAppointmentController::class, 'index'])
            ->name('appointments.index');

    Route::get('/profile', [DoctorProfileController::class, 'show'])
    ->name('profile');

    });

/*
|--------------------------------------------------------------------------
| Staff Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {

Route::get('/health-centers', [StaffHealthCenterController::class, 'index'])
    ->name('health-centers.index');

Route::get('/health-centers/{healthCenter}', [StaffHealthCenterController::class, 'show'])
    ->name('health-centers.show');

Route::get('/dashboard', [StaffDashboardController::class, 'index'])
    ->name('dashboard');

Route::get('/notifications', [PatientAnnouncementController::class, 'notifications'])
    ->name('notifications');

Route::post('/notifications/{notification}/read', [PatientAnnouncementController::class, 'markAsRead'])
    ->name('notifications.read');

Route::get('/patients', [StaffPatientController::class, 'index'])
    ->name('patients.index');

Route::get('/patients/{patient}', [StaffPatientController::class, 'show'])
    ->name('patients.show');

Route::get('/appointments', [StaffAppointmentController::class, 'index'])
    ->name('appointments.index');

Route::get('/appointments/{appointment}', [StaffAppointmentController::class, 'show'])
    ->name('appointments.show');

Route::patch('/appointments/{appointment}/approve', [StaffAppointmentController::class, 'approve'])
    ->name('appointments.approve');

Route::get('/consultations', [StaffConsultationController::class, 'index'])
    ->name('consultations.index');

Route::get('/consultations/{consultation}', [StaffConsultationController::class, 'show'])
    ->name('consultations.show');

Route::get('/announcements', [StaffAnnouncementController::class, 'index'])
    ->name('announcements.index');

Route::get('/announcements/create', [StaffAnnouncementController::class, 'create'])
    ->name('announcements.create');

Route::post('/announcements', [StaffAnnouncementController::class, 'store'])
    ->name('announcements.store');

Route::get('/announcements/{announcement}', [StaffAnnouncementController::class, 'show'])
    ->name('announcements.show');

Route::get('/announcements/{announcement}/edit', [StaffAnnouncementController::class, 'edit'])
    ->name('announcements.edit');

Route::put('/announcements/{announcement}', [StaffAnnouncementController::class, 'update'])
    ->name('announcements.update');

Route::patch('/announcements/{announcement}/toggle-status', [StaffAnnouncementController::class, 'toggleStatus'])
    ->name('announcements.toggle-status');

Route::delete('/announcements/{announcement}', [StaffAnnouncementController::class, 'destroy'])
    ->name('announcements.destroy');

Route::get('/profile', [StaffProfileController::class, 'show'])
    ->name('profile');

    Route::get('/prescriptions', [StaffPrescriptionController::class, 'index'])
    ->name('prescriptions.index');

Route::get('/prescriptions/{prescription}', [StaffPrescriptionController::class, 'show'])
    ->name('prescriptions.show');

Route::patch('/prescriptions/{prescription}/release', [StaffPrescriptionController::class, 'release'])
    ->name('prescriptions.release');
    
    Route::get('/appointment-schedules', [StaffAppointmentScheduleController::class, 'index'])
    ->name('appointment-schedules.index');

Route::get('/appointment-schedules/create', [StaffAppointmentScheduleController::class, 'create']) 
    ->name('appointment-schedules.create');

Route::post('/appointment-schedules',[StaffAppointmentScheduleController::class, 'store']) ///
    ->name('appointment-schedules.store');

Route::delete('/appointment-schedules/{appointmentSchedule}', [StaffAppointmentScheduleController::class, 'destroy'])
    ->name('appointment-schedules.destroy');

        /*
         * Patient Intake
         */
        Route::get(
            '/appointments/{appointment}/intake',
            [StaffPatientIntakeController::class, 'create']
        )->name('appointments.intake.create');

        Route::post(
            '/appointments/{appointment}/intake',
            [StaffPatientIntakeController::class, 'store']
        )->name('appointments.intake.store');
    });


/*
|--------------------------------------------------------------------------
| Patient Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:patient'])

    ->prefix('patient')
    ->name('patient.')
    ->group(function () {

Route::get('/profile', [PatientProfileController::class, 'show'])
    ->name('profile');

Route::patch('/profile', [PatientProfileController::class, 'update'])
    ->name('profile.update');

    Route::get('/appointments', [PatientAppointmentController::class, 'index'])
    ->name('appointments.index');

Route::patch('/appointments/{appointment}/cancel', [PatientAppointmentController::class, 'cancel'])
    ->name('appointments.cancel');

    Route::get('/appointments/create', [PatientAppointmentController::class, 'create'])
    ->name('appointments.create');

Route::post('/appointments', [PatientAppointmentController::class, 'store'])
    ->name('appointments.store');

        Route::get('/dashboard', [PatientDashboardController::class, 'index'])
            ->name('dashboard');

            Route::get('/medical-records', [PatientMedicalRecordController::class, 'index'])
    ->name('medical-records.index');
    Route::get('/medical-records/{consultation}', [PatientMedicalRecordController::class, 'show'])
    ->name('medical-records.show');
    Route::get('/prescriptions', [PatientPrescriptionController::class, 'index'])
    ->name('prescriptions.index');

    Route::get('/prescriptions/{prescription}', [PatientPrescriptionController::class, 'show'])
    ->name('prescriptions.show');

Route::get('/prescriptions/{prescription}/download', [PatientPrescriptionController::class, 'download'])
    ->name('prescriptions.download');

Route::get('/prescriptions/{prescription}/print', [PatientPrescriptionController::class, 'print'])
    ->name('prescriptions.print');
    });