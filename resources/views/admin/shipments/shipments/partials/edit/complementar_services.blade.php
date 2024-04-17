<?php $optFields = json_decode($shipment->optional_fields, true) ?>
@foreach($complementarServices as $service)
    <tr>
        <td>
            {{ Form::label('optional_fields[]', $service->short_name ? $service->short_name : $service->name) }}
        </td>
        <td class="tdinpt input-sm">
            @if($service->form_type_shipments == 'select-io')
                {{ Form::select('optional_fields['.$service->id.']', ['' => 'Não', 1 => 'Sim'], @$optFields[$service->id], ['class' => 'form-control select2', 'data-id' => $service->id, 'data-tag' => $service->type, 'data-input' => 'qty']) }}
            @elseif($service->form_type_shipments == 'select-time')
                <?php
                    $listHours = listNumeric(Setting::get('shipments_wainting_time_fractions') ?: 10, Setting::get('shipments_wainting_min_time') ?: 10, Setting::get('shipments_wainting_min_time') + 520, ' min');
                ?>
                {{ Form::select('optional_fields['.$service->id.']', ['0' => ''] + $listHours, @$optFields[$service->id], ['class' => 'form-control select2', 'data-id' => $service->id, 'data-tag' => $service->type, 'data-input' => 'qty']) }}
            @elseif($service->form_type_shipments == 'input')
                <div class="input-group input-group-money">
                    {{ Form::text('optional_fields['.$service->id.']', @$optFields[$service->id], ['class' => 'form-control input-sm decimal', 'data-id' => $service->id, 'data-tag' => $service->type, 'data-input' => 'qty']) }}
                    <div class="input-group-addon">{{ $service->addon_text ? $service->addon_text : 'Qtd' }}</div>
                </div>
            @elseif($service->form_type_shipments == 'money')
                <div class="input-group input-group-money">
                    {{ Form::text('optional_fields['.$service->id.']', @$optFields[$service->id], ['class' => 'form-control input-sm decimal', 'data-id' => $service->id, 'data-tag' => $service->type, 'data-input' => 'money']) }}
                    <div class="input-group-addon">{{ $service->addon_text ? $service->addon_text : $appCurrency }}</div>
                </div>
            @elseif($service->form_type_shipments == 'percent')
                <div class="input-group input-group-money">
                    {{ Form::text('optional_fields['.$service->id.']', @$optFields[$service->id], ['class' => 'form-control input-sm decimal', 'data-id' => $service->id, 'data-tag' => $service->type, 'data-input' => 'percent']) }}
                    <div class="input-group-addon">{{ $service->addon_text ? $service->addon_text : '%' }}</div>
                </div>
            @else
                {{ Form::checkbox('optional_fields['.$service->id.']', $service->value ? $service->value : 1, @$optFields[$service->id], ['data-id' => $service->id, 'data-tag' => $service->type, 'data-input' => 'qty']) }}
            @endif
        </td>
    </tr>
@endforeach