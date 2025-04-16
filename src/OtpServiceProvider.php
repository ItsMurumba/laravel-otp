<?php

namespace Itsmurumba\Otp;

use Illuminate\Support\ServiceProvider;
use Itsmurumba\Otp\Services\OtpService;
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

        $this->mergeConfigFrom(
            __DIR__.'/../config/otp.php', 'otp'
        );
    }

    /**
     * Register the application services
     */
    public function register()
    {
        $this->app->singleton('otp', function ($app) {
            return new OtpService();
        });
    }

    /**
     * Get the services provided by the provider
     *
     * @return array
     */
    public function provides()
    {
        return ['otp'];
    }
}
