<?php

namespace App\Enums\V1;

enum PlanEnum: string
{
    case FREE = 'free';
    case PRO = 'pro';
    case ENTERPRISE = 'enterprise';
}
