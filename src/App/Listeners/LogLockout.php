<?php

namespace Dimer47\LaravelActivityTracker\App\Listeners;

use Illuminate\Auth\Events\Lockout;
use Dimer47\LaravelActivityTracker\App\Http\Traits\ActivityLogger;

class LogLockout
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
     * @param Lockout $event
     *
     * @return void
     */
    public function handle(Lockout $event)
    {
        if (config('LaravelActivityTracker.logLockOut')) {
            $this->activity(trans('LaravelActivityTracker::laravel-activity-tracker.listenerTypes.lockout'));
        }
    }
}
