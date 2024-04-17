<table class="table m-b-0">
    <tr>
        <th class="w-1 bg-gray-light" style="border-top: none">#</th>
        <th class="w-120px bg-gray-light" style="border-top: none">Pacote</th>
        <th class="bg-gray-light" style="border-top: none; border-left: 1px solid #ddd">Peso</th>
        <th class="bg-gray-light" style="border-top: none;  border-left: 1px solid #ddd">Comprimento</th>
        <th class="bg-gray-light" style="border-top: none">Largura</th>
        <th class="bg-gray-light" style="border-top: none">Altura</th>
        <th class="bg-gray-light" style="border-top: none;  border-left: 1px solid #ddd">Soma</th>
        <th class="bg-gray-light" style="border-top: none">Volume M<sup>3</sup></th>
        <th class="bg-gray-light" style="border-top: none; border-left: 1px solid #ddd">Descrição da mercadoria</th>
        @if(Setting::get('show_adr_fields'))
            <th class="bg-gray-light" style="border-top: none; border-left: 1px solid #ddd">{{ trans('account/global.word.adr_class') }}</th>
            <th class="bg-gray-light" style="border-top: none">{{ trans('account/global.word.adr_letter') }}</th>
            <th class="bg-gray-light" style="border-top: none">{{ trans('account/global.word.adr_number') }}</th>
        @endif
        @if(Setting::get('app_mode') == 'move')
            <th class="bg-gray-light" style="border-top: none; border-left: 1px solid #ddd">{{ trans('account/global.word.adicional-services') }}</th>
        @endif
    </tr>
    @foreach($shipment->pack_dimensions as $key => $dimension)
    <tr>
        <td>{{ $key + 1 }}</td>
        <td>{{ $dimension->qty ? $dimension->qty : 1  }}x</small> {{ @$packTypes[$dimension->type] }}</td>
        {{--<td>{{ $dimension->type ? trans('admin/global.packages_types.' . ($dimension->type ? $dimension->type : 'box'))  : trans('admin/global.packages_types.' . ($shipment->packaging_type ? $shipment->packaging_type : 'box')) }}</td>--}}
        <td style="border-left: 1px solid #ddd">{{ $dimension->weight }} kg</td>
        <td style="border-left: 1px solid #ddd">{{ $dimension->length }} {{ Setting::get('shipments_volumes_mesure_unity') ? Setting::get('shipments_volumes_mesure_unity') : 'cm' }}</td>
        <td>{{ $dimension->width }} {{ Setting::get('shipments_volumes_mesure_unity') ? Setting::get('shipments_volumes_mesure_unity') : 'cm' }}</td>
        <td>{{ $dimension->height }} {{ Setting::get('shipments_volumes_mesure_unity') ? Setting::get('shipments_volumes_mesure_unity') : 'cm' }}</td>
        <td style="border-left: 1px solid #ddd">{{ $dimension->length + $dimension->width + $dimension->height }} {{ Setting::get('shipments_volumes_mesure_unity') ? Setting::get('shipments_volumes_mesure_unity') : 'cm' }}</td>
        <td>{{ $dimension->volume }}</td>
        <td style="border-left: 1px solid #ddd">
            {{ $dimension->description }}
            <small>
            @if($dimension->sku)
                <br/>Ref: {{ $dimension->sku }}
            @endif
            @if($dimension->serial_no)
                <br/>Nº Série: {{ $dimension->serial_no }}
            @endif
            @if($dimension->lote)
                <br/>Lote: {{ $dimension->lote }}
            @endif
            </small>
        </td>
        @if(Setting::get('show_adr_fields'))
            <td style="border-left: 1px solid #ddd">{{ $dimension->adr_class }}</td>
            <td>{{ $dimension->adr_letter }}</td>
            <td>{{ $dimension->adr_number }}</td>
        @endif
        @if(config('app.source') == 'velozeficiente')
            <td style="border-left: 1px solid #ddd">
                @if(!empty($dimension->optional_fields))
                    @foreach($dimension->optional_fields as $key => $value)
                        <i class="far fa-check-square"></i> {{ $key }}
                    @endforeach
                @endif
            </td>
        @endif
    </tr>
    @endforeach
</table>