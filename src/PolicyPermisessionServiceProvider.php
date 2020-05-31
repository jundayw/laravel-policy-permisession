<?php

namespace Jundayw\LaravelPolicyPermisession;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Compilers\BladeCompiler;
use Jundayw\LaravelPolicyPermisession\Policies\PermissionPolicy;

class PolicyPermisessionServiceProvider extends AuthServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/permission.php',
            'permission'
        );

        $this->registerBladeExtensions();
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/permission.php' => config_path('permission.php'),
            ], 'permission-config');
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'permission-migrations');
            $this->publishes([
                __DIR__ . '/../database/seeds' => database_path('seeds'),
            ], 'permission-seeders');
        }

        foreach (app('config')->get('permission.guards') as $guard) {
            $this->policies[$guard] = PermissionPolicy::class;
        }
        $this->registerPolicies();
    }

    public function registerBladeExtensions()
    {
        $this->app->afterResolving('blade.compiler', function(BladeCompiler $bladeCompiler) {
            $bladeCompiler->if('permission', function($permission, $auth = 'admin') {
                return app(\Illuminate\Contracts\Auth\Access\Gate::class)->check($permission, $auth);
            });
            $bladeCompiler->if('permissions', function($permission, $auth = 'admin') {
                return app(\Illuminate\Contracts\Auth\Access\Gate::class)->any($permission, $auth);
            });
        });
    }
}