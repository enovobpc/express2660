{{ Form::open($formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Pagamentos a fornecedores</h4>
</div>
<div class="modal-body">
    <?php $hideSubmit = false ?>
    @if(@$invoices && count(array_unique($invoices->pluck('vat')->toArray())) > 1)
        <?php $hideSubmit = true ?>
        <h4 class="text-center m-t-50 m-b-50 text-red">
            <i class="fas fa-info-circle"></i> As faturas selecionadas têm de pertencer todas ao mesmo fornecedor.
        </h4>
    @else
        <div class="row row-5">
            <div class="col-sm-6">
                <div class="form-group">
                    {{ Form::label('provider_id', 'Fornecedor') }}
                    {{ Form::select('provider_id', @$provider ? [@$provider->id => $provider->code . ' - ' .$provider->company] : [], null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    {{ Form::label('reference', 'Nº Recibo Fornecedor') }}
                    {{ Form::text('reference', null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    {{ Form::label('date', 'Data Documento') }}
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        {{ Form::text('date', date('Y-m-d'), ['class' => 'form-control datepicker']) }}
                    </div>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    {{ Form::label('discount', 'Desc. Financeiro') }}
                    <div class="input-group">
                        <div style="float: left; width: 70%">
                            {{ Form::text('discount', null, ['class' => 'form-control decimal']) }}
                        </div>
                        <div style="float: left; width: 30%; margin-left: -1px">
                            {{ Form::select('discount_unity', [Setting::get('app_currency') => Setting::get('app_currency'), '%' => '%'], null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="alert" style="background: #eee; display: none">
            <p><i class="fas fa-coins"></i> IBAN Pagamento: <b class="iban">{{ @$provider->iban ? $provider->iban : 'Não definido na ficha do fornecedor' }}</b></p>
        </div>
        <h4 class="bold">Documentos a liquidar</h4>
        <table class="table table-condensed">
            <tr>
                <th class="bold bg-gray-light vertical-align-middle w-150px">Tipo Doc.</th>
                <th class="bold bg-gray-light vertical-align-middle">Referência</th>
                <th class="bold bg-gray-light vertical-align-middle w-100px">Data</th>
                <th class="bold bg-gray-light vertical-align-middle w-110px">Vencimento</th>
                <th class="bold bg-gray-light vertical-align-middle w-90px" style="border-left: 2px solid #333">Total</th>
                <th class="bold bg-gray-light vertical-align-middle w-90px">Pendente</th>
                <th class="bold bg-gray-light vertical-align-middle w-120px">{{ Form::text('prefill_val', null, ['class' => 'form-control input-xs decimal', 'placeholder' => 'Valor Liquidar...', 'style' => 'padding: 7px;height: 26px; margin: -1px 0;']) }}</th>
                {{--<th class="bold bg-gray-light vertical-align-middle w-90px">Desconto</th>--}}
                <th class="bold bg-gray-light vertical-align-middle w-1">{{ Form::checkbox('prefill_all', 1, null) }}</th>
            </tr>
            <?php $total = $totalUnpaid = 0 ?>
            @if(@$invoices && !$invoices->isEmpty())
                @foreach($invoices as $invoice)
                    @if($paymentNote->exists)
                        <?php
                        $total+= $invoice->total;
                        $totalUnpaid+= $invoice->invoice->total_unpaid;

                        $duedateClass = '';
                        if(@$invoice->invoice->due_date < date('Y-m-d')) {
                            $duedateClass = 'text-red';
                            $dueIcon = '<small><i class="fas fa-exclamation-circle"></i></small> ';
                        }
                        ?>
                        <tr>
                            <td class="vertical-align-middle {{ $duedateClass }}">{{ trans('admin/billing.types.' . $invoice->invoice->doc_type) }}</td>
                            <td class="vertical-align-middle {{ $duedateClass }}">{{ $invoice->invoice->reference }}</td>
                            <td class="vertical-align-middle {{ $duedateClass }}">{{ $invoice->invoice->doc_date }}</td>
                            <td class="vertical-align-middle {{ $duedateClass }}">{!! $dueIcon !!}{{ $invoice->invoice->due_date }}</td>
                            <td class="vertical-align-middle bold" style="border-left: 2px solid #333">{{ money($invoice->invoice->total, $invoice->currency) }}</td>
                            <td class="vertical-align-middle bold text-red pending-value"><span>{{ money($invoice->invoice->total_unpaid, $invoice->currency) }}</span>{{ $invoice->currency }}</td>
                            <td class="">
                                <div class="input-group">
                                    <input name="invoices[{{ $invoice->invoice->id }}][total]" value="{{ $invoice->total }}"
                                           class="form-control input-sm decimal invoice-price"
                                           data-max="{{ $invoice->invoice->total_unpaid + $invoice->total }}"
                                           style="border-right: 0;">
                                    <div class="input-group-addon" style="border-left: 0;">
                                        {{ Setting::get('app_currency') }}
                                    </div>
                                </div>
                            </td>
                            {{--
                            <td class="">
                                <div class="input-group">
                                    <input name="invoices[{{ $invoice->invoice->id }}][discount]" value="{{ $invoice->total }}"
                                           class="form-control input-sm decimal invoice-price"
                                           data-max="{{ $invoice->invoice->total_unpaid + $invoice->total }}"
                                           style="border-right: 0;">
                                    <div class="input-group-addon" style="border-left: 0;">
                                        {{ Setting::get('app_currency') }}
                                    </div>
                                </div>
                            </td>
                            --}}
                            <td class="vertical-align-middle">
                                {{ Form::checkbox('prefill', 1, null) }}
                            </td>
                        </tr>
                    @else
                        <?php
                        $total+= $invoice->total;
                        $totalUnpaid+= $invoice->total_unpaid;
                        $duedateClass = '';
                        if(@$invoice->invoice->due_date < date('Y-m-d')) {
                            $duedateClass = 'text-red';
                            $dueIcon = '<small><i class="fas fa-exclamation-circle"></i></small> ';
                        }
                        ?>
                        <tr>
                            <td class="vertical-align-middle">{{ trans('admin/billing.types.' . $invoice->doc_type) }}</td>
                            <td class="vertical-align-middle">{{ $invoice->reference }}</td>
                            <td class="vertical-align-middle ">{{ $invoice->doc_date }}</td>
                            <td class="vertical-align-middle {{ $duedateClass }}">{!! $dueIcon !!}{{ $invoice->due_date }}</td>
                            <td class="vertical-align-middle bold" style="border-left: 2px solid #333">{{ money($invoice->total, $invoice->currency) }}</td>
                            <td class="vertical-align-middle bold text-{{ $invoice->total_unpaid < $invoice->total ? 'yellow' : 'red' }}">{{ money($invoice->total_unpaid, $invoice->currency) }}</td>
                            <td class="">
                                <div class="input-group">
                                    <input name="invoices[{{ $invoice->id }}][total]" class="form-control input-sm decimal invoice-price" data-max="{{ $invoice->total_unpaid }}" style="border-right: 0;">
                                    <div class="input-group-addon" style="border-left: 0;">
                                        {{ Setting::get('app_currency') }}
                                    </div>
                                </div>
                            </td>
                            {{--<td class="">
                                <div class="input-group">
                                    <input name="invoices[{{ $invoice->id }}][discount]" class="form-control input-sm decimal invoice-price" data-max="{{ $invoice->total_unpaid }}" style="border-right: 0;">
                                    <div class="input-group-addon" style="border-left: 0;">
                                        {{ Setting::get('app_currency') }}
                                    </div>
                                </div>
                            </td>--}}
                            <td class="vertical-align-middle">
                                {{ Form::checkbox('prefill', 1, null) }}
                            </td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="vertical-align-middle bold" style="border-left: 2px solid #333">
                        {{ money($total, @$invoice->currency) }}
                    </td>
                    <td class="vertical-align-middle bold text-red">
                        {{ money($totalUnpaid, @$invoice->currency) }}
                    </td>
                    <td class="bold fs-15px payment-total">
                        <span>{{ @$paymentNote->exists ? money(@$paymentNote->total) : money(0) }}</span>{{ Setting::get('app_currency') }}
                    </td>
                    <td></td>
                </tr>
            @else
                <tr>
                    <td colspan="8">
                        @if(@$provider)
                        <p class="text-yellow text-center m-t-5 m-b-30"><i class="fas fa-info-circle"></i> Não há faturas possíveis de liquidação.</p>
                        @else
                        <p class="text-muted text-center m-t-5 m-b-30"><i class="fas fa-info-circle"></i> Selecione um fornecedor da lista para apresentar as faturas possíveis de liquidação.</p>
                        @endif
                    </td>
                </tr>
            @endif
        </table>
            <div style="padding: 0 7px 5px;
                        margin: -15px 0 -20px;
                        border: 1px solid #ddd;
                        float: right;
                        border-radius: 5px;
                        background: #eee;
                        text-align: right">
                <h4 style="
                    margin: 0;
                    float: left;
                    font-size: 15px;
                    width: 85px;
                ">
                    <small>Total</small><br/>
                    <span class="doc-subtotal">{{ money($paymentNote->total) }}</span>{{ Setting::get('app_currency') }}
                </h4>
                <h4 style="
                    margin: 0;
            float: left;
            font-size: 15px;
            width: 75px;
        ">
                    <small>Desconto</small><br/>
                    <span class="doc-discount">{{ money($paymentNote->discount) }}</span>{{ Setting::get('app_currency') }}
                </h4>
                <h4 style="
            margin: 0;
            float: left;
            font-size: 15px;
            font-weight: bold;
            width: 90px;
        ">
                    <small>Total Pago</small><br>
                    <span class="doc-total">{{ money($paymentNote->total - $paymentNote->discount) }}</span>{{ Setting::get('app_currency') }}
                </h4>

        </div>
        <div class="clearfix"></div>
        <h4 class="bold">Formas de pagamento</h4>
        <table class="table table-condensed m-0">
            <tr>
                <th class="bold bg-gray-light w-140px">Data</th>
                <th class="bold bg-gray-light w-150px">Meio Pagamento</th>
                <th class="bold bg-gray-light w-120px bank-col">Banco</th>
                <th class="bold bg-gray-light w-130px">Valor</th>
                <th class="bold bg-gray-light">Observações</th>
            </tr>
            <?php $startIt = 0 ?>
            @if($payments)
                @foreach($payments as $i => $paymentMethod)
                    <?php $startIt = $i + 1; ?>
                    <tr class="payment-row">
                        <td class="vertical-align-middle">
                            <div class="input-group">
                                {{ Form::text('payment['.$i.'][date]', $paymentMethod->date, ['class' => 'form-control input-sm datepicker']) }}
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>

                        </td>
                        <td class="vertical-align-middle input-sm">
                            {{ Form::select('payment['.$i.'][payment_method_id]', [''=>''] + $paymentMethods, $paymentMethod->payment_method_id, ['class' => 'form-control input-sm select2 payment-method']) }}
                        </td>
                        <td class="vertical-align-middle input-sm bank-col">
                            {{ Form::select('payment['.$i.'][bank_id]', [''=>''] + $banks, $paymentMethod->bank_id, ['class' => 'form-control input-sm select2']) }}
                        </td>
                        <td class="">
                            <div class="input-group">
                                {{ Form::text('payment['.$i.'][value]', $paymentMethod->total, ['class' => 'form-control input-sm decimal payment-value', 'style' => 'border-right: 0;']) }}
                                <div class="input-group-addon" style="border-left: 0;">
                                    {{ Setting::get('app_currency') }}
                                </div>
                            </div>
                        </td>
                        <td class="vertical-align-middle">
                            {{ Form::text('payment['.$i.'][obs]', $paymentMethod->obs, ['class' => 'form-control input-sm']) }}
                        </td>
                    </tr>
                @endforeach
            @endif
            @for($i=$startIt ; $i<=5 ; $i++)
                <tr style="{{ $i==0 ? : 'display:none' }}" class="payment-row">
                    <td class="vertical-align-middle">
                        <div class="input-group">
                            {{ Form::text('payment['.$i.'][date]',  $i==0 ? date('Y-m-d') : null, ['class' => 'form-control input-sm datepicker']) }}
                            <div class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>

                    </td>
                    <td class="vertical-align-middle input-sm">
                        {{ Form::select('payment['.$i.'][payment_method_id]', [''=>''] + $paymentMethods, null, ['class' => 'form-control input-sm select2 payment-method']) }}
                    </td>
                    <td class="vertical-align-middle input-sm bank-col">
                        {{ Form::select('payment['.$i.'][bank_id]', [''=>''] + $banks, null, ['class' => 'form-control input-sm select2']) }}
                    </td>
                    <td class="">
                        <div class="input-group">
                            {{ Form::text('payment['.$i.'][value]', $i==0 ? $total : null, ['class' => 'form-control input-sm decimal payment-value', 'style' => 'border-right: 0;']) }}
                            <div class="input-group-addon" style="border-left: 0;">
                                {{ Setting::get('app_currency') }}
                            </div>
                        </div>
                    </td>
                    <td class="vertical-align-middle">
                        {{ Form::text('payment['.$i.'][obs]', null, ['class' => 'form-control input-sm']) }}
                    </td>
                </tr>
            @endfor
        </table>
        <button type="button" class="btn btn-xs btn-default m-l-5 btn-add-payment">
            <i class="fas fa-plus"></i> Adicionar outro pagamento
        </button>

        <h4 class="bold">Anexar comprovativo pagamento</h4>
        <div class="form-group m-b-0" id="upload">
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
        <div class="clearfix"></div>


        {{--<div class="col-sm-12">
            <div class="form-group m-b-0">
                {{ Form::label('payment_method', 'Enviar nota de pagamento por e-mail') }}
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    {{ Form::text('email', @$invoice->provider->billing_email, ['class' => 'form-control pull-left email nospace lowercase', 'placeholder' => 'E-mail do fornecedor']) }}
                </div>
            </div>
        </div>--}}
    </div>
    @endif
</div>
<div class="modal-footer">
    <div class="extra-options">
        <div class="input-group input-group-email pull-left" style="width: 280px">
            <div class="input-group-addon" data-toggle="tooltip" title="Ative esta opção para enviar e-mail ao cliente.">
                <i class="fas fa-envelope"></i>
                {{ Form::checkbox('send_email', 1, false) }}
            </div>
            {{ Form::text('billing_email', @$provider->email, ['class' => 'form-control pull-left nospace lowercase', 'placeholder' => 'E-mail do fornecedor']) }}
        </div>
        <div class="pull-left m-t-7 m-l-15 m-r-10">
            <b class="fw-500">Anexar ao e-mail:</b>
        </div>
        <ul class="list-inline pull-left m-t-5 m-b-0">
            <li>
                <div class="checkbox m-b-0 m-t-5">
                    <label>
                        {{ Form::checkbox('attachments[]', 'payment', true, ['disabled']) }}
                        Nota Pagamento
                    </label>
                </div>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        @if(!$hideSubmit)
        <button type="submit"
            class="btn btn-success"
            data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">
            Criar pagamento
        </button>
        @endif
    </div>
</div>
{{ Form::hidden('total') }}
{{ Form::close() }}

<script>

    $('.modal .select2').select2(Init.select2());
    $('.modal .datepicker').datepicker(Init.datepicker());

    $(document).on('change', '[name="discount"],[name="discount_unity"]',function(){
        $('.invoice-price:first-child').trigger('change')
    })

    /**
     * Enable attachments options
     */
    $(document).on('change', '[name="send_email"]',function(){

        if($(this).is(':checked')) {
            $('[name="attachments[]"').prop('disabled', false)
        } else {
            $('[name="attachments[]"').prop('disabled', true)
        }

    })


    $('.payment-method').on('change', function () {
        var method = $(this).val();

        $('.modal .bank-col').show();
        if(method == 'money' || method == 'settlement') {
            $('.modal .bank-col').hide();
        }
    })


    $('.modal .btn-add-payment').on('click', function (e) {
        e.preventDefault()
        $(this).prev().find('tr:visible').last().next().show();
    })

    $('.modal [name="prefill_val"]').keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            $('.modal [name="prefill_val"]').trigger('change');
            return false;
        }
    });

    $('.modal [name="prefill_val"]').on('change', function (e) {
        e.preventDefault();
        var total     = parseFloat($(this).val());
        var remainingTotal = total;

        $('.modal .invoice-price').val('').trigger('change');
        $('.modal [name="prefill"]').prop('checked', false);
        $(this).val('')

        $('.modal .invoice-price').each(function () {

            invoicePrice = parseFloat($(this).data('max'));

            if(invoicePrice >= 0.00 && remainingTotal >= 0.00) { //só pre-preenche faturas

                if (remainingTotal >= invoicePrice) { //possivel liquidar tudo
                    $(this).closest('tr').find('[name="prefill"]').prop('checked', true);
                    $(this).val(invoicePrice.toFixed(2)).trigger('change');
                    remainingTotal-= invoicePrice; //desconta o valor
                } else { //só dá para liquidar parte

                    $(this).val(remainingTotal.toFixed(2)).trigger('change');
                    remainingTotal -= invoicePrice;
                }
            }

            updateTotals();
        })
    });

    $('.modal [name="prefill_all"]').on('change', function () {
        if($(this).is(':checked')) {
            $('.modal [name="prefill"]').each(function () {
                $(this).prop('checked', true)
            })
        } else {
            $('.modal [name="prefill"]').each(function () {
                $(this).prop('checked', false)
            })
        }
    });

    $('.modal [name="prefill"]').on('change', function () {

        var $tr    = $(this).closest('tr')
        var $input = $tr.find('[data-max]');
        var max    = parseFloat($input.data('max'));

        if($(this).is(':checked')) {
            $input.val(max);
        } else {
            $input.val('');
        }

        $input.trigger('change')
    })

    $('.modal [data-max]').on('change', function () {

        var max = parseFloat($(this).data('max'));
        var val = parseFloat($(this).val());

        if(isNaN(val)) {
            $(this).val('')
        } else {
            if (max <= 0.00 && val > 0.00) {
                $(this).val((val * -1).toFixed(2))
            } else {
                $(this).val(val.toFixed(2))
            }
        }

        var total = 0;
        $('.modal [data-max]').each(function(){
            var max   = parseFloat($(this).data('max'));
            var value = parseFloat($(this).val());

            value = isNaN(value) ? 0 : value;

            if(value == 0.00) {
                $(this).css('border-color', '#ccc').css('color', '#555');
            } else if(value > 0.00) {
                if(value > max) {
                    $(this).css('border-color', 'red').css('color', 'red');
                } else {
                    $(this).css('border-color', '#ccc').css('color', '#555');
                }
            } else { //notas crédito
                if(value < max) {
                    $(this).css('border-color', 'red').css('color', 'red');
                } else {
                    $(this).css('border-color', '#ccc').css('color', '#555');
                }
            }

            total+= value;
        })


        docSubtotal = total;
        docDiscount = parseFloat($('[name="discount"]').val() ? $('[name="discount"]').val() : 0);
        if($('[name="discount_unity"]').val() == '%') {
            docDiscount = round(docSubtotal * (docDiscount / 100));
        }

        docTotal = round(docSubtotal - docDiscount);

        total = total.toFixed(2);

        $('.modal [name="total"]').val(total);
        $('.payment-total span').html(total);

        $('.payment-value').val('');
        $('.payment-value').eq(0).val(total)

        $('.payment-row').hide();
        $('.payment-row').eq(0).show();

        $('.doc-subtotal').html(docSubtotal.toFixed(2));
        $('.doc-discount').html(docDiscount.toFixed(2));
        $('.doc-total').html(docTotal.toFixed(2));
    })


    $(".modal select[name=provider_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.invoices.purchase.search.providers.select2') }}")
    });

    $('.modal select[name=provider_id]').on('change', function(){
        var provider = $(this).val();

        var html = '<div class="modal-body">'
            +'<h4 class="modal-title text-center m-t-40 m-b-40 text-muted">'
            +'<i class="fas fa-circle-notch fa-spin"></i> A carregar...'
            +'</h4>'
            +'</div>';

        $('#modal-remote-lg .modal-content').html(html);
        $.get('{{ route('admin.invoices.purchase.payment-notes.create') }}?provider=' + provider, function(data){
            $('#modal-remote-lg .modal-content').html(data);
        })
    })

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

                    if (data.printPdf) {
                        if (!window.open(data.printPdf, '_blank')) {
                            $('#modal-remote-lg').find('.modal-lg').removeClass('modal-lg').find('.modal-content').html(data.html);
                        } else {
                            $('#modal-remote-lg').modal('hide');
                        }
                    } else {
                        $('#modal-remote-lg').modal('hide');
                    }

                    try {
                        $('.selected-rows-action').addClass('hide')
                        oTable.draw();
                        oTablePaymentNotes.draw();
                    } catch (e) {}
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