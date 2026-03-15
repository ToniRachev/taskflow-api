<?php

namespace App\Enums\V1;

enum ActivityLogEventEnum: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case STATUS_CHANGED = 'status_changed';
    case ASSIGNED = 'assigned';
}
