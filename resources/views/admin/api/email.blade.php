{{ Form::model($oauthClient, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Enviar dados API por e-mail</h4>
</div>
<div class="modal-body">

    <p class="fs-15 lh-1-5">
        <b>Documentação</b>: {{ route('api.docs.index') }}<br/>
        <b>URL Seguimento Público</b>: {{ route('tracking.index', ['tracking' => '']) }}<br/><br/>
        <b>client_id</b>: {{ $oauthClient->id }}<br/>
        <b>client_secret</b>: {{ $oauthClient->secret }}<br/>
        <b>username</b>: {{ @$oauthClient->customer->email ? @$oauthClient->customer->email : '<email area cliente>' }}<br/>
        <b>password</b>: {{ @$oauthClient->customer->uncrypted_password ? @$oauthClient->customer->uncrypted_password  : '<password area cliente>' }}<br/>
    </p>

    <hr/>
    <div class="form-group is-required">
        {{ Form::label('email', 'Enviar por E-mail') }}
        {{ Form::text('email', '', ['class' => 'form-control', 'required']) }}
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Enviar</button>
</div>
{{ Form::close() }}
