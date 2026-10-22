<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HealthCenter;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'health_center_id',
        'patient_number',
        'date_of_birth',
        'sex',
        'contact_number',
        'address',
        'emergency_contact_name',
        'emergency_contact_number',
    ];

    public function healthCenter()
{
    return $this->belongsTo(HealthCenter::class)->withTrashed();
}

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}