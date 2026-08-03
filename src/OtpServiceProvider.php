<?php

namespace Itsmurumba\Otp;

use Illuminate\Support\ServiceProvider;
use Itsmurumba\Otp\Services\OtpService;
use Itsmurumba\Otp\Channels\EmailChannel;
use Itsmurumba\Otp\Channels\SlackChannel;
use Itsmurumba\Otp\Channels\WhatsAppChannel;
use Itsmurumba\Otp\Channels\TelegramChannel;
use Itsmurumba\Otp\Generators\NumericGenerator;
use Itsmurumba\Otp\Generators\AlphanumericGenerator;
use Itsmurumba\Otp\Console\InstallOtpPackage;

class OtpServiceProvider extends ServiceProvider
{
    /**
     * Publishes all the config file this package needs to function
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/otp.php' => config_path('otp.php'),
            ], 'otp-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/otp'),
            ], 'otp-views');

            $this->publishes([
                __DIR__.'/database/migrations' => database_path('migrations'),
            ], 'otp-migrations');

            $this->commands([
                InstallOtpPackage::class,
            ]);
        }

        $this->mergeConfigFrom(
            __DIR__.'/../config/otp.php', 'otp'
        );

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'otp');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    /**
     * Register the application services
     */
    public function register()
    {
        $this->app->singleton('otp', function ($app) {
            $generator = match (config('otp.generator')) {
                'alphanumeric' => new AlphanumericGenerator(),
                default => new NumericGenerator(),
            };

            $service = new OtpService($generator);
            $service->registerChannel(new EmailChannel());
            $service->registerChannel(new SlackChannel());
            $service->registerChannel(new WhatsAppChannel());
            $service->registerChannel(new TelegramChannel());
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
