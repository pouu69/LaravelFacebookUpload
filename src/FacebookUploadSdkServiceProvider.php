<?php namespace KwanUng\FacebookUploadSdk;

use Illuminate\Support\ServiceProvider;

class FacebookUploadSdkServiceProvider extends ServiceProvider
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
        $this->app->bind('KwanUng\FacebookUploadSdk\FacebookUploadSdk', function ($app) {
            return new FacebookUploadSdk();
        });
    }
}
