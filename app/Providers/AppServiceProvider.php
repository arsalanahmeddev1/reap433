<?php

namespace App\Providers;

use App\View\Composers\CartComposer;
use App\Models\ProductCustomization;
use App\Policies\ProductCustomizationPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(ProductCustomization::class, ProductCustomizationPolicy::class);
        View::composer('layouts.web.partials.header', CartComposer::class);
    }
}
