{{ Form::model($refund, ['route' => ['admin.refunds.customers.update', $refund->shipment_id], 'method' => 'put', 'files' => true, 'class' => 'form-refunds']) }}
<div class="modal-header">
    <button type="button" class="close pull-right" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Editar informação de reembolso</h4>
</div>
<div class="modal-body">
    @if($shipment->customer_id)
    <div class="modal-alert bg-gray-light" style="display: none">
        <h4 class="pull-left">
            <small>IBAN Reembolso:</small>
            <span class="iban-lbl fw-500">{{ $refund->iban ? $refund->iban : 'Não definido na ficha de cliente.' }}</span>
            <span class="iban-nospaces hide">{{ nospace($refund->iban) }}</span>
        </h4>
        <button type="button" class="btn btn-xs btn-default btn-iban-copy m-l-10"
                data-toggle="copy-clipboard"
                data-target="#modal-remote-lg .iban-nospaces"
                data-feedback="IBAN copiado para a área de transferência.">
            <i class="fas fa-copy"></i> Copiar IBAN
        </button>
        <button type="button" class="btn btn-xs btn-default btn-iban-edit">
            <i class="fas fa-pencil-alt"></i> Editar IBAN
        </button>
        <div class="input-group input-group-sm input-iban-edit" style="width: 315px; float: left; margin: -4px 0 -10px 5px; display: none">
            {{ Form::text('iban', @$refund->iban, ['class' => 'form-control iban-input iban uppercase']) }}
            <span class="input-group-btn">
                <button class="btn btn-primary btn-iban-save" type="button">Gravar</button>
                <button class="btn btn-default btn-iban-cancel" type="button"><i class="fas fa-times"></i></button>
            </span>
        </div>
        <div class="clearfix"></div>
    </div>
    @endif
    <div class="row row-5" style="    margin: -14px -15px 15px -15px;
    border-bottom: 1px solid #ddd;
    padding: 0 15px 10px;">
        <div class="col-sm-9">
            <h3 class="m-0"><small>Reembolso a: </small><br/>
                @if(@$shipment->requested_by)
                    {{ @$shipment->requested_customer->name }}
                @else
                    {{ @$shipment->customer->name }}
                @endif
            </h3>
        </div>
        {{--<div class="col-sm-1 text-center">
            <h3 class="m-0"><small>Guias </small><br/>1</h3>
        </div>--}}
        <div class="col-sm-3 text-right">
            <h3 class="m-0"><small>Total (1 envio) </small><br/><b class="text-blue">{{ money($shipment->charge_price, Setting::get('app_currency')) }}</b></h3>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-6" style="padding-right: 30px;">
            <h4 class="text-uppercase m-t-0 fs-14 text-blue">Recebimento</h4>
            <div class="row row-5">
                <div class="col-sm-7">
                    <div class="form-group">
                        {{ Form::label('received_method', 'Forma de recebimento') }}
                        {{ Form::select('received_method', ['' => ''] + trans('admin/refunds.payment-methods-list'), null, ['class' => 'form-control select2', 'data-placeholder' => ' ', 'data-allow-clear' => 'true']) }}
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        {{ Form::label('received_date', 'Data') }}
                        <div class="input-group">
                            {{ Form::text('received_date', empty($refund->received_date) ? null : $refund->received_date, ['class' => 'form-control datepicker nospace', 'autocomplete' => 'field-1']) }}
                            <div class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
            <h4 class="text-uppercase m-t-10 fs-14 text-blue">Reembolso ao Cliente</h4>
            <div class="row row-5">
                <div class="col-sm-7">
                    <div class="form-group {{ $refund->received_method ? 'is-required' : '' }}">
                        {{ Form::label('payment_method', 'Forma de reembolso') }}
                        {{ Form::select('payment_method', ['' => ''] + trans('admin/refunds.payment-methods-list'), null, ['class' => 'form-control select2', 'data-placeholder' => ' ', 'data-allow-clear' => 'true']) }}
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group {{ $refund->received_method ? 'is-required' : '' }}">
                        {{ Form::label('payment_date', 'Data') }}
                        <div class="input-group">
                            {{ Form::text('payment_date', empty($refund->payment_date) ? null : $refund->payment_date, ['class' => 'form-control datepicker nospace', 'autocomplete' => 'field-1']) }}
                            <div class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-{{ (config('app.source') === 'invictacargo')? '7' : '12' }}">
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
                                <input type="file" name="attachment" data-file-format="jpeg,jpg,png,pdf">
                            </span>
                            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                        </div>
                    </div>
                </div>

                @if (config('app.source') === 'invictacargo')
                <div class="col-sm-5">
                    <div class="form-group">
                        {{ Form::label('operator', 'Motorista') }}
                        {{ Form::select('operator', ['' => ''] + $operators, $refund->shipment->operator_id, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                @endif
            </div>
        </div>
        <div class="col-sm-6">
            <h4 class="text-uppercase m-t-0 fs-14 text-blue">Notas e Observações</h4>
            <div class="form-group">
                {{ Form::label('customer_obs', 'Observações visiveis ao Cliente') }}
                {{ Form::textarea('customer_obs', null, ['class' => 'form-control', 'rows' => 5]) }}
            </div>
            <div class="form-group m-b-0">
                {{ Form::label('obs', 'Observações Internas') }}
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 3]) }}
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
            {{ Form::text('email', $refund->email, ['class' => 'form-control pull-left email nospace lowercase', 'placeholder' => 'E-mail do cliente']) }}
        </div>
        <div class="pull-left">
            <p style="margin: 6px 8px 0 0;"><b>Imprimir</b></p>
        </div>
        <div class="checkbox">
            <label>
                {{ Form::checkbox('print_proof', 1, in_array(config('app.source'), ['avatrans', 'transrimarocha', 'rimaalbe']) ? true : false) }}
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

    //btn edit
    $('.btn-iban-edit').on('click', function () {
        $('#modal-remote-lg .modal-alert .input-iban-edit').show();
        $('#modal-remote-lg .modal-alert .iban-lbl, #modal-remote-lg  .btn-iban-copy, #modal-remote-lg  .btn-iban-edit').hide();
    })

    //btn save
    $('.btn-iban-save').on('click', function () {
        var newIban = $('#modal-remote-lg .modal-alert .iban-input').val();
        var newIbanNospace = Str.nospace(newIban);
        var curIban = $('#modal-remote-lg .modal-alert .iban-lbl').html();

        $('#modal-remote-lg .modal-alert .input-iban-edit').hide();
        $('#modal-remote-lg .modal-alert .iban-lbl, #modal-remote-lg .btn-iban-copy, #modal-remote-lg .btn-iban-edit').show();
        $('#modal-remote-lg .modal-alert .iban-lbl').html(newIban);
        $('#modal-remote-lg .modal-alert .iban-nospaces').html(newIbanNospace);
        if(curIban != newIban) {
            $('#modal-remote-lg [name="save_iban"]').val(1);
        }
    })

    //btn cancel
    $('.btn-iban-cancel').on('click', function () {
        $('#modal-remote-lg .modal-alert .input-iban-edit').hide();
        $('#modal-remote-lg .modal-alert .iban-lbl, #modal-remote-lg .btn-iban-copy, #modal-remote-lg .btn-iban-edit').show();
    })

    $('.modal [name="payment_method"]').on('change', function(){
        if($(this).val() == 'transfer') {
            $('.modal-alert').show();
        } else {
            $('.modal-alert').hide();
        }
    })

    $('[name="received_method"]').on('change', function () {
        if($(this).val() == 'claimed') {
            $('.claimed-label').show();
            $('.received-label').hide();
        } else {
            $('.received-label').show();
            $('.claimed-label').hide();
        }
    });

    $('.form-refunds [name="received_method"], .form-refunds [name="received_date"], .form-refunds [name="payment_method"],.form-refunds [name="payment_date"]').on('change', function(){
        $('.form-refunds .form-group').removeClass('has-error');
    })

    $('.form-refunds').on('submit', function(e){
        e.preventDefault();

        var $receivedMethod = $('.form-refunds [name="received_method"]');
        var $receivedDate   = $('.form-refunds [name="received_date"]');
        var $paymentMethod  = $('.form-refunds [name="payment_method"]');
        var $paymentDate    = $('.form-refunds [name="payment_date"]');

        /*if($receivedMethod.val() == ''
            && $receivedDate.val() == ''
            && $paymentMethod.val() == ''
            && $paymentDate.val() == '') {
            Growl.error('É obrigatório indicar a forma e data de reebimento ou a forma e data de pagamento.')
            return false;
        }*/

        if(($receivedMethod.val() != '' && $receivedDate.val() == '')
            || ($receivedMethod.val() == '' && $receivedDate.val() != '')) {

            if($receivedMethod.val() == ''){
                $receivedMethod.closest('.form-group').addClass('has-error');
                Growl.error('É obrigatório indicar a forma de recebimento.')
            } else {
                $receivedDate.closest('.form-group').addClass('has-error');
                Growl.error('É obrigatório indicar a data de recebimento.')
            }
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

                    windowError = false;

                    if (data.printProof) {
                        if (!window.open(data.printProof, '_blank')) {
                            windowError = true;
                            $('#modal-remote-lg').find('.modal-lg').removeClass('modal-lg').find('.modal-content').html(data.html);
                        }
                    }

                    if (data.printSummary) {
                        if (!window.open(data.printSummary, '_blank')) {
                            windowError = true;
                            $('#modal-remote-lg').find('.modal-lg').removeClass('modal-lg').find('.modal-content').html(data.html);
                        }
                    }

                    try {
                        Growl.success(data.feedback);
                        oTable.draw(false);
                        $('.selected-rows-action').addClass('hide')

                        if(!windowError) {
                            $('#modal-remote-lg').modal('hide');
                        }
                    } catch (e) {}

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