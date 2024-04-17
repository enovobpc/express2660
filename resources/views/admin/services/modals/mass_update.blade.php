<div class="modal" id="modal-mass-update">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.services.selected.update']]) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Editar em massa</h4>
            </div>
            <div class="modal-body">
                <div class="row row-10">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('assign_provider_id', 'Fornecedor:') }}
                            {{ Form::select('assign_provider_id', ['' => '- Não alterar -', '-1' => 'Nenhum fornecedor'] + $providers, null, ['class' => 'form-control select2']) }}
                        </div>
                        <div class="row row-5">
                            <div class="col-xs-12">
                                <a href="#" class="select-all-zones pull-right">Sel. Todos</a>
                                {{ Form::label('assign_zones[]', 'Zonas de faturação') }}
                                {!! tip('Cada zona de faturação corresponderá a uma coluna na tabela de preços.') !!}
                                (<span class="count-selected">0</span> selec.)
                                <div style="height: 300px;overflow: scroll;border: 1px solid #ddd;padding: 8px; position: relative;">
                                    <div style="position: relative;
    left: -9px;
    top: -9px;
    right: -9px;
    height: 30px;
    width: 105%;">
                                        {{ Form::text('filter_box', null, ['class' => 'form-control', 'placeholder' => 'Encontrar na lista...']) }}
                                    </div>
                                    @foreach($billingZones as $unity => $zones)
                                        <p style="display: block; margin-bottom: 5px" class="bold text-uppercase m-t-0 m-b-0" data-label="{{ $unity }}">{{ $unity == 'zip_code' ? 'Zonas Por Códigos Postais' : 'Zonas Por País' }}</p>
                                        @foreach($zones as $zone)
                                            <div class="checkbox m-t-5 m-b-8" data-unity="{{ $unity }}" data-filter-text="{{ $zone->code }} {{ strtolower($zone->name) }}">
                                                <label style="padding-left: 0">
                                                    {{ Form::checkbox('assign_zones[]', $zone->code, null, ['class' => 'row-zone']) }}
                                                    <span class="label label-default text-uppercase" style="min-width: 55px;font-size: 11px;display: inline-block;">{{ $zone->code }}</span> {{ $zone->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('assign_group', 'Organizar por') }}
                            {{ Form::select('assign_group', ['' => '- Não alterar -'] + $servicesGroups, null, ['class' => 'form-control select2']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('assign_unity', 'O preço é calculado baseando-se...') }}
                            {{ Form::select('assign_unity', ['' => '- Não alterar -'] + $types, null, ['class' => 'form-control select2']) }}
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    {{ Form::label('min_hour', 'Horário') }}
                                    {!! tip('Este serviço só estará disponível no horário indicado. Não é possível aos clientes escolherem este serviço fora do horário indicado.') !!}

                                    <div class="input-group input-sm" style="padding: 0; width: 117px">
                                        <span class="input-group-addon"><div class="w-20px">Das</div></span>
                                        {{ Form::select('min_hour', ['' => 'Manter'] + $hours, null, ['class' => 'form-control w-100 select2']) }}
                                        <span class="input-group-addon"><div class="w-20px">Até</div></span>
                                        {{ Form::select('max_hour', ['' => 'Manter'] + $hours, null, ['class' => 'form-control w-100 select2']) }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    {{ Form::label('pickup_hour_difference', 'Dif. Horária Mínima', ['data-toggle' => 'tooltip', 'title' => 'Diferença Horária Mínima de Recolha']) }}
                                    {{ Form::select('pickup_hour_difference', ['' => '- Não alterar -'] + listHours(10, 1, 0, 0, 8), null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <small><a href="#" class="pull-right select-all-weekdays">Sel. Todos</a></small>
                                    {{ Form::label('week_days[]', 'Disponível nos dias') }}
                                    {!! tip("Este serviço só estará disponível nos dias de semana indicados.") !!}
                                    {{ Form::select('week_days[]', trans('datetime.list-weekday'), null, ['class' => 'form-control select2', 'multiple', 'data-placeholder' => '- Não alterar -']) }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('assign_service_id', 'Serv. Recolha Associado:') }}
                            {{ Form::select('assign_service_id', ['' => '- Não alterar -', '-1' => 'Nenhum Serviço'] + $pickupServices, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>

                {{--<div class="checkbox">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('calc_prices', 1, true) }}
                        Calcular de novo os preços de cada envio.
                    </label>
                </div>--}}
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">Gravar</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>