<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default panel-prices-tables">
        <small class="pull-right p-r-10 p-t-3 btn-pricetb-adv-opts">
            <i class="fas fa-cog" data-toggle="tooltip" title="Configurações avançadas"></i> @trans('Avançado')
        </small>
        <a role="button" class="panel-heading" data-toggle="collapse" data-parent="#accordion" href="#accordion-{{ $unity }}" aria-expanded="true" aria-controls="collapseOne">
            <h4 class="panel-title">
                <i class="fas {{ $groupIcon }}"></i>
                {{ $groupName }}
                <small>
                    @foreach($pricesTableData[$unity] as $service)
                    &bull; {{ $service->name }}
                    @endforeach
                </small>
                <i class="fas fa-caret-down pull-right"></i>
            </h4>
        </a>
        <div id="accordion-{{ $unity }}" class="panel-collapse collapse {{ @$collapsed }}" role="tabpanel" aria-labelledby="headingOne">
            <div class="panel-body p-0">
                <div class="row row-0 pricetb-adv-opts" style="padding: 15px 15px 2px; display: {{ (Request::get('origin_zone')) ? 'block' : 'none' }};">
                    <div class="col-sm-2">
                        <div class="row row-0">
                            {{--<div class="col-sm-3">
                                {{ Form::label('origin_zone', 'Origem') }}
                            </div>--}}
                            <div class="col-sm-12 input-sm" style="margin-top: -10px; margin-left: -6px">
                                {{ Form::select('origin_zone', ['' => __('Qualquer Zona Origem')] + $billingZonesList, @$originZone, ['class' => 'form-control select2', 'data-unity' => $unity]) }}
                                <span style="position: absolute;right: -25px;padding: 6px;">
                                    {!! knowledgeTip(125, __('Permite definir uma tabela de preços para uma determinada zona de recolha/origem.')) !!}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5">
                    </div>
                    <div class="col-sm-5">
                        <div class="row">
                            <div class="col-sm-6">
                            </div>
                            <div class="col-sm-6">
                                <div style="float: right; margin-top: -5px; margin-bottom: 8px;">
                                    <form action="#" class="form-inline pull-left m-r-15 form-update-prices">
                                        {{ Form::select('update_target', $servicesGroupsList, $unity, ['class' => 'hide']) }}
                                        <div class="input-group input-sm p-0" style="margin-right: -4px;">
                                            {{ Form::select('update_signal', ['add' => __('Aumentar'), 'sub' => __('Diminuir')], null, ['class' => 'form-control input-sm select2']) }}
                                        </div>
                                        <div class="input-group input-group-sm input-group-money w-60px p-0" style="margin-right: -4px;">
                                            {{ Form::text('update_percent', null, ['class' => 'form-control', 'maxlength' => 3]) }}
                                            <span class="input-group-addon">%</span>
                                        </div>
                                        <div class="input-group">
                                            <button class="btn btn-sm btn-block btn-default increment-prices">@trans('Aplicar')</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                {{ Form::open(['route' => ['admin.prices-tables.services.store', $priceTable->id, 'group' => $unity]]) }}
                {{ Form::hidden('origin_zone', @$originZone) }}

                <div class="table-prices-responsive m-b-5">
                    <table class="table table-condensed table-dashed table-hover m-b-5 table-prices" id="{{ $unity }}-services">
                        <thead>
                        <tr>
                            <th class="w-1 p-0 bg-gray column-delete" style="height: 31px">&nbsp;</th>
                            <th class="w-170px text-center bg-gray column-weight">Serviço &#8250;</th>
                            @foreach($pricesTableData[$unity] as $service)
                                <?php $unityCode  = trans('admin/global.services.unities.labels.'. $service->unity); ?>
                                <th class="text-center bg-gray" style="white-space: nowrap;" colspan="{{ count($service->zones) }}" >
                                    <span data-toggle="tooltip" title="{{ $service->name }}">{{ $service->name }}</span>
                                </th>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="w-1 p-0 column-delete" style="height: 30px; background: #f7f7f7">&nbsp;</td>
                            <td class="w-100px text-center bold bg-gray-light column-weight">{{ @$unityCode }} <i class="fas fa-angle-down"></i></td>
                            @foreach($pricesTableData[$unity] as $service)
                                @if($service->zones)
                                    @foreach($service->zones as $key => $zone)
                                        <td class="text-center text-uppercase bold bg-gray-light">
                                            <span data-toggle="tooltip" title="{{ @$billingZones[$zone] }}">{{ $zone }}</span>
                                        </td>
                                    @endforeach
                                @else
                                    <td class="text-center text-red bold bg-gray-light">PT</td>
                                @endif
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($rowsWeight[$unity] as $weightValue => $tableServices)
                            <?php
                            $isAdicional    = @$rowsAdicional[$unity][$weightValue]['is_adicional'] || $weightValue > 99999;
                            $adicionalUnity = number(@$rowsAdicional[$unity][$weightValue]['adicional_unity']);
                            ?>
                            <tr class="{{ $isAdicional ? 'rw-adicional' : '' }}">
                                <td class="column-delete">
                                    <a href="#" class="text-red hidde" data-target="#{{ $unity }}-services" data-action="table-line-remove">
                                        <i class="fas fa-times m-t-10"></i>
                                    </a>
                                </td>
                                <td class="column-weight">
                                    <div class="input-group">
                                        <div class="input-group-addon kg-adicional" data-toggle="tooltip" title="{{ @$unityCode }} Adicional">
                                            @if($isAdicional)
                                                <i class="fas fa-check-square"></i>
                                            @else
                                                <i class="far fa-square"></i>
                                            @endif
                                        </div>
                                        {{ Form::text('max[]', $weightValue, ['class' => 'form-control decimal', 'placeholder' => 'Max', 'maxlength' => 8, 'required', 'autocomplete' => 'weigh-v1', $readonly]) }}
                                    </div>
                                    @if(!$readonly)
                                        <div class="kg-adc-unity" style="{{ $isAdicional ? '' : 'display:none' }}">
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    {{ @$unityCode }} x
                                                </div>
                                                {{ Form::text('adicional_unity[]', number($adicionalUnity, 0), ['class' => 'form-control decimal', 'placeholder' => @$unityCode. ' Ad', 'maxlength' => 8, 'autocomplete' => 'adcun-v1', $readonly]) }}
                                            </div>
                                        </div>
                                        {{ Form::hidden('is_adicional[]', $isAdicional) }}
                                    @endif
                                </td>

                                @foreach($pricesTableData[$unity] as $service)
                                    @if(empty($service->zones))
                                        <?php $service->zones = [Setting::get('app_country')] ?>
                                    @endif

                                    @foreach($service->zones as $key => $zone)
                                        <?php $data = @$tableServices[@$service->id][$zone][0]; ?>
                                        <td>
                                            {{ Form::text('price['.$service->id.']['.$zone.'][]', (empty($data['price']) || $data['price'] == 0.00) ? null : number($data['price'], Setting::get('app_money_decimals')), ['class' => 'form-control decimal', 'maxlength' => 7, 'tabindex' => $data['service_id'] + ($key*1000), $readonly]) }}
                                        </td>
                                    @endforeach

                                @endforeach

                            </tr>
                        @endforeach

                        {{-- TABELA VAZIA --}}
                        @if(empty($rowsWeight[$unity]))
                            <?php $weights = explode(',', Setting::get('default_weights'));?>
                            @foreach($weights as $weightValue)
                                <?php
                                $weightValue = number($weightValue);
                                $isAdicional = $weightValue > 99999;
                                ?>
                                <tr class="{{ $isAdicional ? 'rw-adicional' : '' }}">
                                    <td class="column-delete">
                                        <a href="#" class="text-red hidde" data-target="#{{ $unity }}-services" data-action="table-line-remove">
                                            <i class="fas fa-times m-t-10"></i>
                                        </a>
                                    </td>
                                    <td class="column-weight">
                                        <div class="input-group">
                                            <div class="input-group-addon kg-adicional" data-toggle="tooltip" title="{{ @$unityCode }} Adicional">
                                                @if($isAdicional)
                                                    <i class="fas fa-check-square"></i>
                                                @else
                                                    <i class="far fa-square"></i>
                                                @endif
                                            </div>
                                            {{ Form::text('max[]', $weightValue, ['class' => 'form-control decimal', 'placeholder' => 'Max', 'maxlength' => 8, 'required', 'autocomplete' => 'weigh-v1', $readonly]) }}
                                        </div>
                                        @if(!$readonly)
                                            <div class="kg-adc-unity" style="{{ $isAdicional ? '' : 'display:none' }}">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        {{ @$unityCode }} x
                                                    </div>
                                                    {{ Form::text('adicional_unity[]', 1, ['class' => 'form-control decimal', 'placeholder' => @$unityCode. ' Ad', 'maxlength' => 8, 'autocomplete' => 'adcun-v1', $readonly]) }}
                                                </div>
                                            </div>
                                            {{ Form::hidden('is_adicional[]', $isAdicional) }}
                                        @endif
                                    </td>
                                    @foreach($pricesTableData[$unity] as $service)
                                        @if(empty($service->zones))
                                            <?php $service->zones = [Setting::get('app_country')] ?>
                                        @endif

                                        @foreach($service->zones as $key => $zone)
                                            <td>{{ Form::text('price['.$service->id.']['.$zone.'][]', null, ['class' => 'form-control decimal', 'maxlength' => 7, 'tabindex' => $service->id + $key], $readonly) }}</td>
                                        @endforeach
                                    @endforeach
                                </tr>
                            @endforeach

                        @endif
                        </tbody>
                    </table>
                </div>
                <div style="margin: 5px">
                    {{ Form::submit(__('Gravar Tabela'), array('class' => 'btn btn-sm btn-primary form-inline' ))}}
                    <button type="button" class="btn btn-sm btn-default" data-action="table-line-add" data-target="#{{ $unity }}-services">
                        <i class="fas fa-list"></i> @trans('Adicionar Linha')
                    </button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>

<style>
    .table-expenses td {
        padding: 3px 5px !important;
        vertical-align: middle !important;
    }

    .table-expenses .input-group-xs input {
        height: 27px;
    }


    .panel-prices-tables {
        position: relative;
        margin-bottom: 0;
        border-radius: 3px;
        border: 1px solid #83878b2b;
        box-shadow: 0 1px 1px rgb(154 161 168);
    }

    .panel-prices-tables .panel-heading {
        display: block;
        color: #222;
        background-color: #8d949a1c;
        border-color: transparent;
    }

    .panel-prices-tables .panel-title {
        color: #195f9b;
        font-weight: bold;
    }

    .panel-prices-tables .panel-title small {
        color: #777;
    }

    .increment-prices {
        padding: 5px 10px;
        z-index: 2;
        position: relative;
        border-radius: 0px !important;
    }

    .prices-tables-toggle {
        float: right;
        padding: 6px 0 5px;
    }

    .brd-black {
        border-top: 2px solid #333;
    }
</style>