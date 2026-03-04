<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $fillable = [
        'bio',
        'phone',
        'github_url',
        'preferences',
    ];

    protected function casts(): array
    {
        return [
            'preferences' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function defaultPreferences(): array
    {
        return [
            'preferences' => [
                'theme' => 'system',
                'language' => 'en',
                'notifications' => [
                    'email' => true,
                    'in_app' => true,
                    'task_assigned' => true,
                    'mentioned' => true,
                    'due_soon' => true
                ]
            ]
        ];
    }
}
