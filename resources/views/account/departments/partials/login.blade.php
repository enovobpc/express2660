@if(empty($department->password))
<div class="alert alert-warning">
    <h4><i class="fas fa-exclamation-triangle"></i> Sem acesso à plataforma</h4>
    <p>Este cliente ainda não possui acesso à plataforma. Complete os dados abaixo e grave de seguida para atribuir uma conta.</p>
</div>
@endif

{{ Form::model($department, ['route'=> ['admin.customers.login', $department->id], 'files' => true]) }}
<div class="row">
    <div class="col-sm-8 col-lg-9">
        <div class="form-group form-group-sm is-required">
            {{ Form::label('display_name', 'Nome de Login')}}
            {{ Form::text('display_name', empty($department->display_name) ? $department->name : null, array('class' =>'form-control', 'required' => true)) }}
        </div>
        <div class="form-group form-group-sm is-required">
            {{ Form::label('email', 'E-mail')}}
            {{ Form::email('email', empty($department->email) ? $department->contact_email : null, array('class' =>'form-control nospace lowercase', 'required' => true)) }}
        </div>

        @if (empty($department->password))
        <div class="form-group form-group-sm is-required">
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
                <div class="form-group form-group-sm">
                    {{ Form::label('password', 'Password')}}
                    {{ Form::password('password', array('class' =>'form-control', 'autocomplete' => 'off', 'placeholder' => 'Deixar vazio para não alterar')) }}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group form-group-sm">
                    {{ Form::label('password_confirmation', 'Confirmar Password')}}
                    {{ Form::password('password_confirmation', array('class' =>'form-control', 'autocomplete' => 'off')) }}
                </div>
            </div>
        </div>
        @endif
        <div class="checkbox m-t-5 m-b-15">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('send_email', 1, $department->password ? false : true) }}
                Enviar email com a palavra-passe
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
                    <span class="fileinput-new">Procurar...</span>
                    <span class="fileinput-exists"><i class="fas fa-sync-alt"></i> Alterar</span>
                    <input type="file" name="image">
                </span>
                <a href="#" class="btn btn-danger btn-block btn-sm fileinput-exists" data-dismiss="fileinput">
                    <i class="fas fa-close"></i> Remover
                </a>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer" style="margin-left: -15px; margin-right: -15px; padding-bottom: 0">
    {{ Form::hidden('delete_photo') }}
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}