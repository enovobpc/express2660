<table class="table budget-dimensions">
    <tr>
        <th class="w-120px" style="border-top: none">{{ trans('account/global.word.pack') }}</th>
        <th class="text-center w-75px" style="border-top: none; border-left: 1px solid #ddd">
            {{ trans('account/global.word.weight') }}
            <small style="font-weight: normal">(kg)</small>
        </th>
        <th class="text-center w-95px" style="border-top: none; border-left: 1px solid #ddd">
            {{ trans('account/global.word.width-abrv') }}
            <small style="font-weight: normal">({{ Setting::get('shipments_volumes_mesure_unity') }})</small>
        </th>
        <th class="text-center w-95px" style="border-top: none">
            {{ trans('account/global.word.length') }}
            <small style="font-weight: normal">({{ Setting::get('shipments_volumes_mesure_unity') }})</small>
        </th>
        <th class="text-center w-95px" style="border-top: none">
            {{ trans('account/global.word.height') }}
            <small style="font-weight: normal">({{ Setting::get('shipments_volumes_mesure_unity') }})</small>
        </th>
        <th class="text-center w-90px" style="border-top: none; border-left: 1px solid #ddd">
            {{ trans('account/global.word.sum') }}
            <small style="font-weight: normal">({{ Setting::get('shipments_volumes_mesure_unity') }})</small>
        </th>
        @if(config('app.source') != 'velozeficiente')
        <th class="text-center w-90px" style="border-top: none">{{ trans('account/global.word.fator-m3') }}</th>
        @endif
        @if(Setting::get('customer_show_adr_fields'))
            <th style="border-top: none; border-left: 1px solid #ddd">{{ trans('account/global.word.adr_class') }}</th>
            <th style="border-top: none">{{ trans('account/global.word.adr_letter') }}</th>
            <th style="border-top: none">{{ trans('account/global.word.adr_number') }}</th>
        @endif
        <th style="border-top: none; border-left: 1px solid #ddd">{{ trans('account/global.word.goods-description') }}</th>
        @if(config('app.source') == 'velozeficiente')
            <th style="border-top: none; border-left: 1px solid #ddd">{{ trans('account/global.word.adicional-services') }}</th>
        @endif
        @if(Setting::get('show_shipment_assembly'))
            <th style="border-top: none; border-left: 1px solid #ddd">{{ trans('account/global.word.assembly') }}</th>
        @endif
    </tr>
    @foreach($shipment->pack_dimensions as $key => $dimension)
        <tr>
            <td><small>{{ $dimension->qty ? $dimension->qty : 1  }}x</small> {{ @$packTypes[$dimension->type] }}</td>
            <td class="text-center" style="border-left: 1px solid #ddd">{{ $dimension->weight }}</td>
            <td class="text-center" style="border-left: 1px solid #ddd">{{ $dimension->length }}</td>
            <td class="text-center">{{ $dimension->width }}</td>
            <td class="text-center">{{ $dimension->height }}</td>
            <td class="text-center" style="border-left: 1px solid #ddd">{{ $dimension->length + $dimension->width + $dimension->height }}</td>
            @if(config('app.source') != 'velozeficiente')
            <td class="text-center">{{ $dimension->volume }}</td>
            @endif

            @if(Setting::get('customer_show_adr_fields'))
                <td style="border-left: 1px solid #ddd">{{ $dimension->adr_class }}</td>
                <td>{{ $dimension->adr_letter }}</td>
                <td>{{ $dimension->adr_number }}</td>
            @endif
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
            @if(config('app.source') == 'velozeficiente')
                <td style="border-left: 1px solid #ddd">
                    @if(!empty($dimension->optional_fields))
                        @foreach($dimension->optional_fields as $key => $value)
                            <i class="far fa-check-square"></i> {{ $key }}
                        @endforeach
                    @endif
                </td>
            @endif
            @if(Setting::get('show_shipment_assembly'))
                <td style="border-left: 1px solid #ddd">
                    @if(!empty($dimension->optional_fields))
                        @foreach($dimension->optional_fields as $key => $value)
                            @if($key == 'Montagem' && $value == 1)
                                <i class="far fa-check-square"></i> {{ $key }}
                            @endif
                        @endforeach
                    @endif
                </td>
            @endif
        </tr>
    @endforeach
</table>