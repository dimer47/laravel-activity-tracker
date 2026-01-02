{{-- Date Filtering and Export Form --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fa fa-filter"></i> {{ trans('LaravelActivityTracker::laravel-activity-tracker.filterAndExport') }}
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('activity') }}" class="form-inline">
            {{-- Date Range Filtering --}}
            @if(config('LaravelActivityTracker.enableDateFiltering'))
            <div class="form-group mr-3 mb-2">
                <label for="date_from" class="mr-2">{{ trans('LaravelActivityTracker::laravel-activity-tracker.fromDate') }}:</label>
                <input type="date" name="date_from" id="date_from" class="form-control"
                       value="{{ request('date_from') }}" />
            </div>

            <div class="form-group mr-3 mb-2">
                <label for="date_to" class="mr-2">{{ trans('LaravelActivityTracker::laravel-activity-tracker.toDate') }}:</label>
                <input type="date" name="date_to" id="date_to" class="form-control"
                       value="{{ request('date_to') }}" />
            </div>

            <div class="form-group mr-3 mb-2">
                <label for="period" class="mr-2">{{ trans('LaravelActivityTracker::laravel-activity-tracker.quickPeriod') }}:</label>
                <select name="period" id="period" class="form-control">
                    <option value="">{{ trans('LaravelActivityTracker::laravel-activity-tracker.allTime') }}</option>
                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>
                        {{ trans('LaravelActivityTracker::laravel-activity-tracker.today') }}
                    </option>
                    <option value="yesterday" {{ request('period') == 'yesterday' ? 'selected' : '' }}>
                        {{ trans('LaravelActivityTracker::laravel-activity-tracker.yesterday') }}
                    </option>
                    <option value="last_7_days" {{ request('period') == 'last_7_days' ? 'selected' : '' }}>
                        {{ trans('LaravelActivityTracker::laravel-activity-tracker.last7Days') }}
                    </option>
                    <option value="last_30_days" {{ request('period') == 'last_30_days' ? 'selected' : '' }}>
                        {{ trans('LaravelActivityTracker::laravel-activity-tracker.last30Days') }}
                    </option>
                    <option value="last_3_months" {{ request('period') == 'last_3_months' ? 'selected' : '' }}>
                        {{ trans('LaravelActivityTracker::laravel-activity-tracker.last3Months') }}
                    </option>
                    <option value="last_6_months" {{ request('period') == 'last_6_months' ? 'selected' : '' }}>
                        {{ trans('LaravelActivityTracker::laravel-activity-tracker.last6Months') }}
                    </option>
                    <option value="last_year" {{ request('period') == 'last_year' ? 'selected' : '' }}>
                        {{ trans('LaravelActivityTracker::laravel-activity-tracker.lastYear') }}
                    </option>
                </select>
            </div>
            @endif

            {{-- Search Fields --}}
            @if(config('LaravelActivityTracker.enableSearch'))
            <div class="form-group mr-3 mb-2">
                <label for="description" class="mr-2">{{ trans('LaravelActivityTracker::laravel-activity-tracker.description') }}:</label>
                <input type="text" name="description" id="description" class="form-control"
                       value="{{ request('description') }}" placeholder="{{ trans('LaravelActivityTracker::laravel-activity-tracker.searchDescription') }}" />
            </div>

            <div class="form-group mr-3 mb-2">
                <label for="user" class="mr-2">{{ trans('LaravelActivityTracker::laravel-activity-tracker.user') }}:</label>
                <select name="user" id="user" class="form-control">
                    <option value="">{{ trans('LaravelActivityTracker::laravel-activity-tracker.allUsers') }}</option>
                    @foreach($users as $user)
                        <option value="{{ $user->{config('LaravelActivityTracker.defaultUserIDField')} }}"
                                {{ request('user') == $user->{config('LaravelActivityTracker.defaultUserIDField')} ? 'selected' : '' }}>
                            {{ $user->email }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="form-group mr-3 mb-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i> {{ trans('LaravelActivityTracker::laravel-activity-tracker.filter') }}
                </button>
            </div>

            <div class="form-group mr-3 mb-2">
                <a href="{{ route('activity') }}" class="btn btn-secondary">
                    <i class="fa fa-refresh"></i> {{ trans('LaravelActivityTracker::laravel-activity-tracker.clearFilters') }}
                </a>
            </div>
        </form>

        {{-- Export Buttons --}}
        @if(config('LaravelActivityTracker.enableExport'))
        <hr>
        <div class="row">
            <div class="col-md-12">
                <h6>{{ trans('LaravelActivityTracker::laravel-activity-tracker.exportData') }}:</h6>
                <div class="btn-group" role="group">
                    <a href="{{ route('export-activity', array_merge(request()->query(), ['format' => 'csv'])) }}"
                       class="btn btn-success btn-sm">
                        <i class="fa fa-file-text-o"></i> {{ trans('LaravelActivityTracker::laravel-activity-tracker.exportCSV') }}
                    </a>
                    <a href="{{ route('export-activity', array_merge(request()->query(), ['format' => 'json'])) }}"
                       class="btn btn-info btn-sm">
                        <i class="fa fa-file-code-o"></i> {{ trans('LaravelActivityTracker::laravel-activity-tracker.exportJSON') }}
                    </a>
                    <a href="{{ route('export-activity', array_merge(request()->query(), ['format' => 'excel'])) }}"
                       class="btn btn-warning btn-sm">
                        <i class="fa fa-file-excel-o"></i> {{ trans('LaravelActivityTracker::laravel-activity-tracker.exportExcel') }}
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
