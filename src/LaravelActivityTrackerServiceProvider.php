<?php

namespace Dimer47\LaravelActivityTracker;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Dimer47\LaravelActivityTracker\App\Http\Middleware\LogActivity;

class LaravelActivityTrackerServiceProvider extends ServiceProvider
{
    const DISABLE_DEFAULT_ROUTES_CONFIG = 'laravel-activity-tracker.disableRoutes';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The event listener mappings for the applications auth scafolding.
     *
     * @var array
     */
    protected $listeners = [

        'Illuminate\Auth\Events\Attempting' => [
            'Dimer47\LaravelActivityTracker\App\Listeners\LogAuthenticationAttempt',
        ],

        'Illuminate\Auth\Events\Authenticated' => [
            'Dimer47\LaravelActivityTracker\App\Listeners\LogAuthenticated',
        ],

        'Illuminate\Auth\Events\Login' => [
            'Dimer47\LaravelActivityTracker\App\Listeners\LogSuccessfulLogin',
        ],

        'Illuminate\Auth\Events\Failed' => [
            'Dimer47\LaravelActivityTracker\App\Listeners\LogFailedLogin',
        ],

        'Illuminate\Auth\Events\Logout' => [
            'Dimer47\LaravelActivityTracker\App\Listeners\LogSuccessfulLogout',
        ],

        'Illuminate\Auth\Events\Lockout' => [
            'Dimer47\LaravelActivityTracker\App\Listeners\LogLockout',
        ],

        'Illuminate\Auth\Events\PasswordReset' => [
            'Dimer47\LaravelActivityTracker\App\Listeners\LogPasswordReset',
        ],

    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $router->middlewareGroup('activity', [LogActivity::class]);
        $this->loadTranslationsFrom(__DIR__.'/resources/lang/', 'LaravelActivityTracker');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        if (file_exists(config_path('laravel-activity-tracker.php'))) {
            $this->mergeConfigFrom(config_path('laravel-activity-tracker.php'), 'LaravelActivityTracker');
        } else {
            $this->mergeConfigFrom(__DIR__.'/config/laravel-activity-tracker.php', 'LaravelActivityTracker');
        }

        if (config(self::DISABLE_DEFAULT_ROUTES_CONFIG) == false) {
            $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        }

        $this->loadViewsFrom(__DIR__.'/resources/views/', 'LaravelActivityTracker');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->registerEventListeners();
        $this->publishFiles();
    }

    /**
     * Get the list of listeners and events.
     *
     * @return array
     */
    private function getListeners()
    {
        return $this->listeners;
    }

    /**
     * Register the list of listeners and events.
     *
     * @return void
     */
    private function registerEventListeners()
    {
        $listeners = $this->getListeners();
        foreach ($listeners as $listenerKey => $listenerValues) {
            foreach ($listenerValues as $listenerValue) {
                Event::listen(
                    $listenerKey,
                    $listenerValue
                );
            }
        }
    }

    /**
     * Publish files for Laravel Activity Tracker.
     *
     * @return void
     */
    private function publishFiles()
    {
        $publishTag = 'LaravelActivityTracker';

        $this->publishes([
            __DIR__.'/config/laravel-activity-tracker.php' => base_path('config/laravel-activity-tracker.php'),
        ], $publishTag);

        $this->publishes([
            __DIR__.'/resources/views' => base_path('resources/views/vendor/'.$publishTag),
        ], $publishTag);

        $this->publishes([
            __DIR__.'/resources/lang' => base_path('resources/lang/vendor/'.$publishTag),
        ], $publishTag);
    }
}
