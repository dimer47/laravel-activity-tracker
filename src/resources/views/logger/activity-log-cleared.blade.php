@extends(config('LaravelActivityTracker.loggerBladeExtended'))

@if(config('LaravelActivityTracker.bladePlacement') == 'yield')
    @section(config('LaravelActivityTracker.bladePlacementCss'))
@elseif (config('LaravelActivityTracker.bladePlacement') == 'stack')
    @push(config('LaravelActivityTracker.bladePlacementCss'))
@endif

        @include('LaravelActivityTracker::partials.styles')

@if(config('LaravelActivityTracker.bladePlacement') == 'yield')
    @endsection
@elseif (config('LaravelActivityTracker.bladePlacement') == 'stack')
    @endpush
@endif

@if(config('LaravelActivityTracker.bladePlacement') == 'yield')
    @section(config('LaravelActivityTracker.bladePlacementJs'))
@elseif (config('LaravelActivityTracker.bladePlacement') == 'stack')
    @push(config('LaravelActivityTracker.bladePlacementJs'))
@endif

        @include('LaravelActivityTracker::partials.scripts', ['activities' => $activities])
        @include('LaravelActivityTracker::scripts.confirm-modal', ['formTrigger' => '#confirmDelete'])
        @include('LaravelActivityTracker::scripts.confirm-modal', ['formTrigger' => '#confirmRestore'])

        @if(config('LaravelActivityTracker.enableDrillDown'))
            @include('LaravelActivityTracker::scripts.clickable-row')
            @include('LaravelActivityTracker::scripts.tooltip')
        @endif

@if(config('LaravelActivityTracker.bladePlacement') == 'yield')
    @endsection
@elseif (config('LaravelActivityTracker.bladePlacement') == 'stack')
    @endpush
@endif


@section('template_title')
    {{ trans('LaravelActivityTracker::laravel-activity-tracker.dashboardCleared.title') }}
@endsection

@php
    switch (config('LaravelActivityTracker.bootstapVersion')) {
        case '4':
            $containerClass = 'card';
            $containerHeaderClass = 'card-header';
            $containerBodyClass = 'card-body';
            break;
        case '3':
        default:
            $containerClass = 'panel panel-default';
            $containerHeaderClass = 'panel-heading';
            $containerBodyClass = 'panel-body';
    }
    $bootstrapCardClasses = (is_null(config('LaravelActivityTracker.bootstrapCardClasses')) ? '' : config('LaravelActivityTracker.bootstrapCardClasses'));
@endphp

@section('content')

    <div class="container-fluid">

        @if(config('LaravelActivityTracker.enablePackageFlashMessageBlade'))
            @include('LaravelActivityTracker::partials.form-status')
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="{{ $containerClass }} {{ $bootstrapCardClasses }}">
                    <div class="{{ $containerHeaderClass }}">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span>
                                {!! trans('LaravelActivityTracker::laravel-activity-tracker.dashboardCleared.title') !!}
                                @if(! config('LaravelActivityTracker.loggerCursorPaginationEnabled'))
                                    <sup class="label">
                                        {{ $totalActivities }} {!! trans('LaravelActivityTracker::laravel-activity-tracker.dashboardCleared.subtitle') !!}
                                    </sup>
                                @endif
                            </span>
                            <div class="btn-group pull-right btn-group-xs">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-v fa-fw" aria-hidden="true"></i>
                                    <span class="sr-only">
                                        {!! trans('LaravelActivityTracker::laravel-activity-tracker.dashboard.menu.alt') !!}
                                    </span>
                                </button>
                                @if(config('LaravelActivityTracker.bootstapVersion') == '4')
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{route('activity')}}" class="dropdown-item">
                                            <span class="text-primary">
                                                <i class="fa fa-fw fa-mail-reply" aria-hidden="true"></i>
                                                {!! trans('LaravelActivityTracker::laravel-activity-tracker.dashboard.menu.back') !!}
                                            </span>
                                        </a>
                                        @if($totalActivities)
                                            @include('LaravelActivityTracker::forms.delete-activity-log')
                                            @include('LaravelActivityTracker::forms.restore-activity-log')
                                        @endif
                                    </div>
                                @else
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{route('activity')}}">
                                                <span class="text-primary">
                                                    <i class="fa fa-fw fa-mail-reply" aria-hidden="true"></i>
                                                    {!! trans('LaravelActivityTracker::laravel-activity-tracker.dashboard.menu.back') !!}
                                                </span>
                                            </a>
                                        </li>
                                        @if($totalActivities)
                                            <li>
                                                @include('LaravelActivityTracker::forms.delete-activity-log')
                                            </li>
                                            <li>
                                                @include('LaravelActivityTracker::forms.restore-activity-log')
                                            </li>
                                        @endif
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        @include('LaravelActivityTracker::logger.partials.activity-table', ['activities' => $activities, 'hoverable' => true])
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('LaravelActivityTracker::modals.confirm-modal', ['formTrigger' => 'confirmDelete', 'modalClass' => 'danger', 'actionBtnIcon' => 'fa-trash-o'])
    @include('LaravelActivityTracker::modals.confirm-modal', ['formTrigger' => 'confirmRestore', 'modalClass' => 'success', 'actionBtnIcon' => 'fa-check'])

@endsection
