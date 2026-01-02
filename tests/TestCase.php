<?php

namespace Dimer47\LaravelActivityTracker\Tests;

use Dimer47\LaravelActivityTracker\LaravelActivityTrackerServiceProvider;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create users table for testing
        Schema::create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Run migrations for testing
        $this->loadMigrationsFrom(__DIR__ . '/../src/database/migrations');
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelActivityTrackerServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function defineEnvironment($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Set up Laravel Activity Tracker configuration
        $app['config']->set('LaravelActivityTracker.defaultActivityModel', \Dimer47\LaravelActivityTracker\App\Models\Activity::class);
        $app['config']->set('LaravelActivityTracker.defaultUserModel', \Dimer47\LaravelActivityTracker\Tests\Fixtures\User::class);
        $app['config']->set('LaravelActivityTracker.defaultUserIDField', 'id');
        $app['config']->set('LaravelActivityTracker.enableDateFiltering', true);
        $app['config']->set('LaravelActivityTracker.enableExport', true);
        $app['config']->set('LaravelActivityTracker.enableSearch', true);
        $app['config']->set('LaravelActivityTracker.loggerPaginationEnabled', false);
        $app['config']->set('LaravelActivityTracker.loggerPaginationPerPage', 25);
        $app['config']->set('LaravelActivityTracker.searchFields', 'description,user,method,route,ip');
        $app['config']->set('LaravelActivityTracker.loggerDatabaseTable', 'laravel_activity_tracker');
        $app['config']->set('LaravelActivityTracker.loggerDatabaseConnection', 'testing');
        $app['config']->set('LaravelActivityTracker.loggerMiddlewareEnabled', true);
        $app['config']->set('LaravelActivityTracker.loggerMiddlewareExcept', []);
        $app['config']->set('LaravelActivityTracker.rolesEnabled', false);
        $app['config']->set('LaravelActivityTracker.rolesMiddlware', 'role:admin');
        $app['config']->set('LaravelActivityTracker.disableRoutes', false);
        $app['config']->set('LaravelActivityTracker.loggerDatatables', false);
        $app['config']->set('LaravelActivityTracker.enableSubMenu', true);
        $app['config']->set('LaravelActivityTracker.enableDrillDown', true);
        $app['config']->set('LaravelActivityTracker.logDBActivityLogFailuresToFile', true);
        $app['config']->set('LaravelActivityTracker.enablePackageFlashMessageBlade', true);
        $app['config']->set('LaravelActivityTracker.loggerBladeExtended', 'layouts.app');
        $app['config']->set('LaravelActivityTracker.bootstapVersion', '4');
        $app['config']->set('LaravelActivityTracker.bootstrapCardClasses', '');
        $app['config']->set('LaravelActivityTracker.bladePlacement', 'yield');
        $app['config']->set('LaravelActivityTracker.bladePlacementCss', 'template_linked_css');
        $app['config']->set('LaravelActivityTracker.bladePlacementJs', 'footer_scripts');
        $app['config']->set('LaravelActivityTracker.enableLiveSearch', true);

        // GeoPlugin configuration
        $app['config']->set('LaravelActivityTracker.enableGeoPlugin', true);
        $app['config']->set('LaravelActivityTracker.geoPluginUrl', 'http://www.geoplugin.net/json.gp?ip=');
    }
}
