<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Superadmin can everything
        Gate::before(function ($user, $ability) {
            if ($user->role === 'superadmin') {
                return true;
            }
        });

        Gate::define('superadmin', function ($user) {
            return $user->role === 'superadmin';
        });

        Gate::define('admin', function ($user) {
            return $user->role === 'Admin';
        });

        // ...other gates if needed...
    }
}
