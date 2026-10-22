<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\HealthCenter;
use App\Models\Service;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'health_center_id',
        'license_number',
        'specialization',
        'contact_number',
    ];

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Health Center
    |--------------------------------------------------------------------------
    */

    public function healthCenter()
    {
        return $this->belongsTo(HealthCenter::class)->withTrashed();
    }

    /*
    |--------------------------------------------------------------------------
    | Services
    |--------------------------------------------------------------------------
    */

    public function services()
    {
        return $this->belongsToMany(
            Service::class,
            'doctor_service'
        )->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Appointments
    |--------------------------------------------------------------------------
    */

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Consultations
    |--------------------------------------------------------------------------
    */

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Prescriptions
    |--------------------------------------------------------------------------
    */

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}