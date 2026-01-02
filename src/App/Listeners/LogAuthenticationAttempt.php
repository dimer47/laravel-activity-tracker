<?php

namespace Dimer47\LaravelActivityTracker\App\Listeners;

use Illuminate\Auth\Events\Attempting;
use Dimer47\LaravelActivityTracker\App\Http\Traits\ActivityLogger;

class LogAuthenticationAttempt
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
     * @param Attempting $event
     *
     * @return void
     */
    public function handle(Attempting $event)
    {
        if (config('LaravelActivityTracker.logAuthAttempts')) {
            $this->activity(trans('LaravelActivityTracker::laravel-activity-tracker.listenerTypes.attempt'));
        }
    }
}
