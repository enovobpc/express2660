@if(empty($department->password))
<div class="alert alert-warning">
    <h4><i class="fas fa-exclamation-triangle"></i> @trans('Sem acesso à plataforma')</h4>
    <p>@trans('Este cliente ainda não possui acesso à plataforma. Complete os dados abaixo e grave de seguida para atribuir uma conta.')</p>
</div>
@endif

{{ Form::model($department, ['route'=> ['admin.customers.login', $department->id], 'files' => true]) }}
<div class="row">
    <div class="col-sm-8 col-lg-9">
        <div class="form-group is-required">
            {{ Form::label('display_name', __('Nome de Login'))}}
            {{ Form::text('display_name', empty($department->display_name) ? $department->name : null, array('class' =>'form-control uppercase', 'required' => true)) }}
        </div>
        <div class="form-group is-required">
            {{ Form::label('email', __('E-mail'))}}
            {{ Form::email('email', empty($department->email) ? $department->contact_email : null, array('class' =>'form-control email lowercase', 'required' => true)) }}
        </div>

        @if (empty($department->password))
        <div class="form-group is-required">
            {{ Form::label('password', 'Password')}}
            <div class="input-group input-group">
                {{ Form::text('password', str_random(8), array('class' =>'form-control', 'required' => true)) }}
                <span class="input-group-btn">
                    <button class="btn btn-default btn-flat" id="random-password" type="button">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </span>
            </div>
        </div>
        @else
        <div class="row row-10">
            <div class="col-sm-6">
                <div class="form-group">
                    {{ Form::label('password', __('Password'))}}
                    {{ Form::password('password', array('class' =>'form-control', 'autocomplete' => 'off', 'placeholder' => 'Deixar vazio para não alterar')) }}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {{ Form::label('password_confirmation', __('Confirmar Password'))}}
                    {{ Form::password('password_confirmation', array('class' =>'form-control', 'autocomplete' => 'off')) }}
                </div>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    {{ Form::label('password', __('Ocultar Preços e Faturação'))}}
                    {{ Form::select('hide_billing', ['' => 'Igual Definições Gerais', '2' => 'Ocultar', '1' => 'Mostrar'], null, ['class' =>'form-control select2']) }}
                </div>
            </div>
        </div>
        <div class="checkbox m-t-5">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('always_cod', 1, $department->always_cod) }}
                @trans('Permitir sempre portes no destino')
            </label>
        </div>
        <div class="checkbox m-t-5">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('active', 1, !$department->active) }}
                @trans('Cliente bloqueado')
            </label>
        </div>
        <div class="checkbox m-t-5 m-b-15">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('hide_old_shipments', 1, $department->password ? null : true) }}
                @trans('Impedir que o cliente veja envios anteriores à atribuição de acesso à Área de Cliente')
            </label>
        </div>
        <div class="checkbox m-t-5 m-b-15">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('send_email', 1, $department->password ? false : true) }}
                @trans('Enviar email com a palavra-passe')
            </label>
        </div>
        <div class="checkbox m-t-5 m-b-15">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('is_commercial', 1, $department->is_commercial) }}
                @trans('Conta comercial')
            </label>
        </div>
        <div class="checkbox m-t-5 m-b-15">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('view_parent_shipments', 1) }}
                @trans('Permitir que o departamento veja envios de outros departamentos')
            </label>
        </div>
        <div class="checkbox m-t-5 m-b-15">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('hide_btn_shipments', 1) }}
                @trans('Ocultar botão de criação de envios')
            </label>
        </div>

    </div>
    <div class="col-sm-4 col-lg-3">
        {{ Form::label('image', 'Imagem:', array('class' => 'form-label')) }}<br/>
        <div class="fileinput {{ $department->filepath ? 'fileinput-exists' : 'fileinput-new'}}" data-provides="fileinput">
            <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                <img src="{{ asset('assets/img/default/avatar.png') }}">
            </div>
            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 150px; max-height: 150px;">
                @if($department->filepath)
                <img src="{{ asset($department->getThumb()) }}">
                @endif
            </div>
            <div>
                <span class="btn btn-default btn-block btn-sm btn-file">
                    <span class="fileinput-new">@trans('Procurar...')</span>
                    <span class="fileinput-exists"><i class="fas fa-sync-alt"></i> @trans('Alterar')</span>
                    <input type="file" name="image">
                </span>
                <a href="#" class="btn btn-danger btn-block btn-sm fileinput-exists" data-dismiss="fileinput">
                    <i class="fas fa-close"></i> @trans('Remover')
                </a>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer" style="margin-left: -15px; margin-right: -15px; padding-bottom: 0">
    {{ Form::hidden('delete_photo') }}
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}