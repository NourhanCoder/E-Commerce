<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Enums\UserType;
use App\View\Composers\NavComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Gate for admin access
        Gate::define('admin-control', function (User $user) {
            return $user->type === UserType::Admin;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        App::setLocale('ar'); //language
        Paginator::useBootstrap(); 

       // Data needed by the navbar (categories and cart)
       View::composer('website.layouts.nav', NavComposer::class);
    }
}
