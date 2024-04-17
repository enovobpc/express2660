<tr data-hash="master" data-addr-id="{{ @$dimensions[$key]['shipment_id'] }}" class="visible">
    <td class="input-sm">
        {{ Form::text('qty[]', @$dimensions[$key]['qty'] ? $dimensions[$key]['qty'] : 1, ['class' => 'form-control input-sm number text-center', 'style' => 'padding: 6px']) }}
        {{ Form::hidden('dim_src[]', @$dimensions[$key]['dim_src']) }}
        {{ Form::hidden('addr_id[]', @$dimensions[$key]['shipment_id']) }}
        {{ Form::hidden('sku[]', @$dimensions[$key]['sku']) }}
        {{ Form::hidden('serial_no[]', @$dimensions[$key]['serial_no']) }}
        {{ Form::hidden('lote[]', @$dimensions[$key]['lote']) }}
        {{ Form::hidden('stock[]', @$dimensions[$key]['stock']) }}
        {{ Form::hidden('product[]', @$dimensions[$key]['product_id']) }}
    </td>
    <td class="input-sm">
        {!! Form::selectWithData('box_type[]', $packTypes, @$dimensions[$key]['type'], ['class' => 'form-control select2']) !!}
    </td>
    <td>
        {{ Form::text('box_description[]', @$dimensions[$key]['description'], ['class' => 'form-control input-sm m-0 ' . (hasModule('logistic') ? (!$shipment->exists || in_array($shipment->status_id, ['1', '2']) ? 'search-sku' : 'disabled') : ''), 'placeholder' => hasModule('logistic') ? 'Descrição mercadoria ou Ref. Artigo' : '']) }}
        <span class="sku-feedback" style="{{ @$dimensions[$key]['sku'] ? '' : 'display: none' }}">
            {!! @$dimensions[$key]['sku'] ? '<small class="text-green">Ref.: '.@$dimensions[$key]['sku'].' | <b></b> Un. Stock</small>' : '' !!}
        </span>
    </td>
    <td>
        <div class="input-group input-group-money input-bordered">
            {{ Form::text('length[]', @$dimensions[$key]['length'], ['class' => 'form-control input-sm m-0 decimal', 'style' => 'padding: 6px']) }}
            <div class="input-group-addon" style="padding:5px 0">{{ Setting::get('shipments_volumes_mesure_unity') ? Setting::get('shipments_volumes_mesure_unity') : 'cm' }}</div>
        </div>
    </td>
    <td>
        <div class="input-group input-group-money input-bordered">
            {{ Form::text('width[]', @$dimensions[$key]['width'], ['class' => 'form-control input-sm m-0 decimal', 'style' => 'padding: 6px']) }}
            <div class="input-group-addon" style="padding:5px 0">{{ Setting::get('shipments_volumes_mesure_unity') ? Setting::get('shipments_volumes_mesure_unity') : 'cm' }}</div>
        </div>
    </td>
    <td>
        <div class="input-group input-group-money input-bordered">
            {{ Form::text('height[]', @$dimensions[$key]['height'], ['class' => 'form-control input-sm m-0 decimal', 'style' => 'padding: 6px']) }}
            <div class="input-group-addon" style="padding:5px 0">{{ Setting::get('shipments_volumes_mesure_unity') ? Setting::get('shipments_volumes_mesure_unity') : 'cm' }}</div>
        </div>
    </td>
    <td>
        <div class="input-group input-group-money">
            {{ Form::text('box_weight[]', @$dimensions[$key]['weight'], ['class' => 'form-control input-sm m-0 decimal', 'style' => 'padding: 6px']) }}
            {{--{{ Form::hidden('fator_m3_row[]', @$dimensions[$key]['volume'], ['class' => 'form-control input-sm m-0', 'readonly', 'style' => 'padding: 6px']) }}
            --}}
            <div class="input-group-addon" style="padding:5px 0">kg</div>
        </div>
        {{--<small class="italic m3lbl" style="{{ @$dimensions[$key]['volume'] ? 'display:black' : 'display:none' }}">M<sup>3</sup>: <span>{{ @$dimensions[$key]['volume'] }}</span></small>
    --}}
    </td>
    <td>
        {{ Form::text('fator_m3_row[]', @$dimensions[$key]['volume'], ['class' => 'form-control input-sm m-0', 'readonly', 'style' => 'padding: 6px']) }}
    </td>
    @if(Setting::get('shp_dimensions_show_price'))
    <td>
        <div class="input-group">
            {{ Form::text('box_price[]', @$dimensions[$key]['price'], ['class' => 'form-control input-sm m-0 decimal', 'style' => 'padding: 6px']) }}
            <div class="input-group-addon" style="padding:5px">{{ Setting::get('app_currency') }}</div>
        </div>
    </td>
    @endif
    @if(Setting::get('show_adr_fields'))
        <td>
            <div class="input-group">
            {{ Form::text('box_adr_class[]', @$dimensions[$key]['adr_class'], ['class' => 'form-control input-sm nospace m-0']) }}
            </div>
        </td>
        <td>
            <div class="input-group">
            {{ Form::text('box_adr_letter[]', @$dimensions[$key]['adr_letter'], ['class' => 'form-control input-sm nospace m-0']) }}
            </div>
        </td>
        <td>
            <div class="input-group">
            {{ Form::text('box_adr_number[]', @$dimensions[$key]['adr_number'], ['class' => 'form-control input-sm nospace m-0']) }}
            </div>
        </td>
    @endif
    @if(Setting::get('shp_dimensions_show_mounting'))
        <td>
            <div class="checkbox" style="margin-top: -4px;" data-target="tooltip" title="Montagem">
                <label style="padding: 0">
                    {{ Form::checkbox('box_optional_fields[][Montagem]', '1', @$dimensions[$key]['optional_fields']["Montagem"]) }}
                    <i class="fas fa-tools"></i>
                </label>
            </div>
        </td>
    @endif
    <td>
        <a href="#" class="copy-dimensions text-muted" style="padding: 5px; margin: 0 -5px 0 -2px;"
           data-toggle="tooltip"
           title="Replicar para linha baixo">
            <i class="m-t-8 far fa-share-square fa-rotate-90"></i>
        </a>
    </td>
    <td class="nbr">
        {{--{{ $key + 1 }}--}}
        <i class="fas fa-times text-red m-t-8 btn-del-dim-row"></i>
    </td>
</tr>