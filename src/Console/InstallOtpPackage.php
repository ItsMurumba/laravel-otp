<?php

namespace Itsmurumba\Otp\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallOtpPackage extends Command
{
    protected $signature = 'otp:install';

    protected $description = 'Install OTP Laravel Package';

    public function handle()
    {
        $this->info('Installing Laravel OTP......');
        $this->info('Publishing otp configuration');

        if (! $this->configExists('otp.php')) {
            $this->publishConfiguration();
            $this->info('Publishing otp configuration');
        } else {
            if ($this->shouldOverwriteConfig()) {
                $this->info('Overwitting otp configuration file......');
                $this->publishConfiguration($force = true);
            } else {
                $this->info('Exiting. OTP configuration was not overwritten');
            }
        }

        $this->info('Installed OTP Package');
    }

    private function configExists($fileName)
    {
        return File::exists(config_path($fileName));
    }

    private function shouldOverwriteConfig()
    {
        return $this->confirm('Config file already exists. Do you want to overwrite it?', false);
    }

    private function publishConfiguration($forcePublish = false)
    {
        $params = [
            '--provider' => "Itsmurumba\Otp\OtpServiceProvider",
            '--tag' => 'otp-config',
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }
}
