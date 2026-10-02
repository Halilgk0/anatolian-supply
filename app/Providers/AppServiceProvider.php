<?php

namespace App\Providers;

use App\Catalog\DatabaseMediaAdapter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

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
        Route::resourceVerbs(['create' => 'yeni', 'edit' => 'duzenle']);

        Storage::extend('database', function (Application $app, array $config): FilesystemAdapter {
            $adapter = new DatabaseMediaAdapter($app['db']->connection($config['connection'] ?? null), $config['table'], $config['url']);

            return new FilesystemAdapter(new Filesystem($adapter, $config), $adapter, $config);
        });
    }
}
