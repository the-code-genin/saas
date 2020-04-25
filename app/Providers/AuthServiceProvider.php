<?php

namespace App\Providers;

use App\Guards\ApiGuard;
use App\Models\Job;
use App\Models\StudentHire;
use App\Policies\JobPolicy;
use App\Policies\StudentHirePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Job::class => JobPolicy::class,
        StudentHire::class => StudentHirePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::extend('auth_token', function ($app, $name, array $config) {
            return new ApiGuard(Auth::createUserProvider($config['provider']), $this->app->make('request'));
        });
    }
}
