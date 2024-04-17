<?php
    $status = @$statusList[$row->status_id][0];
?>
<div>
    @if($row->date > date('Y-m-d'))
        <span class="text-yellow">
            {{ $row->date }} <i class="fas fa-clock" data-toggle="tooltip" title="Envio/Recolha agendada"></i>
        </span>
    @else
        {{ $row->date }}
    @endif
</div>


@if(Setting::get('shipment_list_show_hour'))
    @if($row->start_hour)
        <div>
            <small>{{ $row->start_hour }}{{ $row->end_hour && !Setting::get('shipment_list_show_delivery_date') ?  ' - ' . $row->end_hour : '' }}</small>
        </div>
    @else
        <div>
            {{-- <small>{{ $row->start_hour ? $row->start_hour : $row->created_at->format('H:i') }}</small> --}}
            <small>{{ $row->start_hour ? $row->start_hour : '' }}</small>
        </div>
    @endif
@elseif($row->start_hour)
        @if(Setting::get('app_mode') == 'move')
        <small>{{ $row->start_hour }} - {{ $row->end_hour }}</small>
    @else
        <span class="label bg-red m-r-5" data-toggle="tooltip" title="Horário: {{ $row->start_hour }} - {{ $row->end_hour }}">
            <i class="far fa-clock"></i>
        </span>
    @endif
@endif

@if($row->recipient_pudo_id)
<span class="label bg-orange m-r-3 p-l-4 p-r-3" data-toggle="tooltip" title="Entrega em Ponto Pickup">
    <i class="fas fa-store"></i>
</span>
@endif

@if($row->tags)
    {!! $row->tagsHtml !!}
@endif

@if($row->obs)
<span class="label bg-aqua m-r-3 p-l-6 p-r-6" data-toggle="tooltip" title="Obs: {{ $row->obs }}">
    <i class="fas fa-info"></i>
</span>
@endif

@if($row->obs_internal)
<span class="label bg-blue m-r-3 p-l-6 p-r-6" data-toggle="tooltip" title="Obs Internas: {{ $row->obs_internal }}">
    <i class="fas fa-info"></i>
</span>
@endif

@if($row->vehicle && !Setting::get('shipment_list_show_vehicle'))
<span class="label bg-blue m-r-3 p-l-6 p-r-6"  onclick="CopyToClipboard('{{ $row->vehicle }}')" data-toggle="tooltip" title="{{ $row->vehicle }}{{ $row->trailer ? ' + ' . $row->trailer : '' }}">
    <i class="fas fa-truck"></i>
</span>
@endif

@if($row->is_printed && !@$status['is_final'])
<span class="label bg-blue" data-toggle="tooltip" title="Etiqueta já impressa">
    <i class="fas fa-print"></i>
</span>
@endif