<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'health_center_id',
        'employee_number',
        'position',
        'contact_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function healthCenter()
    {
        return $this->belongsTo(HealthCenter::class)->withTrashed();
    }
}