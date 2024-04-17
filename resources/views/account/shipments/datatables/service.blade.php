<?php
$servicesCanDelete = Setting::get('services_can_delete');
$servicesCanDelete = empty($servicesCanDelete) ? [] : $servicesCanDelete
?>
<div data-toggle="tooltip" title="{{ @$row->service->name }}">
    {{ @$row->service->display_code_alt }}
</div>

@if($row->is_collection)
    @if($row->start_hour)
        <span class="label ship-info bg-red m-r-5"
              data-toggle="tooltip"
              title="Horário Recolha: {{ $row->start_hour }} {{ $row->end_hour ?  '-' . $row->end_hour : '' }}">
            <i class="far fa-clock"></i>
        </span>
    @endif
@endif

@if($row->is_printed && in_array($row->status_id, $servicesCanDelete))
        <span class="label ship-info bg-blue m-r-5"
              data-toggle="tooltip"
              title="Etiqueta já impressa.">
        <i class="fas fa-print"></i>
    </span>
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

{{--
@if($row->is_printed && !@$status['is_final'])
    <span class="label bg-blue" data-toggle="tooltip" title="Etiqueta já impressa">
    <i class="fas fa-print"></i>
</span>
@endif--}}
