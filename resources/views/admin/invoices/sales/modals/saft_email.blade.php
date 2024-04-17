{{ Form::open(['route' => ['admin.invoices.saft.email.send', $year, $month], 'class' => 'send-email']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">
        @if($saft->issued)
            Enviar SAF-T por E-mail
        @else
            Emitir ficheiro SAF-T
        @endif
    </h4>
</div>
<div class="modal-body">
    @if(!$saft->issued)
        <h4 class="bold m-0" style="margin: -15px -15px 15px -15px;
    padding: 15px;
    background: #eee;
    border-bottom: 1px solid #ddd;">
            Confirma a emissão do ficheiro SAF-T?<br/>
            <small class="bold text-blue"><i class="fas fa-file-alt"></i> SAF-T {{ trans('datetime.month.'.$month) }} de {{ $year }}</small>
        </h4>
    @endif
    <div class="form-group" data-toggle="tooltip" title="Envie automáticamente o ficheiro SAF-T para a sua contabilidade. O endereço e-mail fica memorizado para vezes futuras.">
        {{ Form::label('email', 'Enviar SAF-T para o e-mail:') }}
        {{ Form::text('email', Setting::get('accountant_email'), ['class' => 'form-control email nospace lowercase', 'placeholder' => 'Escreva aqui o e-mail do contabilista...']) }}
    </div>
    <div class="form-group m-b-0">
        <label style="font-weight: normal">
            {{ Form::checkbox('email_cc', 1, true) }}
            Enviar uma cópia para o meu e-mail
        </label>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        @if($saft->issued)
            <button type="submit" class="btn btn-primary"
                    data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A emitir...">
                Enviar
            </button>
        @else
        <button type="submit" class="btn btn-primary"
                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A emitir...">
            Emitir SAF-T
        </button>
        @endif
    </div>
</div>
{{ Form::hidden('company', $saft->company_id) }}
{{ Form::close() }}

<script>
    $('form.send-email').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                Growl.success(data.feedback);
                $('#modal-remote-xs').modal('hide');
                oTableSaft.draw()
            } else {
                Growl.error(data.feedback);
            }
        }).fail(function () {
            Growl.error500();
        }).always(function(){
            $button.button('reset');
        })
    });
</script>