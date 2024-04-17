@if(@$services && !$services->isEmpty())
    @foreach($services as $service)
        @include('account.budgeter.partials.results_row')
    @endforeach
@else
    <div class="text-center text-muted m-t-30">
        <i class="fas fa-info-circle fs-30"></i>
        <h4>{{ trans('account/budgeter.results.empty') }}</h4>
    </div>
@endif