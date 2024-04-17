<?php $sign = Setting::get('app_currency'); ?>
@if(@$invoices)
    @foreach($invoices as $providerId => $lines)
        <?php
        $provider = @$lines[0]->provider;
        if(!$provider) {
            $vat  = @$lines[0]->vat;
            $name = @$lines[0]->billing_code . ' - ' .@$lines[0]->billing_name;
        } else {
            $vat = '';
            $name = @$provider->code. ' - '. (@$provider->company ? @$provider->company : @$provider->name);
        }
        ?>
        
        <h1 style="margin-bottom: 0">{{ $name }}</h1>
        <p>
            Contribuinte: {{ @$provider->vat ? @$provider->vat : $vat }}
            @if(@$provider->paymentCondition->name)
                | Pagamento: {{ @$provider->paymentCondition->name }}
            @endif
            @if(@$provider->iban)
                | IBAN: {{ strtoupper(@$provider->iban) }}
            @endif
        </p>
        <table class="table table-bordered table-pdf font-size-7pt" style="border: none; margin: 0">
            <tr>
                <th class="w-65px">Data Doc</th>
                <th class="w-65px">Data Recb</th>
                <th class="w-65px">Vencimento</th>
                <th>Tipo Documento</th>
                <th>Documento</th>
                <th class="w-70px text-right">Subtotal ({{ $sign }})</th>
                <th class="w-70px text-right">IVA ({{ $sign }})</th>
                <th class="w-70px text-right">Total ({{ $sign }})</th>
                <th class="w-70px text-right">Pendente ({{ $sign }})</th>
                <th class="w-70px text-right">Saldo ({{ $sign }})</th>                
            </tr>
            <?php $subtotal = $vat = $total = $unpaid = $docUnpaid = 0; ?>
            
            @foreach($lines as $invoice)
                <?php
                $subtotal+= @$invoice['subtotal'];
                $total+= @$invoice['total'];
                $vat+= @$invoice['vat_total'];
                $unpaid+= @$invoice['total_unpaid'];

                $totalPending = @$invoice['total'];
                if (!empty(@$invoice['total_unpaid'])) {
                    $totalPending = @$invoice['total_unpaid'];
                }
                

                $docUnpaid += $totalPending;
                
                ?>
                <tr>
                    <td>{{ @$invoice['doc_date'] }}</td>
                    <td>{{ @$invoice['doc_received'] }}</td>
                    <td>{{ @$invoice['due_date'] }}</td>
                    <td>{{ trans('admin/billing.types.'.@$invoice['doc_type']) }}</td>
                    <td>{{ @$invoice['reference'] }}</td>
                    <td class="text-right">{{ money(@$invoice['subtotal']) }}</td>
                    <td class="text-right">{{ money(@$invoice['vat_total']) }}</td>
                    <td class="text-right">{{ money(@$invoice['total']) }}</td>
                    @if(@$invoice['total_unpaid'] != @$invoice['total'])
                    <td class="text-right bold" style="color: orange">{{ money(@$invoice['total_unpaid']) }}</td>
                    @else
                    <td class="text-right bold" style="color: red">{{ money(@$invoice['total_unpaid']) }}</td>
                    @endif
                    <td class="text-right bold" style="color: red">{{ money($docUnpaid) }}</td>
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
                <td class="text-right bold" style="font-weight: bold; font-size: 12px; color: #ff0000">{{ money($unpaid) }}</td>
            </tr>
        </table>
    @endforeach
    <hr style="margin-bottom: 10px"/>
    <p class="text-center">Documento gerado em {{ date('Y-m-d') }} às {{ date('H:i:s') }} por {{ Auth::user()->name }}</p>
@endif