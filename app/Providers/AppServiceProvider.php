<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
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
        if (DB::connection()->getDriverName() === 'sqlite') {
            $pdo = DB::connection()->getPdo();

            $pdo->sqliteCreateFunction('radians', fn($deg) => deg2rad($deg));
            $pdo->sqliteCreateFunction('sin', 'sin', 1);
            $pdo->sqliteCreateFunction('cos', 'cos', 1);
            $pdo->sqliteCreateFunction('sqrt', 'sqrt', 1);
            $pdo->sqliteCreateFunction('pow', 'pow', 2);
        }
    }
}
