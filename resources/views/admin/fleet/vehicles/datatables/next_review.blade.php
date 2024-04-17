@if($row->next_review_km)
    {{--
    {{ $row->next_review->format('Y-m-d') }}<br/>
    @if($row->next_review >= Date::now())
    ({{ $row->next_review->diffInDays(Date::now()) }} dias)
    @else
    <span class="text-red"><i class="fas fa-exclamation-triangle"></i> {{ $row->next_review->diffInDays(Date::now()) }} km atrás</span>
    @endif--}}

    {{ money($row->next_review_km, '', 0) }} km<br/>
    @if($row->next_review_km - $row->km > 0)
        @if($row->next_review_km - $row->km > 1000)
            <small class="text-muted">{{ $row->next_review_km - $row->km }}@trans('km restantes')</small>
        @else
            <small class="text-yellow"><i class="fas fa-exclamation-triangle"></i> {{ $row->next_review_km - $row->km }}@trans('km restantes')</small>
        @endif
    @else
        <small class="text-red"><i class="fas fa-exclamation-triangle"></i> {{ $row->km - $row->next_review_km }} @trans('km atrás')</small>
    @endif
@endif