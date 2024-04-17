<div class="row row-5">
    <div class="col-sm-4">
        <div class="form-group is-required">
            {{ Form::label('method', 'Conector') }}
            {{ Form::select('method', ['' => ''] + $webserviceMethods, null, ['class' => 'form-control select2', 'required']) }}
        </div>
    </div>
    <div class="col-sm-5">
        <div class="form-group is-required">
            {{ Form::label('provider_id', 'Ativar para fornecedor') }} {!! tip('Sempre que fizer um envio ou recolha pelo fornecedor indicado, o envio será submetido através do método selecionado.') !!}
            {{ Form::select('provider_id', ['' => ''] + $providers, null, ['class' => 'form-control select2', 'required']) }}
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            {{ Form::label('agency_id', 'Aplicar à Agência') }} {!! tip('Este conector estará disponível apenas para os clientes da agência indicada.') !!}
            {{ Form::select('agency_id', ['' => 'Todas'] + $agencies, null, ['class' => 'form-control select2']) }}
        </div>
    </div>
</div>
<div class="row row-5">
    <div class="col-sm-4">
        <div class="form-group">
            {{ Form::label('agency', 'Agência') }}
            {{ Form::text('agency', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {{ Form::label('user', 'Utilizador') }} {!! tip('Utilize "auto" para que o sistema coloque automáticamente o código do utilizador igual ao código de cliente.') !!}
            {{ Form::text('user', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {{ Form::label('password', 'Password') }}
            {{ Form::text('password', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-sm-12" id="session_id">
        <div class="form-group">
            {{ Form::label('session_id', 'ID de Sessão') }}
            {{ Form::text('session_id', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-sm-12" id="endpoint" style="{{ $webservice->method == 'enovo_tms' ? : 'display: none' }}">
        <div class="form-group">
            {{ Form::label('endpoint', 'Endpoint') }}
            {{ Form::text('endpoint', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-sm-12" id="mrw" style="display: none">
        <div class="row row-5">
            <div class="col-sm-6">
                <div class="form-group">
                    {{ Form::label('abonado', 'Abonado') }}
                    {{ Form::text('abonado', null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {{ Form::label('department', 'Departamento') }}
                    {{ Form::text('department', null, ['class' => 'form-control']) }}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-5">
        <div class="checkbox m-t-5">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('active', 1, $webservice->exists ? null : true) }}
                @trans('Webservice ativo')
            </label>
        </div>
    </div>
    <div class="col-sm-7">
        <div class="checkbox m-t-5">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('auto_enable', 1, $webservice->exists ? null : true) }}
                @trans('Ativar automáticamente ao criar novo cliente')
            </label>
            {!! tip(__('Sempre que criar um novo cliente, é adicionado automáticamente este conector.')) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-5">
        <div class="checkbox m-t-5">
            <label style="padding-left: 0 !important">
                {{ Form::hidden('settings[force_tracking_as_reference]', 0) }}
                {{ Form::checkbox('settings[force_tracking_as_reference]', 1, @$webservice->settings['force_tracking_as_reference'] ?? null) }}
                @trans('Forçar referência igual número de tracking')
            </label>
            {!! tip(__('A referência enviada será sempre o número de tracking do envio.')) !!}
        </div>
    </div>

    {{ Form::hidden('force_sender', 0) }}
    @if (Setting::get('hidden_recipient_on_labels') || Setting::get('hidden_recipient_addr_on_labels'))
    <div class="col-sm-7">
        <div class="checkbox m-t-5 force-sender">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('force_sender', 1, $webservice->exists ? null : false) }}
                @trans('Forçar a sair na etiqueta dados do cliente')
            </label>
            {!! tip(__('Força a que na etiqueta saiam os dados do cliente que remete o envio em vez dos dados da agência.')) !!}
        </div>
    </div>
    @endif
</div>

{{-- <div class="checkbox m-t-5 force-sender" style="{{ $webservice->method != 'ctt' ? 'display: none' : 'display: block' }};">
    <label style="padding-left: 0 !important">
        {{ Form::checkbox('force_sender', 1, $webservice->exists ? null : false) }}
        Forçar a sair na etiqueta dados do cliente
    </label>
    {!! tip('Aplicável ao método CTT. Força a que na etiqueta saiam os dados do cliente que remete o envio em vez dos dados da agência.') !!}
</div> --}}