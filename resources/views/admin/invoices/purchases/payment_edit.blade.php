{{ Form::open($formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Editar Nota Pagamento {{ $paymentNote->code }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('reference', 'Nº Recibo Fornecedor') }}
                {{ Form::text('reference', @$paymentNote->reference, ['class' => 'form-control']) }}
            </div>
            <div class="form-group m-b-0" id="upload">
                {{ Form::label('attachment', 'Anexar Recibo') }}
                <div class="fileinput fileinput-new input-group m-b-0" data-provides="fileinput">
                    <div class="form-control" data-trigger="fileinput">
                        <i class="fas fa-file fileinput-exists"></i>
                        <span class="fileinput-filename"></span>
                    </div>
                    <span class="input-group-addon btn btn-default btn-file">
                    <span class="fileinput-new">Selecionar</span>
                    <span class="fileinput-exists">Alterar</span>
                    <input type="file" name="attachment[]" data-file-format="jpeg,jpg,png,pdf,doc,docx" multiple>
                </span>
                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit"
            class="btn btn-success"
            data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">
        Gravar
    </button>
</div>
{{ Form::close() }}

<script>
    /**
     * Submit
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('form.settle-invoice').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $submitBtn = $form.find('button[type=submit]');
        $submitBtn.button('loading');

        var form = $(this)[0];
        var formData = new FormData(form);
        var method = $form.attr("method");
        if(typeof method === 'undefined'){
            method = "POST";
        }

        $.ajax({
            url: $form.attr('action'),
            data: formData,
            type: method,
            contentType: false,
            processData: false,
            success: function (data) {
                if(data.result) {
                    Growl.success(data.feedback);
                    $('.selected-rows-action').addClass('hide')
                    oTablePaymentNotes.draw();

                    $('#modal-remote-xs').modal('hide');
                } else {
                    Growl.error(data.feedback);
                }
            }
        }).fail(function () {
            Growl.error500()
            $form.find('.modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
        }).always(function () {
            $submitBtn.button('reset');
        });

    });
</script>