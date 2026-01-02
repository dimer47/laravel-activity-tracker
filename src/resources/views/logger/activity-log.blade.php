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
    {{ trans('LaravelActivityTracker::laravel-activity-tracker.dashboard.title') }}
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

       @if(config('LaravelActivityTracker.enableLiveSearch'))
       @include('LaravelActivityTracker::partials.form-live-search')
       @endif

       @if(config('LaravelActivityTracker.enableSearch'))
       @include('LaravelActivityTracker::partials.form-search')
       @endif
       @if(config('LaravelActivityTracker.enablePackageFlashMessageBlade'))
       @include('LaravelActivityTracker::partials.form-status')
       @endif

       @if(config('LaravelActivityTracker.enableDateFiltering') || config('LaravelActivityTracker.enableExport'))
       @include('LaravelActivityTracker::partials.filter-export-form')
       @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="{{ $containerClass }} {{ $bootstrapCardClasses }}">
                    <div class="{{ $containerHeaderClass }}">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            @if(config('LaravelActivityTracker.enableSubMenu'))

                            <span>
                                {!! trans('LaravelActivityTracker::laravel-activity-tracker.dashboard.title') !!}
                                @if(! config('LaravelActivityTracker.loggerCursorPaginationEnabled'))
                                    <small>
                                        <sup class="label label-default">
                                            {{ $totalActivities }} {!! trans('LaravelActivityTracker::laravel-activity-tracker.dashboard.subtitle') !!}
                                        </sup>
                                    </small>
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
                                    @include('LaravelActivityTracker::forms.clear-activity-log')
                                    <a href="{{route('cleared')}}" class="dropdown-item">
                                        <i class="fa fa-fw fa-history" aria-hidden="true"></i>
                                        {!! trans('LaravelActivityTracker::laravel-activity-tracker.dashboard.menu.show') !!}
                                    </a>
                                </div>
                                @else
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li class="dropdown-item">
                                        @include('LaravelActivityTracker::forms.clear-activity-log')
                                    </li>
                                    <li class="dropdown-item">
                                        <a href="{{route('cleared')}}">
                                            <i class="fa fa-fw fa-history" aria-hidden="true"></i>
                                            {!! trans('LaravelActivityTracker::laravel-activity-tracker.dashboard.menu.show') !!}
                                        </a>
                                    </li>
                                </ul>
                                @endif
                            </div>

                            @else
                            {!! trans('LaravelActivityTracker::laravel-activity-tracker.dashboard.title') !!}
                                @if(! config('LaravelActivityTracker.loggerCursorPaginationEnabled'))
                                    <span class="pull-right label label-default">
                                        {{ $totalActivities }}
                                        <span class="hidden-sms">
                                            {!! trans('LaravelActivityTracker::laravel-activity-tracker.dashboard.subtitle') !!}
                                        </span>
                                    </span>
                                @endif
                            @endif

                        </div>
                    </div>
                    <div class="{{ $containerBodyClass }}">
                        @include('LaravelActivityTracker::logger.partials.activity-table', ['activities' => $activities, 'hoverable' => true])
                    </div>
                </div>
            </div>
        </div>
    </div>

@if(config('LaravelActivityTracker.enableLiveSearch'))
@include('LaravelActivityTracker::scripts.live-search-script')
@endif

@include('LaravelActivityTracker::modals.confirm-modal', ['formTrigger' => 'confirmDelete', 'modalClass' => 'danger', 'actionBtnIcon' => 'fa-trash-o'])

@endsection
