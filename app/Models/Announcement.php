<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image_path',
        'health_center_id',
        'created_by',
        'published_at',
        'event_start_date',
        'event_end_date',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'event_start_date' => 'date',
            'event_end_date' => 'date',
            'is_published' => 'boolean',
        ];
    }

    public function healthCenter(): BelongsTo
    {
        return $this->belongsTo(HealthCenter::class)->withTrashed();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeVisibleToUser($query, ?User $user = null)
    {
        $query->where('is_published', true)
            ->where(function ($dates) {
                $dates->whereNull('event_start_date')
                    ->orWhereDate('event_start_date', '<=', today());
            })
            ->where(function ($dates) {
                $dates->whereNull('event_end_date')
                    ->orWhereDate('event_end_date', '>=', today());
            })
            ->where(function ($scope) use ($user) {
                $scope->whereNull('health_center_id');

                if ($user && $user->isPatient() && $user->patient) {
                    $scope->orWhere('health_center_id', $user->patient->health_center_id);
                }

                if ($user && $user->isStaff() && $user->staff) {
                    $scope->orWhere('health_center_id', $user->staff->health_center_id);
                }
            });

        return $query;
    }

    public function scopeCurrentlyVisible($query)
    {
        return $query->where('is_published', true)
            ->where(function ($dates) {
                $dates->whereNull('event_start_date')
                    ->orWhereDate('event_start_date', '<=', today());
            })
            ->where(function ($dates) {
                $dates->whereNull('event_end_date')
                    ->orWhereDate('event_end_date', '>=', today());
            });
    }

    public function publish(): void
    {
        if ($this->is_published) {
            return;
        }

        $this->forceFill([
            'is_published' => true,
            'published_at' => $this->published_at ?? now(),
        ])->save();

        $this->sendNotifications();
    }

    public function unpublish(): void
    {
        $this->forceFill([
            'is_published' => false,
            'published_at' => null,
        ])->save();
    }

    public function sendNotifications(): void
    {
        $userIds = [];

        if (is_null($this->health_center_id)) {
            $userIds = User::whereIn('role', ['patient', 'staff'])
                ->pluck('id')
                ->all();
        } else {
            $userIds = array_values(array_unique(array_merge(
                Patient::where('health_center_id', $this->health_center_id)
                    ->pluck('user_id')
                    ->filter()
                    ->all(),
                Staff::where('health_center_id', $this->health_center_id)
                    ->pluck('user_id')
                    ->filter()
                    ->all()
            )));
        }

        foreach ($userIds as $userId) {
            Notification::firstOrCreate(
                [
                    'user_id' => $userId,
                    'announcement_id' => $this->id,
                    'type' => 'announcement',
                ],
                [
                    'title' => 'New Health Center Announcement',
                    'message' => ($this->health_center_id
                            ? ($this->healthCenter?->name ?? 'Health Center')
                            : 'City Health Office')
                        . ': ' . $this->title,
                    'read_at' => null,
                ]
            );
        }
    }
}
