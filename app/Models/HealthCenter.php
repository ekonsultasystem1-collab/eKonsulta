<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HealthCenter extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'barangay',
        'address',
        'contact_number',
        'email',
        'operating_hours',
        'status',
    ];

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(
            Service::class,
            'health_center_service'
        )->withTimestamps();
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }
}