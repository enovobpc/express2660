<?php
    $status = @$statusList[$row->status_id][0];
?>

<a href="/admin/shipments/{{$row->id}}/delivery-date/edit" class="text-black dt-{{$row->id}}" data-toggle="modal" data-target="#modal-remote-xs">
<div>
    @if($row->date > date('Y-m-d'))
        <span class="text-yellow">
            <i class="fas fa-fw fa-clock" data-toggle="tooltip" title="Envio/Recolha agendada"></i> {{ $row->date }}
        </span>
    @else
        <i class="fas fa-fw fa-sign-in-alt"></i> {{ $row->date }}
        @if($row->start_hour != '00:00')
            <small>{{ $row->start_hour }}</small>
        @endif
    @endif
</div>

@if($row->delivery_date)
    <?php $hour = $row->delivery_date->format('H:i'); ?>
    @if($row->delivery_date->lt(\Carbon\Carbon::now()) && !@$status['is_final'])
        @if(Setting::get('app_mode') == 'move')
            <div data-toggle="tooltip" title="A data para entrega foi ultrapassada.">
                <i class="fas fa-fw fa-sign-out-alt"></i> {{ $row->delivery_date->format('Y-m-d') }}
                @if($hour != '00:00')
                    <small>{{ $row->start_hour_pickup }} - {{ $row->end_hour_pickup }}</small>
                @endif
            </div>
        @else
            <div data-toggle="tooltip" title="A data para entrega foi ultrapassada.">
                <i class="fas fa-fw fa-sign-out-alt"></i> {{ $row->delivery_date->format('Y-m-d') }}
                @if($hour != '00:00')
                    <small>{{ $hour }}</small>
                @endif
            </div>
        @endif
    @else
        <i class="fas fa-fw fa-sign-out-alt"></i> {{ $row->delivery_date->format('Y-m-d') }}
        <small>{{ $hour }}</small>
    @endif
@else
    __/__/__
@endif
</a>

<div>
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

    @if ($row->attachments->isNotEmpty())
    <span class="label bg-purple m-r-3 p-l-6 p-r-6" data-toggle="tooltip" title="Tem anexos">
        <i class="fas fa-file-image"></i>
    </span>
    @endif
</div>