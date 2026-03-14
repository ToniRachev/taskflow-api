<?php

namespace App\Enums\V1;

enum TaskTypeEnum: string
{
    case TASK = 'task';
    case BUG = 'bug';
    case STORY = 'story';
    case EPIC = 'epic';
}
