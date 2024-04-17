@if($ids)
    {{ Form::open(['route' => ['admin.invoices.nodoc.settle.store', '0'], 'method' => 'POST']) }}
    {{ Form::hidden('ids', implode(',', $ids)) }}
@else
    {{ Form::open(['route' => ['admin.invoices.nodoc.settle.store', $invoice->id], 'method' => 'POST']) }}
@endif
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">
        @if($ids)
            <i class="fas fa-file-refresh"></i> Liquidar pagamentos pendentes
        @else
            <i class="fas fa-file-refresh"></i> Liquidar pagamento pendente
        @endif
    </h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-7">
            <div class="form-group is-required">
                {{ Form::label('settle_method', 'Modo Pagamento', ['class' => 'control-label']) }}
                {{ Form::select('settle_method', ['' => ''] + $paymentMethods, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('settle_date', 'Data Pagamento', ['class' => 'control-label']) }}
                <div class="input-group">
                    {{ Form::text('settle_date', null, ['class' => 'form-control datepicker', 'required']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group m-b-0">
                {{ Form::label('settle_obs', 'Observações', ['class' => 'control-label']) }}
                {{ Form::textarea('settle_obs', null, ['class' => 'form-control', 'rows' => 3]) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="checkbox-inline" style="margin: 15px 0 0 0">
                <label>
                    {{ Form::checkbox('is_settle', 1, true) }}
                    Marcar documento como pago
                </label>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-left">
        <p class="text-red m-t-5 m-b-0" id="modal-feedback"></p>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Liquidar</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());
    $('.datepicker').datepicker(Init.datepicker());
</script>