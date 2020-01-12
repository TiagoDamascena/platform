<?php

declare(strict_types=1);

namespace Orchid\Tests;

use Faker\Factory as Faker;
use Faker\Generator;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\Dusk\Options;
use Orchestra\Testbench\Dusk\TestCase;
use Orchid\Platform\Models\User;

/**
 * Class TestConsoleCase.
 */
abstract class TestBrowserCase extends TestCase
{
    use Environment {
        Environment::setUp as setEnvUp;
        Environment::getEnvironmentSetUp as getEnvSetUp;
    }

    /**
     * @var User
     */
    private $user;

    /**
     * @var Generator
     */
    private $faker;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (env('GITHUB_TOKEN') !== null) {
            Options::withoutUI();
        }

        $this->faker = Faker::create();
        $this->setEnvUp();
        $this->artisan('migrate', ['--database' => 'orchid']);
        $this->artisan('orchid:install');
    }

    /**
     * Define environment setup.
     *
     * @param Application $app
     */
    protected function getEnvironmentSetUp(Application $app)
    {
        $this->getEnvSetUp($app);
        config()->set('database.connections.orchid', [
            'driver'                  => 'sqlite',
            'url'                     => env('DATABASE_URL'),
            'database'                => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix'                  => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ]);
    }

    /**
     * @return User
     */
    protected function createAdminUser(): User
    {
        if (is_null($this->user)) {
            $this->user = factory(User::class)->create();
        }

        return $this->user;
    }

    /**
     * @return Generator
     */
    protected function faker(): Generator
    {
        return $this->faker;
    }
}
