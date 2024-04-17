<div class="modal" id="{{ @$pickupTab ? 'modal-update-billing-date-pickup' : 'modal-update-billing-date' }}">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(array('route' => ['admin.billing.customers.shipments.selected.update-billing-date', $customer->id], 'method' => 'POST')) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Alterar data de faturação</h4>
            </div>
            <div class="modal-body">
                <h4 class="m-t-0 bold">Escolha uma nova data de faturação para os envios selecionados.</h4>
                <p>Esta opção permite-lhe faturar os envios selecionados em outro mês, quinzena ou semana.</p>
                <hr/>
                <div class="form-group is-required m-0">
                    <div class="row row-0">
                        <div class="col-sm-7">
                            <div class="sp-5"></div>
                            {{ Form::label('billing_date', 'Alterar data faturação para dia:', ['class' => 'control-label']) }}
                        </div>
                        <div class="col-sm-5">
                            <div class="input-group">
                                {{ Form::text('billing_date',  null, ['class' => 'form-control datepicker', 'required']) }}
                                <span class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-right">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="Aguarde...">Alterar Data</button>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>