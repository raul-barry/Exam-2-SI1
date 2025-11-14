<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        // Evitar ejecutar lógica de BD si las credenciales no están disponibles
        if (empty(env('DB_HOST')) || empty(env('DB_USERNAME'))) {
            return; // Estamos en build o sin conexión real
        }

        if (config('database.default') === 'pgsql') {
            $schema = config('database.connections.pgsql.search_path');

            try {
                DB::statement("CREATE SCHEMA IF NOT EXISTS {$schema}");
                DB::statement("SET search_path TO {$schema}");
            } catch (\Exception $e) {
                // Ignorar errores en runtime, no romper despliegue
            }

            Schema::defaultStringLength(255);
        }
    }
}
