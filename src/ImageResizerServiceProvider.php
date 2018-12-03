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
		$this->publishes(
            // Keep the config_path() empty, so the config file will be published directly to the config directory
                [__DIR__ . '/config' => config_path()],
                'config'
            );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
		$this->app->bind('imageresizer', function() {
            return new ImageResizer();
        });

        $this->mergeConfigFrom(__DIR__ . '/config/imageresizer.php', 'imageresizer');
    }
}
