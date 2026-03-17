<?php

namespace App\Observers\V1;

use App\Models\V1\Board;
use App\Models\V1\Project;

class ProjectObserver
{
    public function created(Project $project): void
    {
        $project->boards()->create(Board::defaultInit(true));
    }
}
