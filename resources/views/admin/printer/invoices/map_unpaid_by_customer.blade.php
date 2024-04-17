<?php
$sign = Setting::get('app_currency');
$docSubtotal = $docVat = $docTotal = $docUnpaid = 0;
?>
@if (@$invoices)
    @foreach ($invoices as $customerId => $lines)
        <?php
        $customer = @$lines[0]->customer;
        if (!$customer) {
            $vat = @$lines[0]->vat;
            $name = @$lines[0]->billing_code . ' - ' . @$lines[0]->billing_name;
        } else {
            $vat = '';
            $name = @$customer->code . ' - ' . @$customer->billing_name;
        }
        ?>

        <h1 style="margin-bottom: 0">{{ $name }}</h1>
        <p>
            Contribuinte: {{ empty($customer->vat) ? $vat : $customer->vat }}
            @if (@$customer->payment_method->name)
                &bull;
                Pagamento: {{ @$customer->paymentCondition->name }}
            @endif
            &bull;
            Contacto: {{ $customer->mobile ?? ($customer->phone ?? '') }}
        </p>
        <table class="table table-bordered table-pdf font-size-7pt" style="border: none; margin: 0">
            <tr>
                <th class="w-65px">Data Doc</th>
                <th class="w-65px">Vencimento</th>
                <th class="w-90px">Tipo Doc.</th>
                <th>Documento</th>
                <th>Referência</th>
                <th class="w-75px text-right">Subtotal ({{ $sign }})</th>
                <th class="w-55px text-right">IVA ({{ $sign }})</th>
                <th class="w-75px text-right">Total ({{ $sign }})</th>
                <th class="w-75px text-right">Pendente ({{ $sign }})</th>
                <th class="w-75px text-right">Saldo ({{ $sign }})</th>
            </tr>
            <?php
            $subtotal = $vat = $total = $unpaid = $docUnpaid = 0;
            $lines = $lines->sortBy('doc_date');
            ?>
            @foreach ($lines as $invoice)
                <?php
                
                if (@$invoice['doc_type'] == 'credit-note' && @$invoice['doc_total'] > 0.0) {
                    $invoice['doc_subtotal'] = @$invoice['doc_subtotal'] * -1;
                    $invoice['doc_vat'] = @$invoice['doc_vat'] * -1;
                    $invoice['doc_total'] = @$invoice['doc_total'] * -1;
                }
                
                $totalPending = @$invoice['doc_total'];
                if (!empty(@$invoice['doc_total_pending'])) {
                    $totalPending = @$invoice['doc_total_pending'];
                }
                
                $subtotal += @$invoice['doc_subtotal'];
                $vat += @$invoice['doc_vat'];
                $total += @$invoice['doc_total'];
                $unpaid += $totalPending;
                
                $docSubtotal += @$invoice['doc_subtotal'];
                $docVat += @$invoice['doc_vat'];
                $docTotal += @$invoice['doc_total'];
                $docUnpaid += $totalPending;
                
                ?>
                <tr>
                    <td>{{ @$invoice['doc_date'] }}</td>
                    <td>{{ @$invoice['due_date'] }}</td>
                    <td>
                        @if ($invoice['doc_type'] == 'nodoc')
                            Sem Documento
                        @else
                            {{ trans('admin/billing.types.' . @$invoice['doc_type']) }}
                        @endif
                    </td>
                    <td>
                        @if ($invoice['doc_type'] == 'nodoc')
                        @else
                            {{ @$invoice['doc_series'] }} {{ @$invoice['doc_id'] }}
                        @endif
                    </td>
                    <td>
                        {{ @$invoice['reference'] }}
                    </td>
                    <td class="text-right">{{ money(@$invoice['doc_subtotal']) }}</td>
                    <td class="text-right">{{ money(@$invoice['doc_vat']) }}</td>
                    <td class="text-right">{{ money(@$invoice['doc_total']) }}</td>
                    @if ($totalPending != @$invoice['doc_total'])
                        <td class="text-right bold" style="color: orange">{{ money($totalPending) }}</td>
                    @else
                        <td class="text-right bold" style="color: red">{{ money($totalPending) }}</td>
                    @endif
                    <td class="text-right bold">{{ money($docUnpaid) }}</td>
                </tr>
            @endforeach
            <tr>
                <td class="text-right" colspan="5" style="border: none">
                    Total
                </td>
                <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($subtotal) }}</td>
                <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($vat) }}</td>
                <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($total) }}</td>
                <td class="text-right bold" style="font-weight: bold; font-size: 12px; color: #ff0000">{{ money($unpaid) }}</td>
                <td class="text-right" style="font-weight: bold; font-size: 12px;">{{ money($unpaid) }}</td>
            </tr>
        </table>
    @endforeach

    <h4 class="text-right m-t-20 m-b-0">
        &nbsp;&nbsp;&nbsp;&nbsp;
        <small>Subtotal: <b class="bold"
                style="color: #000;">{{ money($docSubtotal, Setting::get('app_currency')) }}</b></small>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <small>IVA: <b class="bold"
                style="color: #000;">{{ money($docVat, Setting::get('app_currency')) }}</b></small>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <small>Total: <b class="bold"
                style="color: #000;">{{ money($docTotal, Setting::get('app_currency')) }}</b></small>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <small>Pendente:</small> <b class="bold">{{ money($docUnpaid, Setting::get('app_currency')) }}</b>
    </h4>
    <hr style="margin-bottom: 10px" />
    <p class="text-center">Documento gerado em {{ date('Y-m-d') }} às {{ date('H:i:s') }} por
        {{ Auth::user()->name }}</p>
@endif
