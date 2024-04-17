{{ Form::model($payment, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Criar transferência SEPA</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('code', 'ID') }}
                {{ Form::text('code', null, ['class' => 'form-control', 'required', $payment->edit_mode ? : 'disabled']) }}
            </div>
        </div>
        <div class="col-sm-9">
            <div class="form-group is-required">
                {{ Form::label('name', 'Descrição') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required', $payment->edit_mode ? : 'disabled']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('type', 'Tipo Transação') }}
                {{ Form::select('type', ['' => ''] + trans('admin/billing.sepa-types'), null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-7">
            <div class="form-group is-required">
                {{ Form::label('bank_id', 'Nosso Banco') }}
                {{ Form::select('bank_id', ['' => ''] +  $banks, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-left text-left" style="width: 80%">
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Criar</button>
</div>
{{ Form::close() }}
<script>
    $('.datepicker').datepicker(Init.datepicker());
    $('.select2').select2(Init.select2());

</script>

