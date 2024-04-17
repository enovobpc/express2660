{{ Form::open(['route' => 'admin.webservices.mass-config.store']) }}
<div class="modal-header">
    <button type="button" class="close pull-right" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Ativar/Desativar Webservices em Massa</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-4">
            <div class="form-group is-required m-b-0">
                {{ Form::label('agency_id', 'Agência') }}
                {{ Form::select('agency_id', ['' => ''] + $agencies, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required m-b-0">
                {{ Form::label('action', 'Ação') }}
                {{ Form::select('action', ['' => '', 'create' => 'Criar/Editar', 'delete' => 'Apagar'], null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required m-b-0">
                {{ Form::label('method', 'Conector') }}
                {{ Form::select('method', ['' => ''] + $webserviceMethods, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group m-b-0">
                {{ Form::label('provider_id', 'Fornecedor') }}
                {{ Form::select('provider_id', ['' => ''] + $providers, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
    <hr class="m-b-10"/>
    <div class="row row-5">
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('agency', 'Agência') }}
                {{ Form::text('agency', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('user', 'Utilizador') }}<i class="fas fa-info-circle text-blue" data-toggle="tooltip" title="Deixe em branco se pretende assumir o código do cliente."></i>
                {{ Form::text('user', null, ['class' => 'form-control', 'placeholder' => 'Assumir Código Cliente']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('password', 'Password') }}
                {{ Form::text('password', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-12" id="session_id">
            <div class="form-group m-b-0">
                {{ Form::label('session_id', 'ID de Sessão') }}
                {{ Form::text('session_id', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> Aguarde...">Gravar</button>
    </div>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2())
</script>