{{ Form::model($invoice, ['route' => ['admin.invoices.update', $invoice->id, 'action' => 'admin'], 'method' => 'PUT']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Editar dados registo</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('customer_id', 'customer_id', ['class' => 'control-label']) }}
                {{ Form::text('customer_id', null, ['class' => 'form-control datepicker']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('doc_type', 'doc_type', ['class' => 'control-label']) }}
                {{ Form::text('doc_type', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('doc_id', 'doc_id', ['class' => 'control-label']) }}
                {{ Form::text('doc_id', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('doc_series', 'doc_series', ['class' => 'control-label']) }}
                {{ Form::text('doc_series', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('doc_series_id', 'doc_series_id', ['class' => 'control-label']) }}
                {{ Form::text('doc_series_id', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('payment_condition', 'payment_condition', ['class' => 'control-label']) }}
                {{ Form::text('payment_condition', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('payment_method', 'payment_method', ['class' => 'control-label']) }}
                {{ Form::text('payment_method', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('payment_bank_id', 'payment_bank_id', ['class' => 'control-label']) }}
                {{ Form::text('payment_bank_id', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('payment_date', 'payment_date', ['class' => 'control-label']) }}
                <div class="input-group">
                    {{ Form::text('payment_date', null, ['class' => 'form-control datepicker']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr style="margin: 5px 0 15px; border-color: #333"/>
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('reference', 'Referência', ['class' => 'control-label']) }}
                {{ Form::text('reference', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('doc_date', 'doc_date', ['class' => 'control-label']) }}
                <div class="input-group">
                    {{ Form::text('doc_date', null, ['class' => 'form-control datepicker']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('due_date', 'due_date', ['class' => 'control-label']) }}
                <div class="input-group">
                    {{ Form::text('due_date', null, ['class' => 'form-control datepicker']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr style="margin: 5px 0 15px; border-color: #333"/>
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('doc_subtotal', 'Subtotal', ['class' => 'control-label']) }}
                <div class="input-group">
                    {{ Form::text('doc_subtotal', null, ['class' => 'form-control decimal']) }}
                    <div class="input-group-addon">€</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('doc_vat', 'IVA', ['class' => 'control-label']) }}
                <div class="input-group">
                    {{ Form::text('doc_vat', null, ['class' => 'form-control decimal']) }}
                    <div class="input-group-addon">€</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('doc_total', 'Total', ['class' => 'control-label']) }}
                <div class="input-group">
                    {{ Form::text('doc_total', null, ['class' => 'form-control decimal']) }}
                    <div class="input-group-addon">€</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('doc_total_pending', 'Pendente', ['class' => 'control-label']) }}
                <div class="input-group">
                    {{ Form::text('doc_total_pending', null, ['class' => 'form-control decimal']) }}
                    <div class="input-group-addon">€</div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('doc_total_debit', 'Total Débito (+)', ['class' => 'control-label']) }}
                <div class="input-group">
                    {{ Form::text('doc_total_debit', null, ['class' => 'form-control decimal']) }}
                    <div class="input-group-addon">€</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('doc_total_credit', 'Total Crédito (-)', ['class' => 'control-label']) }}
                <div class="input-group">
                    {{ Form::text('doc_total_credit', null, ['class' => 'form-control decimal']) }}
                    <div class="input-group-addon">€</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('doc_balance', 'Saldo Documento', ['class' => 'control-label']) }}
                <div class="input-group">
                    {{ Form::text('doc_balance', null, ['class' => 'form-control decimal']) }}
                    <div class="input-group-addon">€</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('total_discount', 'Desconto', ['class' => 'control-label']) }}
                <div class="input-group">
                    {{ Form::text('total_discount', null, ['class' => 'form-control decimal']) }}
                    <div class="input-group-addon">€</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('irs_tax', 'Total Retenção', ['class' => 'control-label']) }}
                <div class="input-group">
                    {{ Form::text('irs_tax', null, ['class' => 'form-control decimal']) }}
                    <div class="input-group-addon">€</div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('is_settle', 'is_settle?', ['class' => 'control-label']) }}
                {{ Form::select('is_settle', ['0' => 'Não', '1' => 'Sim'], null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('is_deleted', 'is_deleted?', ['class' => 'control-label']) }}
                {{ Form::select('is_deleted', ['0' => 'Não', '1' => 'Sim'], null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('is_reversed', 'is_reversed?', ['class' => 'control-label']) }}
                {{ Form::select('is_reversed', ['0' => 'Não', '1' => 'Sim'], null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
    <hr style="margin: 5px 0 15px; border-color: #333"/>
    <div class="row row-5">
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('is_draft', 'Rascunho?', ['class' => 'control-label']) }}
                {{ Form::select('is_draft', ['0' => 'Não', '1' => 'Sim'], null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('sort', 'Sort', ['class' => 'control-label']) }}
                {{ Form::text('sort', null, ['class' => 'form-control']) }}
            </div>
        </div>

        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('credit_note_id', 'ID Nota Crd', ['class' => 'control-label']) }}
                {{ Form::text('credit_note_id', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('assigned_invoice_id', 'ID FT Associada', ['class' => 'control-label']) }}
                {{ Form::text('assigned_invoice_id', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('sepa_payment_id', 'ID Pgto SEPA', ['class' => 'control-label']) }}
                {{ Form::text('sepa_payment_id', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-left">
        <p class="m-b-0 m-t-5 text-blue"><i class="fas fa-info-circle"></i> Ao gravar a conta corrente será recalculada.</p>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar">Gravar</button>
</div>
{{ Form::close() }}

<script>
    $('.datepicker').datepicker(Init.datepicker());
    $('.select2').select2(Init.select2());
</script>