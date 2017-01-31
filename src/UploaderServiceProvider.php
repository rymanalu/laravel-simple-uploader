<?php

namespace Rymanalu\LaravelSimpleUploader;

use Illuminate\Support\ServiceProvider;

class UploaderServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Boot the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/uploader.php' => config_path('uploader.php'),
            ], 'uploader');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('uploader', function ($app) {
            return new UploaderManager($app);
        });

        $this->app->alias('uploader', 'Rymanalu\LaravelSimpleUploader\Contracts\Factory');

        $this->app->singleton('uploader.from', function ($app) {
            return $app['uploader']->from();
        });

        $this->app->alias('uploader.from', 'Rymanalu\LaravelSimpleUploader\Contracts\Uploader');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['uploader', 'uploader.from'];
    }
}
