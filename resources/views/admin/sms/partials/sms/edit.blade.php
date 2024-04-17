{{ Form::model($sms, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="form-group is-required">
        {{ Form::label('to[]', 'Enviar SMS para:') }}  {!! tip('Pode pesquisar um contacto na lista ou escrever um contacto livremente.') !!}
        {{ Form::select('to[]', $phones, null, ['class' => 'form-control nospace', 'required', 'multiple']) }}
    </div>
    <div class="form-group is-required">
        {{ Form::label('message', 'Mensagem') }}
        {{ Form::textarea('message', null, ['class' => 'form-control', 'required', 'rows' => 5]) }}
    </div>
    <span>
        <b class="total-sms">
            {{ $sms->exists ? ceil(strlen($sms->message)/160) : '0' }}</b> mensagem &bull;
        <b class="total-chars">{{ $sms->exists ? strlen($sms->message) : '0' }}</b> caractéres
         &bull; {{ $remainingSms }} SMS Disponíveis
        @if(hasPermission('sms_packs'))
            <a href="{{ route('admin.sms.index', ['tab' => 'packs', 'action' => 'new']) }}" target="_blank" class="btn btn-xs btn-default pull-right">
                <i class="fas fa-shopping-cart"></i> Adquitir pacotes SMS
            </a>
        @endif
    </span>
</div>
<div class="modal-footer">

    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button class="btn btn-primary btn-submit">Gravar</button>
</div>
{{ Form::close() }}

<script>
    $('.modal [name="to[]"]').select2({
        tags: true,
        tokenSeparators: [',', ' ']
    })

    $('[data-toggle="tooltip"]').tooltip();

    $(document).on('keyup', '[name="message"]', function(){
        var msg = $(this).val();
        var totalChars = msg.length;
        var totalSMS   = Math.ceil(totalChars/160);

        $('.total-chars').html(totalChars);
        $('.total-sms').html(totalSMS);
    })
</script>
