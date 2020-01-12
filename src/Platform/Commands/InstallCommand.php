<?php

declare(strict_types=1);

namespace Orchid\Platform\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Orchid\Platform\Events\InstallEvent;
use Orchid\Platform\Providers\FoundationServiceProvider;
use Orchid\Platform\Updates;

class InstallCommand extends Command
{
    use DetectsApplicationNamespace;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'orchid:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish files for ORCHID and install package';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $updates = new Updates();
        $updates->updateInstall();

        $this->info('Installation started. Please wait...');
        $this->info("Version: $updates->currentVersion");

        $this
            ->executeCommand('migrate')
            ->executeCommand('vendor:publish', [
                '--force' => true,
                '--tag'   => 'migrations',
            ])
            ->executeCommand('vendor:publish', [
                '--provider' => FoundationServiceProvider::class,
                '--force'    => true,
                '--tag'      => [
                    'config',
                    'migrations',
                    'orchid-stubs',
                ],
            ])
            ->executeCommand('migrate')
            ->executeCommand('storage:link');

        $this->registerOrchidServiceProvider();
        $this->info('Completed!');

        $this
            ->setValueEnv('SCOUT_DRIVER')
            ->comment("To create a user, run 'artisan orchid:admin'");

        $this->line("To start the embedded server, run 'artisan serve'");

        event(new InstallEvent($this));
    }

    /**
     * @param string $command
     * @param array  $parameters
     *
     * @return $this
     */
    private function executeCommand(string $command, array $parameters = []): self
    {
        try {
            $result = $this->call($command, $parameters);
        } catch (\Exception $exception) {
            $result = 1;
            $this->alert($exception->getMessage());
        }

        if ($result) {
            $parameters = http_build_query($parameters, '', ' ');
            $parameters = str_replace('%5C', '/', $parameters);
            $this->alert("An error has occurred. The '{$command} {$parameters}' command was not executed");
        }

        return $this;
    }

    /**
     * @param string $constant
     * @param string $value
     *
     * @return InstallCommand
     */
    private function setValueEnv(string $constant, string $value = 'null'): self
    {
        $str = $this->fileGetContent(app_path('../.env'));

        if ($str !== false && strpos($str, $constant) === false) {
            file_put_contents(app_path('../.env'), $str.PHP_EOL.$constant.'='.$value.PHP_EOL);
        }

        return $this;
    }

    /**
     * @param string $file
     *
     * @return false|string
     */
    private function fileGetContent(string $file)
    {
        if (! is_file($file)) {
            return '';
        }

        return file_get_contents($file);
    }

    /**
     * Register the Orchid service provider in the application configuration file.
     *
     * @return void
     */
    protected function registerOrchidServiceProvider()
    {
        /*
        $namespace = Str::replaceLast('\\', '', $this->getAppNamespace());
        $config = file_get_contents(config_path('app.php'));

        if (Str::contains($config, "{$namespace}\Providers\OrchidServiceProvider::class")) {
            return;
        }

        */
        /*
        file_put_contents(config_path('app.php'), str_replace(
            "{$namespace}\\Providers\EventServiceProvider::class,".PHP_EOL,
            "{$namespace}\\Providers\EventServiceProvider::class,".PHP_EOL."        {$namespace}\Providers\OrchidServiceProvider::class,".PHP_EOL,
            $config
        ));
        */
    }
}
