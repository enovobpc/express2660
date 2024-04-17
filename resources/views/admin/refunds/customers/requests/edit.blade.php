{{ Form::model($refundRequest, ['route' => ['admin.refunds.requests.update', $refundRequest->id], 'method' => 'put', 'files' => true, 'class' => 'form-refunds']) }}
<div class="modal-header">
    <button type="button" class="close pull-right" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Editar pedido de reembolso</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-6" style="padding-right: 30px;">
            <h4 class="text-uppercase m-t-10 fs-14 text-blue">Reembolso ao Cliente</h4>
            <div class="row row-5">
                <div class="col-sm-7">
                    <div class="form-group is-required">
                        {{ Form::label('payment_method', 'Forma de reembolso') }}
                        {{ Form::select('payment_method', ['' => ''] + trans('admin/refunds.payment-methods-list'), null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group is-required">
                        {{ Form::label('payment_date', 'Data') }}
                        <div class="input-group">
                            {{ Form::text('payment_date', empty($refundRequest->payment_date) ? null : $refundRequest->payment_date, ['class' => 'form-control datepicker nospace', 'autocomplete' => 'field-1']) }}
                            <div class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group m-b-0" id="upload">
                        {{ Form::label('attachment', 'Anexar comprovativo de reembolso', ['class' => 'control-label']) }}
                        <div class="fileinput fileinput-new input-group m-b-0" data-provides="fileinput">
                            <div class="form-control" data-trigger="fileinput">
                                <i class="fas fa-file fileinput-exists"></i>
                                <span class="fileinput-filename"></span>
                            </div>
                            <span class="input-group-addon btn btn-default btn-file">
                                <span class="fileinput-new">Selecionar</span>
                                <span class="fileinput-exists">Alterar</span>
                                <input type="file" name="attachment" data-file-format="jpeg,jpg,png">
                            </span>
                            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <h4 class="text-uppercase m-t-0 fs-14 text-blue">Notas e Observações</h4>
            <div class="form-group">
                {{ Form::label('customer_obs', 'Observações visiveis ao Cliente') }}
                {{ Form::textarea('customer_obs', null, ['class' => 'form-control', 'rows' => 2]) }}
            </div>
            <div class="form-group m-b-0">
                {{ Form::label('obs', 'Observações Internas') }}
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 1]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="extra-options w-75">
        <div class="input-group input-email pull-left m-r-20" style="width: 280px">
            <div class="input-group-addon" data-toggle="tooltip" title="Ative esta opção para enviar e-mail ao cliente.">
                <i class="fas fa-envelope"></i>
                {{ Form::checkbox('send_email', 1, false) }}
            </div>
            {{ Form::text('email', @$refundRequest->customer->email, ['class' => 'form-control pull-left email nospace lowercase', 'placeholder' => 'E-mail do cliente']) }}
        </div>
        <div class="pull-left">
            <p style="margin: 6px 8px 0 0;"><b>Imprimir</b></p>
        </div>
        <div class="checkbox">
            <label>
                {{ Form::checkbox('print_proof', 1) }}
                Comprovativo
            </label>
        </div>
        <div class="checkbox" style="margin-left: -8px; margin-top: 10px">
            <label>
                {{ Form::checkbox('print_summary', 1) }}
                Resumo
            </label>
        </div>
    </div>
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">Gravar</button>
    </div>
</div>
{{ Form::hidden('save_iban') }}
{{ Form::close() }}

<script>
    $('.form-refunds .select2').select2(Init.select2());
    $('.form-refunds .datepicker').datepicker(Init.datepicker());

    $('.modal [name="payment_method"]').on('change', function(){
        if($(this).val() == 'transfer') {
            $('.modal-alert').show();
        } else {
            $('.modal-alert').hide();
        }
    })

    $('.form-refunds [name="payment_method"],.form-refunds [name="payment_date"]').on('change', function(){
        $('.form-refunds .form-group').removeClass('has-error');
    })

    $('.form-refunds').on('submit', function(e){
        e.preventDefault();

        var $paymentMethod  = $('.form-refunds [name="payment_method"]');
        var $paymentDate    = $('.form-refunds [name="payment_date"]');

        if($paymentMethod.val() == '' && $paymentDate.val() == '') {
            Growl.error('É obrigatório indicar a forma e data de reebimento ou a forma e data de pagamento.')
            return false;
        }

        if(($paymentMethod.val() != '' && $paymentDate.val() == '')
            || ($paymentMethod.val() == '' && $paymentDate.val() != '')) {

            if($paymentMethod.val() == ''){
                $paymentMethod.closest('.form-group').addClass('has-error');
                Growl.error('É obrigatório indicar a forma de reembolso.')
            } else {
                $paymentDate.closest('.form-group').addClass('has-error');
                Growl.error('É obrigatório indicar a data de reembolso.')
            }
            return false;
        }

        var $form = $(this);
        var $submitBtn = $form.find('button[type=submit]');
        $submitBtn.button('loading');

        var form = $(this)[0];
        var formData = new FormData(form);

        $.ajax({
            url: $form.attr('action'),
            data: formData,
            type: 'POST',
            contentType: false,
            processData: false,
            success: function (data) {
                if (data.result) {
                    Growl.success(data.feedback);
                    oTableRequests.draw(false);
                    $('#modal-remote-lg').modal('hide');

                    if (data.printProof) {
                        if (window.open(data.printProof, '_blank')) {
                            $('#modal-remote-lg').modal('hide');
                        } else {
                            $('#modal-remote-lg').find('.modal-lg').removeClass('modal-lg').find('.modal-content').html(data.html);
                        }
                    }

                    if (data.printSummary) {
                        if (window.open(data.printSummary, '_blank')) {
                            $('#modal-remote-lg').modal('hide');
                        } else {
                            $('#modal-remote-lg').find('.modal-lg').removeClass('modal-lg').find('.modal-content').html(data.html);
                        }
                    }

                } else {
                    Growl.error(data.feedback);
                }
            },
        }).fail(function () {
            Growl.error500();
        }).always(function() {
            $submitBtn.button('reset');
        });
    });
</script>