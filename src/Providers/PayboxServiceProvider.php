<?php

namespace Bnb\PayboxGateway\Providers;

use Illuminate\Support\ServiceProvider;

class PayboxServiceProvider extends ServiceProvider
{

    /**
     * Register service provider.
     */
    public function register()
    {
        // merge module config if it's not published or some entries are missing 
        $this->mergeConfigFrom($this->configFile(), 'paybox');

        // run migrations
        if ( ! method_exists($this, 'loadMigrationsFrom')) {
            $this->publishes([
                realpath(__DIR__ . '/../../migrations') => $this->app['path.base'] . DIRECTORY_SEPARATOR
                    . 'database' . DIRECTORY_SEPARATOR . 'migrations',
            ], 'migrations');
        } else {
            $this->loadMigrationsFrom(__DIR__ . '/../../migrations');
        }

        // publish configuration file
        $this->publishes([
            $this->configFile() => $this->app['path.config'] . DIRECTORY_SEPARATOR . 'paybox.php',
        ], 'config');

        $this->publishes([
            realpath(__DIR__ . '/../../views') => $this->app['path.base'] . DIRECTORY_SEPARATOR
                . 'resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'paybox',
        ], 'views');
    }


    /**
     * Get module config file.
     *
     * @return string
     */
    protected function configFile()
    {
        return realpath(__DIR__ . '/../../config/paybox.php');
    }
}
