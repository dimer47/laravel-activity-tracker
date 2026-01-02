<form action="{{ route('destroy-activity') }}" method="POST" class="mb-0">
    @csrf
    @method('DELETE')
    <button type="button" class="text-danger dropdown-item" data-toggle="modal" data-target="#confirmDelete" data-title="{{ trans('LaravelActivityTracker::laravel-activity-tracker.modals.deleteLog.title') }}" data-message="{{ trans('LaravelActivityTracker::laravel-activity-tracker.modals.deleteLog.message') }}">
        <i class="fa fa-fw fa-eraser" aria-hidden="true"></i>{{ trans('LaravelActivityTracker::laravel-activity-tracker.dashboardCleared.menu.deleteAll') }}
    </button>
</form>
