<?php

namespace App\Enums\V1;

enum ProjectStatusEnum: string
{
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';
    case COMPLETED = 'completed';
}
