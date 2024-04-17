<div class="modal" id="modal-shipment-dimensions">
    <div class="modal-dialog modal-xl" style="padding-left: 20px;padding-right: 20px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detalhe da mercadoria</h4>
            </div>
            <div class="modal-body">
                @if(@$shipment->exists && !in_array($shipment->status_id, [\App\Models\ShippingStatus::PENDING_ID, \App\Models\ShippingStatus::ACCEPTED_ID]))
                <div class="alert" style="margin: -16px -15px 15px;
    border-radius: 0;
    background: #fffabc;
    border-bottom: 1px solid #ffef30;
    color: #584a00;">
                    <p class="m-0"><i class="fas fa-exclamation-triangle"></i> O estado deste envio já não permite fazer alterações aos seus items.</p>
                </div>
                @endif
                <table class="table table-condensed m-b-0 shipment-dimensions">
                    <thead>
                        <tr>
                            <th class="w-1"></th>
                            <th class="w-40px">Qtd</th>
                            <th class="w-90px">Pacote</th>
                            <th>Descrição mercadoria <i><small>(opcional)</small></i></th>
                            <th class="w-100px">Comprim.</th>
                            <th class="w-100px">Largura</th>
                            <th class="w-100px">Altura</th>
                            <th class="w-95px">Peso</th>
                            <th style="width: 62px">M3</th>
                            @if(Setting::get('shp_dimensions_show_price'))
                            <th class="w-85px">Preço Un.</th>
                            @endif
                            @if(Setting::get('show_adr_fields'))
                                <th class="w-50px">Classe <div style="position: absolute;top: 0;border-bottom: 1px solid #333;width: 140px;text-align: center;">ADR</div></th>
                                <th class="w-50px">Letra</th>
                                <th class="w-50px">Num.</th>
                            @endif
                            @if(Setting::get('shipment_price_per_package_line'))
                                <th class="w-95px">Total <i class="fas fa-sync-alt btn-sync-all-dims" data-toggle="tooltip" title="Atualizar preços" style="float: right;margin-top: 4px;"></i></th>
                            @endif
                            @if(Setting::get('shp_dimensions_show_mounting'))
                                <th class="w-105px"></th>
                            @endif
                            {{--<th class="w-1"></th>--}}
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $volumes = @$shipment->volumes ? $shipment->volumes : 1;
                    ?>
                    @if(!$shipment->pack_dimensions->isEmpty())
                        <?php
                        $dimensions = $shipment->pack_dimensions->toArray();
                        $volumes    = $shipment->pack_dimensions->count() + 1;
                        ?>
                    @endif

                        @for($key = 0 ; $key < $volumes ; $key++)
                            <tr data-hash="master">
                                <td class="nbr">
                                    {{--{{ $key + 1 }}--}}
                                    <i class="fas fa-times text-red btn-del-dim-row"></i>
                                </td>
                                <td class="input-sm">
                                    {{ Form::text('qty[]', @$dimensions[$key]['qty'] ? $dimensions[$key]['qty'] : 1, ['class' => 'form-control input-sm number text-center', 'style' => 'padding: 6px']) }}
                                    {{ Form::hidden('dim_src[]') }}
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
                                    {{ Form::text('box_description[]', @$dimensions[$key]['description'], ['class' => 'form-control input-sm m-0 ' . (hasModule('logistic') ? (!$shipment->exists || in_array($shipment->status_id, ['1', '2']) ? 'search-sku' : 'disabled') : '')]) }}
                                    <span class="sku-feedback" style="{{ @$dimensions[$key]['sku'] ? '' : 'display: none' }}">
                                        {!! @$dimensions[$key]['sku'] ? '<small class="text-green">Ref.: '.@$dimensions[$key]['sku'].' | <b></b> Un. Stock</small>' : '' !!}
                                    </span>
                                </td>
                                <td>
                                    <div class="input-group input-bordered">
                                        {{ Form::text('length[]', @$dimensions[$key]['length'], ['class' => 'form-control input-sm m-0 decimal', 'style' => 'padding: 6px']) }}
                                        <div class="input-group-addon" style="padding:5px">{{ Setting::get('shipments_volumes_mesure_unity') ? Setting::get('shipments_volumes_mesure_unity') : 'cm' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-bordered">
                                        {{ Form::text('width[]', @$dimensions[$key]['width'], ['class' => 'form-control input-sm m-0 decimal', 'style' => 'padding: 6px']) }}
                                        <div class="input-group-addon" style="padding:5px">{{ Setting::get('shipments_volumes_mesure_unity') ? Setting::get('shipments_volumes_mesure_unity') : 'cm' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-bordered">
                                        {{ Form::text('height[]', @$dimensions[$key]['height'], ['class' => 'form-control input-sm m-0 decimal', 'style' => 'padding: 6px']) }}
                                        <div class="input-group-addon" style="padding:5px">{{ Setting::get('shipments_volumes_mesure_unity') ? Setting::get('shipments_volumes_mesure_unity') : 'cm' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        {{ Form::text('box_weight[]', @$dimensions[$key]['weight'], ['class' => 'form-control input-sm m-0 decimal', 'style' => 'padding: 6px']) }}
                                        {{--{{ Form::hidden('fator_m3_row[]', @$dimensions[$key]['volume'], ['class' => 'form-control input-sm m-0', 'readonly', 'style' => 'padding: 6px']) }}
                                        --}}
                                        <div class="input-group-addon" style="padding:5px">kg</div>
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
                                @if(Setting::get('shipment_price_per_package_line'))
                                    <td>
                                        <div class="input-group">
                                            <div class="input-group">
                                                {{ Form::text('box_total_price[]', @$dimensions[$key]['total_price'], ['class' => 'form-control input-sm nospace m-0', 'style' => 'border-right: 0']) }}
                                                <div class="input-group-addon pldg" style="padding:5px">€</div>
                                            </div>

                                            {{ Form::hidden('box_cost[]', @$dimensions[$key]['total_cost']) }}
                                        </div>
                                    </td>
                                @endif
                                @if(Setting::get('shp_dimensions_show_mounting'))
                                    <td>
                                        <div class="checkbox" style="margin-top: -4px;">
                                            <label style="padding: 0">
                                                {{ Form::checkbox('box_optional_fields[][Montagem]', '1', @$dimensions[$key]['optional_fields']["Montagem"]) }}
                                                Montagem
                                            </label>
                                        </div>
                                    </td>
                                @endif
                                {{--<td>
                                    <button type="button" class="btn btn-sm btn-default copy-dimensions" style="padding: 5px; margin: 0 -5px 0 -2px;" data-toggle="tooltip" title="Replicar para linha baixo">
                                        <i class="fas fa-level-down-alt"></i>
                                    </button>
                                </td>--}}
                            </tr>
                        @endfor
                        </tbody>
                </table>
                <button type="button" class="btn btn-xs btn-default m-l-15 btn-new-dim-row">
                    <i class="fas fa-plus"></i> Adicionar nova linha
                </button>
            </div>
            <div class="modal-footer">
                <div class="row row-5 text-left" style="margin-top: -15px">
                    <div class="col-xs-12 col-sm-9 input-sm">
                        {{--{{ Form::select('packaging_type', $packTypes, null, ['style' => 'display:none']) }}--}}
                        {{--@if(Setting::get('show_adr_fields'))
                        <div class="row row-5">

                            <div class="col-sm-2">
                                <label class="control-label p-r-0">Classe ADR</label>
                                {{ Form::text('adr_class', null, ['class' => 'form-control input-sm']) }}
                            </div>
                            <div class="col-sm-2">
                                <label class="control-label p-r-0">Letra ADR</label>
                                {{ Form::text('adr_letter', null, ['class' => 'form-control input-sm']) }}
                            </div>
                            <div class="col-sm-2">
                                <label class="control-label p-r-0">Nº ADR</label>
                                {{ Form::text('adr_number', null, ['class' => 'form-control input-sm']) }}
                            </div>
                        </div>
                        @endif--}}
                    </div>
                    <div class="col-xs-12 col-sm-3">
                        <button type="button" class="btn btn-default confirm-dimensions pull-right m-t-20">Confirmar</button>
                        <button type="button" class="btn btn-default cancel-dimensions pull-right m-t-20 m-r-5">Cancelar</button>
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
                <h4 class="modal-title">Alterar número volumes</h4>
            </div>
            <div class="modal-body">
                <h4 class="m-0">
                    Pretende alterar o número de volumes da expedição para <span class="cvol"></span>?
                </h4>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-answer="0">Não</button>
                    <button type="button" class="btn btn-default" data-answer="1">Sim</button>
                </div>
            </div>
        </div>
    </div>
</div>