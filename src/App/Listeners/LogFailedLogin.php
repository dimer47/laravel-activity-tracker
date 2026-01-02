<?php

namespace Dimer47\LaravelActivityTracker\App\Listeners;

use Illuminate\Auth\Events\Failed;
use Dimer47\LaravelActivityTracker\App\Http\Traits\ActivityLogger;

class LogFailedLogin
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
     * @param Failed $event
     *
     * @return void
     */
    public function handle(Failed $event)
    {
        if (config('LaravelActivityTracker.logFailedAuthAttempts')) {
            $this->activity(trans('LaravelActivityTracker::laravel-activity-tracker.listenerTypes.failed'));
        }
    }
}
