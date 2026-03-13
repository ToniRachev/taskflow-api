<?php

namespace App\Enums\V1;

enum TaskPriorityEnum: string
{
    case CRITICAL = 'critical';
    case HIGH = 'high';
    case MEDIUM = 'medium';
    case LOW = 'low';
}
