<?php
    $locale = $customer->billing_country == 'pt' ? 'pt' : 'en';
    $documentTotal  = 0;
    $totalShipments = 0;
    $collectionsAssigned = [];
?>
<div>
    @if(!$shipments->isEmpty())
    <h4>{{ translation('admin\global.billing.pdf.section01', $locale) }}</h4>
    <table class="table table-bordered table-pdf m-b-5" style="font-size: 6.3pt;">
        <tr>
            <th>{{ translation('admin\global.word.shipment', $locale) }}</th>
            @if(Setting::get('billing_customers_pdf_provider_trk'))
            <th>{{ translation('admin\global.word.provider_trk', $locale) }}</th>
            @endif
            <th style="width: 30px">{{ translation('admin\global.word.serv', $locale) }}</th>
            <th>
                @if(Setting::get('shipments_reference3_visible'))
                    Referências
                @elseif(Setting::get('shipments_reference2_visible'))
                    / {{ Setting::get('shipments_reference2_name') ? Setting::get('shipments_reference2_name') : 'Ref.2'}}
                @else
                    {{ translation('admin\global.word.reference', $locale) }}
                @endif
            </th>
            <th style="width: 150px">{{ translation('admin\global.word.sender_name', $locale) }}</th>
            <th style="width: 150px">{{ translation('admin\global.word.recipient_name', $locale) }}</th>
            <th>{{ translation('admin\global.word.remittance', $locale) }}</th>
            <th>{{ translation('admin\global.word.charge', $locale) }}</th>
            <th class="w-100px">{{ translation('admin\global.word.obs', $locale) }}</th>
            <th class="w-45px">{{ translation('admin\global.word.price', $locale) }}</th>
        </tr>
        <?php
            $countTotal = 0;
            $totalExpenses = 0;
            $hasPaymentAtRecipient = false;
            $totalWithoutVat = 0;
            $totalExpensesWithoutVat = 0;
        ?>

        @foreach($shipments as $shipment)

            @if(($shipment->is_collection && $shipment->status_id == 18) || !$shipment->is_collection) {{-- ignora da listagem recolhas que tenham sido bem sucedidas--}}

            <?php
                if($period == 'single' || (!$shipment->ignore_billing && !$shipment->payment_at_recipient &&  $customer->id == $shipment->customer_id)) {
                    $totalShipments += $shipment->total_price;

                    if($shipment->isExport()) {
                        $totalWithoutVat+= $shipment->total_price;
                    }
                }

                $countTotal++;

                $hasCollectionExpense = false;
                $shipment->collection_price = 0;

                if($shipment->collection_tracking_code) {

                    $collection = $shipment->collection_price = $shipments->filter(function($item) use($shipment) {
                        return ($item->tracking_code == $shipment->collection_tracking_code) && $item->status_id != 18;
                    })->first();

                    if($collection) {
                        $hasCollectionExpense = true;
                        $countTotal++;
                        $shipment->collection_price = $collection->total_price + $collection->total_expenses;

                        if(!$collection->ignore_billing && !$collection->payment_at_recipient &&  (empty($shipment->requested_by) || $shipment->requested_by == $shipment->customer_id)) {
                            $totalShipments += $collection->total_price;

                            if($collection->isExport()) {
                                $totalWithoutVat+= $collection->total_price;
                            }
                        }
                    }
                }
            ?>
            <tr>
                @if(!$shipment->expenses->isEmpty() || $hasCollectionExpense)
                <td rowspan="2">
                @else
                <td>
                @endif
                    <b class="bold">{{ $shipment->tracking_code }}</b><br/>
                    <i>{{ $shipment->date }}</i>
                </td>

                @if(Setting::get('billing_customers_pdf_provider_trk'))
                <td>
                    @if($shipment->provider_tracking_code)
                        {{ $shipment->provider_tracking_code }}
                        <br/>
                        {{ $shipment->provider->_tracking_code }}
                    @endif
                </td>
                @endif

                <td class="text-center">
                    {{ @$shipment->service->display_code }}<br/>
                    {{ strtoupper($shipment->recipient_country) }}
                </td>

                <td>
                    @if($shipment->provider_id != 3)
                        {{ $shipment->reference }}
                    @endif

                    @if(Setting::get('shipments_reference2_visible'))
                        <br/>{{ $shipment->reference2 }}
                    @endif

                    @if(Setting::get('shipments_reference3_visible'))
                        <br/>{{ $shipment->reference3 }}
                    @endif
                </td>
                <td>
                    @if($shipment->sender_attn)
                        A/C: {{ $shipment->sender_attn }}
                        <br/>
                    @endif
                    {{ $shipment->sender_name }}<br/>{{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}
                </td>
                <td>
                    @if($shipment->recipient_attn)
                        A/C: {{ $shipment->recipient_attn }}
                        <br/>
                    @endif
                    {{ $shipment->recipient_name }}
                    <br/>{{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
                </td>
                <td>
                    {{ $shipment->volumes }} vol.<br/>
                    @if(@$shipment->service->unity == 'm3')
                        {{ $shipment->volume_m3 }} m<sup>3</sup>
                    @elseif(@$shipment->service->unity == 'km')
                        {{ $shipment->kms ? $shipment->kms : 0 }} km
                    @else
                        {{ $shipment->weight > $shipment->volumetric_weight ? $shipment->weight : $shipment->volumetric_weight }} kg
                    @endif
                </td>
                <td>{{ money($shipment->charge_price, Setting::get('app_currency')) }}</td>
                <td>
                    @if($shipment->requester_name)
                        ## Solicitado {{ $shipment->requester_name }} ##
                        <br/>
                    @endif

                    @if($shipment->status_id == '18')
                        ### {{ translation('admin\global.word.pickup-failed', $locale) }} ###
                    @endif

                    {{ $shipment->obs }}
                </td>
                <td>
                    @if($period == 'single' || !$shipment->ignore_billing)
                        @if($shipment->payment_at_recipient)
                            <?php $hasPaymentAtRecipient = true; ?>
                            0.00*
                        @else
                            {{ money($shipment->total_price, Setting::get('app_currency')) }}
                            @if(Setting::get('billing_show_vat'))
                                <br/>
                                @if($shipment->isExport())
                                    <small><i>{{ translation('admin\global.word.vat', $locale) }} 0%</i></small>
                                @else
                                    <small><i>{{ translation('admin\global.word.vat', $locale) }} {{ Setting::get('vat_rate_normal') }}%</i></small>
                                @endif
                            @endif
                        @endif
                    @endif
                </td>
            </tr>
            @if(!$shipment->expenses->isEmpty() || $hasCollectionExpense)
                <tr>
                    <td colspan="{{ Setting::get('billing_customers_pdf_provider_trk') ? 9 : 8 }}" style="padding: 0; border: none">
                        <table class="w-100" style="border:none">

                            @if($hasCollectionExpense)
                                <tr>
                                    <td style="width: {{ Setting::get('billing_customers_pdf_provider_trk') ? '11mm' : '20px'  }}; text-align: center">REC</td>
                                    <td style="width: {{ Setting::get('billing_customers_pdf_provider_trk') ? '130mm' : ''  }}">
                                        {{ translation('admin\global.billing.pdf.pickup_tax', $locale) }} {{ $shipment->collection_tracking_code }}
                                    </td>
                                    <td style="{{ Setting::get('billing_customers_pdf_provider_trk') ? 'width: 10px' : 'width: 44px'  }}">
                                        {{ money($shipment->collection_price, Setting::get('app_currency')) }}
                                    </td>
                                </tr>
                            @endif

                            @foreach($shipment->expenses as $expense)
                                <?php
                                    if(!$shipment->ignore_billing && !$shipment->payment_at_recipient) {
                                        if($shipment->isExport()) {
                                            $totalExpensesWithoutVat+= $expense->pivot->subtotal;
                                        }

                                        $totalExpenses+= $expense->pivot->subtotal;
                                    }
                                ?>
                                <tr>
                                    <td style="width: {{ Setting::get('billing_customers_pdf_provider_trk') ? '11mm' : '20px'  }}; text-align: left">
                                        {{ $expense->code }}
                                    </td>
                                    <td style="width: {{ Setting::get('billing_customers_pdf_provider_trk') ? '130mm' : ''  }}">
                                        {{ $expense->name }}
                                        (<i>{{ translation('admin\global.word.quantity', $locale) }}:</i> {{ $expense->pivot->qty }})
                                    </td>
                                    <td style="{{ Setting::get('billing_customers_pdf_provider_trk') ? 'width: 10px' : 'width: 44px'  }}">
                                        @if($period == 'single' || !$shipment->ignore_billing)
                                            @if($shipment->payment_at_recipient)
                                                <?php $hasPaymentAtRecipient = true; ?>
                                                0.00*
                                            @else
                                                <?php $totalExpenses+= $expense->pivot->subtotal; ?>
                                                {{ money($expense->pivot->subtotal, Setting::get('app_currency')) }}
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
            @endif
           @endif {{-- IF !collection --}}
        @endforeach
    </table>
    <div style="width: 100%">
        @if($hasPaymentAtRecipient)
            <div style="width: 14%" class="pull-left">*{{ translation('admin\global.word.payment_at_recipient', $locale) }}</div>
        @endif

        @if($period == 'single')
        <h4 class="pull-right text-right m-t-0" style="width: 100%">
            <div style="width: 140px; float: right">
                <small>Total Nac./Import.:<br/>
                <b class="bold" style="color: #000;">{{ money(($totalShipments - $totalWithoutVat) + ($totalExpenses - $totalExpensesWithoutVat), Setting::get('app_currency')) }}</b>
                </small>
            </div>
            <div style="width: 140px; float: right">
                <small>Total Export.:<br/>
                <b class="bold" style="color: #000;">{{ money($totalWithoutVat + $totalExpensesWithoutVat, Setting::get('app_currency')) }}</b>
                </small>
            </div>
            <div style="width: 100px; float: right">
                <small style="width: 100px; float: left">{{ translation('admin\global.word.expenses', $locale) }}: <br/>
                    <b class="bold" style="color: #000;">{{ money($totalExpenses, Setting::get('app_currency')) }}</b></small>
            </div>
            <div style="width: 100px; float: right">
                <small style="width: 100px; float: left">{{ translation('admin\global.word.env-rec', $locale) }}: <br/><b class="bold" style="color: #000;">{{ money($totalShipments, Setting::get('app_currency')) }}</b></small>
            </div>
            <div style="width: 100px; float: right">
                <small>{{ translation('admin\global.word.num-env-rec', $locale) }}: <br/><b class="bold" style="color: #000;">{{ $countTotal }}</b></small>
            </div>
        </h4>
       @else
        <h4 class="pull-right text-right m-t-0" style="width: 100%">
            <div style="width: 140px; float: right">
                <small>Total Nac./Import.:<br/>
                    <b class="bold" style="color: #000;">{{ money($billingData->billing_subtotal_with_vat, Setting::get('app_currency')) }}</b>
                </small>
            </div>
            <div style="width: 140px; float: right">
                <small>Total Export.:<br/>
                    <b class="bold" style="color: #000;">{{ money(($billingData->billing_subtotal_with_novat), Setting::get('app_currency')) }}</b>
                </small>
            </div>
            <div style="width: 100px; float: right">
                <small style="width: 100px; float: left">{{ translation('admin\global.word.expenses', $locale) }}: <br/>
                    <b class="bold" style="color: #000;">{{ money($billingData->total_expenses, Setting::get('app_currency')) }}</b></small>
            </div>
            <div style="width: 100px; float: right">
                <small style="width: 100px; float: left">{{ translation('admin\global.word.env-rec', $locale) }}: <br/><b class="bold" style="color: #000;">{{ money($billingData->total_shipments, Setting::get('app_currency')) }}</b></small>
            </div>
            <div style="width: 100px; float: right">
                <small>{{ translation('admin\global.word.num-env-rec', $locale) }}: <br/><b class="bold" style="color: #000;">{{ $billingData->count_shipments }}</b></small>
            </div>
        </h4>
        @endif
    </div>
    <div class="clearfix"></div>
    <?php $documentTotal+= ($totalShipments + $totalExpenses);?>
    @endif

    @if(!$customer->productsBought->isEmpty())
        <hr class="m-t-0 m-b-0"/>
        <h4>{{ translation('admin\global.billing.pdf.section02', $locale) }}</h4>
        <table class="table table-bordered table-pdf m-b-5" style="font-size: 6.3pt">
            <tr>
                <th>{{ translation('admin\global.word.product', $locale) }}</th>
                <th class="w-90px">{{ translation('admin\global.word.price_un', $locale) }}</th>
                <th class="w-90px">{{ translation('admin\global.word.quantity', $locale) }}</th>
                <th class="w-90px">{{ translation('admin\global.word.subtotal', $locale) }}</th>
            </tr>
            <?php $total = 0; ?>
            @foreach($customer->productsBought as $product)
                <?php $total += $product->subtotal ?>
                <tr>
                    <td>{{ $product->product->name }}</td>
                    <td>{{ money($product->price, Setting::get('app_currency')) }}</td>
                    <td>{{ $product->qty }}</td>
                    <td>{{ money($product->subtotal, Setting::get('app_currency')) }}</td>
                </tr>
            @endforeach
        </table>
        <h4 class="text-right m-t-0">
            <small>{{ translation('admin\global.word.net_price', $locale) }}:</small>
            @if($period == 'single')
            <b class="bold">{{ money($total, Setting::get('app_currency')) }}</b>
            @else
            <b class="bold">{{ money($billingData->total_products, Setting::get('app_currency')) }}</b>
            @endif
        </h4>
        <?php $documentTotal+= $total;?>
    @endif

    @if(!$customer->covenants->isEmpty())
        @if(!$shipments->isEmpty())
        <hr class="m-t-0 m-b-0"/>
        @endif
        <h4>{{ translation('admin\global.billing.pdf.section03', $locale) }}</h4>
        <table class="table table-bordered table-pdf m-b-5" style="font-size: 6.3pt">
            <tr>
                <th>Avença</th>
                <th class="w-90px">Data Início</th>
                <th class="w-90px">Data Termo</th>
                <th class="w-90px">Preço</th>
            </tr>
            <?php $total = 0; ?>
            @foreach($customer->covenants as $covenant)
                <?php $total += $covenant->amount ?>
                <tr>
                    <td>
                        {{ $covenant->description }}
                        @if($covenant->type == 'variable')
                            (Até {{ $covenant->max_shipments }} envios)
                        @endif
                    </td>
                    <td>{{ $covenant->start_date->format('Y-m-d') }}</td>
                    <td>{{ $covenant->end_date->format('Y-m-d') }}</td>
                    <td>{{ money($covenant->amount, Setting::get('app_currency')) }}</td>
                </tr>
            @endforeach
        </table>
        <h4 class="text-right m-t-0">
            <small>{{ translation('admin\global.word.net_price', $locale) }}:</small>
            @if($period == 'single')
            <b class="bold">{{ money($total, Setting::get('app_currency')) }}</b>
            @else
            <b class="bold">{{ money($billingData->total_covenants, Setting::get('app_currency')) }}</b>
            @endif
        </h4>
        <?php $documentTotal+= $total;?>
    @endif
    <hr class="m-b-10 m-t-10"/>
    <h3 class="text-right m-t-0" style="float: left">
        <small>{{ translation('admin\global.word.net_price', $locale) }}:</small>
        @if($period == 'single')
        <b class="bold">{{ money($documentTotal, Setting::get('app_currency')) }}</b>
        @else
        <b class="bold">{{ money($billingData->total_month, Setting::get('app_currency')) }}</b>
        @endif
    </h3>
</div>