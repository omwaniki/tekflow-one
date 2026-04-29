<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 🔐 SUPER ADMIN OVERRIDE (EXISTING LOGIC)
        |--------------------------------------------------------------------------
        */
        Gate::before(function ($user, $ability) {

            if ($user->email === 'onesmugendi@gmail.com') {
                return true;
            }

            if ($user->hasRole('admin')) {
                return true;
            }

            return null;
        });

        /*
        |--------------------------------------------------------------------------
        | 🎨 GLOBAL THEME (SAFE + DYNAMIC)
        |--------------------------------------------------------------------------
        */

        if (class_exists(\App\Models\Setting::class)) {
            try {
                $primary = Setting::where('key', 'primary_color')->value('value') ?? '#0f172a';
                $dark = Setting::where('key', 'primary_dark')->value('value') ?? '#020617';
                $light = Setting::where('key', 'primary_light')->value('value') ?? '#e5e7eb';
            } catch (\Exception $e) {
                $primary = '#0f172a';
                $dark = '#020617';
                $light = '#e5e7eb';
            }
        } else {
            $primary = '#0f172a';
            $dark = '#020617';
            $light = '#e5e7eb';
        }

        View::share('primaryColor', $primary);
        View::share('primaryDark', $dark);
        View::share('primaryLight', $light);
    }
}