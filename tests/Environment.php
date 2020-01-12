<?php

declare(strict_types=1);

namespace Orchid\Tests;

use DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator;
use DaveJamesMiller\Breadcrumbs\BreadcrumbsManager;
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Orchid\Database\Seeds\OrchidDatabaseSeeder;
use Orchid\Platform\Models\User;
use Orchid\Platform\Providers\FoundationServiceProvider;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Dashboard;
use Orchid\Tests\Exemplar\ExemplarServiceProvider;
use Watson\Active\Active;

/**
 * Trait Environment.
 */
trait Environment
{
    /**
     * Setup the test environment.
     * Run test: php vendor/bin/phpunit --coverage-html ./logs/coverage ./tests
     * Run 1 test:  php vendor/bin/phpunit  --filter= UserTest tests\\Unit\\Platform\\UserTest --debug.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('orchid:install');

        $this->loadMigrations();

        $this->withFactories(Dashboard::path('database/factories'));

        $this->artisan('db:seed', [
            '--class' => OrchidDatabaseSeeder::class,
        ]);

        $this->artisan('orchid:admin', [
            'name'     => 'admin',
            'email'    => 'admin@admin.com',
            'password' => 'password',
        ]);
    }

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        /** @var Repository $config */
        $config = $app['config'];

        $config->set('app.debug', true);
        $config->set('auth.providers.users.model', User::class);

        // set up database configuration
        $config->set('database.connections.orchid', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $config->set('scout.driver', null);
        $config->set('database.default', 'orchid');

        $config->set('breadcrumbs', [
            'view'                                     => 'breadcrumbs::bootstrap4',
            'files'                                    => base_path('routes/breadcrumbs.php'),
            'unnamed-route-exception'                  => false,
            'missing-route-bound-breadcrumb-exception' => false,
            'invalid-named-breadcrumb-exception'       => false,
            'manager-class'                            => BreadcrumbsManager::class,
            'generator-class'                          => BreadcrumbsGenerator::class,
        ]);
    }

    /**
     * @return array
     */
    protected function getPackageProviders(): array
    {
        return [
            FoundationServiceProvider::class,
            ExemplarServiceProvider::class,
        ];
    }

    /**
     * @return array
     */
    protected function getPackageAliases(): array
    {
        return [
            'Alert'       => Alert::class,
            'Active'      => Active::class,
            'Breadcrumbs' => Breadcrumbs::class,
            'Dashboard'   => Dashboard::class,
        ];
    }

    /**
     * Load the migrations for the test environment.
     *
     * @return void
     */
    protected function loadMigrations(): void
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom([
            '--database' => 'sqlite',
            '--realpath' => realpath('./database/migrations'),
        ]);

        $this->artisan('migrate', ['--database' => 'orchid']);
    }
}
