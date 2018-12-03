<?php
namespace ImageResizer;
use Illuminate\Support\ServiceProvider;
class LaratrustServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
		$this->publishes([__DIR__.'/config/imageresizer.php' => config_path('imageresizer.php')], 'imageresizer');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
		$this->mergeConfigFrom(__DIR__.'/config/imageresizer.php','imageresizer');
		$this->app->bind('imageresizer', function ($app) {
            return new ImageResizer($app);
        });
    }
}
