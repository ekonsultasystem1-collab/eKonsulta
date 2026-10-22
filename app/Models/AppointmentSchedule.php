<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'health_center_id',
        'doctor_id',
        'service_id',
        'schedule_date',
        'appointment_time',
        'capacity',
    ];

    protected function casts(): array
    {
        return [
            'schedule_date' => 'date',
            'appointment_time' => 'datetime:H:i',
            'capacity' => 'integer',
        ];
    }

    public function healthCenter()
    {
        return $this->belongsTo(HealthCenter::class)->withTrashed();
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}