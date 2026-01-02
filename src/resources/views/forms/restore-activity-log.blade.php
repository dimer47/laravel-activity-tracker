<form action="{{ route('restore-activity') }}" method="POST" class="mb-0">
    @csrf
    <button type="button" class="text-success dropdown-item" data-toggle="modal" data-target="#confirmRestore" data-title="{{ trans('LaravelActivityTracker::laravel-activity-tracker.modals.restoreLog.title') }}" data-message="{{ trans('LaravelActivityTracker::laravel-activity-tracker.modals.restoreLog.message') }}">
        <i class="fa fa-fw fa-history" aria-hidden="true"></i>{{ trans('LaravelActivityTracker::laravel-activity-tracker.dashboardCleared.menu.restoreAll') }}
    </button>
</form>
