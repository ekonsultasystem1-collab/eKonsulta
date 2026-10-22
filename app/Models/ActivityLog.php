<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record(
        int $userId,
        string $action,
        string $description
    ): self {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
        ]);
    }
}