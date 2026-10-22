<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function healthCenters()
    {
        return $this->belongsToMany(
            HealthCenter::class,
            'health_center_service'
        )->withTimestamps();
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function appointmentSchedules()
    {
        return $this->hasMany(AppointmentSchedule::class);
    }

    public function doctors()
{
    return $this->belongsToMany(
        Doctor::class,
        'doctor_service'
    )->withTimestamps();
}
}