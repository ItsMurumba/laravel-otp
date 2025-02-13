<?php

namespace Itsmurumba\Otp;

use Illuminate\Support\ServiceProvider;
use Itsmurumba\Otp\Console\InstallOtpPackage;

class OtpServiceProvider extends ServiceProvider
{
    /**
     * Publishes all the config file this package needs to function
     */
    public function boot()
    {
        $config = realpath(__DIR__.'/../resources/config/otp.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $config => config_path('otp.php'),
            ], 'otp-config');

            $this->commands([
                InstallOtpPackage::class,
            ]);
        }
    }

    /**
     * Register the application services
     */
    public function register()
    {
        $this->app->bind('laravel-otp', function () {

            return new Otp();
        });
    }

    /**
     * Get the services provided by the provider
     *
     * @return array
     */
    public function provides()
    {

        return ['laravel-otp'];
    }
}
