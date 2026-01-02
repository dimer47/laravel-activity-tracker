<form action="{{ route('clear-activity') }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="button" data-toggle="modal" data-target="#confirmDelete" data-title="{{ trans('LaravelActivityTracker::laravel-activity-tracker.modals.clearLog.title') }}" data-message="{{ trans('LaravelActivityTracker::laravel-activity-tracker.modals.clearLog.message') }}" class="dropdown-item">
        <i class="fa fa-fw fa-trash" aria-hidden="true"></i>{{ trans('LaravelActivityTracker::laravel-activity-tracker.dashboard.menu.clear') }}
    </button>
</form>
