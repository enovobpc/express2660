<div class="modal" id="modal-refund-all">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.refunds.cod.selected.update'], 'payment_method' => 'post']) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Marcar selecionados como recebidos</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5">
                    <div class="col-sm-7">
                        <div class="form-group is-required">
                            {{ Form::label('payment_method', 'Forma de pagamento:') }}
                            {{ Form::select('payment_method', trans('admin/refunds.payment-methods-list'), null, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group is-required">
                            {{ Form::label('payment_date', 'Pago em:') }}
                            <div class="input-group">
                                {{ Form::text('payment_date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                                <span class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{ Form::label('obs', 'Observações:') }}
                    {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 3]) }}
                </div>
                <div class="form-group m-b-0">
                    <div class="checkbox m-b-0">
                        <label style="padding-left: 0">
                            {{ Form::checkbox('paid', 1, false) }}
                            Marcar envio como Pago
                        </label>
                    </div>
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