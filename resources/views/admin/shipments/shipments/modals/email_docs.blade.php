{{ Form::open(['route' => ['admin.shipments.email.submit', $shipment->id, 'docs'], 'class' => 'send-email']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Enviar documentos por e-mail</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('email', 'Enviar documentos para o(s) e-mail(s)') }}
                {{ Form::text('email', @$shipment->customer->contact_email, ['class' => 'form-control email nospace lowercase', 'required']) }}
            </div>
        </div>
    </div>
    <label>Que documentos pretende anexar ao e-mail?</label>
    <div class="clearfix"></div>
    
    @if(app_mode_cargo())
    <div class="checkbox-inline" style="margin: 5px 5px 0 0; padding-left: 0">
        <label style="padding-left: 0; font-weight: normal">
            {{ Form::checkbox('attachments[]', 'crm', true) }}
                CRM 
        </label>
    </div>
    @endif

    <div class="checkbox-inline" style="margin: 5px 5px  0 15px; padding-left: 0">
        <label style="padding-left: 0; font-weight: normal">
            {{ Form::checkbox('attachments[]', 'guide', true) }}
            Guia de Transporte
        </label>
    </div>

    
    <div class="checkbox-inline" style="margin: 5px 5px  0 15px; padding-left: 0">
        <label style="padding-left: 0; font-weight: normal">
            {{ Form::checkbox('attachments[]', 'label', true) }}
            Etiquetas
        </label>
    </div>
    
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary"
                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A enviar...">
            Enviar E-mail
        </button>
    </div>
</div>
{{ Form::close() }}

<script>
    $('.modal .select2').select2(Init.select2());

    $('.modal [name="mailing_list"]').on('change', function(){
        if($(this).val() == '') {
            $('.modal [name="email"]').prop('disabled', false);
        } else {
            $('.modal [name="email"]').val('').prop('disabled', true);
        }
    })


    $('.modal form.send-email').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                Growl.success(data.feedback);
                $('#modal-remote').modal('hide');
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