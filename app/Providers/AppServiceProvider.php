<?php

namespace App\Providers;

use App\Models\V1\Organization;
use App\Models\V1\Project;
use App\Models\V1\User;
use App\Observers\V1\UserObserver;
use App\Policies\V1\OrganizationPolicy;
use App\Policies\V1\ProjectPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });
    }
}
