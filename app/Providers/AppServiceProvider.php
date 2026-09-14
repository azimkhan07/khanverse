<?php

namespace App\Providers;

use App\Contracts\AiServiceContract;
use App\Services\Ai\AiService;
use App\Services\Payments\OrderPaymentService;
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
        // Payments ------------------------------------------------
        $this->app->singleton(OrderPaymentService::class);

        // AI ------------------------------------------------------
        $this->app->singleton(AiServiceContract::class, AiService::class);
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