@if(!is_null($row->end_date) && $row->end_date->lt(Date::today()))
    <span class="label label-info">
        @trans('Concluído')
    </span>
@else
    @if(is_null($row->end_date))
        <span class="label" style="background:darkorange">
            @trans('Registado')
        </span>
    @else
        <span class="label label-success">
            @trans('Aprovado')
        </span>
    @endif

@endif