{{ Form::model($contact, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="form-group">
        {{ Form::label('department', __('Área ou Departamento')) }}
        {{ Form::text('department', null, ['class' => 'form-control']) }}
    </div>
    <div class="form-group is-required">
        {{ Form::label('name', __('Nome do Responsável')) }}
        {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
    </div>
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('phone', __('Telefone')) }}
                {{ Form::text('phone', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('mobile', __('Telemóvel')) }}
                {{ Form::text('mobile', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('email', __('E-mail')) }}
                {{ Form::text('email', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}

