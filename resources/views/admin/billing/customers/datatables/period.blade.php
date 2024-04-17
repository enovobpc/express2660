{{ trans('datetime.month-tiny.'.$row->month).' '.$row->year }}
<small class="text-muted">
    @if($period == '1q')
        1ª Quin.
    @elseif($period == '2q')
        2ª Quin.
    @else
        Mensal
    @endif
</small>

