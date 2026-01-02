
@if(config('LaravelActivityTracker.enablejQueryCDN'))
    <script type="text/javascript" src="{{ config('LaravelActivityTracker.JQueryCDN') }}"></script>
@endif

@if(config('LaravelActivityTracker.enableBootstrapJsCDN'))
    <script type="text/javascript" src="{{ config('LaravelActivityTracker.bootstrapJsCDN') }}"></script>
@endif

@if(config('LaravelActivityTracker.enablePopperJsCDN'))
    <script type="text/javascript" src="{{ config('LaravelActivityTracker.popperJsCDN') }}"></script>
@endif

@if(config('LaravelActivityTracker.loggerDatatables'))
    @if (count($activities) > 10)
        @include('LaravelActivityTracker::scripts.datatables')
    @endif
@endif

@include('LaravelActivityTracker::scripts.add-title-attribute')