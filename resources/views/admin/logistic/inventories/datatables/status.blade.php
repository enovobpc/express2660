@if($row->status_id == 1)
    <span class="label label-info">
        @trans('Em Edição')
    </span>
@else
    <span class="label label-success">
        @trans('Fechado')
    </span>
@endif
