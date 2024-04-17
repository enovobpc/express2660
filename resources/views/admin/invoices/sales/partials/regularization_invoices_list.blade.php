<?php $total = $totalUnpaid = $totalSelected = 0 ?>
@if(!@$invoices->isEmpty())
<?php $totalCustomers = $invoices->groupBy('customer_id')->count(); ?>
<div style="border: 1px solid #ddd; border-bottom: none">
    <table class="table table-condensed m-0">
        <thead>
        <tr>
            @if($totalCustomers > 1)
            <th class="bold bg-gray-light vertical-align-middle w-60px">Cliente</th>
            @endif
            <th class="bold bg-gray-light vertical-align-middle">Fatura</th>
            <th class="bold bg-gray-light vertical-align-middle w-90px">Data</th>
            <th class="bold bg-gray-light vertical-align-middle w-105px">Vencimento</th>
            <th class="bold bg-gray-light vertical-align-middle w-90px" style="border-left: 2px solid #333">Total</th>
            <th class="bold bg-gray-light vertical-align-middle w-90px">Pendente</th>
            <th class="bold bg-gray-light vertical-align-middle w-120px">{{ Form::text('prefill_val', null, ['class' => 'form-control input-xs decimal', 'placeholder' => 'Valor Liquidar...', 'style' => 'padding: 7px;height: 26px; margin: -1px 0;']) }}</th>
            <th class="bold bg-gray-light vertical-align-middle w-30px">{{ Form::checkbox('prefill_all', 1, null) }}</th>
        </tr>
        </thead>
    </table>
</div>
<div style="max-height: 215px; border: 1px solid #ddd; overflow-y: auto;" class="nicescroll">
    <table class="table table-condensed m-0 table-items">
        <tbody>
            
            @foreach($invoices as $invoice)
            <?php

                $isCredit = ($invoice->doc_type == 'credit-note' || $invoice->doc_type == 'sinc');
                $selectedValue = @$invoicesValues[$invoice->id];

                if($isCredit && $invoice->doc_total > 0.00) {
                    $invoice->doc_total = $invoice->doc_total * -1; //garante que se tiver valor positivo fica
                }

                $total+= $invoice->doc_total;
                $totalUnpaid+= $invoice->doc_total_pending ? $invoice->doc_total_pending : $invoice->doc_total;
                $totalSelected+= $selectedValue;

                $duedateClass = $dueIcon = '';
                if($invoice->due_date < date('Y-m-d')) {
                    $duedateClass = 'text-red';
                    $dueIcon = '<small></small>';
                }

                $pendingColor = 'red';
                if($invoice->doc_total_pending) {
                    $pendingColor = 'yellow';
                }
                ?>
                <tr>
                    @if($totalCustomers > 1)
                    <td class="w-60px vertical-align-middle">{{ $invoice->billing_code }}</td>
                    @endif
                    <td class="vertical-align-middle">{!! $invoice->doc_series ? $invoice->doc_series :  '<i class="fas fa-exclamation-triangle text-red" data-toggle="tooltip" title="Série não identificada. Por favor, atualize a conta corrente do cliente para corrigir o problema."></i>' !!} {{ $invoice->doc_id }}</td>
                    <td class="w-90px vertical-align-middle">{{ $invoice->doc_date }}</td>
                    <td class="w-105px vertical-align-middle {{ $duedateClass }}">{!! $dueIcon !!}{{ $invoice->due_date }}</td>
                    <td class="w-90px vertical-align-middle bold" style="border-left: 2px solid #333">{{ money($invoice->doc_total, $currency) }}</td>
                    <td class="w-90px vertical-align-middle bold text-{{ $pendingColor }} pending-value"><span>{{ money((@$invoice->doc_total_pending ? $invoice->doc_total_pending : $invoice->doc_total), $currency) }}</span></td>
                    <td class="w-120px">
                        <div class="input-group">
                            @if($isCredit)
                            <div class="input-group-addon" style="border-right: 0; padding-right: 0">-</div>
                            @endif
                            <input name="invoices[{{ $invoice->id }}]" value="{{ @$selectedValue ? abs($selectedValue) : '' }}"
                                   style="border-right: 0; {{ $isCredit ? ' border-left: 0; padding-left: 0;' : '' }}"
                                   {{ $invoice->doc_series ? : 'disabled' }}
                                   class="form-control input-sm decimal invoice-price"
                                   data-type="{{ $invoice->doc_type }}"
                                   data-max="{{ abs(@$invoice->doc_total_pending ? $invoice->doc_total_pending : $invoice->doc_total) }}">
                            <div class="input-group-addon" style="border-left: 0;">
                                {{ $currency }}
                            </div>
                        </div>
                    </td>
                    <td class="vertical-align-middle w-30px">
                        {{ Form::checkbox('prefill', 1, null) }}
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
            <td class="w-100px"></td>
            <td class="w-110px"></td>
            <td class="w-90px"></td>
            <td class="vertical-align-middle bold w-90px" style="border-left: 2px solid #333">
                {{ money($total, $currency) }}
            </td>
            <td class="vertical-align-middle bold text-red w-90px">
                {{ money($totalUnpaid, $currency) }}
            </td>
            <td class="bold fs-15px payment-total w-120px">
                <span>{{ money($totalSelected) }}</span>{{ $currency }}
            </td>
            <td class="w-30px"></td>
        </tr>
    </table>
</div>
@else
    <div class="p-t-120 p-b-125 text-center text-muted" style="border: 1px solid #ddd">
        <i class="fas fa-info-circle"></i> Não há documentos internos para regularizar no cliente {{ @$customer->display_name }}
    </div>
@endif