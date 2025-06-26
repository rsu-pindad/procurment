<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PowerComponents\LivewirePowerGrid\Button;

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
        // (opsional) set locale Laravel juga
        \App::setLocale('id');

        Button::macro('navigate', function () {
            $this->attributes([
                'wire:navigate' => true
            ]);

            return $this;
        });
    }
}
