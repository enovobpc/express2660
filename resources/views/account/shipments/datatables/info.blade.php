<?php
    $startDt    = new Date($row->date);
    $deliveryDt = new Date($row->delivery_date);
    $deliveryHr = $deliveryDt->format('H:i');
?>


@if($row->is_collection)
    <div class="text-center">
    <a href="{{ route('account.shipments.show', [$row->id, 'tab' => 'status']) }}"  data-toggle="modal" data-target="#modal-remote-xl">
    @if($row->hasSyncError() && Setting::get('customers_show_webservice_errors'))
        <span class="label bg-red" style="background-color: #cc0000">
                <i class="fas fa-exclamation-triangle"></i> ERRO SUBMISSÃO
            </span>
        @elseif(!$row->is_closed && ($row->status_id == 1 || $row->status_id == 2))
            <span class="label" style="background-color: #cc0000" data-toggle="tooltip" title="Envio Ainda não fechado.">
                    Por Fechar
                </span>
        @else
        <span class="label" style="background-color: {{ $row->status->color }}">
            {{ $row->status->name }}
        </span>
        @endif
    </a>
    </div>
@else
    @if(Setting::get('customers_show_delivery_date'))
        @if($row->delivery_date)
        <div class="fs-13">
            <i class="far fa-calendar-alt"></i> {{ $deliveryDt->format('Y-m-d') }}
            @if($deliveryHr != '00:00')
                <br/><i class="far fa-clock"></i> {{ $deliveryHr }}
            @endif
        </div>
        @endif
    @else
        <div class="fs-13">
            <i class="far fa-fw fa-calendar-alt"></i> {{ $startDt->format('Y-m-d') }}
            @if($row->start_hour)
                <br/>
                <span>
                    <i class="far fa-fw fa-clock"></i> {{ $row->start_hour }}
                    @if(!$startDt->diffInDays($deliveryDt))
                    - {{ $deliveryHr }}
                    @endif
                </span>
            @endif
        </div>
    @endif
@endif