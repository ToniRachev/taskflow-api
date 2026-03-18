<?php

namespace App\Models\V1;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
    use HasUuid, HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'is_default'
    ];

    protected $attributes = [
        'is_default' => false
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public static function defaultInit(bool $isDefault): array
    {
        return [
            'name' => 'Board',
            'description' => null,
            'is_default' => $isDefault
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function columns(): HasMany
    {
        return $this->hasMany(Column::class);
    }
}
