@if(Auth::user()->ability(Config::get('permissions.role.admin'), 'prices_tables'))
    {{ Form::model($customer, ['route' => ['admin.customers.update', $customer->id], 'method' => 'PUT']) }}
    <button type="submit" class="btn btn-sm btn-primary m-b-10" data-loading-text="A gravar...">
        <i class="fas fa-save"></i> @trans('Gravar Volumetrias')
    </button>
@endif

<div class="row row-5">
    <div class="col-xs-12">
        @if($allServices)
            <table class="table table-condensed table-expenses">
                <tr>
                    <th class="bg-gray w-60px">@trans('Código')</th>
                    <th class="bg-gray">@trans('Serviço')</th>
                    <th class="bg-gray">@trans('Grupo')</th>
                    <th class="bg-gray w-150px">@trans('Zona')</th>
                    <th class="bg-gray w-110px">@trans('Dimensão Min')</th>
                    <th class="bg-gray w-100px">@trans('Cubicagem')</th>
                </tr>

                <?php $lastService = null ?>
                @foreach($allServices as $service)
                    @if($service->zones)
                        @foreach($service->zones as $key => $zone)
                            <?php
                            $valMin = @$customer->custom_volumetries[$service->id]['dim_min'][$zone];
                            $valMin = $valMin ? number($valMin) : '';

                            $coeficient = @$customer->custom_volumetries[$service->id]['coeficient'][$zone];
                            $coeficient = $coeficient ? number($coeficient) : '';

                            ?>
                            <tr class="{{ $lastService && $lastService != $service->id ? 'brd-black' : ''}}">
                                @if(!$key)
                                    <td rowspan="{{ count($service->zones) }}" style="vertical-align: top !important;">{{ $service->code }}</td>
                                    <td rowspan="{{ count($service->zones) }}" style="vertical-align: top !important;">{{ $service->name }}</td>
                                    <td rowspan="{{ count($service->zones) }}" style="vertical-align: top !important;">{{ @$service->serviceGroup->name }}</td>
                                @endif
                                <td class="brd-no">{{ @$billingZonesList[$zone] }}</td>
                                <td class="brd-no">
                                    <div class="input-group input-group-xs input-group-money">
                                        {{ Form::text('custom_volumetries['.$service->id.'][dim_min]['.$zone.']', $valMin, ['class' => 'form-control decimal text-right', 'maxlength' => 6, 'placeholder' => 'Auto']) }}
                                        <div class="input-group-addon">
                                            @trans('cm')
                                        </div>
                                    </div>
                                </td>
                                <td class="brd-no">
                                    {{ Form::text('custom_volumetries['.$service->id.'][coeficient]['.$zone.']', $coeficient, ['class' => 'form-control decimal input-sm text-right', 'maxlength' => 6, 'placeholder' => 'Auto']) }}
                                </td>
                            </tr>
                            <?php $lastService = $service->id; ?>
                        @endforeach

                    @endif
                @endforeach
            </table>
        @endif
    </div>
</div>

@if(Setting::get('shipments_average_weight'))
    <div class="col-sm-6">
        <label>@trans('Preços Escalonados')</label>
        {!! tip(__('Se esta opção estiver ativa, o preço do envio é calculado com base no peso médio da encomenda e multiplicado pelo número de volumes (Preço = Peso total/Volumes x Volumes)')) !!}
        <div class="checkbox m-t-5 m-b-15">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('average_weight', 1, null) }}
                @trans('Ativar preços escalonados')
            </label>
        </div>
    </div>
@endif


@if(Auth::user()->ability(Config::get('permissions.role.admin'), 'prices_tables'))
    <button type="submit" class="btn btn-sm btn-primary" data-loading-text="A gravar...">
        <i class="fas fa-save"></i> @trans('Gravar Volumetrias')
    </button>
    {{ Form::close() }}
@endif





{{--

@if(Setting::get('insurance_tax'))
                <div class="col-xs-6">
                    <div class="form-group">
                        {{ Form::label('insurance_tax', 'Taxa Seguro') }}
                        <div class="input-group">
                            {{ Form::text('insurance_tax', null, ['class' => 'form-control decimal', 'placeholder' => Setting::get('insurance_tax')]) }}
                            <div class="input-group-addon">%</div>
                        </div>
                    </div>
                </div>
            @endif--}}