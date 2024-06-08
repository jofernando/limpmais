<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Filament::registerScripts([
            'https://unpkg.com/@alpinejs/mask@3.x.x/dist/cdn.min.js',
        ], true);
        Filament::serving(function () {
            Filament::registerUserMenuItems([
                'update-password' => UserMenuItem::make()->url(route('password.update'))->label('Alterar senha'),
                // ...
            ]);
        });
    }
}
