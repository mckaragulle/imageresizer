<?php
namespace Karagulle\ImageResizer;
use Illuminate\Support\ServiceProvider;
class ImageResizerServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        //config publishing
        $this->publishes([__DIR__.'/config/imageresizer.php' => config_path('imageresizer.php')], 'imageresizer');
        //commands publishing
        if ($this->app->runningInConsole()) {
            $this->commands([ImageResizer::class]);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
		/*$this->app->bind('imageresizer', function() {
            return new ImageResizer();
        });

        $this->mergeConfigFrom(__DIR__ . '/config/imageresizer.php', 'imageresizer');*/
    }
}
