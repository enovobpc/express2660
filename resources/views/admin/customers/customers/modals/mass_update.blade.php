<div class="modal" id="modal-mass-update">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.customers.selected.update'], 'method' => 'POST']) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title">@trans('Editar clientes em massa')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('assign_agency_id', __('Agência')) }}
                            {{ Form::select('assign_agency_id', ['' => __('- Não alterar -')] + $agencies, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('assign_seller_id', __('Comercial')) }}
                            {{ Form::select('assign_seller_id', ['' => __('- Não alterar -'), '-1' => __('Sem comercial')] + $sellers, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('assign_route_id', __('Rota')) }}
                            {{ Form::select('assign_route_id', ['' => __('- Não alterar -'), '-1' => __('Sem rota')] + $routes, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('assign_price_table_id', __('Tabela de preços')) }}
                            {{ Form::select('assign_price_table_id', ['' => __('- Não alterar -')] + $pricesTables, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('assign_type_id', __('Tipo Cliente')) }}
                            {{ Form::select('assign_type_id', ['' => __('- Não alterar -')] + $types, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('assign_payment_method', __('Pagamento')) }}
                            {{ Form::select('assign_payment_method', ['' => __('- Não alterar -')] + $paymentConditions, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('bank_code', __('Banco')) }}
                            {!! Form::selectWithData('bank_code', $banks, null, ['class' => 'form-control select2']) !!}
                            {{ Form::hidden('bank_name', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('bank_swift', __('BIC/Swift')) }}
                            {{ Form::text('bank_swift', null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="form-group m-b-0">
                            {{ Form::label('bank_iban', __('IBAN')) }}
                            {{ Form::text('bank_iban', null, ['class' => 'form-control iban']) }}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group m-b-0">
                            {{ Form::label('contact_email', __('E-mail Contacto')) }}
                            {{ Form::text('contact_email', null, ['class' => 'form-control email']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">@trans('Gravar')</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>