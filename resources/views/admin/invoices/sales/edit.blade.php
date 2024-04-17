<?php
$currencySymbol = @$invoice->currency ? @$invoice->currency : Setting::get('app_currency');
$requiredVat    = in_array(Setting::get('app_country'), ['pt', 'ptmd', 'ptac', 'ao']);
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
    @include('admin.invoices.sales.partials.schedule_block')
    <div class="row">
        <div class="col-sm-7">
            <div style="min-height: 229px">
                <h4 class="text-blue m-t-0 m-b-15">Produtos e Serviços</h4>
                <div class="items-list">
                    <table class="table table-billing-items table-condensed table-hover m-t-10 m-b-5">
                        <thead>
                            <tr class="bg-gray">
                                <th>Artigo</th>
                                <th class="w-40px text-center">Qtd</th>
                                <th class="w-70px text-center">Valor</th>
                                <th class="w-70px text-center">Desconto</th>
                                <th class="w-75px text-center">Subtotal</th>
                                <th class="w-70px text-center">Taxa IVA</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $rowsVisible = count(json_decode(json_encode($billing->lines), true));
                        if($rowsVisible)
                             $rowsVisible = $rowsVisible + 2;
                        $i = 0;
                        $documentSubtotal = 0;
                        $documentTotal = 0;
                        $documentTotalVat = 0;
                        ?>
                        @if($billing->lines)
                            @foreach($billing->lines as $key => $line)
                                <?php
                                $billingProductId = @$line->billing_product_id;
                                $billingProduct   = @$line->billingProduct;

                                $key        = @$line->key;
                                $ref        = @$line->reference;
                                $desc       = @$line->description;
                                $lineObs    = @$line->obs;
                                $qty        = @$line->qty;
                                $price      = @$line->total_price;
                                $subtotal   = @$line->subtotal;
                                $discount   = @$line->discount;
                                $exemption  = @$line->exemption_reason ? $line->exemption_reason : (@$line->tax_rate ? @$line->tax_rate : Setting::get('tax_rate_normal'));
                                ?>
                                @if(!@$line->hidden)
                                @include('admin.invoices.sales.partials.table_line')
                                @endif
                            @endforeach
                        @endif

                        @for($i = $rowsVisible ; $i <= $rowsVisible+50 ; $i++)
                            <?php
                            $key = 'item_' . $i;
                            $qty = 1;
                            $ref = $desc = $price = $subtotal = $discount = $exemption = $lineObs = '';
                            ?>
                            @include('admin.invoices.sales.partials.table_line')
                        @endfor
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-xs btn-default btn-add-product-row">
                    <i class="fas fa-plus"></i> Adicionar outro Produto ou Serviço
                </button>

                <button type="button"
                    class="btn btn-xs btn-default m-r-1"
                    data-toggle="modal"
                    data-target="#modal-remote"
                    href="{{ route('admin.billing.items.create') }}">
                    <i class="fas fa-plus"></i> Novo Artigo
                </button>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="text-blue m-t-25">Totais do Documento</h4>
                    <div class="panel-billing-totals">
                        <div class="row row-0">
                            <div class="col-sm-6">
                                <table class="table table-condensed table-billing-totals m-0">
                                    {{--<tr class="discount-row">
                                        <td>
                                            <p>Taxa de Combustível</p>
                                        </td>
                                        <td class="w-120px">
                                            <div class="input-group">
                                                {{ Form::text('fuel_tax', number($billing->fuel_tax), ['class' => 'form-control  input-sm input-sm nosapce decimal', 'required']) }}
                                                <div class="input-group-addon">%</div>
                                            </div>
                                        </td>
                                    </tr>--}}
                                    <tr class="discount-row">
                                        <td>
                                            <p>Desconto Geral</p>
                                        </td>
                                        <td class="w-120px">
                                            <div class="input-group">
                                                {{ Form::text('total_discount', $billing->total_discount ? number($billing->total_discount) : number($customer->billing_discount_value ?? 0), ['class' => 'form-control input-sm nosapce decimal', 'required']) }}
                                                <div class="input-group-addon">%</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="discount-row">
                                        <td>
                                            <p>Retenção na Fonte</p>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {{ Form::text('irs_tax', number($billing->irs_tax), ['class' => 'form-control input-sm nosapce decimal', 'required']) }}
                                                <div class="input-group-addon">%</div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-sm-6">
                                <table class="table table-condensed table-billing-totals m-0">
                                    <tr class="total-row">
                                        <td>
                                            <p>Total Líquido</p>
                                        </td>
                                        <td class="w-130px">
                                            <div class="input-group">
                                                {{ Form::text('document_subtotal', number($billing->document_subtotal), ['class' => 'form-control input-sm', 'required', 'readonly']) }}
                                                {{ Form::hidden('total_month', number($billing->total_month + $billing->fuel_tax_total), ['class' => 'form-control input-sm', 'required', 'readonly']) }}
                                                {{ Form::hidden('total_month_vat', number($billing->total_month_vat + $billing->fuel_tax_total_vat), ['class' => 'form-control input-sm', 'required', 'readonly']) }}
                                                {{ Form::hidden('total_month_no_vat', number($billing->total_month_no_vat + $billing->fuel_tax_total_no_vat), ['class' => 'form-control input-sm', 'required', 'readonly']) }}
                                                <div class="input-group-addon">{{ $currencySymbol }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="total-row">
                                        <td>
                                            <p>Total de IVA</p>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {{ Form::text('document_vat', number($billing->document_vat), ['class' => 'form-control input-sm', 'required', 'readonly']) }}
                                                <div class="input-group-addon">{{ $currencySymbol }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="total-row">
                                        <td>
                                            <p>TOTAL DOCUMENTO</p>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {{ Form::text('document_total', number($billing->document_total), ['class' => 'form-control input-sm', 'required', 'readonly']) }}
                                                <div class="input-group-addon">{{ $currencySymbol }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{ Form::hidden('total_month_saved', number($billing->total_month)) }}
                    {{ Form::hidden('total_month_no_vat_saved', number($billing->total_month_no_vat)) }}
                </div>
            </div>
        </div>

        <div class="col-sm-5">
            <h4 class="text-blue m-t-0 m-b-15">Emissão de Documento de Venda</h4>
            @if(!hasModule('invoices'))
                <div class="row row-5">
                    <div class="col-sm-5">
                        <div class="form-group">
                            {{ Form::label('doc_type', 'Tipo Documento') }}
                            {{ Form::select('doc_type', ['' => '', 'nodoc' => 'Sem Documento Venda'], $billing->invoice_type, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                </div>
                <hr style="margin-top: 5px"/>
                <div class="row">
                    <div class="col-sm-12">
                        <p class="text-info bold">
                            <i class="fas fa-info-circle"></i> A sua plataforma não possui licença ativa para utilização do módulo de ligação com Software de Faturação Online.
                        </p>
                        <br/>
                        <img src="https://www.keyinvoice.com/images/logo.png">
                        <p>Facturação 100% online e com um clique.</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check"></i> Emissão de faturas automáticamente;</li>
                            <li><i class="fas fa-check"></i> Criação e Atualização de clientes no software de faturação a partir da plataforma;</li>
                            <li><i class="fas fa-check"></i> Disponibilização da conta corrente na área de cliente;</li>
                            <li><i class="fas fa-check"></i> Envio de faturas diretamente via e-mail para o cliente;</li>
                        </ul>
                        <br/>
                        <a href="mailto:geral@enovo.pt" class="btn btn-sm btn-default">Contacte-nos para saber mais.</a>
                        <div class="sp-30"></div>
                    </div>
                </div>
            @endif
            <div class="row row-5 {{ !hasModule('invoices') ? 'hide' : '' }}">
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('doc_type', 'Tipo Documento') }}
                        {{ Form::select('doc_type', ['' => ''] + trans('admin/billing.types-list-selectbox'), $billing->invoice_type, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-4 invoice_data" style="{{ $billing->invoice_type == 'nodoc' ? 'display:none' : '' }}">
                    <div class="form-group">
                        {{ Form::label('api_key', 'Série a Usar') }}
                        @if(count($apiKeys) > 1)
                        {{ Form::select('api_key', ['' => ''] + $apiKeys, $billing->api_key, ['class' => 'form-control select2', 'required']) }}
                        @else
                        {{ Form::select('api_key', $apiKeys, $billing->api_key, ['class' => 'form-control select2']) }}
                        @endif
                    </div>
                </div>
                <div class="col-sm-4" style="{{ $billing->invoice_type == 'nodoc' ? 'display:none' : '' }}">
                    <div class="form-group">
                        {{ Form::label('docref', 'Referência') }}
                        @if(@$schedule)
                            {!! tip('Dicas<br/>:month - Substituir pelo mês<br/>:year - Substituir pelo ano') !!}
                        @endif
                        {{ Form::text('docref', $billing->reference, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-12 doc-info" style="display: none">
                    <small class="text-orange" style="margin-top: -14px; margin-bottom: -5px; display: block"><i class="fas fa-info-circle"></i> Tipo documento sem validade fiscal.</small>
                </div>
            </div>

            <div class="{{ !hasModule('invoices') ? 'hide' : '' }}" style="{{ $billing->invoice_doc_id || !$billingMonth ? '' : 'display: block' }}">
                <hr class="m-t-3 m-b-15"/>
                <div class="row row-5">
                    <div class="col-sm-12">
                        <div class="form-group">
                            @if($billing->invoice_type != 'nodoc')
                            <a href="javascript:" class="pull-right invoice_data add-billing-address">Alterar designação social/morada</a>
                            @endif
                            {{ Form::label('customer_id', 'Cliente') }}
                            <div class="input-group p-r-2">
                                {{ Form::select('customer_id', $customer->id ? [$customer->id => @$customer->code.' - '. str_limit(@$customer->billing_name)] : [], null, ['class' => 'form-control search-customer', 'required']) }}
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-default" data-target="#modal-create-customer">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-5" id="add-billing-address" style="display: none">
                    <div class="col-sm-12">
                        <div class="form-group">
                            {{ Form::label('billing_name', 'Designação Social') }}
                            {{ Form::text('billing_name', @$customer->billing_name, ['class' => 'form-control uppercase', 'id' => 'billing_name']) }}
                            {{ Form::hidden('billing_code', @$customer->code, ['class' => 'form-control uppercase nospace', 'maxlength' => 6]) }}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            {{ Form::label('billing_address', 'Morada') }}
                            {{ Form::text('billing_address', @$customer->billing_address, ['class' => 'form-control uppercase', 'id' => 'billing_address']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('billing_zip_code', 'C.Postal') }}
                            {{ Form::text('billing_zip_code', @$customer->billing_zip_code, ['class' => 'form-control uppercase', 'id' => 'billing_zip_code']) }}
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group">
                            {{ Form::label('billing_city', 'Localidade') }}
                            {{ Form::text('billing_city', @$customer->billing_city, ['class' => 'form-control uppercase', 'id' => 'billing_city']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('agency_id', 'Agência') }}
                            {{ Form::select('agency_id', ['' => ''] + $agencies, @$customer->agency_id, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                </div>
                <div class="row row-5 invoice_data">
                    <div class="col-sm-3">
                        <div class="form-group is-required" style="position: relative">
                            <label for="vat">País</label>
                            <div class="vat-readonly" style="position: absolute;background: rgb(219 219 219 / 40%);top: 19px;bottom: 0;right: 0;left: 0; z-index: 10;"></div>
                            {{ Form::select('billing_country', trans('country'), @$customer->billing_country, ['class' => 'form-control select2', 'id' => 'billing_country']) }}
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label for="vat">
                                NIF {!! tip('Se o cliente não existir no software de faturação, será criado com os dados registados na ficha de cliente. Pode personalizar aqui o nome Fiscal que será criado no programa de faturação. Para emitir como consumidor final deixe o campo vazio.') !!}
                            </label>
                            <div class="input-group" style="position: relative">
                                <div class="vat-readonly" style="position: absolute;background: rgb(219 219 219 / 40%);top: 0;bottom: 0;right: 67px;left: 0;z-index: 10;"></div>
                                {{ Form::text('vat', @$customer->vat, ['class' => 'form-control nospace vat', 'autocomplete' => 'off', 'data-country' => 'billing_country']) }}
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default btn-validate-nif"
                                            data-toggle="modal"
                                            data-target="#modal-vat-validation"
                                            data-href="{{ coreUrl('helper/vat/info') }}"
                                            data-vv-country="#billing_country"
                                            data-vv-name="#billing_name"
                                            data-vv-address="#billing_address"
                                            data-vv-zip-code="#billing_zip_code"
                                            data-vv-city="#billing_city"
                                            data-vv-phone="#phone"
                                            data-vv-mobile="#mobile">
                                        Validar
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="sp-27"></div>
                        <div class="checkbox m-0">
                            <label style="padding-left: 0" data-toggle="tooltip" title="Ao selecionar esta opção vai ser ignorado qualquer artigo isento de IVA">
                                {{ Form::checkbox('final_consumer', 1, $customer->is_particular) }}
                                Cliente Particular <i class="fas fa-info-circle"></i>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row row-5">
                    <div class="col-sm-4">
                        <div class="form-group is-required {{ @$schedule ? 'schedule-start-date' : '' }}">
                            @if(@$schedule)
                                {{ Form::label('docdate', 'Data Primeira Fatura') }}
                            @else
                                {{ Form::label('docdate', 'Data Documento') }}
                            @endif
                            <div class="input-group">
                                {{ Form::text('docdate', $docDate, ['class' => 'form-control datepicker', 'required']) }}
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 payment-condition">
                        <div class="form-group">
                            {{ Form::label('payment_condition', 'Vencimento') }}
                            {{ Form::select('payment_condition', $paymentConditions + ['custom' => 'Personalizar'], $billing->payment_condition, ['class' => 'form-control select2', 'required']) }}
                        </div>
                        <div class="duedate" style="display: none; margin-top: -16px">
                            <div class="form-group">
                                <div class="input-group">
                                    {{ Form::text('duedate', $docLimitDate, ['class' => 'form-control datepicker']) }}
                                    <div class="input-group-addon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 payment-date" style="{{ in_array($billing->doc_type, ['invoice-receipt', 'simplified-invoice']) ? '' : 'display: none' }}">
                        <div class="form-group">
                            {{ Form::label('payment_date', 'Data Pagamento') }}
                            <div class="input-group">
                                {{ Form::text('payment_date', $billing->payment_date ? null : date('Y-m-d'), ['class' => 'form-control datepicker']) }}
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 payment-method" style="{{ $billing->doc_type != 'proforma-invoice' ? : 'display:none' }}">
                        <div class="form-group">
                            {{ Form::label('payment_method', 'Recebimento') }}
                            {{ Form::select('payment_method', ['' => ''] + $paymentMethods, $billing->payment_method, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-4 doc-after-payment" style="{{ $billing->doc_type == 'proforma-invoice' ? : 'display:none' }}">
                        <div class="form-group">
                            {{ Form::label('doc_after_payment', 'Doc. após pagamento') }}
                            {{ Form::select('doc_after_payment', ['' => 'Nenhum'] + trans('admin/billing.types-list-selectbox'), $billing->doc_after_payment, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
                <div class="form-group m-b-0">
                    {{ Form::label('obs', 'Observações') }}
                    {{ Form::textarea('obs', substr($billing->obs, 0, 255), ['class' => 'form-control', 'rows' => 2, 'maxlength' => '255']) }}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    @if(@$schedule)
        <div class="extra-options">
            <ul class="list-inline pull-left m-t-5 m-b-0">
                <li>
                    <div class="checkbox m-b-0 m-t-5" style="margin-right: -15px">
                        <label>
                            {{ Form::checkbox('schedule_email', 1, $schedule->exists ? @$schedule->send_email : true) }}
                            Enviar por E-mail ao cliente
                        </label>
                    </div>
                </li>
                <li>
                    <div class="checkbox m-b-0 m-t-5" style="margin-right: -15px">
                        <label>
                            {{ Form::checkbox('schedule_draft', 1, @$invoice->exists ? false : $schedule->is_draft) }}
                            Criar como rascunho
                        </label>
                    </div>
                </li>
                <li class="proforma-options" style="{{ $billing->invoice_type == 'proforma-invoice' ? '' : 'display: none' }}">
                    <div class="checkbox m-b-0 m-t-5" style="margin-right: -15px">
                        <label>
                            {{ Form::checkbox('mb_active', 1, @$invoice->exists ? false : $schedule->mb_active) }}
                            Multibanco
                        </label>
                    </div>
                </li>
                <li class="proforma-options" style="{{ $billing->invoice_type == 'proforma-invoice' ? '' : 'display: none' }}">
                    <div class="checkbox m-b-0 m-t-5" style="margin-right: -15px">
                        <label>
                            {{ Form::checkbox('mbw_active', 1, @$invoice->exists ? false : $schedule->mbw_active) }}
                            MBWay
                        </label>
                    </div>
                </li>
                <li class="proforma-options" style="{{ $billing->invoice_type == 'proforma-invoice' ? '' : 'display: none' }}">
                    <div class="checkbox m-b-0 m-t-5" style="margin-right: -15px">
                        <label>
                            {{ Form::checkbox('paypal_active', 1, @$invoice->exists ? false : $schedule->paypal_active) }}
                            Paypal
                        </label>
                    </div>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
    @else
    <div class="extra-options">
        <div class="input-group input-group-email pull-left" style="width: 270px; margin-top: -3px">
            <div class="input-group-addon" data-toggle="tooltip" title="Ative esta opção para enviar e-mail ao cliente.">
                <i class="fas fa-envelope"></i>
                {{ Form::checkbox('send_email', 1, $customer->billing_email ? true : false) }}
            </div>
            {{ Form::text('billing_email', @$customer->billing_email, ['class' => 'form-control pull-left nospace lowercase', 'placeholder' => 'E-mail do cliente']) }}
        </div>
        <div class="pull-left m-t-5 m-l-15 m-r-10">
            <b class="fw-500">Anexar ao e-mail:</b>
        </div>
        <ul class="list-inline pull-left m-t-0 m-b-0">
            @if(empty($billing->billing_type) || in_array($billing->billing_type, ['month', 'partial']))
            <li>
                <div class="checkbox m-b-0 m-t-5" style="margin-right: -15px">
                    <label>
                        {{ Form::checkbox('attachments[]', 'summary', $customer->billing_email ? true : false) }}
                        Resumo PDF
                    </label>
                </div>
            </li>
            <li>
                <div class="checkbox m-b-0 m-t-5" style="margin-right: -15px">
                    <label>
                        {{ Form::checkbox('attachments[]', 'excel', Setting::get('billing_attach_excel')) }}
                        Resumo Excel
                    </label>
                </div>
            </li>
            @elseif(in_array($billing->billing_type, ['single']))
            <li>
                <div class="checkbox m-b-0 m-t-5" style="margin-right: -15px">
                    <label>
                        {{ Form::checkbox('attachments[]', 'shipment', $customer->billing_email ? true : false) }}
                        Comprovativo de Envio
                    </label>
                </div>
            </li>
            @endif

            @if(hasModule('invoices'))
            <li>
                <div class="checkbox m-b-0 m-t-5">
                    <label style="margin: 0; padding: 0">
                        {{ Form::checkbox('attachments[]', 'invoice', true) }}
                        <span class="attach-doc-name">Fatura</span>
                    </label>
                </div>
            </li>
            @else
                <li>
                    <div class="checkbox m-b-0 m-t-5" data-toggle="tooltip" title="Não possui ativo o módulo de faturação.">
                        <label>
                            {{ Form::checkbox('', 'invoice', false, ['disabled']) }}
                            Fatura
                        </label>
                    </div>
                </li>
            @endif

            @if(in_array(Setting::get('app_country'), ['pt', 'ptmd', 'ptac']))
                @if(hasModule('keyinvoice_mb'))
                    <li>
                        <div class="checkbox m-b-0 m-t-5">
                            <label>
                                {{ Form::checkbox('ref_mb', '1') }}
                                <img src="{{ asset('assets/img/default/mb-icon.svg') }}" style="width: 13px; margin-left: 2px"/>
                            </label>
                        </div>
                    </li>
                @else
                    <li data-toggle="tooltip" title="Não possui este módulo contratado.">
                        <div class="checkbox m-b-0 m-t-5" style="opacity: 0.6">
                            <label>
                                {{ Form::checkbox('_ref_', 0, false, ['disabled']) }}
                                <img src="{{ asset('assets/img/default/mb-icon.svg') }}" style="width: 13px; margin-left: 2px"/>
                            </label>
                        </div>
                    </li>
                @endif
            @endif
        </ul>
        <div class="clearfix"></div>
    </div>
    @endif
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    @if(hasModule('invoices'))
       @if(@$schedule)
            <button type="button"
                    class="btn btn-primary btn-store-invoice"
                    data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A Agendar...">
                Agendar
            </button>
       @else
        <span class="actions-invoice">
            <button type="button"
                    class="btn btn-default btn-store-draft"
                    data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">
                Gravar rascunho
            </button>
            <button type="button"
                    class="btn btn-primary btn-store-invoice"
                    data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A emitir...">
                Emitir Fatura
            </button>
        </span>
        <span class="actions-no-invoice" style="display: none;">
            <button type="button"
                    class="btn btn-primary btn-store-invoice"
                    data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">
                Gravar
            </button>
        </span>
        @endif
    @else
        <span class="actions-no-invoice">
        <button type="button"
                class="btn btn-primary btn-store-invoice"
                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">
            Gravar
        </button>
    </span>
    @endif
</div>
<span style="display: none">
    {{ Form::checkbox('draft', 1, $billing->is_draft) }}
    {{ Form::hidden('billing_type', @$billing->billing_type ? @$billing->billing_type : 'month') }}
    {{ Form::hidden('shipments', implode(',', $billing->shipments ? $billing->shipments : [])) }}
    {{ Form::hidden('covenants', implode(',', $billing->covenants ? $billing->covenants : [])) }}
    {{ Form::hidden('products', implode(',', $billing->products ? $billing->products : [])) }}
    {{ Form::hidden('empty_vat', !empty(@$billing->vat) && $billing->vat != '999999990' ? '0' : '1') }}
    {{ Form::hidden('submit_confirmed', '0') }}
</span>
{{ Form::close() }}

@include('admin.customers.customers.modals.create_customer')

@if($requiredVat)
<div class="modal" id="modal-confirm-empty-vat">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Emitir fatura sem NIF</h4>
            </div>
            <div class="modal-body">
                <h4 class="m-0"><div class="empty-nif m-b-10"><i class="fas fa-exclamation-triangle"></i> Não existe NIF associado.<br/></div>A fatura vai ser emitida como Consumidor Final e ficará associada à ficha de cliente de consumidor final. Pretende continuar?</h4>
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
@endif

<div class="modal" id="modal-confirm-submit">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    @if(hasModule('invoices'))
                        Confirmar emissão fatura
                    @else
                        Fechar faturação do mês
                    @endif
                </h4>
            </div>
            <div class="modal-body">
                @if(hasModule('invoices'))
                    @if(@$schedule)
                        <h4 class="m-t-0">Confirma o agendamento de fatura?</h4>
                        <p class="m-b-4">Cliente: <span class="ft-nm bold">{{ $customer->billing_name }}</span></p>
                    @else
                    <h4 class="m-t-0">Confirma a emissão do documento?</h4>
                    <p class="m-b-4">Cliente: <span class="ft-nm bold">{{ $customer->billing_name }}</span></p>
                    <p class="m-b-4">Data Documento: <span class="ft-dt bold">{{ $docDate }}</span></p>
                    <p class="m-0">Total Documento: <span class="ft-val bold">{{ number($billing->total_month + $billing->fuel_tax_total) }}{{ $currencySymbol }}</span></p>
                    @endif
                @else
                    <h4 class="m-t-0">Confirma o fecho do mês?</h4>
                @endif
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
    .schedule-start-date input,
    .schedule-start-date .input-group-addon {
        background: #ffe6b0;
        border: 1px solid #e6c77f;
    }

    .schedule-start-date input {
        border-right: 0;
    }

    .schedule-start-date .input-group-addon {
        border-left: 0;
    }

    .schedule-start-date label {
        color: #b98202;
    }

    .table-billing-items td {
        vertical-align: top !important;
    }

    .lineobs {
        position: relative;
        z-index: 8;
        margin-right: -325px;
    }

    .btn-lineobs {
        display: none;
        position: absolute;
        margin-top: 7px;
        right: 1px;
        z-index: 3;
        cursor: pointer;
        padding: 3px 3px 3px 5px;
        background: #fff;
        color: #777;
        border-radius: 3px;
    }

    tr:hover .btn-lineobs {
        display: block;
    }

    tr:hover .billing-remove-row {
        display: block;
    }

    tr .billing-remove-row {
        cursor: pointer;
        display: none;
        position: absolute;
        left: 4px;
        padding: 6px 0;
        width: 40px;
        position: absolute;
        z-index: 1;
    }

    .invoice-schedule-panel {
        background: #ffe7b0;
        border-bottom: 1px solid #e6c77f;
        padding: 15px 10px;
        margin: -16px -15px 15px -15px;
    }
</style>

<script>
    var BILLING_ALLOW_NEGATIVE_STOCK = {{ Setting::get('billing_allow_negative_stock') ? 1 : 0 }};

    $('.modal .datepicker').datepicker(Init.datepicker());
    $('.modal .select2').select2(Init.select2());
    $('.modal [data-toggle="tooltip"]').tooltip();

    $('[name="doc_type"]').on('change', function(){

        $('.modal [name="payment_method"]').closest('.form-group').show()
        $('.modal [name="duedate"]').closest('.form-group').show()
        $('.modal [name="payment_condition"]').closest('.form-group').show()
        $('.modal [name="payment_method"], .modal [name="payment_date"]').prop('required', false);
        $('[data-target="#modal-create-customer"]').prop('disabled', false);

        @if(!@$schedule)
        $('.btn-store-invoice').html('Emitir Fatura');
        @endif
        $('.attach-doc-name').html('Fatura')
        $('.extra-options, .payment-condition, .payment-method').show();
        $('.doc-info,.proforma-options, .payment-date, .doc-after-payment').hide();
        $('.modal [name="doc_after_payment"]').val('').trigger('change.select2');

        if($(this).val() == 'internal-doc' || $(this).val() == 'proforma-invoice') {
            $('.doc-info,.proforma-options').show();
        }

        if($(this).val() == 'proforma-invoice') {
            $('.doc-after-payment').show();
            $('.payment-method').hide();
            $('.modal [name="payment_method"]').val('').trigger('change.select2');
            $('.modal [name="payment_date"]').val('');
        }

        if($(this).val() == 'nodoc') {
            $('.actions-invoice').hide();
            $('.actions-no-invoice').show();
            $('.invoice_data').hide();
            $('.payment-condition').hide();
            $('[data-target="#modal-create-customer"]').prop('disabled', true);
        } else if($(this).val() == 'internal-doc') {
            $('.extra-options, .payment-condition').hide();
            $('.modal [name="send_email"]').prop('checked', false);
            $('.modal [name="payment_condition"]').val('prt');
            @if(!@$schedule)
            $('.btn-store-invoice').html('Emitir Documento');
            @endif
        } else {
            $('.actions-invoice').show();
            $('.actions-no-invoice').hide();
            $('.invoice_data').show();

            if($(this).val() == 'invoice-receipt') {
                $('.payment-condition').hide();
                $('.payment-date').show();
                $('.modal [name="payment_method"], .modal [name="payment_date"]').prop('required', true);
            }

            if($(this).val() == 'credit-note') {
                $('.modal [name="payment_method"]').closest('.form-group').hide()
                $('.modal [name="duedate"]').closest('.form-group').hide()
                $('.modal [name="payment_condition"]').closest('.form-group').hide()
                $('.btn-store-invoice').html('Emitir Nota Crédito');
                $('.attach-doc-name').html('Nota Crédito')
            }
        }
    })

    $('#modal-vat-validation .vv-accept').on('click', function(){
        var vat = $('.modal [name="vat"]').val();
        $('.modal [name="billing_name"]').trigger('change');
        $('.modal [name="vat"]').val(vat)
    });

    $('.modal .btn-lineobs').on('click', function(){
        if($(this).closest('tr').find('.lineobs textarea').val() != '') {
            Growl.info('<i class="fas fa-info-circle"></i> Não pode ocultar o campo de detalhes porque tem informação escrita.')
        } else {
            $(this).closest('tr').find('.lineobs').toggle();
        }

    })

    /**
     * Change tax rate
     */
    $(document).on('change', '.modal-xl .tax-rate', function(){
        updateTotals();
    })

    /**
     * Enable attachments options
     */
    $(document).on('change', '[name="send_email"]',function(){
        if($(this).is(':checked')) {
            $('[name="attachments[]"').prop('disabled', false);
        } else {
            $('[name="attachments[]"').prop('disabled', true);
        }
    })

    /**
     * Enable final consumer
     */
    $(document).on('change', '[name="final_consumer"]',function(){

        if($(this).is(':checked')) {
            var totalVat   =  parseFloat($('[name=total_month_vat]').val());
            var totalNoVat = parseFloat($('[name=total_month_no_vat]').val());
            $('[name=total_month_vat]').val((totalVat + totalNoVat).toFixed(2));
            $('[name=total_month_no_vat]').val('0.00');
        } else {
            var totalVat =  parseFloat($('[name=total_month_vat]').val());
            var totalNoVat = parseFloat($('[name=total_month_no_vat_saved]').val());
            $('[name=total_month_vat]').val((totalVat - totalNoVat).toFixed(2));
            $('[name=total_month_no_vat]').val(totalNoVat.toFixed(2));
        }
    })


    /**
     * Change qty, price or discount
     */
    $('.input-qty, .input-price, .input-discount').on('change', function() {
        var qty      = parseFloat($(this).closest('tr').find('.input-qty').val());
        var price    = parseFloat($(this).closest('tr').find('.input-price').val());
        var discount = parseFloat($(this).closest('tr').find('.input-discount').val());
        var subtotal = (qty * price);

        subtotal = subtotal - (subtotal * (discount/100));

        $(this).closest('tr').find('.input-subtotal').val(subtotal.toFixed(2));
        $(this).closest('tr').find('.tax-rate').trigger('change');

        updateTotals()
    });

    $(document).on('change', '.input-subtotal, [name="total_discount"], [name="irs_tax"], [name=fuel_tax]', function() {
        var value = $(this).val();

        if(value == '') {
            $(this).val('0.00');
        } else {
            $(this).val(parseFloat(value).toFixed(2));
        }

        updateTotals();
    })

    $(document).ready(function () {
        // Trigger ao desconto total ao carregar a página (super importante quando a ficha de cliente tem uma taxa de desconto pré definida)
        $('input[name="total_discount"]').trigger('change');
    });

    $('.add-billing-address').on('click', function () {
        $('#add-billing-address').toggle();
    })

    $('[name="doc_type"]').on('change', function(){
        if($(this).val() == '') {
            $('#invoice-data').hide();
        } else {
            $('#invoice-data').show();
        }
    })

    function updateTotals() {

        var totalMonthNoVat = totalMonth = documentTotal = documentVat = fuelTaxVat = 0;
        var totalDiscount   = parseFloat($('[name="total_discount"]').val());
        var totalIRS        = parseFloat($('[name="irs_tax"]').val());
        var docSubtotal     = parseFloat($('[name="document_subtotal"]').val());
        var docVat          = parseFloat($('[name="document_vat"]').val());

        $('.input-subtotal').each(function() {
            var $tr      = $(this).closest('tr');
            var subtotal = parseFloat($(this).val().replace(',', '.'));
            var taxRate  = $tr.find('.tax-rate').val()

            // console.log(taxRate);
            //var discount = parseFloat($tr.find('.input-discount').val());

            totalMonth+= subtotal;
            //totalDiscount+= discount; //comentado 26/08

            if (taxRate != null) {
                if (taxRate.indexOf('M') > -1) {
                    totalMonthNoVat+= subtotal;
                } else {
                    taxRate = parseFloat(taxRate);
                    documentVat+= subtotal * (taxRate/100)
                }
            }
        })

        var totalMonthVat = totalMonth - totalMonthNoVat;

        $('[name="total_month_no_vat_saved"]').val(round(totalMonthNoVat).toFixed(2))
        $('[name="total_month_saved"]').val(round(totalMonthVat + totalMonthNoVat).toFixed(2))
        //$('[name="total_discount"]').val(totalDiscount.toFixed(2))

        //APLICA DESCONTO GLOBAL
        docSubtotal   = totalMonth - (totalMonth * (totalDiscount / 100));
        documentVat   = documentVat - (documentVat * (totalDiscount / 100));
        totalMonthVat = totalMonthVat - (totalMonthVat * (totalDiscount / 100));

        //sub discount
        $('[name="total_month_vat"]').val(round(totalMonthVat).toFixed(2))
        $('[name="total_month_no_vat"]').val(round(totalMonthNoVat).toFixed(2))
        $('[name="total_month"]').val(round(totalMonth).toFixed(2))

        //APLICA IRS
        var documentTotal = ((docSubtotal - (docSubtotal * (totalIRS / 100))) + documentVat);

        //console.log('TTL='+documentTotal+ '-SUBTOTAL=' +docSubtotal + '-IRS='+totalIRS);
        $('[name="document_vat"]').val(round(documentVat).toFixed(2))
        $('[name="document_total"]').val(round(documentTotal).toFixed(2))
        $('[name="document_subtotal"]').val(round(docSubtotal).toFixed(2))


        $('.ft-val').html((round(totalMonth).toFixed(2)) + '{{ $currencySymbol }}')
    }

    $('.modal [name="vat"]').on('change', function(){
        if($(this).val() == '' || $(this).val() == '999999990') {
            $('[name="empty_vat"]').val(1);
        } else {
            $('[name="empty_vat"]').val(0);
            var vat = $('.form-billing [name="vat"]').val();

            //verifica entidade associada ao NIF
            $('[for="vat"]').append('<i class="fas fa-spin fa-circle-notch vat-loading"></i>')
            $.post('{{ route('admin.invoices.search.customers.vat') }}', {vat:vat}, function(data){
                if(data.exists) {
                    $('.form-billing [name="vat"]').closest('.form-group').removeClass('has-error')
                    $('.form-billing [name="billing_code"]').val(data.code);
                    $('.form-billing [name="billing_name"]').val(data.name);
                    $('.form-billing [name="billing_address"]').val(data.address);
                    $('.form-billing [name="billing_city"]').val(data.city);
                    $('.form-billing [name="billing_zip_code"]').val(data.zip_code);
                    $('.form-billing [name="billing_country"]').val(data.country).trigger('change.select2');
                    $('.form-billing [name="agency_id"]').val(data.agency_id).trigger('change.select2');
                    $('.form-billing [name="billing_email"]').val(data.email);
                    $('.form-billing [name="vat"]').val(data.vat);
                    if(data.is_particular == "1") {
                        $('.form-billing [name="final_consumer"]').prop('checked', true);
                    } else {
                        $('.form-billing [name="final_consumer"]').prop('checked', false);
                    }
                    $('.form-billing [name="total_discount"]').val(data.billing_discount_value).trigger('change');

                    Growl.success('O NIF é válido.')
                }
            }).always(function(){
                $('.vat-loading').remove();
            })
        }
    });

    $('#modal-confirm-empty-vat [data-answer]').on('click', function(){
        if($(this).data('answer') == '1') {
            $('[name="empty_vat"]').val(0);
            $('.form-billing').submit();
        }
        $(this).closest('.modal').removeClass('in').hide();
    });

    $('#modal-confirm-submit [data-answer]').on('click', function(){
        if($(this).data('answer') == '1') {
            $('[name="submit_confirmed"]').val('1')
            $('.form-billing').submit();
        }
        $(this).closest('.modal').removeClass('in').hide();
    });

    /**
     * SEARCH PRODUCT
     * ajax method
     */
    function searchProductConfig() {
        var customerId = "?customer_id=" + $('.modal-xl [name="customer_id"]').val();

        return {
            serviceUrl: "{{ route('admin.invoices.sales.search.item') }}" + customerId,
            onSearchStart: function () {},
            beforeRender: function (container, suggestions) {
                container.find('.autocomplete-suggestion').each(function(key, suggestion, data){
                    if (suggestions[key].create) {
                        $(this).prepend('<span class="text-green"><i class="fas fa-plus fa-fw"></i></span>');
                        return;
                    }

                    $(this).prepend('<span class="autocomplete-address">' + suggestions[key].reference + ' - </span>');
                    if (suggestions[key].has_stock) {
                        $(this).append(' | ' + suggestions[key].stock_total_html);
                    }
                });
            },
            onSelect: function (suggestion) {
                console.log(suggestion.tax_rate);
                var $this = $(this);

                $this.find('.input-qty').removeData('stock-total');
                if (!BILLING_ALLOW_NEGATIVE_STOCK && suggestion.has_stock) {
                    if (suggestion.stock_total <= 0.00) {
                        $this.closest('tr').find('.search-product').val('');
                        Growl.error('O stock desse artigo não permite a sua venda.');
                        return;
                    }

                    var $inputQty = $this.closest('tr').find('.input-qty');
                    $inputQty.data('stock-total', suggestion.stock_total);
                }

                $this.closest('tr').find('.label-reference').html(suggestion.reference)
                $this.closest('tr').find('.input-reference').val(suggestion.reference)
                $this.closest('tr').find('.input-id').val(suggestion.data)
                $this.closest('tr').find('.tax-rate').val(suggestion.tax_rate).trigger('change')
                $this.val(suggestion.name)
                $this.closest('tr').find('input, .input-group-addon').css('color', '#555')

                if(suggestion.price != '0.00') {
                    $this.closest('tr').find('.input-price').val(suggestion.price);
                    $this.closest('tr').find('.input-price').trigger('change');
                }
                $this.closest('tr').find('.tax-rate').val(suggestion.tax_rate).change();
            },
        };
    }
    $('.search-product').autocomplete(searchProductConfig());

    $(document).on('change', '.search-product', function(){
        var $tr = $(this).closest('tr');

        if($(this).val() == '') {
            $tr.find('.label-reference').html('')
            $tr.find('.input-reference').val('')
            $tr.find('.input-id').val('')
        } else if($tr.find('.input-reference').val() == '') {
            //$tr.find('input, .input-group-addon').css('color', 'red') //colca linhas de faturação a vermelho se não tiverem referencia
        } else {
            $tr.find('input, .input-group-addon').css('color', '#555')
        }
    })

    // Quantity changes
    $('.input-qty').on('change', function () {
        if (BILLING_ALLOW_NEGATIVE_STOCK) {
            return;
        }

        var $this      = $(this);
        var value      = parseFloat($this.val());
        var stockTotal = parseFloat($this.data('stock-total'));

        if (!stockTotal) {
            return;
        }

        if (stockTotal < $this.val()) {
            Growl.error('O stock máximo desse artigo é de ' + stockTotal + ' unidades.');
            $this.val(stockTotal);
        }
    });
    //--

    $('.btn-add-product-row').on('click', function() {
        $(this).prev().show();
        $(this).prev().find('tbody tr:hidden:first').show();
        updateTotals();
    })

    //remove row
    $('.billing-remove-row').on('click', function($q){
        var $table = $(this).closest('table');
        var $tr = $(this).closest('tr');
        $tr.find('.input-id').val('');
        $tr.find('.label-reference').html('');
        $tr.find('.input-reference, .search-product').val('');
        $tr.find('.input-qty').removeData('stock-total');
        $tr.find('.input-price').val(0).trigger('change');
        if($table.find('tbody tr:visible').length >= 2) {
            $tr.css('display', 'none')
        }

        $tr.appendTo($table);
    })

    $('[name="doc_type"]').on('change', function(){
        if($(this).val() == '') {
            $('.billed').show();
        } else {
            $('.billed').hide();
        }

        $('.modal [name="docdate"]').trigger('change')
    })

    /**
     * SEARCH CUSTOMER
     * ajax method
     */
    $(".modal select[name=customer_id]").select2({
        ajax: {
            url: "{{ route('admin.invoices.sales.search.customer') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                $('select[name=customer_id] option').remove()

                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    //SELECT SEARCH RESULT
    $('.modal [name=customer_id]').on('select2:select', function (e) {
        var suggestion = e.params.data;
        var $this      = $(this);

        //$('.form-billing [name="customer_id"]').val(suggestion.data);
        $('.form-billing [name="billing_code"]').val(suggestion.code);
        $('.form-billing [name="billing_name"]').val(suggestion.name).trigger('change');
        $('.form-billing [name="billing_address"]').val(suggestion.address);
        $('.form-billing [name="billing_zip_code"]').val(suggestion.zip_code);
        $('.form-billing [name="billing_city"]').val(suggestion.city);
        $('.form-billing [name="billing_country"]').val(suggestion.country).trigger('change.select2');
        $('.form-billing [name="payment_condition"]').val(suggestion.payment_condition).trigger('change');
        $('.form-billing [name="vat"]').val(suggestion.vat);
        $('.form-billing [name="billing_email"]').val(suggestion.email);
        $('.form-billing [name="agency_id"]').val(suggestion.agency_id).trigger("change.select2");
        $('.form-billing [name="empty_vat"]').val(0);
        $('.form-billing [name="total_discount"]').val(suggestion.billing_discount_value).trigger('change');
        $('.form-billing .ft-nm').html(suggestion.name);

        if(suggestion.reference) {
            $('.form-billing [name="docref"]').val(suggestion.reference);
        }

        $('.search-product').autocomplete(searchProductConfig());
    })


    /*==============================================*/
    /*=============== CREATE CUSTOMER ==============*/
    /*==============================================*/
    $('[data-target="#modal-create-customer"]').on('click', function () { //show
        $('#modal-create-customer').addClass('in').show();
    });

    $('#modal-create-customer .cancel-create-customer').on('click', function () { //hide
        resetModalCreateCustomer();
        $('#modal-create-customer').removeClass('in').hide();
    });

    $('#modal-create-customer .confirm-create-customer').on('click', function () {

        var $form    = $(this).closest('form');
        var formData = $form.serialize();
        var $btn     = $(this);

        countEmptyFields = $("#modal-create-customer [required]").filter(function(){
            return !$(this).val();
        }).length;


        if(countEmptyFields) {
            Growl.error('Preencha todos os campos obrigatórios.');
        } else {
            $btn.button('loading');

            $.post($form.attr('action'), formData, function (data) {
                if (data.result) {

                    suggestion = data.customer;
                    $('.form-billing [name="billing_code"]').val(suggestion.code);
                    $('.form-billing [name="billing_name"]').val(suggestion.name).trigger('change');
                    $('.form-billing [name="billing_address"]').val(suggestion.address);
                    $('.form-billing [name="billing_zip_code"]').val(suggestion.zip_code);
                    $('.form-billing [name="billing_city"]').val(suggestion.city);
                    $('.form-billing [name="billing_country"]').val(suggestion.country).trigger('change.select2');
                    $('.form-billing [name="vat"]').val(suggestion.vat);
                    $('.form-billing [name="billing_email"]').val(suggestion.billing_email ? suggestion.billing_email : suggestion.email);
                    $('.form-billing [name="agency_id"]').val(suggestion.agency_id).trigger("change.select2");
                    $('.form-billing [name="empty_vat"]').val(0);
                    $('.form-billing [name="total_discount"]').val(suggestion.billing_discount_value).trigger('change');

                    if(suggestion.payment_condition) {
                        $('.form-billing [name="payment_condition"]').val(suggestion.payment_condition).trigger('change');
                    } else {
                        $('.form-billing [name="payment_condition"]').trigger('change');
                    }

                    if(suggestion.reference) {
                        $('.form-billing [name="docref"]').val(suggestion.reference);
                    }

                    $('.form-billing [name="customer_id"]').val(data.customer.id);
                    $('.form-billing [name="customer_id"]').html('<option value="'+data.customer.id+'">'+data.customer.code+' - '+data.customer.name+'</option>');

                    resetModalCreateCustomer();
                    $('#modal-create-customer').removeClass('in').hide();
                } else {
                    Growl.error(data.feedback)
                }

            }).fail(function () {
                Growl.error500();
            }).always(function () {
                $btn.button('reset');
            })
        }
    });

    function resetModalCreateCustomer() {
        $('#modal-create-customer input').val('');
        $('#modal-create-customer select').val('').trigger('change.select2')
        $('#modal-create-customer select[name="payment_method"]').val('30d').trigger('change.select2')
        $('#modal-create-customer select[name="country"]').val('pt').trigger('change.select2')
        $('#modal-create-customer select[name="billing_country"]').val('pt').trigger('change.select2')
        $('#modal-create-customer select[name="default_invoice_type"]').val('invoice').trigger('change.select2')
    }

    //old method with autocomplete
    /* var users;
    users = $('.search-customer').autocomplete({
        serviceUrl: '{{ route('admin.invoices.search.customers') }}',
        minChars: 2,
        onSearchStart: function () {
            $('.form-billing [name="customer_id"]').val('');
            $('.form-billing [name="empty_vat"]').val(1);
        },
        beforeRender: function (container, suggestions) {
            container.find('.autocomplete-suggestion').each(function(key, suggestion, data){
                $(this).append('<div class="autocomplete-address">' + suggestions[key].address + ' - ' + suggestions[key].zip_code + ' ' +  suggestions[key].city + '</div>')
            });
        },
        onSelect: function (suggestion) {
            $('.form-billing [name="billing_code"]').val(suggestion.code);
            $('.form-billing [name="customer_id"]').val(suggestion.data);
            $('.form-billing [name="billing_name"]').val(suggestion.name).trigger('change');
            $('.form-billing [name="billing_address"]').val(suggestion.address);
            $('.form-billing [name="billing_zip_code"]').val(suggestion.zip_code);
            $('.form-billing [name="billing_city"]').val(suggestion.city);
            $('.form-billing [name="billing_country"]').val(suggestion.country).trigger('change.select2');
            $('.form-billing [name="payment_condition"]').val(suggestion.payment_condition).trigger('change');
            $('.form-billing [name="vat"]').val(suggestion.vat);
            $('.form-billing [name="billing_email"]').val(suggestion.email);
            $('.form-billing [name="agency_id"]').val(suggestion.agency_id).trigger("change.select2");
            $('.form-billing [name="empty_vat"]').val(0);
            $('.form-billing [name="total_discount"]').val(suggestion.billing_discount_value).trigger('change');

            if(suggestion.reference) {
                $('.form-billing [name="docref"]').val(suggestion.reference);
            }
        },
    }); 

    $('.search-customer').on('change', function(){
        if($('.form-billing [name="customer_id"]').val() == '') {
            $('.form-billing [name="billing_code"]').val('{{ @$newCustomerCode }}');
            $('.form-billing [name="vat"]').val('');
            $('.form-billing [name="billing_address"]').val('');
            $('.form-billing [name="billing_zip_code"]').val('');
            $('.form-billing [name="billing_city"]').val('');
            $('.form-billing [name="billing_country"]').val('pt').trigger('change.select2');
            $('#add-billing-address').show();
            $('.vat-readonly').hide();
        } else {
            $('#add-billing-address').hide();
            $('.vat-readonly').show();
        }

        $('.ft-nm').html($(this).val());
    })

    */

    $('.btn-store-draft').on('click', function(){
        $('[name="draft"]').prop('checked', true);
        $(this).closest('form').submit();
    })

    $('.btn-store-invoice').on('click', function(){
        $('[name="draft"]').prop('checked', false);
        $(this).closest('form').submit();
    })

    $('[name="draft"]').on('change', function(){
        if($(this).is('checked')) {
            $('.btn-store-invoice').prop('disabled', true)
        } else {
            $('.btn-store-invoice').prop('disabled', false)
        }
    })

    $(".modal [name=docdate],.modal [name=payment_condition]").on('change', function(){
        var condition = ($(".modal [name=payment_condition]").val());

        if(condition == '') {
            $('.modal .duedate').show()
        } else {
            $('.modal .duedate').hide()

            if(condition == null) {
                condition = '30d';
            }

            var date      = $('.modal [name=docdate]').val();
            var duedate   = new Date(date);
            var days      = parseInt(condition.replace('d', ''));
            var docType   = $(".modal [name=doc_type]").val();


            if(isNaN(days)) {
                days = 0;
            }

            $("[name=duedate]").prop('readonly', false);
            $(".modal [name=payment_condition]").prop('disabled', false);
            if(docType == 'invoice-receipt' || docType == 'simplified-invoice') {
                $("[name=duedate]").prop('readonly', true);
                $(".modal [name=payment_condition]").val('prt').prop('disabled', true).trigger('change.select2');
                days = 0;
            } else {
                if(typeof condition === "undefined" || condition == '' || condition == 'sft' || condition == 'prt') {
                    if(condition == 'prt') {
                        days = 0;
                    } else {
                        days = 30;
                    }
                }
            }
        }

        duedate.setDate(duedate.getDate() + days);

        var dd = duedate.getDate();
        var mm = duedate.getMonth() + 1;
        var y = duedate.getFullYear();

        duedate = y + '-' + ("0" + mm).slice(-2) + '-' + ("0" + dd).slice(-2);

        $('[name=duedate]').datepicker('remove')
        $("[name=duedate]").val(duedate)

        if(date != '') {
            $('[name=duedate]').datepicker({
                format: 'yyyy-mm-dd',
                language: 'pt',
                todayHighlight: true,
                startDate: date
            });
        }

        $('.ft-dt').html(date);
    });

    $(".modal [name=payment_condition]").trigger("change");

     @if(@$schedule)
     /**
      * SCHEDULE FUNCTIONS
      */
     $('[name="schedule_frequency"]').on('change', function(){
         var frequency = $(this).val();

         if(frequency == 'day') {
             $('.schedule-repeat').hide();
             $('.schedule-weekdays').hide();
             $('.schedule-month-days').hide();
             $('.schedule-year-days').hide();
         } else if(frequency == 'week') {
             $('.schedule-repeat').hide();
             $('.schedule-weekdays').show();
             $('.schedule-month-days').hide();
             $('.schedule-year-days').hide();
         } else if(frequency == 'month') {
             $('.schedule-repeat').show();
             $('.schedule-weekdays').hide();
             $('.schedule-month-days').show();
             $('.schedule-year-days').hide();
         } else if(frequency == 'year') {
             $('.schedule-repeat').hide();
             $('.schedule-weekdays').hide();
             $('.schedule-month-days').hide();
             $('.schedule-year-days').show();
         }
     })

    $('[name="schedule_repeat"]').on('change', function(){
        var repeat = $(this).val();

        if(repeat == 'day') {
            $('.schedule-weekdays').hide();
            $('.schedule-month-days').show();
        } else {
            $('.schedule-weekdays').show();
            $('.schedule-month-days').hide().find('input').val('');
        }
    })

    $('[name="schedule_end_time"]').on('change', function(){
        var type = $(this).val();

        if(type == 'date') {
            $('[name="schedule_end_date"]').val('').prop('required', true).closest('.input-group').show();
            $('[name="schedule_end_repetitions"]').val('').prop('required', false).closest('.input-group').hide();
        } else {
            $('[name="schedule_end_date"]').val('').prop('required', false).closest('.input-group').hide();
            $('[name="schedule_end_repetitions"]').val('').prop('required', true).closest('.input-group').show();
        }
    })
     @endif
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

        var emptyProducts = true;
        $('.search-product').each(function () {
            if ($(this).val() != '') {
                emptyProducts = false;
            }
        })

        if ($('.modal [name="customer_id"]').val() == '') {
            Growl.error('O cliente é obrigatório.')
        } else if ($('.modal [name="doc_type"]').val() == '') {
            Growl.error('Não selecionou o tipo de documento a emitir.')
        } else if ($('.modal [name="api_key"]').val() == '' && $('.modal [name="doc_type"]').val() != 'nodoc') {
            Growl.error('Não selecionou a série a usar.');
        } else if ($('.modal [name="docref"]').val() == '' && $('.modal [name="doc_type"]').val() == 'credit-note') {
            Growl.error('A referência é obrigatória.');
        } else if ($('.modal [name="agency_id"]').val() == '') {
            Growl.error('Não selecionou a agência do cliente.');
            $('#add-billing-address').show();
        } else if ($('[name="billing_code"]').val() == '' && $('[name="empty_vat"]').val() == '0') {
            Growl.error('O campo "Código" do cliente deve estar preenchido.')
        } else if (emptyProducts) {
            Growl.error('Não selecionou nenhum artigo a faturar.')
        } else if ($('[name="empty_vat"]').val() == '1') {
            @if($requiredVat)
            $('.empty-nif').show();
            if ($('[name="vat"]').val() == '999999990') {
                $('.empty-nif').hide();
            }
            $('#modal-confirm-empty-vat').addClass('in').show();
            @endif
        } else if ($('[name="total_month"]').val() == '0.00' || $('[name="total_month"]').val() == '') {
            Growl.error('Não pode emitir uma fatura sem valor.')
        } else if ($('[name="submit_confirmed"]').val() == '0' && !$('[name="draft"]').is(':checked')) {
            $('#modal-confirm-submit').addClass('in').show();
        } else {

            @if(hasModule('invoices'))
            if($('[name="draft"]').is(':checked')) {
                var $btn = $('.btn-store-draft');
                $('.btn-store-invoice').prop('disabled', true)
            } else {
                var $btn = $('.btn-store-invoice');
                $('.btn-store-draft').prop('disabled', true)
            }
            @endif

            $btn.button('loading');

            $.ajax({
                url: $form.attr('action'),
                data: $form.serialize(),
                type: 'POST',
                success: function(data) {
                    if(data.result) {
                        @if(@$schedule)
                        oTableScheduled.draw(false); //update datatable
                        @else
                        oTable.draw(false); //update datatable
                        @endif
                        Growl.success(data.feedback);
                        $('#modal-remote-xl').modal('hide');
                        $('.billing-header').html(data.html_header)
                        $('.billing-sidebar').html(data.html_sidebar)

                        //update current account
                        if(data.balanceUpdate) {
                            $.post(data.balanceUpdate, function(data){});
                        }

                        if (data.printPdf) {
                            if (!window.open(data.printPdf, '_blank')) {
                                Growl.error('Não foi possivel abrir o separador para impressão. Verifique as definições de POP-UPS do browser.')
                            }
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
