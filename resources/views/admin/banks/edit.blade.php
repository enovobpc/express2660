{{ Form::model($bank, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('company_id', 'Empresa') }}
                {{ Form::select('company_id', count($companies) > 1 ? ['' => ''] + $companies : $companies, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required', 'maxlength' => 35]) }}
            </div>
        </div>
        <div class="col-sm-3">
            <small><a href="{{ route('admin.banks.index', ['tab' => 'banks-institutions']) }}" target="_blank" class="pull-right"><i class="fas fa-plus"></i> Adicionar</a></small>
            <div class="form-group is-required">
                {{ Form::label('bank_code', 'Banco') }}
                {{ Form::select('bank_code', ['' => ''] + $banks, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group is-required">
                {{ Form::label('bank_iban', 'IBAN') }}
                {{ Form::text('bank_iban', null, ['class' => 'form-control iban nospace uppercase', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('bank_swift', 'BIC/SWIFT') }}
                {{ Form::text('bank_swift', null, ['class' => 'form-control nospace uppercase', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('credor_code', 'ID Credor') }}
                {{ Form::text('credor_code', null, ['class' => 'form-control uppercase']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('titular_vat', 'NIF Titular') }}
                {{ Form::text('titular_vat', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-9">
            <div class="form-group is-required">
                {{ Form::label('titular_name', 'Nome Titular') }}
                {{ Form::text('titular_name', null, ['class' => 'form-control', 'required', 'maxlength' => 35]) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('obs', 'Observações') }}
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 2]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-left">
        <div class="checkbox m-b-0 m-t-5">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('is_active', 1, $bank->exists ? null : true) }}
                Ativo
            </label>
        </div>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}
<script>
    $('.modal .select2').select2(Init.select2())
</script>
