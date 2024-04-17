@if($row->hasSync())
    <a href="{{ route('admin.shipments.show', $row->id) }}" data-toggle="modal" data-target="#modal-remote-xl">
        {{ $row->tracking_code }}

        @if(!$row->is_closed && ($row->status_id == 1 || $row->status_id == 2))
            <span data-toggle="tooltip" title="Sincronizado. Ainda não fechado.">
               <i class="far fa-clock text-yellow"></i>
            </span>
        @else
            <span data-toggle="tooltip" title="Sincronizado">
               <i class="fas fa-check text-green"></i>
            </span>
        @endif
    </a>
@elseif($row->hasSyncError())
    <a href="{{ route('admin.shipments.show', $row->id) }}" data-toggle="modal" data-target="#modal-remote-xl" class="text-red">
        {{ $row->tracking_code }}
        <span data-toggle="tooltip" title="Erro de sincronização">
            <i class="fas fa-exclamation-triangle text-red"></i>
        </span>
    </a>
@else
    <a href="{{ route('admin.shipments.show', $row->id) }}" data-toggle="modal" data-target="#modal-remote-xl">
        {{ $row->tracking_code }}
    </a>
@endif
<br/>

{{-- DELETED --}}
@if($row->deleted_at)
    <span class="label label-danger" data-toggle="tooltip" title="Envio Eliminado em {{ $row->deleted_at }}">
        <i class="fas fa-trash-alt"></i> ELIMINADO
    </span>
    <br/>
@endif

{{--@if($row->children_tracking_code)
<span class="label bg-red">{{ $row->children_type }} {{ $row->children_tracking_code }}</span>
@endif--}}

@if($row->type == \App\Models\Shipment::TYPE_PICKUP)
    <span class="label label-info" data-toggle="tooltip" title="Pedido de Recolha Associado">
        <i class="fas fa-cube"></i> {{ $row->parent_tracking_code }}
    </span>
    <br/>
@elseif($row->type == \App\Models\Shipment::TYPE_RETURN)
    <span class="label label-success" data-toggle="tooltip" title="Retorno" data-s-filter="{{ $row->parent_tracking_code }}">
        <i class="fas fa-undo"></i> {{ $row->parent_tracking_code }}
    </span>
    <br/>
@elseif($row->type == \App\Models\Shipment::TYPE_DEVOLUTION)
    <span class="label bg-orange" data-toggle="tooltip" title="Devolução" data-s-filter="{{ $row->parent_tracking_code }}">
        <i class="fas fa-arrow-left"></i> {{ $row->parent_tracking_code }}
    </span>
    <br/>
@elseif($row->type == \App\Models\Shipment::TYPE_RECANALIZED)
    <span class="label bg-yellow" data-toggle="tooltip" title="Recanalização" data-s-filter="{{ $row->parent_tracking_code }}">
        <i class="fas fa-undo"></i> {{ $row->parent_tracking_code }}
    </span>
    <br/>
@elseif($row->type == \App\Models\Shipment::TYPE_LINKED)
    <span class="label bg-purple" data-toggle="tooltip" title="Envio Ligado" data-s-filter="{{ $row->parent_tracking_code }}">
        <i class="fas fa-link"></i> {{ $row->parent_tracking_code }}
    </span>
    <br/>
@elseif($row->type == \App\Models\Shipment::TYPE_MASTER)
    <span style="color: #ff5f01; font-size: 12px;" data-toggle="tooltip" title="Serviço Agrupado" data-s-filter="{{ $row->parent_tracking_code }}">
        <i class="fas fa-level-up-alt"></i> {{ $row->parent_tracking_code }}
    </span>

    <br/>
@elseif($row->children_type == \App\Models\Shipment::TYPE_MASTER)
    @if(!Setting::get('shipment_list_detail_master'))
    {{-- <span class="label" style="background: #ff5f01;" data-toggle="tooltip" title="Serviço Agrupado. Código MASTER do serviço agrupado." data-s-filter="{{ $row->tracking_code }}">
        Serviço Agrupado
        <span class="details-control">&nbsp;&nbsp;<i class="fas fa-angle-down"></i></span>
    </span> --}}
    <span class="label details-control" style="background: #ff5f01;">
        Várias Cargas &nbsp;<i class="fas fa-angle-down"></i></span>
    </span>
    @else
    <span class="label" style="background: #ff5f01;" data-toggle="tooltip" title="Serviço Agrupado. Código MASTER do serviço agrupado." data-s-filter="{{ $row->tracking_code }}">
        {{ $row->tracking_code }}
    </span>
    
    @endif
    <br/>
@elseif($row->type == \App\Models\Shipment::TYPE_TRANSHIPMENT)
    <span class="label" style="background: #9135ff;" data-toggle="tooltip" title="Transbordo ou Dobragem" data-s-filter="{{ $row->tracking_code }}">
        <i class="fas fa-random"></i> {{ $row->parent_tracking_code }}
    </span>
    <br/>
@endif

{{-- PROVIDER TRK --}}
@if(Setting::get('shipment_list_show_provider_trk') && $row->provider_tracking_code)
    <small>{{ str_limit($row->provider_tracking_code, 16) }}</small>
    <br/>
@endif

<?php

    if($row->sender_agency_id){
        $senderAgencyCode = @$agencies[$row->sender_agency_id][0]['code'];
        $senderAgencyName = @$agencies[$row->sender_agency_id][0]['name'];
        $senderAgencyColor = @$agencies[$row->sender_agency_id][0]['color'];
    } else {
        $senderAgencyCode = null;
    }

    if($row->sender_agency_id){
        $recipientAgencyCode = @$agencies[$row->recipient_agency_id][0]['code'];
        $recipientAgencyName = @$agencies[$row->recipient_agency_id][0]['name'];
        $recipientAgencyColor = @$agencies[$row->recipient_agency_id][0]['color'];
    } else {
        $recipientAgencyCode = null;
    }

?>

@if(!empty($senderAgencyCode))
    <span class="label" style="background: {{ $senderAgencyColor }}" data-toggle="tooltip" title="{{ $senderAgencyName }}">{{ $senderAgencyCode }}</span>
@else
    <i class="fas fa-exclamation-triangle text-red" data-toggle="tooltip" title="Sem A. Origem"></i>
@endif

@if(!empty($recipientAgencyCode) && $senderAgencyCode != $recipientAgencyCode)
    &nbsp;<i class="fas fa-angle-right"></i>
    <span class="label" style="background: {{ $recipientAgencyColor }}" data-toggle="tooltip" title="{{ $recipientAgencyName }}">{{ $recipientAgencyCode }}</span>
@endif
