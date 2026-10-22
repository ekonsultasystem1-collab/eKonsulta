<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use App\Models\ActivityLog;
use App\Models\HealthCenter;
use App\Models\EmailOtp;
use Illuminate\Support\Facades\Mail;



class AuthController extends Controller
{
    private const OTP_EXPIRATION_MINUTES = 5;

    private const OTP_RESEND_COOLDOWN_SECONDS = 300;

    /*
    |--------------------------------------------------------------------------
    | Show Login Page
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        return view('auth.login');
    }


    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

 public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    $remember = $request->boolean('remember');

    if (Auth::attempt($credentials, $remember)) {

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();

            return back()
                ->withErrors([
                    'email' => 'Your account has been deactivated. Please contact the administrator.',
                ])
                ->onlyInput('email');
        }

       $request->session()->regenerate();

ActivityLog::record(
    $user->id,
    'User Login',
    'User logged into the system.'
);

return $this->redirectByRole($user);
    }

    return back()
        ->withErrors([
            'email' => 'The provided credentials are incorrect.',
        ])
        ->onlyInput('email');
}


    /*
    |--------------------------------------------------------------------------
    | Role-Based Redirect
    |--------------------------------------------------------------------------
    */

    private function redirectByRole(User $user)
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'doctor' => redirect()->route('doctor.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            'patient' => redirect()->route('patient.dashboard'),
            default => abort(403, 'Invalid user role.'),
        };
    }


    /*
    |--------------------------------------------------------------------------
    | Show Registration Page
    |--------------------------------------------------------------------------
    */

public function showRegister()
{
    $healthCenters = HealthCenter::where('status', 'active')
        ->orderBy('barangay')
        ->orderBy('name')
        ->get();

    $barangays = $healthCenters
        ->pluck('barangay')
        ->filter()
        ->unique()
        ->values();

    return view('auth.register', compact(
        'healthCenters',
        'barangays'
    ));
}


    /*
    |--------------------------------------------------------------------------
    | Patient Registration
    |--------------------------------------------------------------------------
    */

public function register(Request $request)
{
    $validated = $request->validate([
      'first_name' => [
    'required',
    'string',
    'max:100',
    'regex:/^[\p{L}\p{M}\s.\'-]+$/u',
],

'middle_name' => [
    'nullable',
    'string',
    'max:100',
    'regex:/^[\p{L}\p{M}\s.\'-]+$/u',
],

'last_name' => [
    'required',
    'string',
    'max:100',
    'regex:/^[\p{L}\p{M}\s.\'-]+$/u',
],

'suffix' => [
    'nullable',
    'string',
    'max:30',
    'regex:/^[\p{L}\p{M}\s.\'-]+$/u',
],

        'email' => [
            'required',
            'email',
            'max:255',
            'unique:users,email',
            'ends_with:@gmail.com',
        ],

        'password' => [
            'required',
            'string',
            'confirmed',
            Password::min(8)->mixedCase()->numbers(),
        ],

        'date_of_birth' => [
            'required',
            'date_format:Y-m-d',
            'after_or_equal:' . now()->subYears(110)->toDateString(),
            'before_or_equal:' . today()->toDateString(),
        ],

        'sex' => [
            'required',
            'string',
            'max:20',
        ],



        'contact_number' => [
            'required',
            'digits:11',
        ],

        'address' => [
            'required',
            'string',
        ],

'barangay' => [
    'required',
    'string',
    'max:255',
],

        'health_center_id' => [
            'required',
            'exists:health_centers,id',
        ],



        'emergency_contact_name' => [
            'required',
            'string',
            'max:255',
            'regex:/^[\p{L}\p{M}\s.\'-]+$/u',
        ],

        'emergency_contact_number' => [
            'required',
            'digits:11',
        ],
    ]);

    $resendKey = $this->registrationResendKey($validated['email']);

    if (RateLimiter::tooManyAttempts($resendKey, 1)) {
        return back()
            ->withErrors([
                'email' => 'A verification code was already sent. Please wait five minutes before requesting another.',
            ])
            ->withInput();
    }

    // Make sure the selected health center is active.
 $healthCenter = HealthCenter::where('id', $validated['health_center_id'])
    ->where('status', 'active')
    ->where('barangay', $validated['barangay'])
    ->first();

if (!$healthCenter) {
    session()->forget('pending_registration');

    return redirect()
        ->route('register')
        ->withErrors([
            'health_center_id' =>
                'The selected health center does not belong to the selected barangay or is no longer available.',
        ]);
}

    /*
    |--------------------------------------------------------------------------
    | Store registration information temporarily
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | No User or Patient record is created yet.
    | The account will only be created after the OTP is verified.
    |
    */

    session([
        'pending_registration' => $validated,
    ]);

    /*
    |--------------------------------------------------------------------------
    | Generate OTP
    |--------------------------------------------------------------------------
    */

    $otp = str_pad(
        (string) random_int(0, 999999),
        6,
        '0',
        STR_PAD_LEFT
    );

    /*
    |--------------------------------------------------------------------------
    | Remove previous OTPs for this email
    |--------------------------------------------------------------------------
    */

    EmailOtp::where('email', $validated['email'])->delete();

    /*
    |--------------------------------------------------------------------------
    | Create new OTP
    |--------------------------------------------------------------------------
    */

    EmailOtp::create([
        'email' => $validated['email'],
        'otp' => $otp,
        'expires_at' => now()->addMinutes(self::OTP_EXPIRATION_MINUTES),
        'attempts' => 0,
    ]);
    RateLimiter::hit($resendKey, self::OTP_RESEND_COOLDOWN_SECONDS);
    session([
        'registration_otp_resend_at' => now()->addSeconds(self::OTP_RESEND_COOLDOWN_SECONDS)->timestamp,
    ]);

    /*
    |--------------------------------------------------------------------------
    | Send OTP Email
    |--------------------------------------------------------------------------
    */

    Mail::raw(
        "Your E Konsulta verification code is: {$otp}\n\n" .
        "This code will expire in 5 minutes.\n\n" .
        "If you did not request this registration, you may ignore this email.",
        function ($message) use ($validated) {
            $message
                ->to($validated['email'])
                ->subject('E Konsulta Email Verification Code');
        }
    );

    return redirect()
        ->route('register.verify')
        ->with('success', 'A verification code has been sent to your Gmail address.');
}

public function showVerifyOtp()
{
    if (!session()->has('pending_registration')) {
        return redirect()
            ->route('register')
            ->withErrors([
                'email' => 'Please complete the registration form first.',
            ]);
    }

    return view('auth.verify-otp', [
        'resendAvailableAt' => session('registration_otp_resend_at', now()->timestamp),
    ]);
}


public function verifyOtp(Request $request)
{
    $pendingRegistration = session('pending_registration');

    if (!$pendingRegistration) {
        return redirect()
            ->route('register')
            ->withErrors([
                'email' => 'Your registration session has expired. Please register again.',
            ]);
    }

    $request->validate([
        'otp' => [
            'required',
            'digits:6',
        ],
    ]);

    $email = $pendingRegistration['email'];

    $emailOtp = EmailOtp::where('email', $email)
        ->whereNull('verified_at')
        ->latest()
        ->first();

    if (!$emailOtp) {
        return back()
            ->withErrors([
                'otp' => 'No active verification code was found. Please request a new code.',
            ]);
    }

    
    /*
    |--------------------------------------------------------------------------
    | Check expiration
    |--------------------------------------------------------------------------
    */

    if (now()->greaterThan($emailOtp->expires_at)) {
        return back()
            ->withErrors([
                'otp' => 'Your verification code has expired. Please resend a new code.',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Check attempts
    |--------------------------------------------------------------------------
    */

    if ($emailOtp->attempts >= 5) {
        return back()
            ->withErrors([
                'otp' => 'Too many incorrect attempts. Please request a new code.',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Check OTP
    |--------------------------------------------------------------------------
    */

    if ($request->otp !== $emailOtp->otp) {

        $emailOtp->increment('attempts');

        return back()
            ->withErrors([
                'otp' => 'The verification code is incorrect. Please try again.',
            ])
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | Mark OTP as verified
    |--------------------------------------------------------------------------
    */

    $emailOtp->update([
        'verified_at' => now(),
    ]);

    /*
    |--------------------------------------------------------------------------
    | Create User + Patient
    |--------------------------------------------------------------------------
    */

    $validated = $pendingRegistration;

    $healthCenter = HealthCenter::where('id', $validated['health_center_id'])
        ->where('status', 'active')
        ->first();

    if (!$healthCenter) {
        session()->forget([
            'pending_registration',
            'registration_otp_resend_at',
        ]);

        return redirect()
            ->route('register')
            ->withErrors([
                'health_center_id' =>
                    'The selected health center is no longer available.',
            ]);
    }

   $fullName = trim(implode(' ', array_filter([
    $validated['first_name'],
    $validated['middle_name'] ?? null,
    $validated['last_name'],
    $validated['suffix'] ?? null,
])));

$user = User::create([
    'name' => $fullName,
    'first_name' => $validated['first_name'],
    'middle_name' => $validated['middle_name'] ?? null,
    'last_name' => $validated['last_name'],
    'suffix' => $validated['suffix'] ?? null,
    'email' => $validated['email'],
    'password' => Hash::make($validated['password']),
    'role' => 'patient',
]);

    $patientNumber = 'PAT-' . date('Y') . '-' .
        str_pad($user->id, 5, '0', STR_PAD_LEFT);

    Patient::create([
        'user_id' => $user->id,
        'health_center_id' => $healthCenter->id,
        'patient_number' => $patientNumber,
        'date_of_birth' => $validated['date_of_birth'],
        'sex' => $validated['sex'],
        'contact_number' => $validated['contact_number'],
        'address' => $validated['address'],
        'emergency_contact_name' => $validated['emergency_contact_name'],
        'emergency_contact_number' => $validated['emergency_contact_number'],
    ]);

    /*
    |--------------------------------------------------------------------------
    | Clear temporary registration information
    |--------------------------------------------------------------------------
    */

    session()->forget('pending_registration');

    Auth::login($user);

    $request->session()->regenerate();

    ActivityLog::record(
        $user->id,
        'Patient Registration',
        'Patient account was registered successfully and assigned to ' .
            $healthCenter->name . ' after email verification.'
    );

    return redirect()
        ->route('patient.dashboard')
        ->with(
            'success',
            'Registration successful. Welcome to E Konsulta!'
        );
}

public function resendOtp(Request $request)
{
    $pendingRegistration = session('pending_registration');

    if (!$pendingRegistration) {
        return redirect()
            ->route('register')
            ->withErrors([
                'email' => 'Your registration session has expired. Please register again.',
            ]);
    }

    $email = $pendingRegistration['email'];
    $resendKey = $this->registrationResendKey($email);

    if (RateLimiter::tooManyAttempts($resendKey, 1)) {
        return back()->withErrors([
            'otp' => 'Please wait before requesting another verification code.',
        ]);
    }

    $otp = str_pad(
        (string) random_int(0, 999999),
        6,
        '0',
        STR_PAD_LEFT
    );

    EmailOtp::where('email', $email)->delete();

    EmailOtp::create([
        'email' => $email,
        'otp' => $otp,
        'expires_at' => now()->addMinutes(self::OTP_EXPIRATION_MINUTES),
        'attempts' => 0,
    ]);
    RateLimiter::hit($resendKey, self::OTP_RESEND_COOLDOWN_SECONDS);
    session([
        'registration_otp_resend_at' => now()->addSeconds(self::OTP_RESEND_COOLDOWN_SECONDS)->timestamp,
    ]);

    Mail::raw(
        "Your new E Konsulta verification code is: {$otp}\n\n" .
        "This code will expire in 5 minutes.",
        function ($message) use ($email) {
            $message
                ->to($email)
                ->subject('E Konsulta New Verification Code');
        }
    );

    return back()
        ->with('success', 'A new verification code has been sent to your Gmail address.');
}

private function registrationResendKey(string $email): string
{
    return 'registration-otp-resend:' . hash('sha256', strtolower($email));
}

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

   public function logout(Request $request)
{
    $user = Auth::user();

    if ($user) {
        ActivityLog::record(
            $user->id,
            'User Logout',
            'User logged out of the system.'
        );
    }

    Auth::logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect()->route('login');
}
}