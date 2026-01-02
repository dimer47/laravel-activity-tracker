<?php

namespace Dimer47\LaravelActivityTracker\App\Listeners;

use Illuminate\Auth\Events\PasswordReset;
use Dimer47\LaravelActivityTracker\App\Http\Traits\ActivityLogger;

class LogPasswordReset
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
     * @param PasswordReset $event
     *
     * @return void
     */
    public function handle(PasswordReset $event)
    {
        if (config('LaravelActivityTracker.logPasswordReset')) {
            $this->activity(trans('LaravelActivityTracker::laravel-activity-tracker.listenerTypes.reset'));
        }
    }
}
