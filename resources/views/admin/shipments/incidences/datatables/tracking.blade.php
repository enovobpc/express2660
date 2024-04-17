<a href="{{ route('admin.shipments.show', [$row->shipment_id, 'tab' => 'incidences']) }}" data-toggle="modal" data-target="#modal-remote-xl">
    {{ $row->tracking_code }}
</a>
<br/>
@if(Setting::get('shipment_list_show_provider_trk') && !empty($row->provider_tracking_code))
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
