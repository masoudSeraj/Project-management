<?php

namespace App\Providers;

use Database\Factories\TaskFactory;
use Illuminate\Support\ServiceProvider;
use App\Services\DateService\DateService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app()->bind(TaskFactory::class, function() {
        //     return new TaskFactory(DateService::class);
        // });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
