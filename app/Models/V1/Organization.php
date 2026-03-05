<?php

namespace App\Models\V1;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasUuid;
    protected $fillable = [
        'name',
        'slug',
        'logo_url',
        'plan',
        'settings',
        'is_active'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];
}
