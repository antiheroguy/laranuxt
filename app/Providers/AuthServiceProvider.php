<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Passport::tokensExpireIn(now()->addSeconds(config('setting.short_lived_token_lifetime')));

        Passport::refreshTokensExpireIn(now()->addSeconds(config('setting.long_lived_token_lifetime')));

        Passport::personalAccessTokensExpireIn(now()->addSeconds(config('setting.long_lived_token_lifetime')));

        Gate::before(function ($user, $ability) {
            return $user->hasRole(Role::ADMIN) ? true : null;
        });
    }
}
