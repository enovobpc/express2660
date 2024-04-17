@if($row->serial_no)
    {{ $row->serial_no }}<br/>
    <small><span class="label label-warning">@trans('Nº Série')</span></small>
@elseif($row->lote)
    {{ $row->lote }}
    <br/>
    <small><span class="label label-info">@trans('Lote')</span></small>
@endif
