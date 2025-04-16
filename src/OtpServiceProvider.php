<?php

namespace Itsmurumba\Otp;

use Illuminate\Support\ServiceProvider;
use Itsmurumba\Otp\Services\OtpService;
use Itsmurumba\Otp\Channels\EmailChannel;
use Itsmurumba\Otp\Channels\SlackChannel;
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
                __DIR__.'/../resources/views' => resource_path('views/vendor/otp'),
            ], 'otp-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'otp-migrations');

            $this->commands([
                InstallOtpPackage::class,
            ]);
        }

        $this->mergeConfigFrom(
            __DIR__.'/../config/otp.php', 'otp'
        );

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'otp');
    }

    /**
     * Register the application services
     */
    public function register()
    {
        $this->app->singleton('otp', function ($app) {
            $service = new OtpService();
            $service->registerChannel(new EmailChannel());
            $service->registerChannel(new SlackChannel());
            return $service;
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
