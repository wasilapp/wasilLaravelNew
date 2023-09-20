<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /* if(str_contains(request()->url(),'/ar')){
           $this->app->bind('path.lang', function() { return base_path().'/resources/ar/lang'; });
        }
        else{
           $this->app->bind('path.lang', function() { return base_path().'/resources/en/lang'; });
        } */
        // dd(base_path());
        
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);

        //
    }
}
