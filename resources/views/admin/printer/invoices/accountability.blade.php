@foreach($invoices as $operator => $rows)
    <h4 class="m-t-0">
        {{ $operator }}
    </h4>
    <table class="table table-bordered table-pdf font-size-7pt m-b-10">
        <tr>
            <th class="w-70px">Data Doc.</th>
            <th class="w-70px">Documento</th>
            <th>Referência</th>
            <th class="w-70px text-right">Subtotal</th>
            <th class="w-60px text-right">IVA</th>
            <th class="w-70px text-right">Total</th>
            <th class="w-60px text-right">Saldo</th>
            <th>Pagamento</th>
        </tr>
        <?php
        $docSubtotal = 0;
        $docVat = 0;
        $docTotal = 0;
        $count = 0;
        $payments = [];
        $invoiceTypes = [];
        ?>
        @foreach($rows as $invoice)
            <?php
            $count++;

            $total    = valueWithVat($invoice->total_vat) + $invoice->total_no_vat;
            $subtotal = $invoice->total;
            $vat      = $invoice->total_vat  * (Setting::get('vat_rate_normal')/100);

            $docSubtotal+= $invoice->total;
            $docVat+= $vat;
            $docTotal+= $total;

                if(@$invoiceTypes[$invoice->doc_type]) {
                    $invoiceTypes[$invoice->doc_type]+= $total;
                } else {
                    $invoiceTypes[$invoice->doc_type] = $total;
                }

                if(@$payments[$invoice->payment_method]) {
                    $payments[$invoice->payment_method]+= $total;
                } else {
                    $payments[$invoice->payment_method] = $total;
                }
            ?>
            <tr>
                <td>{{ $invoice->doc_date }}</td>
                <td>
                    {{ trans('admin/billing.types_code.' . $invoice->doc_type) }} {{ $invoice->doc_type == 'nodoc' ? '' : $invoice->doc_id }}
                </td>
                <td>{{ $invoice->reference }}</td>
                <td class="text-right">{{ money($subtotal) }}</td>
                <td class="text-right">{{ money($vat) }}</td>
                <td class="text-right">{{ money($total) }}</td>
                <td class="text-right">{{ money($docTotal) }}</td>
                <td>
                    @if($invoice->payment_method)
                        {{ @$invoice->paymentMethod->name }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
    <div style="width: 33%; float: left; font-size: 12px">
        <p style="font-weight: bold; margin: 0">Resumo Valores</p>
        <table class="table table-condensed w-70 m-0" cellpadding="0" cellspacing="0" style="font-size: 12px">
            <tr>
                <td class="w-30px" style="padding: 1px 0">Documentos</td>
                <td class="w-70px text-right" style="padding: 0">{{ $count }}</td>
            </tr>
            <tr>
                <td style="padding: 1px 0">Subtotal</td>
                <td class="text-right" style="padding: 1px 0">{{ money($docSubtotal, Setting::get('app_currency')) }}</td>
            </tr>
            <tr>
                <td style="padding: 1px 0">IVA</td>
                <td style="padding: 1px 0" class="text-right">{{ money($docVat, Setting::get('app_currency')) }}</td>
            </tr>
            <tr>
                <td style="padding: 1px 0"><span style="font-weight: bold; font-size: 15px;">Total:</span></td>
                <td style="padding: 1px 0" class="text-right">
                    <span style="font-weight: bold; font-size: 15px;">
                        {{ money($docTotal,  Setting::get('app_currency')) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>
    <div style="width: 34%; float: left; font-size: 12px">
        <p style="font-weight: bold; margin: 0">Documentos</p>
        <table class="table table-condensed w-80 m-0" style="font-size: 12px">
            @foreach($invoiceTypes as $invoiceType => $total)
                <tr>
                    <td style="padding: 1px 0">{{ trans('admin/billing.types-list.' . $invoiceType) }}</td>
                    <td style="padding: 1px 0" class="w-70px text-right">{{ money($total, Setting::get('app_currency')) }}</td>
                </tr>
            @endforeach
        </table>
    </div>
    <div style="width: 33%; float: left; font-size: 12px">
        <p style="font-weight: bold; margin: 0">Métodos de Pagamento</p>
        <table class="table table-condensed w-90 m-0" style="font-size: 12px">
            @foreach($payments as $paymentMethod => $total)
                <tr>
                    <td style="padding: 1px 0">{{ $paymentMethod ? (@$paymentMethods[$paymentMethod] ? @$paymentMethods[$paymentMethod] : $paymentMethod) : 'Sem método pagamento' }}</td>
                    <td style="padding: 1px 0" class="w-70px text-right">{{ money($total, Setting::get('app_currency')) }}</td>
                </tr>
            @endforeach
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    <div class="clearfix"></div>
@endforeach