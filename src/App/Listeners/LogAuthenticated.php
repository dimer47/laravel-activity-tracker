<?php

namespace Dimer47\LaravelActivityTracker\App\Listeners;

use Illuminate\Auth\Events\Authenticated;
use Dimer47\LaravelActivityTracker\App\Http\Traits\ActivityLogger;

class LogAuthenticated
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
     * Handle ANY authenticated event.
     *
     * @param Authenticated $event
     *
     * @return void
     */
    public function handle(Authenticated $event)
    {
        if (config('LaravelActivityTracker.logAllAuthEvents')) {
            $this->activity(trans('LaravelActivityTracker::laravel-activity-tracker.listenerTypes.auth'));
        }
    }
}
