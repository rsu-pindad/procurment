<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PowerComponents\LivewirePowerGrid\Button;
use App\Models\Ajuan;
use App\Observers\AjuanObserver;

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
        \Illuminate\Support\Carbon::setLocale('id');
        \App::setLocale('id');

        Button::macro('navigate', function () {
            $this->attributes([
                'wire:navigate' => true
            ]);

            return $this;
        });

        Ajuan::observe(AjuanObserver::class);
    }
}
