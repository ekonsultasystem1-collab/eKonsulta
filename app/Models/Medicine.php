<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'health_center_id',
        'name',
        'generic_name',
        'dosage_form',
        'strength',
        'stock_quantity',
        'unit',
        'expiration_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'expiration_date' => 'date',
            'stock_quantity' => 'integer',
        ];
    }

    public function healthCenter()
    {
        return $this->belongsTo(HealthCenter::class)->withTrashed();
    }

    public function prescriptionItems()
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}