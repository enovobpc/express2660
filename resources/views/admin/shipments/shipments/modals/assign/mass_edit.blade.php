<div class="modal" id="modal-assign-vehicle">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.shipments.selected.update', 'vehicle']]) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Associar registos a viatura ou motorista</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5">

                    <div class="col-sm-2">
                        <div class="form-group">
                            {{ Form::label('assign_volumes', 'Nº Volumes') }}
                            {{ Form::text('assign_volumes', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            {{ Form::label('assign_weight', 'Alterar Peso') }}
                            <div class="input-group">
                                {{ Form::text('assign_weight', null, ['class' => 'form-control']) }}
                                <div class="input-group-addon">
                                    kg
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            {{ Form::label('assign_cost_price', 'Preço Custo') }}
                            <div class="input-group">
                                {{ Form::text('assign_cost_price', null, ['class' => 'form-control']) }}
                                <div class="input-group-addon">
                                    {{ Setting::get('app_currency') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            {{ Form::label('assign_total_price', 'Preço Venda') }}
                            <div class="input-group">
                                {{ Form::text('assign_total_price', null, ['class' => 'form-control']) }}
                                <div class="input-group-addon">
                                    {{ Setting::get('app_currency') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-5">
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('assign_price_fixed', 'Bloquear Preço') }}
                            {{ Form::select('assign_price_fixed', [''=>'Manter', '1' => 'Sim', '0'=>'Não'], null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('assign_ignore_billing', 'Marcar como Pago') }}
                            {{ Form::select('assign_ignore_billing', [''=>'Manter', '1' => 'Sim', '0'=>'Não'], null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('assign_sender_country', 'País Origem') }}
                            {{ Form::select('assign_sender_country', [''=>'Manter'] + trans('country'), null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('assign_recipient_country', 'País Destino') }}
                            {{ Form::select('assign_recipient_country', [''=>'Manter'] + trans('country'), null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
                <div class="row row-5">
                    <div class="col-sm-6">
                        <div class="row row-5">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    {{ Form::label('assign_date', 'Data Envio/Carga:') }}
                                    <div class="input-group">
                                        {{ Form::text('assign_date', null, ['class' => 'form-control datepicker']) }}
                                        <div class="input-group-addon">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('assign_start_hour', 'Hora:') }}
                                    {{ Form::time('assign_start_hour', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row row-5">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    {{ Form::label('assign_date', 'Data Entrega:') }}
                                    {{ Form::text('assign_delivery_date', null, ['class' => 'form-control datepicker']) }}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('assign_end_hour', 'Hora:') }}
                                    {{ Form::time('assign_end_hour', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-5">
                    <div class="{{ @$trailers ? 'col-sm-4' : 'col-sm-4' }}">
                        <div class="form-group">
                            {{ Form::label('assign_vehicle', 'Associar à viatura:') }}
                            {{ Form::select('assign_vehicle', ['' => '', '-1' => '- Sem viatura -'] + $vehicles, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    @if(@$trailers)
                        <div class="col-sm-4">
                            <div class="form-group">
                                {{ Form::label('assign_trailer', 'Associar ao reboque:') }}
                                {{ Form::select('assign_trailer', ['' => '', '-1' => '- Sem reboque -'] + $trailers, null, ['class' => 'form-control select2']) }}
                            </div>
                        </div>
                    @endif
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('assign_operator_id', 'Associar ao motorista:') }}
                            {{ Form::select('assign_operator_id', ['' => ''] + $operators, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fa fa-spin fa-circle-o-notch'></i> Aguarde...">Gravar</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>