{{ Form::model($webservice, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('method', __('Método do Webservice')) }}
                {{ Form::select('method', ['' => ''] + $webserviceMethods, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-8">
            <div class="form-group is-required">
                {{ Form::label('provider_id', __('Ativar webservice para envios do fornecedor')) }}
                {{ Form::select('provider_id', ['' => ''] + $providers, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('agency', __('Agência')) }}
                {{ Form::text('agency', null, ['class' => 'form-control nospace']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('user', __('Utilizador')) }}
                {{ Form::text('user', null, ['class' => 'form-control nospace']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('password', __('Password')) }}
                {{ Form::text('password', null, ['class' => 'form-control nospace']) }}
            </div>
        </div>
        <div class="col-sm-12" id="session_id">
            <div class="form-group">
                {{ Form::label('session_id', __('ID de Sessão')) }}
                {{ Form::text('session_id', null, ['class' => 'form-control nospace']) }}
            </div>
        </div>
        <div class="col-sm-12" id="endpoint" style="{{ $webservice->method == 'enovo_tms' ? : 'display: none' }}">
            <div class="form-group">
                {{ Form::label('endpoint', __('Endpoint')) }}
                {{ Form::text('endpoint', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-12" id="mrw" style="display: none">
            <div class="row row-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        {{ Form::label('abonado', __('Abonado')) }}
                        {{ Form::text('abonado', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {{ Form::label('department', __('Departamento')) }}
                        {{ Form::text('department', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-5">
        <div class="col-sm-4">
            <div class="checkbox m-t-5">
                <label style="padding-left: 0 !important">
                    {{ Form::checkbox('active', 1, $webservice->exists ? null : true) }}
                    @trans('Webservice ativo')
                </label>
            </div>
        </div>

        <div class="col-sm-8">
            @if (Setting::get('hidden_recipient_on_labels') || Setting::get('hidden_recipient_addr_on_labels'))
            <div class="checkbox m-t-5 force-sender">
                <label style="padding-left: 0 !important">
                    {{ Form::hidden('force_sender', 0) }}
                    {{ Form::checkbox('force_sender', 1, $webservice->exists ? null : false) }}
                    @trans('Forçar a sair na etiqueta dados do cliente')
                </label>
                {!! tip(__('Força a que na etiqueta saiam os dados do cliente que remete o envio em vez dos dados da agência.')) !!}
            </div>
            @endif
        </div>
    </div>
    
    <div class="row row-5">
        <div class="col-sm-8">
            <div class="checkbox m-t-5">
                <label style="padding-left: 0 !important">
                    {{ Form::hidden('settings[force_tracking_as_reference]', 0) }}
                    {{ Form::checkbox('settings[force_tracking_as_reference]', 1, @$webservice->settings['force_tracking_as_reference'] ?? null) }}
                    @trans('Forçar referência igual número de tracking')
                </label>
                {!! tip(__('A referência enviada será sempre o número de tracking do envio.')) !!}
            </div>
        </div>
    </div>

    {{-- <div class="checkbox m-t-5 m-0 force-sender" style="display: {{ $webservice->exists && $webservice->method != 'ctt' ? 'none' : 'block' }};">
        <label style="padding-left: 0 !important">
            {{ Form::checkbox('force_sender', 1, $webservice->exists ? null : false) }}
            Forçar sair na etiqueta do fornecedor os dados do cliente
        </label>
    </div> --}}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());

    $('[name="method"]').on('change', function(){

        var method = $(this).val();

        $('#mrw').hide().find('input').val('');
        $('#endpoint').hide().find('input').val('');

        if(method == 'enovo_tms') {
            $('#endpoint').show().find('input').val('');
            $('#session_id').show().find('input').val('');
            $('.force-sender').show();
            $('label[for="agency"]').html('Client ID');
            $('label[for="user"]').html('Username');
            $('label[for="session_id"]').html('Secret');
        } else if(method == 'ctt') {
            $('#session_id').show().find('input').val('');
            $('.force-sender').show();
            $('label[for="agency"]').html('Nº Contrato');
            $('label[for="user"]').html('N.º Cliente');
            $('label[for="session_id"]').html('Código Activação (Autentication ID)');
        } else if(method == 'envialia' || method == 'tipsa') {
            $('#session_id').hide().find('input').val('');
            $('.force-sender').hide();
            $('label[for="agency"]').html('Agência');
            $('label[for="user"]').html('Utilizador');
            $('label[for="session_id"]').html('ID de Sessão (UID)');
        } else if(method == 'mrw') {
            $('label[for="agency"]').html('Franquia');
            $('#mrw').show().find('input').val('')
            $('#session_id').hide()
        } else if(method == 'via_directa') {
            $('label[for="agency"]').html('Código Cliente');
            $('#session_id').hide()
        } else if(method == 'dhl') {
            $('label[for="agency"]').html('Account');
            $('label[for="user"]').html('API UserID');
            $('label[for="password"]').html('API Key');
            $('#session_id').hide()
        } else {
            $('#session_id').show().find('input').val('');
            $('.force-sender').hide();
            $('label[for="agency"]').html('Agência');
            $('label[for="user"]').html('Utilizador');
            $('label[for="session_id"]').html('ID de Sessão (UID)');
        }
    })

    $('[name="abonado"], [name="department"]').on('change', function(){
        $('[name="session_id"]').val($('[name="abonado"]').val() + '#' + $('[name="department"]').val())
    })
</script>
