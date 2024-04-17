<?php $total = $totalUnpaid = $totalSelected = 0 ?>
@if(!@$invoices->isEmpty())
<?php $totalCustomers = $invoices->groupBy('customer_id')->count(); ?>
<div style="border: 1px solid #ddd; border-bottom: none">
    <table class="table table-condensed m-0">
        <thead>
        <tr>
            <th class="bold bg-gray-light vertical-align-middle">Cliente</th>
            <th class="bold bg-gray-light vertical-align-middle w-90px">Fatura</th>
            <th class="bold bg-gray-light vertical-align-middle w-90px">Data</th>
            <th class="bold bg-gray-light vertical-align-middle w-105px">Vencimento</th>
            <th class="bold bg-gray-light vertical-align-middle text-right w-90px" style="border-left: 2px solid #333">Total</th>
<!--            <th class="bold bg-gray-light vertical-align-middle w-120px">{{ Form::text('prefill_val', null, ['class' => 'form-control input-xs decimal', 'placeholder' => 'Valor Liquidar...', 'style' => 'padding: 7px;height: 26px; margin: -1px 0;']) }}</th>-->
            <th class="bold bg-gray-light vertical-align-middle w-30px">{{ Form::checkbox('prefill_all', 1, true) }}</th>
        </tr>
        </thead>
    </table>
</div>
<div style="max-height: 215px; border: 1px solid #ddd; overflow-y: auto;" class="nicescroll">
    <table class="table table-condensed m-0 table-items">
        <tbody>
            <?php $hasErrors = false; ?>
            @foreach($invoices as $invoice)
                <?php
                $selectedValue = @$invoicesValues[$invoice->id];

                if($invoice->doc_type == 'credit-note') {
                    $invoice->doc_total = $invoice->doc_total < 0.00 ? (-1*$invoice->doc_total) : $invoice->doc_total;
                    $selectedValue = $selectedValue < 0.00 ? -1*$selectedValue : $selectedValue;
                    $total-= $invoice->doc_total;
                    $totalUnpaid-= $invoice->doc_total;
                    $totalSelected-= $selectedValue;
                } else {
                    $total+= $invoice->doc_total;
                    $totalUnpaid+= $invoice->doc_total_pending ? $invoice->doc_total_pending : $invoice->doc_total;
                    $totalSelected+= $selectedValue;
                }

                $duedateClass = $dueIcon = '';
                if($invoice->due_date < date('Y-m-d')) {
                    $duedateClass = 'text-red';
                    $dueIcon = '<small></small>';
                }

                $pendingColor = 'red';
                if($invoice->doc_total_pending) {
                    $pendingColor = 'yellow';
                }

                $rowClass = '';
                if(!@$invoice->customer->bank_iban || !@$invoice->customer->bank_swift || !@$invoice->customer->bank_mandate) {
                    $rowClass = 'background: #f7bdbd';
                    $hasErrors = true;
                }
                ?>
                <tr style="{{ $rowClass }}" class="{{ $rowClass ? 'line-error' : '' }}">
                    <td class="vertical-align-middle">
                        {{ substr(@$invoice->customer->name, 0, 40) }}
                        @if($rowClass)
                            <small>
                            @if(!@$invoice->customer->bank_iban)
                            <div class="text-red">IBAN em falta</div>
                            @endif
                            @if(!@$invoice->customer->bank_iban)
                                <div class="text-red">SWIFT em falta</div>
                            @endif
                            @if(!@$invoice->customer->bank_mandate)
                                <div class="text-red">Código Mandato em falta</div>
                            @endif
                            </small>
                        @endif
                    </td>
                    <td class="w-90px vertical-align-middle">{!! $invoice->doc_series ? $invoice->doc_series :  '<i class="fas fa-exclamation-triangle text-red" data-toggle="tooltip" title="Série não identificada. Por favor, atualize a conta corrente do cliente para corrigir o problema."></i>' !!} {{ $invoice->doc_id }}</td>
                    <td class="w-90px vertical-align-middle">{{ $invoice->doc_date }}</td>
                    <td class="w-105px vertical-align-middle {{ $duedateClass }}">{!! $dueIcon !!}{{ $invoice->due_date }}</td>
                    <td class="w-90px vertical-align-middle text-right bold" style="border-left: 2px solid #333"><span>{{ money((@$invoice->doc_total_pending ? $invoice->doc_total_pending : $invoice->doc_total), Setting::get('app_currency')) }}</span></td>
                    <td class="vertical-align-middle w-30px">
                        {{ Form::checkbox('invoice[]', $invoice->id, $rowClass ? false : true, ['data-value' => (@$invoice->doc_total_pending ? $invoice->doc_total_pending : $invoice->doc_total), 'class' => 'prefill', $rowClass ? 'disabled' : '']) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div style="border-left: 1px solid #fff; border-right: 1px solid #fff; margin-top: -1px">
    <table class="table table-condensed m-0">
        <tr>
            <td></td>
            <td></td>
            <td class="w-100px"></td>
            <td class="w-110px"></td>
            <td class="w-90px"></td>
            <td class="vertical-align-middle bold w-90px text-right total-selected">
                {{ money($totalUnpaid, Setting::get('app_currency')) }}
            </td>
            <td class="w-30px"></td>
        </tr>
    </table>
</div>
@else
    <div class="p-t-120 p-b-125 text-center text-muted" style="border: 1px solid #ddd">
        <i class="fas fa-info-circle"></i> Não há faturas para listar ou as faturas já foram associadas a uma transferência SEPA
    </div>
@endif