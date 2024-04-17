{{ Form::open(array('route' => array('admin.account.update', 'action' => 'password'))) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Alterar palavra-passe')</h4>
</div>
<div class="modal-body">
   <div class="form-group">
        {{ Form::label('current_password', __('Palavra-passe atual')) }}
        {{ Form::password('current_password', array('class' => 'form-control', 'autocomplete' => 'off', 'required' => true)) }}
    </div>
    <div class="form-group">
        {{ Form::label('password', __('Nova palavra-passe')) }}
        {{ Form::password('password', array('class' => 'form-control', 'autocomplete' => 'off', 'required' => true)) }}
    </div>
    <div class="form-group">
        {{ Form::label('password_confirmation', __('Confirmar nova palavra-passe')) }}
        {{ Form::password('password_confirmation', array('class' => 'form-control', 'autocomplete' => 'off', 'required' => true)) }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}