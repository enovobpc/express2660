<div class="row row-5">
    <div class="col-sm-12">
        <h4 class="m-t-0  bold text-blue">Ponto de Situação</h4>
        <div class="btn-group business-status">
            @foreach(trans('admin/prospects.status') as $key => $status)
                @if($prospect->business_status == $key)
                    <button type="button" class="btn btn-default" style="background: {{ trans(trans('admin/prospects.status-label.'.$key)) }}; border-color: {{ trans(trans('admin/prospects.status-label.'.$key)) }}; color: #fff" data-color="{{ trans(trans('admin/prospects.status-label.'.$key)) }}" data-id="{{ $key }}">{{ $status }}</button>
                @else
                    <button type="button" class="btn btn-default" data-color="{{ trans(trans('admin/prospects.status-label.'.$key)) }}" data-id="{{ $key }}">{{ $status }}</button>
                @endif
            @endforeach
        </div>
        {{ Form::hidden('business_status') }}
        <hr/>
    </div>
    <div class="col-sm-7">
        <h4 class="m-0  bold text-blue">@trans('Operacional')</h4>
        <div>
            <div class="col-sm-3">
                <div class="checkbox m-t-25">
                    <label style="padding: 0">
                        {{ Form::checkbox('pickup_daily', 1) }}
                        @trans('Recolha Diária?')'
                    </label>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {{ Form::label('pickup_schedule', __('Horário de Recolha')) }}
                    {{ Form::text('pickup_schedule', null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {{ Form::label('courier', __('Transportador Atual')) }}
                    {{ Form::select('courier', ['' => ''] + trans('admin/prospects.couriers'), null, ['class' => 'form-control select2']) }}
                </div>
            </div>
        </div>
        <h4 class="bold text-blue">@trans('Quantidade média de envios')</h4>
        <table class="table table-condensed">
            <tr>
                <th>@trans('Por Dia')</th>
                <th>@trans('Por Semana')</th>
                <th>@trans('Por Mês')</th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td>{{ Form::text('shipments_qtd_day', null, ['class' => 'form-control']) }}</td>
                <td>{{ Form::text('shipments_qtd_week', null, ['class' => 'form-control']) }}</td>
                <td>{{ Form::text('shipments_qtd_month', null, ['class' => 'form-control']) }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th>@trans('Local/Regional')</th>
                <th>@trans('Nacional')</th>
                <th>@trans('Espanha')</th>
                <th>@trans('Internacional')</th>
                <th>@trans('Ilhas')</th>
            </tr>
            <tr>
                <td>{{ Form::text('shipments_qtd_day', null, ['class' => 'form-control']) }}</td>
                <td>{{ Form::text('shipments_qtd_week', null, ['class' => 'form-control']) }}</td>
                <td>{{ Form::text('shipments_qtd_month', null, ['class' => 'form-control']) }}</td>
                <td>{{ Form::text('shipments_qtd_charge', null, ['class' => 'form-control']) }}</td>
                <td>{{ Form::text('shipments_qtd_devolutions', null, ['class' => 'form-control']) }}</td>
            </tr>
            <tr>
                <th>@trans('Cobranças')</th>
                <th>@trans('Recolhas')</th>
                <th>@trans('Devoluções')</th>
                <th>@trans('Peso Médio')</th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td>{{ Form::text('shipments_qtd_charge', null, ['class' => 'form-control']) }}</td>
                <td>{{ Form::text('shipments_qtd_pickups', null, ['class' => 'form-control']) }}</td>
                <td>{{ Form::text('shipments_qtd_devolutions', null, ['class' => 'form-control']) }}</td>
                <td>
                    <div class="input-group">
                        {{ Form::text('shipments_average_weight', null, ['class' => 'form-control']) }}
                        <span class="input-group-addon">KG</span>
                    </div>
                </td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <div class="row">
            <div class="col-sm-12">
                <div class="col-sm-6">
                    <h4 class="m-b-0 bold text-blue">@trans('Formato dos Envios')</h4>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="checkbox">
                                <label>
                                    {{ Form::checkbox('shipments_format[]', 'small_box') }}
                                    @trans('Caixas Pequenas/Médias')
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    {{ Form::checkbox('shipments_format[]', 'big_box') }}
                                    @trans('Caixas Grandes')
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    {{ Form::checkbox('shipments_format[]', 'bag') }}
                                    @trans('Sacos ou Envelopes')
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    {{ Form::checkbox('shipments_format[]', 'pallet') }}
                                    @trans('Paletes')
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    {{ Form::checkbox('shipments_format[]', 'other') }}
                                    @trans('Outros')
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <h4 class="m-b-0 bold text-blue">@trans('Características dos Envios')</h4>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="checkbox">
                                <label>
                                    {{ Form::checkbox('shipments_features[]', 'urgente') }}
                                    @trans('Urgente')
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    {{ Form::checkbox('shipments_features[]', 'fragil') }}
                                    @trans('Frágil')
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    {{ Form::checkbox('shipments_features[]', 'pecas_auto') }}
                                    @trans('Peças Auto')
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    {{ Form::checkbox('shipments_features[]', 'liquidos') }}
                                    @trans('Liquidos')
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-5">
        <div class="p-l-30">
            <h4 class="m-t-0 m-b-15 bold text-blue">@trans('Observações e Anotações')</h4>
            <div class="form-group m-0">
                {{ Form::textarea('obs_business', null, ['class' => 'form-control', 'rows' => 24]) }}
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <h4 class="m-b-0 bold text-blue">@trans('Tipologia dos Serviços')</h4>
        <div class="row">
            @foreach($servicesList as $id => $name)
                <div class="col-sm-3">
                    <div class="checkbox m-b-0 m-t-3">
                        <label style="padding-left: 15px">
                            {{ Form::checkbox('shipments_service[]', $id) }}
                            {{ $name }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<hr/>
<button type="submit" class="btn btn-primary pull-left">@trans('Gravar')</button>
<div class="clearfix"></div>
