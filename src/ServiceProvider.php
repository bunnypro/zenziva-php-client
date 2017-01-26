<?php namespace Bunnypro\Zenziva;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Client::class, function($app) {
            $config = @$app['config']['zenziva'] ?: [];

            return new Client($config);
        });

        $this->publishes([
            __DIR__.'/config/zenziva.php' => config_path('zenziva.php'),
        ]);
    }
}