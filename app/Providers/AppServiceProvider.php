<?php

namespace App\Providers;

use App\Interfaces\StorageMedium;
use Illuminate\Support\ServiceProvider;
use App\Services\StorageMediums\GoogleSheet\Service;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        # ensure you configure the right channel you use
        config(['logging.channels.single.path' => \Phar::running()
            ? dirname(\Phar::running(false)) . ('/storage/logs/production.log')
            : storage_path('logs/laravel.log')
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(StorageMedium::class, Service::class);
    }
}
