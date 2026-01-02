<?php

namespace Dimer47\LaravelActivityTracker\App\Listeners;

use Illuminate\Auth\Events\Logout;
use Dimer47\LaravelActivityTracker\App\Http\Traits\ActivityLogger;

class LogSuccessfulLogout
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
     * @param Logout $event
     *
     * @return void
     */
    public function handle(Logout $event)
    {
        if (config('LaravelActivityTracker.logSuccessfulLogout')) {
            $this->activity(trans('LaravelActivityTracker::laravel-activity-tracker.listenerTypes.logout'));
        }
    }
}
