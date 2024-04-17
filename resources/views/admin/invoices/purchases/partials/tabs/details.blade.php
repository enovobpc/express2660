<div class="row">
    <div class="col-sm-7">
        <div style="{{ $invoice->exists ? 'min-height: 193px' : 'min-height: 260px' }}">
            <h4 class="text-blue m-t-0 m-b-5">Produtos e Serviços <small><i>&bullet; Preenchimento opcional</i></small></h4>
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
                    $rowsVisible = count(json_decode(json_encode(@$invoice->lines), true));
                    $i = 0;
                    ?>
                    @if($invoice->lines)
                        @foreach($invoice->lines as $key => $line)
                            <?php
                            $key        = @$line->key;
                            $ref        = @$line->reference;
                            $billingProductId = @$line->billing_product_id;
                            $desc       = @$line->description;
                            $qty        = @$line->qty;
                            $price      = @$line->total_price;
                            $subtotal   = @$line->subtotal;
                            $discount   = @$line->discount;
                            $exemption  = @$line->exemption_reason ? $line->exemption_reason : $line->tax_rate;
                            ?>
                            @if(!@$line->hidden)
                                @include('admin.invoices.purchases.partials.table_line')
                            @endif
                        @endforeach
                    @endif

                    @for($i = $rowsVisible ; $i <= $rowsVisible+50 ; $i++)
                        <?php
                        $key = 'item_' . $i;
                        $qty = 1;
                        $ref = $billingProductId = $desc = $price = $subtotal = $discount = $exemption = '';
                        ?>
                        @include('admin.invoices.purchases.partials.table_line')
                    @endfor
                    </tbody>
                </table>
            </div>
            @if($canEdit)
            <button type="button" class="btn btn-xs btn-default btn-add-product-row">
                <i class="fas fa-plus"></i> Adicionar outro Produto ou Serviço
            </button>

            <button type="button"
                    class="btn btn-xs btn-primary m-r-1"
                    data-toggle="modal"
                    data-target="#modal-remote"
                    href="{{ route('admin.billing.items.create') }}">
                    <i class="fas fa-plus"></i> Novo Artigo Faturação
                </button>
            @endif
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h4 class="text-blue m-t-25">Totais do Documento</h4>
                <div class="panel-billing-totals">
                    <div class="row row-0">
                        <div class="col-sm-6">
                            <table class="table table-condensed table-billing-totals m-0">
                                <tr class="discount-row">
                                    <td>
                                        <p>Desconto Geral</p>
                                    </td>
                                    <td class="w-130px">
                                        <div class="input-group bg-white">
                                            {{ Form::text('total_discount', number($invoice->total_discount), ['class' => 'form-control input-sm nosapce decimal', 'required']) }}
                                            <div class="input-group-addon">€</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="discount-row">
                                    <td>
                                        <p>Retenção na Fonte</p>
                                    </td>
                                    <td>
                                        <div class="input-group bg-white">
                                            {{ Form::text('irs_tax', number($invoice->irs_tax), ['class' => 'form-control input-sm nosapce decimal', 'required']) }}
                                            <div class="input-group-addon">%</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="discount-row">
                                    <td>
                                        <p>Acerto/Arredondamento</p>
                                    </td>
                                    <td>
                                        <div class="input-group bg-white">
                                            {{ Form::text('rounding_value', number($invoice->rounding_value), ['class' => 'form-control input-sm nosapce decimal', 'required']) }}
                                            <div class="input-group-addon currency">{{ $currencySymbol }}</div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-6">
                            <table class="table table-condensed table-billing-totals m-0">
                                <tr class="total-row">
                                    <td>
                                        <p>Subtotal</p>
                                    </td>
                                    <td class="w-130px">
                                        <div class="input-group bg-white">
                                            {{ Form::text('subtotal', number($invoice->subtotal), ['class' => 'form-control input-sm decimal', 'required', 'style' => 'cursor: default;']) }}
                                            <div class="input-group-addon currency">{{ $currencySymbol }}</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="total-row">
                                    <td>
                                        <p>Total IVA</p>
                                    </td>
                                    <td>
                                        <div class="input-group bg-white">
                                            {{ Form::text('vat_total', number($invoice->vat_total), ['class' => 'form-control input-sm decimal', 'required', 'style' => 'cursor: default;']) }}
                                            <div class="input-group-addon currency">{{ $currencySymbol }}</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="total-row">
                                    <td>
                                        <p>TOTAL</p>
                                    </td>
                                    <td>
                                        <div class="input-group bg-white">
                                            {{ Form::text('total', number($invoice->total), ['class' => 'form-control input-sm decimal', 'required', 'style' => 'cursor: default;']) }}
                                            <div class="input-group-addon currency">{{ $currencySymbol }}</div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-5">
        <div class="invoice_data">
            <div class="sp-10"></div>
            <div class="row row-5">
                <div class="col-sm-3">
                    @if($invoice->is_scheduled)
                        <div class="form-group is-required">
                            {{ Form::label('is_scheduled', 'Periodicidade') }}
                            {{ Form::select('is_scheduled', ['monthly' => 'Mensalmente', 'biweekly' => 'Quinzenalmente', 'quarterly' => 'Trimestralmente'], $invoice->is_scheduled, ['class' => 'form-control select2']) }}
                        </div>
                    @else
                        <div class="form-group is-required">
                            {{ Form::label('docref', 'Nº Fatura') }} &nbsp; {!! tip('Indique a referência ou número da fatura do seu fornecedor.') !!}
                            {{ Form::text('docref', $invoice->reference, ['class' => 'form-control uppercase', 'required','style' => 'border: 2px solid #999;border-radius: 2px;']) }}
                        </div>
                    @endif
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('doc_type', 'Tipo Doc.') }}
                        {{ Form::select('doc_type', ['' => ''] + trans('admin/billing.types-list-purchase'), @$invoice->doc_type, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group is-required">
                        <a href="{{ route('admin.invoices.purchase.types.index') }}"
                           data-toggle="modal"
                           data-target="#modal-remote"
                           class="pull-right"><small>Gerir Tipos</small></a>
                        {{ Form::label('type_id', 'Tipo despesa') }}
                        {!! Form::selectWithData('type_id', $purchasesTypes, @$invoice->type_id, ['class' => 'form-control select2', 'required']) !!}
                    </div>
                </div>
                <div class="invoice_data" style="display: none">
                    <div class="form-group">
                        {{ Form::label('api_key', 'Série a Usar') }}
                        {{ Form::select('api_key', $apiKeys, $invoice->api_key, ['class' => 'form-control select2']) }}
                    </div>
                </div>
            </div>
            <hr class="m-t-0 m-b-15"/>
            <div class="row row-5">
                <div class="col-sm-2">
                    <div class="form-group">
                        {{ Form::label('billing_code', 'Código') }}
                        {{ Form::text('billing_code', @$invoice->provider->code, ['class' => 'form-control uppercase nospace', 'maxlength' => 5]) }}
                        {{ Form::hidden('provider_id', @$invoice->provider_id) }}
                    </div>
                </div>
                <div class="col-sm-10">
                    <div class="form-group is-required">
                        <a href="javascript:" class="pull-right add-billing-address">Editar/Adicionar Morada</a>
                        {{ Form::label('billing_name', 'Fornecedor') }}
                        {{ Form::text('billing_name', @$invoice->billing_name, ['class' => 'form-control search-provider uppercase', 'id' => 'billing_name']) }}
                    </div>
                </div>
            </div>
            <div class="row row-5" id="add-billing-address" style="display: none">
                <div class="col-sm-12">
                    <div class="form-group">
                        {{ Form::label('billing_address', 'Morada') }}
                        {{ Form::text('billing_address', @$invoice->billing_address, ['class' => 'form-control uppercase', 'id' => 'billing_address']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('billing_zip_code', 'C.Postal') }}
                        {{ Form::text('billing_zip_code', @$invoice->billing_zip_code, ['class' => 'form-control uppercase', 'id' => 'billing_zip_code']) }}
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        {{ Form::label('billing_city', 'Localidade') }}
                        {{ Form::text('billing_city', @$invoice->billing_city, ['class' => 'form-control uppercase', 'id' => 'billing_city']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group is-required">
                        {{ Form::label('category_id', 'Tipo Fornecedor') }}
                        {{ Form::select('category_id', [''=>''] + $providerCategories, @$provider->category_id, ['class' => 'form-control select2']) }}
                    </div>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-3">
                    <div class="form-group is-required">
                        <label for="vat">País</label>
                        {{ Form::select('billing_country', trans('country'), @$invoice->billing_country, ['class' => 'form-control select2', 'id' => 'billing_country']) }}
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        <label for="vat">
                            NIF
                        </label>
                        <div class="input-group">
                            {{ Form::text('vat', @$invoice->vat, ['class' => 'form-control nospace vat', 'autocomplete' => 'off', 'data-country' => 'billing_country']) }}
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default btn-validate-nif"
                                        style="height: 34px;"
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
                    <div class="form-group">
                        {{ Form::label('currency', 'Moeda') }}
                        {{ Form::select('currency', trans('admin/localization.currencies'), $invoice->currency, ['class' => 'form-control select2']) }}
                    </div>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-4">
                    <div class="form-group is-required">
                        {{ Form::label('docdate', 'Data Documento') }}
                        <div class="input-group">
                            {{ Form::text('docdate', $docDate, ['class' => 'form-control datepicker', 'required']) }}
                            <div class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('received_date', 'Data Recebimento', ['data-toggle'=>'tooltip', 'title' => 'Data em que a fatura foi efetivamente recebida.']) }}
                        <div class="input-group">
                            {{ Form::text('received_date', @$invoice->received_date, ['class' => 'form-control datepicker']) }}
                            <div class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('payment_condition', 'Cond. Pagamento') }}
                        {{ Form::select('payment_condition', ['' => ''] + $paymentConditions,  $invoice->payment_condition ? $invoice->payment_condition : '30d', ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-6 payment-receipt" style="display: none">
                    <div class="form-group">
                        {{ Form::label('payment_method_id', 'Método de Pagamento') }}
                        {{ Form::select('payment_method_id', ['' => ''] + $paymentMethods, null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-6 payment-receipt" style="display: none">
                    <div class="form-group">
                        {{ Form::label('bank_id', 'Banco') }}
                        {{ Form::select('bank_id', ['' => ''] + $banks, null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-4" style="display: none">
                    <div class="form-group">
                        {{ Form::label('duedate', 'Data Vencimento', ['data-toggle'=>'tooltip', 'title' => 'Data de vencimento fiscal da fatura']) }}
                        <div class="input-group">
                            {{ Form::text('duedate', $docLimitDate, ['class' => 'form-control datepicker']) }}
                            <div class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4" style="display: none">
                    <div class="form-group">
                        {{ Form::label('payment_until', 'Limite Pgto', ['data-toggle'=>'tooltip', 'title' => 'Data de vencimento fiscal da fatura']) }}
                        <div class="input-group">
                            {{ Form::text('payment_until', @$paymentUntil, ['class' => 'form-control datepicker']) }}
                            <div class="input-group-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('obs', 'Observações da fatura') }}
                {{ Form::textarea('obs', $invoice->obs, ['class' => 'form-control', 'rows' => 2]) }}
            </div>
            @if(!$invoice->exists)
            <div class="form-group m-b-0" style="display: {{ ($invoice->exists && $invoice->filepath) ?  'none' : 'block' }};" }}>
                {{ Form::label('name', 'Ficheiro a anexar') }}
                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                    <div class="form-control" data-trigger="fileinput">
                        <i class="fas fa-file fileinput-exists"></i>
                        <span class="fileinput-filename"></span>
                    </div>
                    <span class="input-group-addon btn btn-default btn-file">
                        <span class="fileinput-new">Procurar...</span>
                        <span class="fileinput-exists">Alterar</span>
                        <input type="file" name="file">
                    </span>
                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remover</a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>