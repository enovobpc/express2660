<?php
$currencySymbol = @$receipt->currency ? @$receipt->currency : Setting::get('app_currency');
$customersList = @$customersList ? $customersList : [@$customer->id => @$customer->billing_name];
?>
{{ Form::open($formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    @if (@$blocked)
        <div class="row">
            <div class="col-sm-12">
                <div class="m-t-60 m-b-60 text-center">
                    <h4 class="text-red">
                        <i class="fas fa-info-circle fs-30"></i><br />
                        Os documentos que selecionou não pertencem todas ao mesmo cliente.
                    </h4>
                    <p>Só pode emitir recibos para documentos do mesmo cliente.</p>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            @if (@$customersSameVat && @$customersSameVat->count() > 1)
                <div class="col-sm-12">
                    <div class="alert bg-blue">
                        <h4><i class="fas fa-info-circle"></i> <b>Atenção!</b></h4>
                        Os documentos selecionados pertencem a várias filiais do cliente com o NIF
                        {{ $customer->vat }}.</b>
                        <br />
                        Selecione na caixa de seleção do cliente, a filial/conta-corrente na qual pretende associar o
                        recibo.
                    </div>

                </div>
            @endif
            <div class="col-sm-4">
                <div class="row row-5">
                    <div class="col-sm-12">
                        <h4 class="text-blue m-t-0 m-b-10">Emissão de Recibo</h4>
                        <div class="form-group">
                            {{ Form::label('customer_id', 'Cliente') }}
                            {{ Form::select('customer_id', $customersList, null, ['class' => 'form-control', 'data-placeholder' => '']) }}
                        </div>
                        <hr style="margin: 0 0 5px 0" />
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group is-required">
                            {{ Form::label('docdate', 'Data') }}
                            <div class="input-group input-group-money">
                                {{ Form::text('docdate', @$receipt->exists ? @$receipt->doc_date : date('Y-m-d'), ['class' => 'form-control','required']) }}
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="form-group is-required">
                            {{ Form::label('api_key', 'Série a Usar') }}
                            {{ Form::select('api_key', $apiKeys, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>

                    <div class="col-sm-5">
                        <div class="form-group is-required">
                            {{ Form::label('payment_method', 'Forma Pagamento') }}
                            {{ Form::select('payment_method', ['' => ''] + $paymentMethods, @$receipt->payment_method, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="form-group">
                            @if(hasPermission('banks'))
                            <a href="{{ route('admin.banks.index') }}" class="pull-right" target="_blank"><small><i class="fas fa-cog"></i> Gerir Bancos</small></a>
                            @endif
                            {{ Form::label('payment_bank_id', 'Banco') }}
                            {{ Form::select('payment_bank_id', [''=>''] + $banks, @$receipt->bank_id, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group">
                            {{ Form::label('docref', 'Referência') }}
                            {{ Form::text('docref', @$receipt->reference, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="form-group m-0">
                            {{ Form::label('obs', 'Notas') }}
                            {{ Form::text('obs', @$receipt->obs, ['class' => 'form-control']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="invoices-list">
                    <h4 class="text-blue m-t-0 m-b-10">Faturas a liquidar</h4>
                    <div class="items-list">
                        @include('admin.invoices.sales.partials.receipt_invoices_list')
                    </div>
                </div>
            </div>
            {{-- <div class="col-xs-12">
            <div class="text-yellow">
                <p class="m-0"><i class="fas fa-info-circle"></i> Será criado um recibo por cada fatura.</p>
            </div>
        </div> --}}
        </div>
    @endif
</div>
<div class="modal-footer">
    @if (!@$blocked)
        <div class="extra-options">
            <div class="input-group input-group-email pull-left" style="width: 280px">
                <div class="input-group-addon" data-toggle="tooltip"
                    title="Ative esta opção para enviar e-mail ao cliente.">
                    <i class="fas fa-envelope"></i>
                    {{ Form::checkbox('send_email', 1, @$customer->billing_email ? true : false) }}
                </div>
                {{ Form::text('billing_email', @$customer->billing_email, ['class' => 'form-control pull-left nospace lowercase','placeholder' => 'E-mail do cliente']) }}
            </div>
            <div class="pull-left m-t-7 m-l-15 m-r-10">
                <b class="fw-500">Anexar ao e-mail:</b>
            </div>
            <ul class="list-inline pull-left m-t-5 m-b-0">
                <li>
                    <div class="checkbox m-b-0 m-t-5" style="margin-right: -15px">
                        <label>
                            {{ Form::checkbox('attachments[]', 'receipt', @$customer->billing_email ? true : false) }}
                            Recibo
                        </label>
                    </div>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
    @endif

    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>

    @if (!@$blocked)
        <span class="actions-invoice">
            <button type="button" class="btn btn-default btn-store-draft"
                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">
                Gravar rascunho
            </button>
            <button type="button" class="btn btn-primary btn-store-invoice"
                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A emitir...">
                Emitir Recibo
            </button>
        </span>
    @endif
</div>
<span style="display: none">
    {{ Form::checkbox('draft', 1, true) }}
    {{ Form::hidden('submit_confirmed', '0') }}
</span>
{{ Form::close() }}
<div class="modal" id="modal-confirm-submit">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    Confirmar emissão de recibo
                </h4>
            </div>
            <div class="modal-body">
                <h4 class="m-t-0">Confirma a emissão do recibo?</h4>
                <p class="m-b-4">Cliente: <span class="ft-nm bold">{{ @$customer->billing_name }}</span>
                </p>
                <p class="m-b-4">Data recibo: <span
                        class="ft-dt bold">{{ @$receipt->exists ? $receipt->docdate : date('Y-m-d') }}</span></p>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-answer="0">Não</button>
                    <button type="button" class="btn btn-default" data-answer="1">Sim</button>
                </div>
            </div>
        </div>
    </div>
</div>

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


{{ Html::script(asset('vendor/jquery.nicescroll-master/jquery.nicescroll.js')) }}
<script>
    $('.nicescroll').niceScroll(Init.niceScroll())

    $('.form-billing [name="docdate"]').datepicker({
        format: 'yyyy-mm-dd',
        language: 'pt',
        todayHighlight: true,
        endDate: '{{ date('Y-m-d') }}'
    });
    $('.form-billing .select2').select2(Init.select2());
    $('.form-billing [data-toggle="tooltip"]').tooltip();

    prefill()

    $(document).on('change', '.modal [name="payment_method"]', function() {
        if ($(this).val() == 'transfer' 
        || $(this).val() == 'dd' 
        || $(this).val() == 'mb'
        || $(this).val() == 'mbway'
        || $(this).val() == 'factoring'
        || $(this).val() == 'confirming'
        || $(this).val() == 'settlement') {
            $('.modal [name="payment_bank_id"]').closest('.form-group').addClass('is-required');
            $('.modal [name="payment_bank_id"]').prop('required', true);
        } else {
            $('.modal [name="payment_bank_id"]').closest('.form-group').removeClass('is-required');
            $('.modal [name="payment_bank_id"]').prop('required', false);
        }
    });


    @if (count($customersList) == 1)
        $(".form-billing select[name=customer_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.invoices.sales.search.customer') }}")
        });

        $('.form-billing [name="customer_id"]').on('change', function() {
        var customerId = $(this).val();

        $.post('{{ route('admin.invoices.sales.get.customer.invoices') }}', {customerId: customerId}, function (data) {
        $('.invoices-list .items-list').html(data.html);
        $('.modal [name="billing_email"]').val(data.email)

        if(data.email) {
        $('.modal [name="send_email"], .modal [name="attachments[]"]').prop('checked', true);
        }

        $('.ft-nm').html(data.name)

        prefill();
        $('.nicescroll').niceScroll(Init.niceScroll())
        }).fail(function () {
        Growl.error('Falha na obtenção da listagem de faturas do cliente.')
        })
        });
    @else
        $(".form-billing select[name=customer_id]").select2(Init.select2());
    @endif

    @if (!empty($customer->id))
        if($('#customer_id').val() == '' || $('#customer_id').val() == null){
        $('.form-billing [name="customer_id"]').trigger('change');
        }
    @endif

    $('.form-billing [name="docdate"]').on('change', function() {
        $('.ft-dt').html($(this).val())
    });

    $(document).find('.modal [name="prefill_val"]').keydown(function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            $('.modal [name="prefill_val"]').trigger('change');
            return false;
        }
    });

    $(document).on('change', '.modal [name="prefill_val"]', function(e) {
        e.preventDefault();
        var total = parseFloat($(this).val());
        var remainingTotal = total;

        $('.modal .invoice-price').val('').trigger('change');
        $('.modal [name="prefill"]').prop('checked', false);
        $(this).val('')

        $('.modal .invoice-price').each(function() {

            invoicePrice = parseFloat($(this).data('max'));

            if (invoicePrice >= 0.00 && remainingTotal >= 0.00) { //só pre-preenche faturas

                if (remainingTotal >= invoicePrice) { //possivel liquidar tudo
                    $(this).closest('tr').find('[name="prefill"]').prop('checked', true);
                    $(this).val(invoicePrice.toFixed(2)).trigger('change');
                    remainingTotal -= invoicePrice; //desconta o valor
                } else { //só dá para liquidar parte

                    $(this).val(remainingTotal.toFixed(2)).trigger('change');
                    remainingTotal -= invoicePrice;
                }
            }

        })
    });

    $(document).on('change', '.modal [name="prefill_all"]', function() {
        if ($(this).is(':checked')) {
            $('.modal [name="prefill"]').each(function() {
                $(this).prop('checked', true).trigger('change');
            })
        } else {
            $('.modal [name="prefill"]').each(function() {
                $(this).prop('checked', false).trigger('change');
            })
        }
    });


    function prefill() {

        $('.modal [name="prefill"]').on('change', function() {
            var $tr    = $(this).closest('tr')
            var $input = $tr.find('[data-max]');
            var max    = $input.data('max');

            if ($(this).is(':checked')) {
                $input.val(max);
            } else {
                $input.val('');
            }

            $input.trigger('change')
        })

        $('.modal [data-max]').on('change', function() {
            var total = 0;
            $('.modal [data-max]').each(function() {
                var type = $(this).data('type');
                var max = parseFloat($(this).data('max'));
                var value = parseFloat($(this).val());

                value = isNaN(value) ? 0 : value;

                if (value == 0.00) {
                    $(this).css('border-color', '#ccc').css('color', '#555');
                } else {

                    if (value > max) { //creditos forçados a ter igualmente valor + (para evitar a pessoa escrever sinal negativo)
                        $(this).css('border-color', 'red').css('color', 'red');
                    } else {
                        $(this).css('border-color', '#ccc').css('color', '#555');
                    }

                    /* if (max >= 0.00) { //debitos
                        if (value > max) {
                            $(this).css('border-color', 'red').css('color', 'red');
                        } else {
                            $(this).css('border-color', '#ccc').css('color', '#555');
                        }
                    } else { //creditos
                        if (value < max) {
                            $(this).css('border-color', 'red').css('color', 'red');
                        } else {
                            $(this).css('border-color', '#ccc').css('color', '#555');
                        }
                    } */
                }


                /*if(value > max) {
                    $(this).css('border-color', 'red').css('color', 'red');
                } else {
                    $(this).css('border-color', '#ccc').css('color', '#555');
                }*/

                if (type == 'credit-note') {
                    total -= value;
                } else {
                    total += value;
                }
            })

            total = total.toFixed(2);
            $('.modal [name="total"]').val(total);
            $('.payment-total span').html(total);

            $('.payment-value').val('');
            $('.payment-value').eq(0).val(total)

            $('.payment-row').hide();
            $('.payment-row').eq(0).show();
        })
    }

    /**
     * Enable attachments options
     */
    $(document).on('change', '[name="send_email"]', function() {

        if($(this).is(':checked')) {
            $('[name="attachments[]"').prop('disabled', false);
        } else {
            $('[name="attachments[]"').prop('disabled', true);
        }
    })

    $('#modal-confirm-submit [data-answer]').on('click', function() {
        if ($(this).data('answer') == '1') {
            $('[name="submit_confirmed"]').val('1')
            $('.form-billing').submit();
        }
        $(this).closest('.modal').removeClass('in').hide();
    });


    $('.btn-store-draft').on('click', function() {
        $('[name="draft"]').prop('checked', true);
        $(this).closest('form').submit();
    })

    $('.btn-store-invoice').on('click', function() {
        $('[name="draft"]').prop('checked', false);
        $(this).closest('form').submit();
    })

    $('[name="draft"]').on('change', function() {
        if($(this).is(':checked')) {
            $('.btn-store-invoice').prop('disabled', true)
        } else {
            $('.btn-store-invoice').prop('disabled', false)
        }
    })

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-billing').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var $form = $(this);
        var $btn = $form.find('button[type=button]');
        var total = parseFloat($('.payment-total span').html());

        var hasInvoices = false;
        $(document).find('.modal .invoice-price').each(function() {
            if ($(this).val() != '') {
                hasInvoices = true;
            }
        })

        if ($('.form-billing [name="customer_id"]').val() == '') {
            Growl.error('Não escolheu nenhum cliente da lista.')
        } else if ($('[name="empty_vat"]').val() == '1') {
            $('.empty-nif').show();
            if ($('[name="vat"]').val() == '999999990') {
                $('.empty-nif').hide();
            }
            $('#modal-confirm-empty-vat').addClass('in').show();
        } else if (!hasInvoices) {
            Growl.error('Não selecionou nenhuma fatura para liquidar.')
        } else if (total < 0.00) {
            Growl.error('Não pode emitir recibos com valor negativo.')
        } else if ($('.modal [name="payment_method"]').val() == '' && total > 0.00) {
            Growl.error('É obrigatório indicar a forma pagamento.')
        } else if ($('.modal [name="payment_bank_id"]').is(':required') && $('.modal [name="payment_bank_id"]').val() == '') {
            Growl.error('É obrigatório indicar o banco.')
        } else if ($('[name="submit_confirmed"]').val() == '0' && !$('[name="draft"]').is(':checked')) {
            $('#modal-confirm-submit').addClass('in').show();
        } else {

            if ($('[name="draft"]').is(':checked')) {
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
                    if (data.result) {
                        try {
                            oTable.draw(false); //update datatable
                        } catch (e) {}

                        try {
                            oTableBalance.draw(false); //update datatable on balance details
                        } catch (e) {}

                        if (data.printPdf) {
                            if (!window.open(data.printPdf, '_blank')) {
                                Growl.error(
                                    'Não foi possivel abrir o separador para impressão. Verifique as definições de POP-UPS do browser.'
                                )
                            }
                        }

                        Growl.success(data.feedback);

                        if ($('#modal-remote-xlg').is(':visible')) {
                            $('#modal-remote-xlg').modal('hide');
                            $('#modal-remote-xl').css('overflow-y', 'scroll')
                        } else {
                            $('#modal-remote-xl').modal('hide');
                        }

                    } else {
                        Growl.error(data.feedback);
                    }
                }
            }).fail(function() {
                Growl.error500();
            }).always(function() {
                $btn.button('reset');
                $('.btn-store-invoice').prop('disabled', false)
                $('.btn-store-draft').prop('disabled', false)
            });
        }
    });
</script>
