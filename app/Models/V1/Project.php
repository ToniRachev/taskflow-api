<?php

namespace App\Models\V1;

use App\Enums\V1\ProjectStatusEnum;
use App\Enums\V1\ProjectVisibilityEnum;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'name',
        'key',
        'description',
        'start_date',
        'end_date'
    ];

    protected $attributes = [
        'status' => ProjectStatusEnum::ACTIVE->value,
        'visibility' => ProjectVisibilityEnum::PRIVATE->value,
    ];

    protected function casts(): array
    {
        return [
            'status' => ProjectStatusEnum::class,
            'visibility' => ProjectVisibilityEnum::class,
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
