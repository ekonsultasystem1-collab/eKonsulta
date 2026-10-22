<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'health_center_id',
        'service_id',
        'appointment_schedule_id',
        'appointment_date',
        'appointment_time',
        'reason',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'appointment_time' => 'datetime:H:i',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function healthCenter()
    {
        return $this->belongsTo(HealthCenter::class)->withTrashed();
    }

    public function service()
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }

    public function appointmentSchedule()
    {
        return $this->belongsTo(AppointmentSchedule::class);
    }

    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }

    /**
     * Patient intake for this appointment.
     */
    public function patientIntake(): HasOne
    {
        return $this->hasOne(PatientIntake::class);
    }
}