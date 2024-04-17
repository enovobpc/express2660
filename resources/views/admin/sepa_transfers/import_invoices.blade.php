<?php
$hash = str_random(5);
$processingDate = Date::today()->addDays(2)->format('Y-m-d');
?>
{{ Form::open($formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Gerar transferência SEPA</h4>
</div>
<div class="modal-body {{ $hash }}">
    <div class="row">
        <div class="col-sm-4">
            <h4 class="text-blue m-t-0 m-b-10">SEPA - Débito Direto</h4>
            <div class="row row-5">
                <div class="col-sm-12">
                    <div class="form-group">
                        {{ Form::label('bank_id', 'Nossa Conta Bancária') }}
                        {{ Form::select('bank_id', ['' => ''] + $banks, null, ['class' => 'form-control select2', 'required']) }}
                    </div>
                    <hr/>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('code', 'Código') }}
                        {{ Form::text('code', $payment->code, ['class' => 'form-control', 'maxlength' => 35, 'required']) }}
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="form-group">
                        {{ Form::label('name', 'Descrição') }}
                        {{ Form::text('name', null, ['class' => 'form-control', 'maxlength' => 35, 'required', 'placeholder' => 'Ex: Pagamentos Maio 2021']) }}
                    </div>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-5">
                    <div class="form-group">
                        {{ Form::label('processing_date', 'Data Débito') }}
                        <div class="input-group">
                            {{ Form::text('processing_date', $processingDate, ['class' => 'form-control', 'required']) }}
                            <div class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('service_type', 'Tipo Serv.') }}
                        {{ Form::select('service_type', ['CORE' => 'CORE', 'B2B' => 'B2B'], null, ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('sequence_type', 'Frequencia') }}
                        {{ Form::select('sequence_type', trans('admin/billing.sepa-sequence-types'), null, ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="invoices-list">
                <h4 class="text-blue m-t-0 m-b-10">Faturas a incluir nas transações</h4>
                <p class="text-red m-b-10 error-msg" style="display: none"><i class="fas fa-exclamation-triangle"></i> Existem transações com erro. Corrija os erros antes de criar uma transferência SEPA</p>
                <div class="items-list">
                    @include('admin.sepa_transfers.partials.invoices_list')
                </div>
            </div>
        </div>
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <span class="actions-invoice">
            <button type="button"
                    disabled
                    class="btn btn-primary disabled btn-sepa-disabled" style="display: none">
            Gerar Transf. SEPA
        </button>
            <button type="submit"
                    class="btn btn-primary btn-store-sepa"
                    data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">
            Gerar Transf. SEPA
        </button>
    </span>
</div>
{{ Form::close() }}

<style>
    .modal .table-condensed>tbody>tr>td,
    .modal .table-condensed>tbody>tr>th,
    .modal .table-condensed>tfoot>tr>td,
    .modal .table-condensed>tfoot>tr>th,
    .modal .table-condensed>thead>tr>td,
    .modal .table-condensed>thead>tr>th {
        padding: 3px 5px;
    }
</style>

{{ Html::script(asset('vendor/devbridge-autocomplete/dist/jquery.autocomplete.js')) }}
{{ Html::script(asset('vendor/jquery.nicescroll-master/jquery.nicescroll.js')) }}
<script>

    if($('.{{ $hash }} .line-error').length > 0) {
        $('.btn-store-sepa').hide()
        $('.btn-sepa-disabled, .error-msg').show()
    }

    $('.nicescroll').niceScroll(Init.niceScroll())

    $('.form-billing input').iCheck(Init.iCheck());
    $('.form-billing [name="processing_date"]').datepicker({
        format: 'yyyy-mm-dd',
        language: 'pt',
        todayHighlight: true,
        startDate: '{{ $processingDate }}'
    });
    $('.form-billing .select2').select2(Init.select2());
    $('.form-billing [data-toggle="tooltip"]').tooltip();

    $(document).on('ifChanged', '.{{ $hash }} [name="prefill_all"]', function() {
        if($(this).is(':checked')) {
            $('.form-billing .prefill').iCheck('check')
        } else {
            $('.form-billing .prefill').iCheck('uncheck')
        }
    });

    $(document).on('ifChanged', '.{{ $hash }} [name=prefill]', function() {
        var total = 0;
        $('.form-billing .prefill').each(function(){
            if($(this).is(':checked')){
                total+= parseFloat($(this).data('value'))
            }
        })

        value = round(total) + "{{ Setting::get('app_currency') }}"
        $(document).find('.{{ $hash }} .total-selected').html(value)
    })


    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.{{ $hash }}').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var $form = $(this);
        var $btn = $form.find('button[type=button]');
        var total = parseFloat($('.payment-total span').html());

        var hasInvoices = false;
        $(document).find('.modal .invoice-price').each(function () {
            if($(this).val() != '') {
                hasInvoices = true;
            }
        })

        if ($('[name="customer_id"]').val() == '') {
            Growl.error('Não escolheu nenhum cliente da lista.')
        } else if ($('[name="empty_vat"]').val() == '1') {
            $('.empty-nif').show();
            if ($('[name="vat"]').val() == '999999990') {
                $('.empty-nif').hide();
            }
            $('#modal-confirm-empty-vat').addClass('in').show();
        } else if (!hasInvoices) {
            Growl.error('Não selecionou nenhuma fatura para liquidar.')
        } else if(total < 0.00) {
            Growl.error('Não pode emitir recibos com valor negativo.')
        } else if ($('[name="submit_confirmed"]').val() == '0' && !$('[name="draft"]').iCheck('update')[0].checked) {
            $('#modal-confirm-submit').addClass('in').show();
        } else {

            if($('[name="draft"]').iCheck('update')[0].checked) {
                var $btn = $('.btn-store-draft');
                $('.btn-store-invoice').prop('disabled', true)
            } else {
                var $btn = $('.btn-store-invoice');
                $('.btn-store-draft').prop('disabled', true)
            }

            $btn.button('loading');

            $.ajax({
                url: $form.attr('action'),
                data: $form.serialize(),
                type: $form.attr('method'),
                success: function(data) {
                    if(data.result) {
                        try {
                            oTable.draw(false); //update datatable
                        } catch (e) {}

                        try {
                            oTableBalance.draw(false); //update datatable on balance details
                        } catch (e) {}

                        if (data.printPdf) {
                            if (!window.open(data.printPdf, '_blank')) {
                                Growl.error('Não foi possivel abrir o separador para impressão. Verifique as definições de POP-UPS do browser.')
                            }
                        }

                        Growl.success(data.feedback);

                        if($('#modal-remote-xlg').is(':visible')) {
                            $('#modal-remote-xlg').modal('hide');
                            $('#modal-remote-xl').css('overflow-y', 'scroll')
                        } else {
                            $('#modal-remote-xl').modal('hide');
                        }

                    } else {
                        Growl.error(data.feedback);
                    }
                }
            }).fail(function () {
                Growl.error500();
            }).always(function () {
                $btn.button('reset');
                $('.btn-store-invoice').prop('disabled', false)
                $('.btn-store-draft').prop('disabled', false)
            });
        }
    });
</script>