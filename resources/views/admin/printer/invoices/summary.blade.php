<table class="table table-bordered table-pdf font-size-7pt" style="border: none">
    <tr>
        <th class="w-70px">Data Doc.</th>
        <th class="w-70px">Vencimento</th>
        <th class="w-100px">Documento</th>
        <th class="w-50px">NIF</th>
        <th>Cliente</th>
        <th>Referência</th>
        <th>Pagamento</th>
        <th class="w-70px text-right">Subtotal</th>
        <th class="w-60px text-right">IVA</th>
        <th class="w-70px text-right">Total</th>
        <th class="w-70px text-right">Pendente</th>
    </tr>
    <?php $documentTotal = $documentVat = $documentSubtotal = $countTotal = $documentTotalUnpaid = 0; ?>
    @foreach($invoices as $invoice)
        <?php

        $subtotal = $invoice->doc_subtotal;
        $total    = $invoice->doc_total;
        $vat      = $invoice->doc_vat;
        $unpaid   = 0;

        if(!$invoice->is_settle) {
            $unpaid = $invoice->doc_total_pending;
            $unpaid = $unpaid ? $unpaid : $total;
        }

        if($invoice->doc_type == 'nodoc') {
            $vat   = 0;
            $total = 0;
        }

        if($invoice->doc_type == 'credit-note' && $total > 0.00) {
            $subtotal = $subtotal * -1;
            $total    = $total * -1;
            $vat      = $vat * -1;
            $unpaid   = $unpaid * -1;
        }



        $documentTotal+= $total;
        $documentVat+=$vat;
        $documentSubtotal+=$subtotal;
        $countTotal++;

        if(!$invoice->is_settle && !$invoice->is_deleted) {
            $documentTotalUnpaid+= $unpaid;
        }
        ?>
        <tr>
            <td>{{ $invoice->doc_date }}</td>
            <td>{{ $invoice->due_date }}</td>
            <td>
                @if($invoice->doc_type == 'nodoc')
                    {{ $invoice->reference }}
                @else
                    @if($invoice->is_draft)
                        Racunho
                    @elseif($invoice->doc_series)
                        {{ $invoice->doc_series }} <b>{{ $invoice->doc_id }}</b>
                    @else
                        {{ trans('admin/billing.types_code.' . $invoice->doc_type) }} {{ $invoice->doc_type == 'nodoc' ? '' : $invoice->doc_id }}
                    @endif
                @endif
            </td>
            <td>{{ $invoice->doc_type != 'nodoc' ? $invoice->vat : '' }}</td>
            <td>{{ strtoupper($invoice->billing_name) }}</td>
            <td>{{ $invoice->reference }}</td>
            <td>{{ $invoice->payment_method ? @$invoice->paymentMethod->name : '' }}</td>
            <td class="text-right">{{ money($subtotal) }}</td>
            <td class="text-right">{{ money($vat) }}</td>
            <td class="text-right bold">{{ money($total) }}</td>
            @if($invoice->is_settle)
                <td></td>
            @else
                <td class="text-right bold" style="color: #FF0000">
                    @if($invoice->is_deleted)
                        Anulado
                    @else    
                        {{ money($unpaid) }}
                    @endif
                </td>
            @endif
            {{--<td class="text-right">{{ $invoice->is_settle ? 'Pago' : 'Por Pagar' }}</td>--}}
        </tr>
    @endforeach
    <tr>
        <td colspan="7" style="border: none;font-size: 12px" class="text-right">
            Total ({{ $countTotal }} documentos)
        </td>
        <td class="text-right" style="font-weight: bold; font-size: 12px">{{ money($documentSubtotal) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px">{{ money($documentVat) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px">{{ money($documentTotal) }}</td>
        <td class="text-right" style="font-weight: bold; font-size: 12px; color: #FF0000">
            {{ money($documentTotalUnpaid) }}
        </td>
        {{--<td style="border: none"></td>--}}
    </tr>
</table>