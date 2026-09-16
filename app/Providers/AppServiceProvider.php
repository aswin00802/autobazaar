<?php

namespace App\Providers;

use App\Interfaces\AutoInterface;
use App\Repositories\AutoRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Api\V1\SoldAutoController type-hints the interface (GET api/get-sold-auto).
        $this->app->bind(AutoInterface::class, AutoRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
