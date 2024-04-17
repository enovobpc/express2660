@if($row->cod == 'D' || !empty($row->requested_by) && ($row->customer_id != $row->requested_by))
    <span class="label label-warning" data-toggle="tooltip" title="Pagamento pelo destinatário">Pag. Dest.</span>
@elseif($row->cod == 'S' || !empty($row->requested_by) && ($row->customer_id != $row->requested_by))
    <span class="label label-warning" data-toggle="tooltip" title="Pagamento pelo Remetente">Pag. Rem.</span>
@else
    @if($customer->show_billing && $row->billing_subtotal > 0.00 && $row->status_id != \App\Models\ShippingStatus::CANCELED_ID)
        @if(hasModule('account_wallet') && !$customer->is_mensal)
            <?php
            $base = $row->billing_subtotal;

            if(is_null($row->vat_rate)) { //iva automatico pelo sistema
                $total = $row->billing_total;
            } else {
                $total = valueWithVat($base, $row->vat_rate);
            }
            ?>
            <span data-toggle="tooltip"
                data-html="true"
                title="Base: {{ money($base, Setting::get('app_currency')) }} <br/> IVA: {{ money($total - $base, Setting::get('app_currency')) }}">
                {{ money($total, Setting::get('app_currency')) }}
            </span>
            @if($row->ignore_billing || $row->invoice_id)
                <span class="label label-success"><i class="fas fa-check"></i> {{ trans('account/global.word.paid') }}</span>
            @else
                <span class="label label-danger"><i class="fas fa-exclamation-triangle"></i> {{ trans('account/global.word.unpaid') }}</span>
            @endif
        @else
            <div>{{ money($row->shipping_price, Setting::get('app_currency')) }}</div>
            @if($row->expenses_price + $row->fuel_price)
                <span class="label label-success" data-toggle="tooltip" title="{{ trans('account/shipments.modal-shipment.tips.price-expense') }}">
                    +{{ money($row->expenses_price + $row->fuel_price, Setting::get('app_currency')) }}
                </span>
            @endif
        @endif
    @endif
@endif
