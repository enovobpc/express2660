<div class="modal" id="modal-mass-update-pickups">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.billing.customers.shipments.selected.update']]) }}
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
                            {{ Form::label('assign_service_id', 'Serviço') }}
                            {{ Form::select('assign_service_id', ['' => '- Não alterar -'] + $services, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('assign_provider_id', 'Fornecedor:') }}
                            {{ Form::select('assign_provider_id', ['' => '- Não alterar -'] + $providers, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
                <div class="row row-10">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('assign_sender_country', 'País de origem') }}
                            {{ Form::select('assign_sender_country', ['' => '- Não alterar -'] + trans('country'), null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('assign_recipient_country', 'País de destino') }}
                            {{ Form::select('assign_recipient_country', ['' => '- Não alterar -'] + trans('country'), null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
                <div class="row row-10">
                    <div class="col-sm-6">
                        {{ Form::label('assign_cost', 'Preço de Custo', ['class' => 'control-label']) }}
                        <div class="input-group">
                            {{ Form::text('assign_cost',  null, ['class' => 'form-control']) }}
                            <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        {{ Form::label('assign_price', 'Preço de Venda', ['class' => 'control-label']) }}
                        <div class="input-group">
                            {{ Form::text('assign_price',  null, ['class' => 'form-control']) }}
                            <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                        </div>
                    </div>
                </div>


                <div class="checkbox">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('calc_prices', 1, true) }}
                        Calcular de novo os preços de cada envio.
                    </label>
                </div>
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