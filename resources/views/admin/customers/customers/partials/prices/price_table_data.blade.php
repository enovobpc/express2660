@php
    $serviceGroup = $servicesGroups->first();

    $readonly = '';

    if(!Auth::user()->hasRole(Config::get('permissions.role.admin'))) {
        if(Auth::user()->can('prices_tables_view') || $customer->price_table_id) {
            $readonly = 'readonly';
        }
    }
@endphp

@if(@$pricesTableData[$serviceGroup->code] && !$pricesTableData[$serviceGroup->code]->isEmpty())
    <?php
    $unity      = $serviceGroup->code;
    $originZone = Request::get('origin-zone');
    ?>
@endif

@if (!$customer->price_table_id && !@$customer->prices_tables[$unity] && Auth::user()->ability(Config::get('permissions.role.admin'), 'prices_tables'))
    {{ Form::open(['route' => ['admin.customers.services.store', $customer->id, 'group' => $unity]]) }}
    {{ Form::hidden('origin_zone', @$originZone) }}
@endif
<div class="table-prices-responsive m-b-5" style="{{ @$customer->prices_tables[$unity] ? 'opacity: 0.4' : '' }}">
    <table class="table table-condensed table-dashed table-hover m-b-5 table-prices" id="{{ $unity }}-services">
        <thead>
            <tr>
                <th class="w-1 p-0 bg-gray column-delete" style="height: 31px">&nbsp;</th>
                <th class="w-170px text-center bg-gray column-weight">@trans('Serviço') &#8250;</th>
                @foreach ($pricesTableData[$unity] as $service)
                    <?php $unityCode = trans('admin/global.services.unities.labels.' . $service->unity); ?>
                    <th class="text-center bg-gray" style="white-space: nowrap;" colspan="{{ count($service->zones) }}">
                        <span data-toggle="tooltip" title="{{ $service->name }}">{{ $service->name }}</span>
                    </th>
                @endforeach
            </tr>
            <tr>
                <td class="w-1 p-0 column-delete" style="height: 30px; background: #f7f7f7">&nbsp;</td>
                <td class="w-100px text-center bold bg-gray-light column-weight">{{ @$unityCode }} <i class="fas fa-angle-down"></i></td>
                @foreach ($pricesTableData[$unity] as $service)
                    @if ($service->zones)
                        @foreach ($service->zones as $key => $zone)
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
            @foreach ($rowsWeight[$unity] as $weightValue => $tableServices)
                <?php
                $isAdicional = @$rowsAdicional[$unity][$weightValue]['is_adicional'] || $weightValue > 99999;
                $adicionalUnity = number(@$rowsAdicional[$unity][$weightValue]['adicional_unity']);
                ?>
                <tr class="{{ $isAdicional ? 'rw-adicional' : '' }}">
                    <td class="column-delete">
                        <a class="text-red hidde" data-target="#{{ $unity }}-services" data-action="table-line-remove" href="#">
                            <i class="fas fa-times m-t-10"></i>
                        </a>
                    </td>
                    <td class="column-weight">
                        <div class="input-group">
                            <div class="input-group-addon kg-adicional" data-toggle="tooltip" title="{{ @$unityCode }} Adicional">
                                @if ($isAdicional)
                                    <i class="fas fa-check-square"></i>
                                @else
                                    <i class="far fa-square"></i>
                                @endif
                            </div>
                            {{ Form::text('max[]', $weightValue, ['class' => 'form-control decimal', 'placeholder' => 'Max', 'maxlength' => 8, 'required', 'autocomplete' => 'weigh-v1', $readonly]) }}
                        </div>
                        @if (!$readonly)
                            <div class="kg-adc-unity" style="{{ $isAdicional ? '' : 'display:none' }}">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        {{ @$unityCode }} x
                                    </div>
                                    {{ Form::text('adicional_unity[]', number($adicionalUnity, 0), ['class' => 'form-control decimal', 'placeholder' => @$unityCode . ' Ad', 'maxlength' => 8, 'autocomplete' => 'adcun-v1', $readonly]) }}
                                </div>
                            </div>
                            {{ Form::hidden('is_adicional[]', $isAdicional) }}
                        @endif
                    </td>

                    @foreach ($pricesTableData[$unity] as $service)
                        @if (empty($service->zones))
                            <?php $service->zones = [Setting::get('app_country')]; ?>
                        @endif

                        @foreach ($service->zones as $key => $zone)
                            <?php $data = @$tableServices[@$service->id][$zone][0]; ?>
                            <td>
                                {{ Form::text('price[' . $service->id . '][' . $zone . '][]', empty($data['price']) || $data['price'] == 0.0 ? null : number($data['price'], Setting::get('app_money_decimals')), ['class' => 'form-control decimal', 'maxlength' => 7, 'tabindex' => $data['service_id'] + $key * 1000, $readonly]) }}
                            </td>
                        @endforeach
                    @endforeach

                </tr>
            @endforeach

            {{-- TABELA VAZIA --}}
            @if (empty($rowsWeight[$unity]))
                <?php $weights = explode(',', Setting::get('default_weights')); ?>
                @foreach ($weights as $weightValue)
                    <?php
                    $weightValue = number($weightValue);
                    $isAdicional = $weightValue > 99999;
                    ?>
                    <tr class="{{ $isAdicional ? 'rw-adicional' : '' }}">
                        <td class="column-delete">
                            <a class="text-red hidde" data-target="#{{ $unity }}-services" data-action="table-line-remove" href="#">
                                <i class="fas fa-times m-t-10"></i>
                            </a>
                        </td>
                        <td class="column-weight">
                            <div class="input-group">
                                <div class="input-group-addon kg-adicional" data-toggle="tooltip" title="{{ @$unityCode }} Adicional">
                                    @if ($isAdicional)
                                        <i class="fas fa-check-square"></i>
                                    @else
                                        <i class="far fa-square"></i>
                                    @endif
                                </div>
                                {{ Form::text('max[]', $weightValue, ['class' => 'form-control decimal', 'placeholder' => 'Max', 'maxlength' => 8, 'required', 'autocomplete' => 'weigh-v1', $readonly]) }}
                            </div>
                            @if (!$readonly)
                                <div class="kg-adc-unity" style="{{ $isAdicional ? '' : 'display:none' }}">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            {{ @$unityCode }} x
                                        </div>
                                        {{ Form::text('adicional_unity[]', 1, ['class' => 'form-control decimal', 'placeholder' => @$unityCode . ' Ad', 'maxlength' => 8, 'autocomplete' => 'adcun-v1', $readonly]) }}
                                    </div>
                                </div>
                                {{ Form::hidden('is_adicional[]', $isAdicional) }}
                            @endif
                        </td>
                        @foreach ($pricesTableData[$unity] as $service)
                            @if (empty($service->zones))
                                <?php $service->zones = [Setting::get('app_country')]; ?>
                            @endif

                            @foreach ($service->zones as $key => $zone)
                                <td>{{ Form::text('price[' . $service->id . '][' . $zone . '][]', null, ['class' => 'form-control decimal', 'maxlength' => 7, 'tabindex' => $service->id + $key], $readonly) }}</td>
                            @endforeach
                        @endforeach
                    </tr>
                @endforeach

            @endif
        </tbody>
    </table>
</div>

@if (!$customer->price_table_id && !@$customer->prices_tables[$unity] && Auth::user()->ability(Config::get('permissions.role.admin'), 'prices_tables'))
    <div style="margin: 5px">
        {{ Form::submit('Gravar Tabela', ['class' => 'btn btn-sm btn-primary form-inline']) }}
        <button class="btn btn-sm btn-default" data-action="table-line-add" data-target="#{{ $unity }}-services" type="button">
            <i class="fas fa-list"></i> @trans('Adicionar Linha')
        </button>
    </div>
    {{ Form::close() }}
@endif