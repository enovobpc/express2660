<div class="modal" id="modal-shipment-dimensions">
    <div class="modal-dialog modal-xlg"
         style="{{ Setting::get('customer_show_adr_fields') ? 'padding: 30px' : '' }}">
        <div class="modal-content">
            <div class="modal-header" style="background: #1f2c33">
                <h4 class="modal-title">{{ trans('account/global.word.pack-dimensions') }}</h4>
            </div>
            <div class="modal-body">
                <table class="table table-condensed m-b-0 shipment-dimensions">
                    <thead>
                    <tr>
                        <th class="w-45px">{{ trans('account/global.word.qty') }}</th>
                        <th class="w-100px">{{ trans('account/global.word.type') }}</th>
                        <th>{{ trans('account/global.word.goods-description') }} (Opcional)</th>
                        <th class="w-95px">{{ trans('account/global.word.width') }}</th>
                        <th class="w-95px">{{ trans('account/global.word.length') }}</th>
                        <th class="w-95px">{{ trans('account/global.word.height') }}</th>
                        <th class="w-95px">{{ trans('account/global.word.weight') }}</th>

                        @if(Setting::get('shp_dimensions_show_price'))
                        <th class="w-95px">{{ trans('account/global.word.price') }}</th>
                        @endif
                        <th class="w-60px hidde">M3</th>
                        @if(Setting::get('customer_show_adr_fields'))
                        <th class="w-90px">Classe ADR</th>
                        <th class="w-90px">Letra ADR</th>
                        <th class="w-90px">Nº ADR</th>
                        @endif
                        @if(Setting::get('shp_dimensions_show_mounting'))
                            <th class="w-1">
                                <i class="fas fa-tools" data-toggle="tooltip" title="Montagem Necessária"></i>
                            </th>
                        @endif
                        <th class="w-1"></th>
                        <th class="w-1"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $volumes = @$shipment->volumes ? $shipment->volumes : 5;

                    $volumes = $volumes > 5 ? $volumes : 5;
                    ?>
                    @if(!$shipment->pack_dimensions->isEmpty())
                        <?php
                        $dimensions = $shipment->pack_dimensions->toArray();
                        $volumes    = $shipment->pack_dimensions->count();
                        ?>
                    @endif
                    @for($key = 0 ; $key < $volumes ; $key++)
                        <tr data-hash="master">

                            <td>
                                {{ Form::text('qty[]', @$dimensions[$key]['qty'] ? @$dimensions[$key]['qty'] : 1, ['class' => 'form-control input-sm number', 'maxlength' => 5, 'style' => 'padding: 6px 0; text-align: center;']) }}
                                {{ Form::hidden('sku[]', @$dimensions[$key]['sku']) }}                                
                                {{ Form::hidden('serial_no[]', @$dimensions[$key]['serial_no'] ?? '') }}
                                {{ Form::hidden('lote[]', @$dimensions[$key]['lote'] ?? '') }}
                                {{ Form::hidden('stock[]', @$dimensions[$key]['stock'] ?? '') }}
                                {{ Form::hidden('product[]', @$dimensions[$key]['product_id'] ?? '') }}

                            </td>

                            <td class="input-sm bxtp">
                                {!! Form::selectWithData('box_type[]', $packTypes, @$dimensions[$key]['type'], ['class' => 'form-control select2']) !!}
                            </td>
                            <td>
                                {{ Form::text('box_description[]', @$dimensions[$key]['description'], ['class' => 'form-control input-sm  m-0 ' . (hasModule('logistic') ? (!$shipment->exists || in_array($shipment->status_id, ['1', '2']) ? 'search-sku' : 'disabled') : ''), 'placeholder' => (hasModule('logistic') && !@$isPickup ? 'Descrição material ou pesquisar SKU...' : '')]) }}
                                <span class="sku-feedback" style="{{ @$dimensions[$key]['sku'] ? '' : 'display: none' }}">
                                    {!! @$dimensions[$key]['sku'] ? '<small class="text-green">Ref.: '.@$dimensions[$key]['sku'].' | <b></b> Un. Stock</small>' : '' !!}
                                </span>
                            </td>

                            <td>
                                <div class="input-group">
                                    {{ Form::text('length[]', @$dimensions[$key]['length'], ['class' => 'form-control input-sm  decimal m-0', 'maxlength' => 7, 'style' => 'border-right:none']) }}
                                    <div class="input-group-addon" style="padding:5px;background: #fff;border-left: none;">{{ Setting::get('shipments_volumes_mesure_unity') }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    {{ Form::text('width[]', @$dimensions[$key]['width'], ['class' => 'form-control input-sm  decimal m-0', 'maxlength' => 7, 'style' => 'border-right:none']) }}
                                    <div class="input-group-addon" style="padding:5px;background: #fff;border-left: none;">{{ Setting::get('shipments_volumes_mesure_unity') }}</div>
                                </div>
                            </td>

                            <td>
                                <div class="input-group">
                                    {{ Form::text('height[]', @$dimensions[$key]['height'], ['class' => 'form-control input-sm  decimal m-0', 'maxlength' => 7, 'style' => 'border-right:none']) }}
                                    <div class="input-group-addon" style="padding:5px;background: #fff;border-left: none;">{{ Setting::get('shipments_volumes_mesure_unity') }}</div>
                                </div>
                            </td>

                            <td>
                                <div class="input-group">
                                    {{ Form::text('box_weight[]', @$dimensions[$key]['weight'] ?? '', ['class' => 'form-control input-sm  decimal m-0', 'maxlength' => 7, 'style' => 'border-right:none']) }}
                                    <div class="input-group-addon" style="padding:5px;background: #fff;border-left: none;">kg</div>
                                </div>
                            </td>

                            @if(Setting::get('shp_dimensions_show_price'))
                            <td>
                                <div class="input-group">
                                    {{ Form::text('box_price[]', @$dimensions[$key]['price'] ?? '', ['class' => 'form-control decimal m-0', 'style' => 'border-right:none']) }}
                                    <div class="input-group-addon" style="padding:5px;background: #fff;border-left: none;">
                                        {{ Setting::get('app_currency') }}
                                    </div>
                                </div>
                            </td>

                            @endif
                            <td class="hidde">
                                {{ Form::text('fator_m3_row[]', @$dimensions[$key]['volume'] ?? '', ['class' => 'form-control m-0', 'readonly']) }}
                            </td>

                            @if(Setting::get('customer_show_adr_fields'))
                            <td>
                                {{ Form::text('adr_class[]', @$dimensions[$key]['adr_class'] ?? '', ['class' => 'form-control nospace m-0']) }}
                            </td>
                            <td>
                                {{ Form::text('adr_letter[]', @$dimensions[$key]['adr_letter'] ?? '', ['class' => 'form-control nospace m-0']) }}
                            </td>
                            <td>
                                {{ Form::text('adr_number[]', @$dimensions[$key]['adr_number'] ?? '', ['class' => 'form-control nospace m-0']) }}
                            </td>
                            @endif

                            @if(Setting::get('shp_dimensions_show_mounting'))
                                <td>
                                    <div class="checkbox" style="padding-top: 5px;">
                                        <label style="padding: 0">
                                            {{ Form::checkbox('box_optional_fields[][Montagem]', '1', @$dimensions[$key]['optional_fields']["Montagem"] ?? '') }}
                                        </label>
                                    </div>
                                </td>
                            @endif
                            <td>
                                <i class="m-t-8 far fa-share-square fa-rotate-90 copy-dimensions m-t-10" data-toggle="tooltip" title="Replicar linha abaixo" style="cursor: pointer"></i>
                            </td>
                            <td class="nbr">
                                <i class="fas fa-times text-red btn-del-dim-row m-t-10" style="cursor: pointer"></i>
                            </td>
                        </tr>

                    @endfor
                    </tbody>
                </table>
                <button type="button" class="btn btn-xs btn-default m-l-3 m-t-5 btn-new-dim-row">
                    <i class="fas fa-plus"></i> {{ trans('account/shipments.modal-dimensions.new-line') }}
                </button>
            </div>
            <div class="modal-footer">
                <div class="row row-5 text-left">
                    <div class="col-xs-12 col-sm-6">
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <button type="button" class="btn btn-black confirm-dimensions pull-right">{{ trans('account/global.word.confirm') }}</button>
                        <div class="pull-right">
                            <h5 class="m-0 pull-left m-r-15">
                                <small>Volumes</small><br/>
                                <i class="fas fa-boxes"></i> <span class="dims-ttl-vols">{{ $shipment->volumes ? $shipment->volumes : '0.000' }}</span>
                            </h5>
                            <h5 class="m-0 pull-left m-r-15">
                                <small>Peso Total</small><br/>
                                <span style="position: absolute;color: #fff;font-size: 7px;font-weight: bold;padding: 5px 3.5px;">kg</span>
                                <i class="fas fa-weight-hanging"></i> <span class="dims-ttl-weight">{{ $shipment->weight ? $shipment->weight : '0.00' }}</span>kg
                            </h5>
                            <h5 class="m-0 pull-left m-r-15">
                                <small>Cubicagem</small><br/>
                                <i class="fas fa-cube"></i> <span class="dims-ttl-m3">{{ $shipment->fator_m3 ? $shipment->fator_m3 : '0.000' }}</span>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="modal-confirm-vols">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('account/shipments.modal-dimensions.confirm.title') }}</h4>
            </div>
            <div class="modal-body">
                <h4 class="m-0">
                    {{ trans('account/shipments.modal-dimensions.confirm.message') }} <span class="cvol"></span>?
                </h4>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-answer="0">{{ trans('account/global.word.no') }}</button>
                    <button type="button" class="btn btn-default" data-answer="1">{{ trans('account/global.word.yes') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .shipment-dimensions td {
        padding: 2px !important;
        border-radius: 2px;
        border-top: none !important;
    }

    .shipment-dimensions tbody tr:first-child td {
        padding-top: 5px;
    }

    .shipment-dimensions input[type="text"] {
        font-size: 13px;
        height: 33px;
        padding: 5px;
    }

    .shipment-dimensions .select2-container .select2-selection--single {
        height: 33px;
    }

    .has-error .select2-selection {
        border-color: red;
        background: #ff000021;
    }

    .search-sku::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
        font-style: italic;
        color: #ccc;
        opacity: 1; /* Firefox */
    }

    .search-sku:-ms-input-placeholder { /* Internet Explorer 10-11 */
        color: #ccc;
        font-style: italic;
    }

    .search-sku::-ms-input-placeholder { /* Microsoft Edge */
        color: #ccc;
        font-style: italic;
    }
</style>