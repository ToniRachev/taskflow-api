<?php

namespace App\Models\V1;

use App\Enums\OrganizationMembershipRoleEnum;
use Illuminate\Database\Eloquent\Model;

class OrganizationMembership extends Model
{
    protected $fillable = [
        'role'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];
}
