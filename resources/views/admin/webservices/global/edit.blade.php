{{ Form::model($webservice, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="tabbable-line m-b-15">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#tab-info" data-toggle="tab">
                Configuração Base
            </a>
        </li>
        <li>
            <a href="#tab-mapping-services" data-toggle="tab">
                Mapeamento de Serviços
            </a>
        </li>
    </ul>
</div>
<div class="modal-body p-t-0 p-b-0">
    <div class="tab-content m-b-0">
        <div class="tab-pane active" id="tab-info">
            @include('admin.webservices.global.partials.details')
        </div>
        <div class="tab-pane" id="tab-mapping-services">
            @include('admin.webservices.global.partials.mapping_services')
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
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
            $('label[for="password"]').html('User ID');
            $('label[for="session_id"]').html('Auth ID');
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
            $('label[for="session_id"]').html('Prefixo do Tracking (Ex: ACTxxxxxxxxxxxx)');
        } else if(method == 'dhl') {
            $('label[for="agency"]').html('Account');
            $('label[for="user"]').html('API UserID');
            $('label[for="password"]').html('API Key');
            $('#session_id').hide()
        } else if(method == 'integra2') {
            $('.force-sender').hide();
            $('label[for="agency"]').html('Nº Cliente');
            $('label[for="user"]').html('Utilizador FTP');
            $('label[for="password"]').html('Password FTP');
            $('label[for="session_id"]').html('Nº Agência');
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
