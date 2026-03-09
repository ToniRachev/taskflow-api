<?php

namespace App\Models\V1;

use App\Enums\OrganizationMembershipRoleEnum;
use App\Enums\V1\PlanEnum;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'settings',
    ];

    protected $attributes = [
        'plan' => PlanEnum::FREE->value,
        'is_active' => true,
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'plan' => PlanEnum::class
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_memberships')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function hasAdminAccess(User $user): bool
    {
        return $this->members()
            ->where('user_id', $user->id)
            ->whereIn('role', [
                OrganizationMembershipRoleEnum::ADMIN->value,
                OrganizationMembershipRoleEnum::OWNER->value,
            ])->exists();
    }

    public function isMember(User $user): bool
    {
        return $this->members()
            ->where('user_id', $user->id)
            ->exists();
    }
}
