<div class="row">
    <div class="col-sm-12">
        <div class="table-goods-hawb-alert" style="{{ $waybill->exists && $waybill->has_hawb ? '' : 'display:none' }}">
            <h4 class="m-t-5 m-b-20 text-blue">
                <i class="fas fa-info-circle"></i> Existem HAWB para esta carta de Porte. A mercadoria é assumida automáticamente a partir das respetivas HAWB.
            </h4>
        </div>
        <div class="table-goods-container" style="position: relative">
            <div class="layer-disabled" style="{{ $waybill->exists && $waybill->has_hawb ? '' : 'display:none' }}"></div>
            <table class="table table-condensed m-0 table-goods">
                <tr class="bg-gray-light">
                    <th class="w-100px">Volumes</th>
                    <th>Unidade</th>
                    <th>Peso Bruto</th>
                    <th>Peso Taxável</th>
                    <th class="w-150px">Classe Tarifa</th>
                    <th>Tarifa</th>
                    <th>Nº Tarifa Esp.</th>
                    <th>Total</th>
                    <th class="w-1"></th>
                </tr>
                <?php
                $rowsVisible = 3;

                if($waybill->exists) {
                    $totalGoods  = count($waybill->goods);
                    $rowsVisible = $totalGoods > $rowsVisible ? $totalGoods : $rowsVisible;
                }
                ?>
                @for($i=0 ; $i<12 ; $i++)
                <tr style="{{ $i >= $rowsVisible ? 'display:none' : '' }}">
                    <td style="padding-left: 0">
                        {{ Form::text('goods['.$i.'][volumes]', null, ['class' => 'form-control input-sm volumes']) }}
                    </td>
                    <td class="input-sm">
                        {{ Form::select('goods['.$i.'][unity]', trans('admin/air_waybills.unities'), null, ['class' => 'form-control select2 input-sm']) }}
                    </td>
                    <td>
                        <div class="input-group">
                            {{ Form::text('goods['.$i.'][weight]', null, ['class' => 'form-control input-sm weight']) }}
                            <span class="input-group-addon">kg</span>
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            {{ Form::text('goods['.$i.'][chargeable_weight]', null, ['class' => 'form-control input-sm chargeable-weight']) }}
                            <span class="input-group-addon">kg</span>
                        </div>
                    </td>
                    <td class="input-sm">
                        {{ Form::select('goods['.$i.'][rate_class]', trans('admin/air_waybills.rate_classes'), null, ['class' => 'form-control select2 input-sm rate_class']) }}
                    </td>
                    <td>
                        <div class="input-group">
                            {{ Form::text('goods['.$i.'][rate_charge]', null, ['class' => 'form-control input-sm rate-charge']) }}
                            <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                        </div>
                    </td>
                    <td>
                        {{ Form::text('goods['.$i.'][rate_no]', null, ['class' => 'form-control input-sm']) }}
                    </td>
                    <td>
                        <div class="input-group">
                            {{ Form::text('goods['.$i.'][total]', null, ['class' => 'form-control input-sm total', 'readonly']) }}
                            <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                        </div>
                    </td>
                    <td>
                        <a href="#" class="text-red remove-goods">
                            <i class="fas fa-times m-t-8"></i>
                        </a>
                    </td>
                </tr>
                @endfor
            </table>
            {{ Form::hidden('volumes') }}
            {{ Form::hidden('weight') }}
            {{ Form::hidden('chargable_weight') }}
            <button type="button" class="btn btn-xs btn-default btn-add-goods"><i class="fas fa-plus"></i> Adicionar Artigo</button>
        </div>
        <hr style="margin: 15px 0 10px 0;"/>
        <div class="row">
            <div class="col-sm-1">
                <div class="form-group form-group-sm m-b-5" style="margin-left: 0">
                    {{ Form::label('customs_status', 'Estatuto Aduaneiro', ['class' => 'p-r-0', 'style' => 'line-height: 14px;']) }}
                    <div class="clearfix"></div>
                    @foreach(trans('admin/air_waybills.customs_status') as $key => $item)
                        <label style="width: 45px">{{ Form::radio('customs_status', $key, null, ['required', 'id' => 'status-' . $key]) }} {{ $item }}</label>
                    @endforeach
                </div>
            </div>
            <div class="col-sm-11">
                <div class="row">
                    <div class="col-sm-4 p-r-0">
                        <div class="form-group form-group-sm m-b-0">
                            <div class="col-sm-12">
                                {{ Form::label('adicional_info', 'Informações Adicionais', ['class' => 'p-r-0']) }}
                                {{ Form::textarea('adicional_info', null, ['class' => 'form-control', 'rows' => 10]) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 p-r-0">
                        <div class="form-group form-group-sm m-b-0">
                            <div class="col-sm-12">
                                {{ Form::label('handling_info', 'Informações de Manuseamento', ['class' => 'p-r-0']) }}
                                {{ Form::textarea('handling_info', null, ['class' => 'form-control', 'rows' => 10]) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group form-group-sm m-b-0">
                            <div class="col-sm-12">
                                {{ Form::label('nature_quantity_info', 'Natureza e Quantidade', ['class' => 'p-r-0']) }}
                                {{ Form::textarea('nature_quantity_info', null, ['class' => 'form-control', 'rows' => 10]) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>