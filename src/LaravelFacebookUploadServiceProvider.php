<?php namespace pouu69\LaravelFacebookUpload;

use Illuminate\Support\ServiceProvider;

class LaravelFacebookUploadServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the service providers.
     *
     * @return void
     */
    public function register()
    {
        // Main Service
        $this->app->bind('pouu69\LaravelFacebookUpload\LaravelFacebookUpload', function ($app) {
            return new LaravelFacebookUpload();
        });
    }
}
