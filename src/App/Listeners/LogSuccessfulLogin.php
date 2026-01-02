<?php

namespace Dimer47\LaravelActivityTracker\App\Listeners;

use Illuminate\Auth\Events\Login;
use Dimer47\LaravelActivityTracker\App\Http\Traits\ActivityLogger;

class LogSuccessfulLogin
{
    use ActivityLogger;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param Login $event
     *
     * @return void
     */
    public function handle(Login $event)
    {
        if (config('LaravelActivityTracker.logSuccessfulLogin')) {
            $this->activity(trans('LaravelActivityTracker::laravel-activity-tracker.listenerTypes.login'));
        }
    }
}
