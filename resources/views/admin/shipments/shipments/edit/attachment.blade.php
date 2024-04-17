<?php 
$titles = [
    'CMR Digitalizado'     => __('CMR Digitalizado'),
    'Adjudicação de Carga' => __('Adjudicação de Carga'),
    'Fatura Cliente'       => __('Fatura Cliente'),
    'Fatura Fornecedor'    => __('Fatura Fornecedor')
];

?>
{{ Form::model($attachment, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-8">
            <div class="form-group is-required">
                {{ Form::label('name', __('Título do documento')) }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('default_name', __('Títulos rápidos')) }}
                {{ Form::select('default_name', [''=> ''] + $titles, $attachment->exists ? $attachment->name : null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
   
    @if(!$attachment->exists)
        <div class="form-group is-required">
            {{ Form::label('name', 'Ficheiro a anexar') }}
            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                <div class="form-control" data-trigger="fileinput">
                    <i class="fas fa-file fileinput-exists"></i>
                    <span class="fileinput-filename"></span>
                </div>
                <span class="input-group-addon btn btn-default btn-file">
                <span class="fileinput-new">@trans('Procurar...')</span>
                <span class="fileinput-exists">@trans('Alterar')</span>
                <input type="file" name="file" required>
            </span>
                <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">@trans('Remover')</a>
            </div>
        </div>
    @endif
    <div class="form-group">
        {{ Form::label('obs', __('Anotações ou Observações')) }}
        {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 3]) }}
    </div>
    <div class="form-group m-b-5">
        <div class="checkbox m-0">
            <label style="padding-left: 0">
                {{ Form::checkbox('operator_visible', 1) }}
                @trans('Anexo disponível ao motorista')
            </label>
        </div>
    </div>
    <div class="form-group m-0">
        <div class="checkbox m-0">
            <label style="padding-left: 0">
                {{ Form::checkbox('customer_visible', 1) }}
                @trans('Anexo disponível ao cliente')
            </label>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());

    $('.modal [name="default_name"]').on('change', function(){
        $('.modal [name="name"]').val($(this).val());
    })
</script>