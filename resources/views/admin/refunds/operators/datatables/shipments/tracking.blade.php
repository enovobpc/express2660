<b>{{ $row->tracking_code }}</b>
<br/>
{{ $row->date }}
<br/>
@if($row->type == \App\Models\Shipment::TYPE_PICKUP)
    <span class="label label-info" data-toggle="tooltip" title="Pedido de Recolha Associado">
        <i class="fas fa-cube"></i> {{ $row->parent_tracking_code }}
    </span>
    <br/>
@elseif($row->type == \App\Models\Shipment::TYPE_RETURN)
    <span class="label label-success" data-toggle="tooltip" title="Retorno">
        <i class="fas fa-undo"></i> {{ $row->parent_tracking_code }}
    </span>
    <br/>
@elseif($row->type == \App\Models\Shipment::TYPE_DEVOLUTION)
    <span class="label bg-orange" data-toggle="tooltip" title="Devolução">
        <i class="fas fa-arrow-left"></i> {{ $row->parent_tracking_code }}
    </span>
    <br/>
@elseif($row->type == \App\Models\Shipment::TYPE_RECANALIZED)
    <span class="label bg-yellow" data-toggle="tooltip" title="Recanalização">
        <i class="fas fa-undo"></i> {{ $row->parent_tracking_code }}
    </span>
    <br/>
@elseif($row->type == \App\Models\Shipment::TYPE_LINKED)
    <span class="label bg-purple" data-toggle="tooltip" title="Envio Ligado">
        <i class="fas fa-link"></i> {{ $row->parent_tracking_code }}
    </span>
    <br/>
@elseif($row->type == \App\Models\Shipment::TYPE_MASTER)
    <span class="label bg-lime" data-toggle="tooltip" title="Serviço Agrupado">
        <i class="fas fa-level-up-alt"></i> {{ $row->parent_tracking_code }}
    </span>
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

