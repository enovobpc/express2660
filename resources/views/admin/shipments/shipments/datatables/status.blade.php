<?php
    $status   = @$statusList[$row->status_id][0];
    $operatorDelivery = @$operatorsList[$row->operator_id][0];
    $operatorPickup  = @$operatorsList[$row->pickup_operator_id][0];
?>

<div class="text-center">
    <a href="{{ route('admin.shipments.history.create', $row->id) }}" data-toggle="modal" data-target="#modal-remote">
        <span class="label cursor-pointer" style="background-color: {{ @$status['color'] }}">
            {{ @$status['name'] }}
        </span>
        <br/>
        <div class="opnm">
            @if($row->operator_id && $row->pickup_operator_id == $row->operator_id)
                <span data-toggle="tooltip" title="Rec/Entrega: {{ @$operatorDelivery['name'] }}">
                    @if(@$operatorDelivery['code_abbrv'])
                        {{ @$operatorDelivery['code_abbrv'] }}
                    @elseif(@$operatorDelivery['code'])
                        {{ @$operatorDelivery['code'] }}
                    @else
                        {{ str_limit(@$operatorDelivery['name'], 10) }}
                    @endif
                </span>
            @endif

            <?php $bull = '' ?>
            @if($row->pickup_operator_id)
                <span data-toggle="tooltip" title="Recolha: {{ @$operatorPickup['name'] }}">
                    @if(@$operatorPickup['code_abbrv'])
                        <b>R:</b> {{ @$operatorPickup['code_abbrv'] }}
                    @elseif(@$operatorPickup['code'])
                        <b>R:</b> {{ @$operatorPickup['code'] }}
                    @else
                        <b>R:</b> {{ str_limit(@$operatorPickup['name'], 10) }}
                    @endif
                </span>
                <?php $bull = '&bull;' ?>
            @endif

            @if($row->operator_id)
                {{ $bull }}
                <span data-toggle="tooltip" title="Entrega: {{ @$operatorDelivery['name'] }}">
                    @if(@$operatorDelivery['code_abbrv'])
                        <b>E:</b> {{ @$operatorDelivery['code_abbrv'] }}
                    @elseif(@$operatorDelivery['code'])
                        <b>E:</b> {{ @$operatorDelivery['code'] }}
                    @else
                        <b>E:</b> {{ str_limit(@$operatorDelivery['name'], 10) }}
                    @endif
                </span>
            @endif
        </div>
    </a>
</div>

{{--<div class="text-center">
    <a href="{{ route('admin.shipments.history.create', $row->id) }}" data-toggle="modal" data-target="#modal-remote">
        <span class="label cursor-pointer" style="background-color: {{ $row->status->color }}">
            {{ $row->status->name }}
        </span>
        @if($row->operator_id)
        <br/>
        <small class="italic text-muted" data-toggle="tooltip" title="{{ @$row->operator->name }}">
            @if(@$row->operator->code_abbrv)
                {{ @$row->operator->code_abbrv }}
            @else
            {{ @$row->operator->code ? @$row->operator->code : str_limit(@$row->operator->name, 10) }}
            @endif
        </small>
        @endif
    </a>
</div>--}}