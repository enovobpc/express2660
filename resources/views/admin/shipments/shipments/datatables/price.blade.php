<?php
$row->expenses_price = $row->expenses_price + $row->fuel_price;
$subtotal = $row->shipping_price + $row->expenses_price;

if(Setting::get('shipment_sum_expenses')) {
    $row->shipping_price = $row->shipping_price + $row->expenses_price;
    $subtotal = $row->shipping_price;
}
?>

@if(Auth::user()->showPrices() && $row->type != \App\Models\Shipment::TYPE_MASTER)


    @if(empty(Auth::user()->agencies) || in_array($row->agency_id, Auth::user()->agencies))
        @if($row->invoice_doc_id && $row->invoice_type)
            <span class="text-center">
                <a href="{{ route('admin.invoices.download', [$row->customer_id, $row->invoice_doc_id, 'id' => $row->invoice_id, 'type' => $row->invoice_type, 'key' => $row->invoice_key]) }}"
                   class="label bg-blue btn-invoice" target="_blank"
                   data-toggle="tooltip"
                   title="{{ $row->invoice_type == 'nodoc' ? 'Faturado sem emissão de documento' : 'Download Fatura' }}">
                    <i class="fas fa-file-invoice"></i> {{ money($row->shipping_price + $row->expenses_price, $row->currency) }}
                </a>
            </span>
        @elseif($row->invoice_type == 'nodoc')
            <span class="text-center label bg-blue btn-invoice" data-toggle="tooltip" title="Faturado sem emissão de documento.">
                <i class="fas fa-file-invoice"></i> {{ money($row->shipping_price + $row->expenses_price, $row->currency) }}
            </span>
        @else

            @if($row->ignore_billing)
                <strike>
                    <span class="label bg-blue italic" data-toggle="tooltip" title="Portes Pagos. Ignorado da faturação">
                        <b>D</b> {{ money($row->billing_subtotal, $row->currency) }}
                    </span>
                </strike>
            @elseif($row->cod == 'D')
                <span class="label bg-orange" data-toggle="tooltip" title="Portes no Destino">
                    <b>D</b> {{ money($row->shipping_price, $row->currency) }}
                </span>
            @elseif($row->cod == 'S')
                <span class="label bg-orange" data-toggle="tooltip" title="Portes na Recolha">
                    <b>R</b> {{ money($row->shipping_price, $row->currency) }}
                </span>
            @elseif(!empty($row->requested_by) && $row->requested_by != $row->customer_id)
                <span class="text-orange" data-toggle="tooltip" title="Portes no Destino. Faturação mensal ao destinatário">
                    <i class="fas fa-user"></i> {{ money($row->shipping_price ? $row->shipping_price : 0, $row->currency) }}
                </span>
            @elseif(!$row->price_fixed && (empty($row->shipping_price) || $row->shipping_price == 0.00))
                <span class="text-red">
                    <i class="fas fa-exclamation-circle"></i> {{ money($row->shipping_price, $row->currency) }}
                </span>
            @else
                <span data-total="{{ $subtotal }}">
                    {{ money($row->shipping_price, $row->currency) }}
                </span>
            @endif

            @if($row->price_fixed && !$row->invoice_doc_id)
                <span data-target="tooltip" title="Preço bloqueado. Este preço não será alterado.">
                    <i class="text-red fas fa-lock"></i>
                </span>
            @elseif($row->invoice_doc_id && !$row->invoice_draft)
                <a href="" target="_blank">
                    <span data-target="tooltip" title="Fatura Gerada">
                        <i class="text-blue fas fa-file-alt"></i>
                    </span>
                </a>
            @elseif($row->invoice_doc_id && $row->invoice_draft)
                <span data-target="tooltip" title="Rascunho Gerado">
                    <i class="text-info fas fa-file-alt"></i>
                </span>
            @endif

            @if($row->expenses_price && !Setting::get('shipment_sum_expenses') && !$row->ignore_billing)
                <br/>
                <span class="label label-green-inverse" data-toggle="tooltip" title="Encargos adicionais do envio">
                    +{{ money($row->expenses_price, $row->currency) }}
                </span>
            @endif

        @endif



    @else
        <i class="text-blue" data-toggle="tooltip" title="Valor pela Entrega">{{ money($row->delivery_price, $row->currency) }}</i>
    @endif

@elseif(Auth::user()->showPrices() && $row->type == \App\Models\Shipment::TYPE_MASTER)
    @if($row->expenses_price > 0.00 || $row->shipping_price > 0.00)
        {{ $row->shipping_price > 0.00 ? money($row->shipping_price ? $row->shipping_price : 0, $row->currency) : '-.--' }}
        <br/>
        <span class="label bg-green" data-toggle="tooltip" title="Encargos adicionais do envio">
            +{{ money($row->expenses_price, $row->currency) }}
        </span>
    @endif
@endif

{{-- PRINT CUSTOMERS --}}
@if($row->customer_id && !Setting::get('shipment_list_show_customer_name'))
    <div>
        <small>
            <i class="text-muted" data-toggle="tooltip" title="{{ @$row->customer->name }}">
                {{ @$row->customer->code_abbrv ? @$row->customer->code_abbrv : @$row->customer->code }}
            </i>
        </small>
    </div>
@endif