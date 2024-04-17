<?php $locale = @$customer->billing_country == 'pt' || empty(@$customer->billing_country) ? 'pt' : 'en'; ?>
@foreach ($groupedResults as $customerName => $shipments)
    <?php
    $isParticular = @$customer->is_particular;
    $period = @$period ? $period : 'single';
    $documentSubtotal = 0;
    $documentTotal = 0;
    $documentVat = 0;
    $totalShipments = 0;
    $vatRateNormal = Setting::get('vat_rate_normal');
    $vatRate = $vatRateNormal / 100;
    $collectionsAssigned = [];
    $currencySymbol = Setting::get('app_currency');
    $invoicesIds = [];
    $fuelTaxes = [];
    ?>

    <div>
        @if (!$shipments->isEmpty())
            @if ($groupByCustomer)
                <h1 style="text-transform: uppercase">{{ $customerName }}</h1>
            @else
                <h5 style="margin-bottom: 3px">{{ translation('admin/global.billing.pdf.section01', $locale) }}</h5>
            @endif
            <table class="table table-bordered table-pdf m-b-5" style="font-size: 6.3pt; border: none">
                <tr>
                    <th>{{ translation('admin/global.word.shipment', $locale) }}</th>
                    @if (Setting::get('billing_customers_pdf_provider_trk'))
                        <th>{{ translation('admin/global.word.provider_trk', $locale) }}</th>
                    @endif
                    <th style="width: 30px">{{ translation('admin/global.word.serv', $locale) }}</th>
                    <th>{{ translation('admin/global.word.reference', $locale) }}</th>
                    <th style="width: 150px">{{ translation('admin/global.word.sender_name', $locale) }}</th>
                    <th style="width: 150px">{{ translation('admin/global.word.recipient_name', $locale) }}</th>
                    <th>{{ translation('admin/global.word.remittance', $locale) }}</th>
                    @if (in_array(Setting::get('app_mode'), ['freight', 'cargo']))
                        <th style="width: 55px">{{ translation('admin/global.word.cargo_date', $locale) }}</th>
                    @endif
                    @if (in_array(Setting::get('app_mode'), ['freight', 'cargo']))
                        <th style="width: 55px">{{ translation('admin/global.word.delivery_date', $locale) }}</th>
                    @else
                        <th>{{ translation('admin/global.word.charge', $locale) }}</th>
                    @endif
                    <th class="w-100px">{{ translation('admin/global.word.obs', $locale) }}</th>
                    <th class="w-45px">{{ translation('admin/global.word.price', $locale) }}</th>
                </tr>
                <?php
                $countTotal = 0;
                $totalExpenses = 0;
                $hasPaymentAtRecipient = false;
                $totalWithoutVat = 0;
                $totalExpensesWithoutVat = 0;
                ?>

                @foreach ($shipments as $shipment)
                    {{-- @if (($shipment->is_collection && $shipment->status_id == 18) || !$shipment->is_collection) --}} {{-- ignora da listagem recolhas que tenham sido bem sucedidas --}}

                    <?php

                    $invoicesIds[] = $shipment->invoice_id;
                    $services[@$shipment->service->display_code] = @$shipment->service;

                    $isVatExempt = $shipment->isVatExempt();
                    $shipment->payment_at_recipient = $shipment->payment_at_recipient ? $shipment->payment_at_recipient : !empty($shipment->requested_by) && $shipment->requested_by != $shipment->customer_id && $shipment->customer_id != @$customer->id;
                    $totalChargePrice = $shipments->sum('charge_price');

                    if (($period == 'single' && !$shipment->payment_at_recipient) || (!$shipment->ignore_billing && !$shipment->payment_at_recipient && @$customer->id == $shipment->customer_id)) {
                        $totalShipments += $shipment->total_price;

                        if ($isVatExempt) {
                            $totalWithoutVat += $shipment->total_price;
                        } else {
                            $documentVat+= $shipment->total_price * $vatRate;
                        }


                        if($shipment->fuel_tax) {
                            $fuelSubtotalWithVat = $fuelVat = 0;
                            $fuelBasePrice = (float) @$fuelTaxes[$shipment->fuel_tax]['incidence_price'] + $shipment->total_price + $shipment->total_expenses;
                            $fuelCount     = (int) @$fuelTaxes[$shipment->fuel_tax]['count'] + 1;
                            $fuelSubtotal  = (float) @$fuelTaxes[$shipment->fuel_tax]['subtotal'] + $shipment->fuel_price;
                            $documentSubtotal+= $shipment->fuel_price;


                            if (!$isVatExempt) {
                                $fuelSubtotalWithVat = (float) @$fuelTaxes[$shipment->fuel_tax]['incidence_vat_price'] + $shipment->total_price + $shipment->total_expenses;
                                $fuelVat             = (float) @$fuelTaxes[$shipment->fuel_tax]['vat'] + ($shipment->fuel_price * $vatRate);

                                $documentVat+= ($shipment->fuel_price * $vatRate);
                            }

                            $fuelTaxes[$shipment->fuel_tax] = [
                                'tax'             => $shipment->fuel_tax,
                                'count'           => $fuelCount,
                                'incidence_price' => $fuelBasePrice,
                                'incidence_vat_price' => $fuelSubtotalWithVat,
                                'subtotal'        => $fuelSubtotal,
                                'vat'             => $fuelVat,
                                'total'           => $fuelSubtotal + $fuelVat,
                            ];
                        }
                    }

                    $countTotal++;

                    $hasCollectionExpense = false;
                    $shipment->collection_price = 0;

                    if ($shipment->collection_tracking_code) {
                        $collection = $shipment->collection_price = $shipments
                            ->filter(function ($item) use ($shipment) {
                                return $item->tracking_code == $shipment->collection_tracking_code && $item->status_id != 18;
                            })
                            ->first();

                        if ($collection) {
                            $collectionsAssigned[] = $shipment->collection_tracking_code;

                            $hasCollectionExpense = true;
                            //$countTotal++;
                            $shipment->collection_price = $collection->total_price + $collection->total_expenses;

                            /*if(!$collection->ignore_billing && !$collection->payment_at_recipient && (empty($shipment->requested_by) || $shipment->requested_by == $shipment->customer_id)) {}*/
                        }
                    }
                    ?>

                    @if (!$shipment->is_collection || ($shipment->is_collection && !in_array($shipment->tracking_code, $collectionsAssigned)))
                        <tr>
                            @if (!Setting::get('shipment_sum_expenses') && (!$shipment->expenses->isEmpty() || $hasCollectionExpense))
                                <td rowspan="2">
                                @else
                                <td>
                            @endif
                            <b class="bold">{{ $shipment->tracking_code }}</b><br />
                            @if ($shipment->type == \App\Models\Shipment::TYPE_MASTER || $shipment->children_type == \App\Models\Shipment::TYPE_MASTER)
                                @if (empty($shipment->parent_tracking_code))
                                    SRV AGRUPADO
                                @else
                                    &#8593;{{ $shipment->parent_tracking_code }}
                                @endif
                            @endif
                            <i>{{ $shipment->date }}</i>
                            @if ($shipment->start_hour)
                                <br />{{ $shipment->start_hour }}
                                @if ($shipment->end_hour)
                                    - {{ $shipment->end_hour }}
                                @endif
                            @endif
                            </td>

                            @if (Setting::get('billing_customers_pdf_provider_trk'))
                                <td>
                                    @if ($shipment->provider_tracking_code)
                                        {{ $shipment->provider_tracking_code }}
                                    @endif
                                </td>
                            @endif

                            <td class="text-center">
                                {{ @$shipment->service->display_code }}<br />
                                {{ strtoupper($shipment->recipient_country) }}
                            </td>

                            <td>
                                @if ($shipment->provider_id != 3 || config('app.source') == 'entregaki')
                                    {{ wordwrap($shipment->reference, 16, "\n", true) }}
                                @endif

                                @if (Setting::get('shipments_reference2_visible'))
                                    <br />{{ $shipment->reference2 }}
                                @endif

                                @if (Setting::get('shipments_reference3_visible'))
                                    <br />{{ $shipment->reference3 }}
                                @endif
                            </td>
                            <td>
                                @if ($shipment->sender_attn)
                                    A/C: {{ $shipment->sender_attn }}
                                    <br />
                                @endif
                                {{ $shipment->sender_name }}<br />{{ $shipment->sender_zip_code }}
                                {{ $shipment->sender_city }}
                            </td>
                            <td>
                                @if ($shipment->recipient_attn)
                                    A/C: {{ $shipment->recipient_attn }}
                                    <br />
                                @endif
                                {{ $shipment->recipient_name }}
                                <br />{{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
                            </td>
                            <td>
                                {{ $shipment->volumes }} vol.<br />
                                @if (@$shipment->service->unity == 'm3')
                                    {{ $shipment->volume_m3 }} m<sup>3</sup>
                                @elseif(@$shipment->service->unity == 'km')
                                    {{ $shipment->kms ? $shipment->kms : 0 }} km
                                @elseif(@$shipment->service->unity == 'hours')
                                    {{ $shipment->hours ? $shipment->hours : 0 }} h
                                @else
                                    {{ $shipment->weight > $shipment->volumetric_weight ? $shipment->weight : $shipment->volumetric_weight }}
                                    kg
                                @endif
                            </td>
                            @if (in_array(Setting::get('app_mode'), ['freight', 'cargo']))
                                <td>
                                    {{ $shipment->date ? $shipment->date : '' }}
                                    @if ($shipment->start_hour)
                                        <br />{{ $shipment->start_hour }}
                                    @endif
                                </td>
                            @endif
                            @if (in_array(Setting::get('app_mode'), ['freight', 'cargo']))
                                <td>
                                    {{ $shipment->delivery_date ? $shipment->delivery_date->format('Y-m-d') : '' }}
                                    @if ($shipment->end_hour)
                                        <br />{{ $shipment->end_hour }}
                                    @endif
                                </td>
                            @else
                                <td>{{ money($shipment->charge_price, $currencySymbol) }}</td>
                            @endif
                            <td>
                                @if (in_array(Setting::get('app_mode'), ['freight', 'cargo']) && $shipment->vehicle)
                                    {{ translation('admin/global.word.vehicle', $locale) }}:
                                    {{ $shipment->vehicle }}
                                    @if ($shipment->trailer)
                                        +{{ $shipment->trailer }}
                                    @endif

                                    <br />
                                @endif

                                @if ($shipment->requester_name)
                                    ## Solicitado {{ $shipment->requester_name }} ##
                                    <br />
                                @endif

                                @if ($shipment->status_id == '18')
                                    ### {{ translation('admin/global.word.pickup-failed', $locale) }} ###
                                @endif

                                {{ $shipment->obs }}
                            </td>
                            <td style="text-align: center">
                                @if ($period == 'single' || !$shipment->ignore_billing)
                                    @if ($shipment->payment_at_recipient)
                                        <?php $hasPaymentAtRecipient = true; ?>
                                        0.00*
                                    @else
                                        @if (($shipment->type == \App\Models\Shipment::TYPE_MASTER && empty($shipment->total_price)) || $shipment->total_price == 0.0)
                                        @else
                                            @if (Setting::get('shipment_sum_expenses'))
                                                <strong>{{ money($shipment->total_price + $shipment->total_expenses, $currencySymbol) }}</strong>
                                            @else
                                                <b style="font-weight: bold">{{ money($shipment->total_price, $currencySymbol) }}@if(!Setting::get('billing_show_vat') && !$isVatExempt)*@endif</b>
                                            @endif

                                            @if($shipment->fuel_price > 0.00)
                                                <small>Fuel: {{ (float) $shipment->fuel_tax }}%</small>
                                            @endif

                                            @if (Setting::get('billing_show_vat'))
                                                <br />
                                                @if ($isVatExempt)
                                                    <small><i>{{ translation('admin/global.word.vat', $locale) }}
                                                            0%</i></small>
                                                @else
                                                    <small><i>+{{ translation('admin/global.word.vat', $locale) }}
                                                            {{ $vatRateNormal }}%</i></small>
                                                @endif
                                            @endif

                                        @endif
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @if (!Setting::get('shipment_sum_expenses') && (!$shipment->expenses->isEmpty() || $hasCollectionExpense))
                            <?php

                            $colspan = 8;
                            $codeColumn = '29.5px';
                            $nameColumn = '';
                            $priceColumn = '44.9px';

                            if (in_array(Setting::get('app_mode'), ['freight', 'cargo'])) {
                                $colspan = 11;
                                $codeColumn = '23px';
                                $nameColumn = '143mm';
                                $priceColumn = '20px';
                            }

                            if (Setting::get('billing_customers_pdf_provider_trk')) {
                                $colspan = 9;
                                $codeColumn = '63.5px';
                                $nameColumn = '135.5mm';
                                $priceColumn = '8px';
                            }
                            ?>
                            <tr>
                                <td colspan="{{ $colspan }}" style="padding: 0; border: none">
                                    <table class="w-100" style="border:none">

                                        @if ($hasCollectionExpense && $shipment->collection_price > 0.0)
                                            <tr>
                                                <td style="width: {{ $codeColumn }}; text-align: center">REC</td>
                                                <td style="width: {{ $nameColumn }}">
                                                    {{ translation('admin/global.billing.pdf.pickup_tax', $locale) }}
                                                    {{ $shipment->collection_tracking_code }}
                                                </td>
                                                <td style="text-align: center; {{ $priceColumn }}">
                                                    {{ money($shipment->collection_price, $currencySymbol) }}
                                                </td>
                                            </tr>
                                        @endif

                                        @foreach ($shipment->expenses as $expense)
                                            <?php
                                            if (!$shipment->ignore_billing && !$shipment->payment_at_recipient) {
                                                if (@$shipment->service->is_mail || ($shipment->isExport() && !$isParticular)) {
                                                    $totalExpensesWithoutVat += $expense->pivot->subtotal;
                                                } else {
                                                    $documentVat+= $expense->pivot->subtotal * $vatRate;
                                                }

                                                $totalExpenses += $expense->pivot->subtotal;
                                            }
                                            ?>
                                            <tr>
                                                <td style="width: {{ $codeColumn }}; text-align: left">
                                                    {{ $expense->code }}
                                                </td>
                                                <td style="width: {{ $nameColumn }}">
                                                    {{ $expense->name }}
                                                    {{-- (<i>{{ translation('admin/global.word.quantity', $locale) }}:</i> {{ $expense->pivot->qty }}) --}}
                                                    ({{ $expense->pivot->qty }}x
                                                    {{ money($expense->pivot->price, $currencySymbol) }})
                                                </td>
                                                <td style="text-align: center; width: {{ $priceColumn }}">
                                                    @if ($period == 'single' || !$shipment->ignore_billing)
                                                        @if ($shipment->payment_at_recipient)
                                                            <?php $hasPaymentAtRecipient = true; ?>
                                                            0.00*
                                                        @else
                                                            @if ($shipment->type == \App\Models\Shipment::TYPE_MASTER && ((empty($shipment->total_price) || $shipment->total_price == 0.0) && (empty($shipment->total_expenses) || $shipment->total_expenses == 0.0)))
                                                                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                                            @else
                                                                <?php /*$totalExpenses+= $expense->pivot->subtotal; */?>
                                                                {{ money($expense->pivot->subtotal, $currencySymbol) }}
                                                            @endif
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
                @if(!Setting::get('billing_show_vat'))
                    * Acresce IVA à taxa legal em vigor.
                @endif

                @if ($hasPaymentAtRecipient)
                    <div style="width: 14%" class="pull-left">
                        *{{ translation('admin/global.word.payment_at_recipient', $locale) }}
                    </div>
                @endif

                @if ($period == 'single')
                    <h5 class="pull-right text-left m-t-0" style="width: 100%; font-size: 15px">
                        <div style="width: 80px; float: right; text-align: right">
                            <small>{{ translation('admin/global.word.subtotal', $locale) }}<br />
                                <b class="bold"
                                   style="color: #000;">{{ money($totalShipments + $totalExpenses, $currencySymbol) }}</b>
                            </small>
                        </div>
                        <div style="width: 90px; float: right;">
                            <small>{{ translation('admin/global.word.vat_incidence', $locale) }}<br/>
                                <b class="bold"
                                   style="color: #000;">
                                    {{ money(($totalShipments - $totalWithoutVat) + ($totalExpenses - $totalExpensesWithoutVat), $currencySymbol) }}</b>
                                <small style="color: #111">{{ translation('admin/global.word.vat', $locale) }} {{ Setting::get('vat_rate_normal') }}%</small>
                            </small>
                        </div>
                        <div style="width: 110px; float: right">
                            <small>{{ translation('admin/global.word.vat_incidence', $locale) }}<br />
                                <b class="bold"
                                   style="color: #000;">
                                    {{ money($totalWithoutVat + $totalExpensesWithoutVat, $currencySymbol) }}</b>
                                <small style="color: #111">{{ translation('admin/global.word.vat', $locale) }} 0%</small>
                            </small>
                        </div>
                        <div style="width: 80px; float: right;">
                            <small
                                    style="width: 150px; float: left">{{ translation('admin/global.word.taxes', $locale) }}
                                <br />
                                <b class="bold"
                                   style="color: #000;">{{ money($totalExpenses, $currencySymbol) }}</b></small>
                        </div>
                        <div style="width: 80px; float: right">
                            <small
                                    style="width: 100px; float: left">{{ translation('admin/global.word.env-rec', $locale) }}
                                <br /><b class="bold"
                                         style="color: #000;">{{ money($totalShipments, $currencySymbol) }}</b></small>
                        </div>
                        @if($totalChargePrice > 0.00)
                         <div style="width: 70px; float: right; text-align: right">
                            <small>{{ translation('admin/global.word.refunds', $locale) }}<br/>
                                <b class="bold"
                                   style="color: #000;">{{ money($totalChargePrice, $currencySymbol) }}</b>
                            </small>
                        </div>
                        @endif
                        <div style="width: 50px; float: right">
                            <small>{{ translation('admin/global.word.items', $locale) }} <br/><b
                                        class="bold"
                                        style="color: #000;">{{ $shipments->count() }}</b></small>
                        </div>
                        <div style="width: 40px; float: right;">
                            <small>Vols.<br />
                                <b class="bold" style="color: #000;">{{ $shipments->sum('volumes') }}</b>
                            </small>
                        </div>
                    </h5>
                @else
                    <h5 class="pull-right text-left m-t-0" style="width: 100%; font-size: 15px">
                        <div style="width: 80px; float: right; text-align: right;">
                            <small>{{ translation('admin/global.word.subtotal', $locale) }}<br />
                                <b class="bold"
                                   style="color: #000;">{{ money($billingData->total_shipments + $billingData->total_expenses, $currencySymbol) }}</b>
                            </small>
                        </div>
                        <div style="width: 90px; float: right;">
                           <small>{{ translation('admin/global.word.vat_incidence', $locale) }}<br/>
                                <b class="bold"
                                   style="color: #000;">
                                    {{ money($billingData->total_shipments_vat + $billingData->total_expenses_vat,$currencySymbol) }}</b>
                               <small style="color: #111">{{ translation('admin/global.word.vat', $locale) }} {{ Setting::get('vat_rate_normal') }}%</small>
                            </small>
                        </div>
                        <div style="width: 110px; float: right;">
                            <small>{{ translation('admin/global.word.vat_incidence', $locale) }}<br />
                                <b class="bold"
                                   style="color: #000;">
                                    {{ money($billingData->total_shipments_no_vat + $billingData->total_expenses_no_vat, $currencySymbol) }}</b>
                                <small style="color: #111">{{ translation('admin/global.word.vat', $locale) }} 0%</small>
                            </small>
                        </div>
                        <div style="width: 80px; float: right;">
                            <small style="width: 150px; float: left">{{ translation('admin/global.word.taxes', $locale) }}
                                <br/>
                                <b class="bold" style="color: #000;">{{ money($billingData->total_expenses, $currencySymbol) }}</b>
                            </small>
                        </div>
                        <div style="width: 80px; float: right">
                            <small
                                style="width: 100px; float: left">{{ translation('admin/global.word.env-rec', $locale) }}
                                <br /><b class="bold"
                                    style="color: #000;">{{ money($billingData->total_shipments, $currencySymbol) }}</b></small>
                        </div>
                        @if($totalChargePrice > 0.00)
                         <div style="width: 70px; float: right; text-align: right">
                            <small>{{ translation('admin/global.word.refunds', $locale) }}<br/>
                                <b class="bold"
                                   style="color: #000;">{{ money($totalChargePrice, $currencySymbol) }}</b>
                            </small>
                        </div>
                        @endif
                        <div style="width: 40px; float: right;">
                            <small>{{ translation('admin/global.word.items', $locale) }} <br />
                                <b class="bold" style="color: #000;">{{ $billingData->count_shipments }}</b>
                            </small>
                        </div>
                        <div style="width: 40px; float: right;">
                            <small>Vols.<br />
                                <b class="bold" style="color: #000;">{{ $billingData->count_shipments_volumes }}</b>
                            </small>
                        </div>
                    </h5>
                @endif
            </div>
           {{-- <div class="clearfix"></div>--}}
            <?php $documentSubtotal += $totalShipments + $totalExpenses; ?>

                @if (!empty(@$billingData->fuel_taxes) || !empty($fuelTaxes))
                    <?php
                    if(empty(@$billingData->fuel_taxes) && !empty($fuelTaxes)) {
                        @$billingData = [];
                        @$billingData['fuel_taxes'] = $fuelTaxes;
                        $billingData = (object) $billingData;
                    }
                    ?>
                    <h5 style="margin-top: -15px; margin-bottom: 3px">{{ translation('admin/global.word.fuel_taxes', $locale) }}</h5>
                    <table class="table table-bordered table-pdf m-b-5" style="font-size: 6.3pt; border: none">
                        <tr>
                            <th>{{ translation('admin/global.word.fuel_taxes', $locale) }}</th>
                            <th class="w-40px">{{ translation('admin/global.word.services', $locale) }}</th>
                            <th class="w-50px text-right">{{ translation('admin/global.word.incidence', $locale) }}</th>
                            <th class="w-70px text-right">{{ translation('admin/global.word.vat_incidence', $locale) }}</th>
                            <th class="w-50px text-right">{{ translation('admin/global.word.subtotal', $locale) }}</th>
                            <th class="w-50px text-right">{{ translation('admin/global.word.vat', $locale) }}</th>
                            <th class="w-50px text-right">{{ translation('admin/global.word.total', $locale) }}</th>
                        </tr>
                        <?php $fuelCount = $fuelIncidence = $fuelIncidenceVat = $fuelSubtotal = $fuelVat = $fuelTotal = 0 ?>
                        @foreach (@$billingData->fuel_taxes as $fuelTax)
                            <tr>
                                <td>{{ translation('admin/global.word.fuel_tax', $locale) }} - {{ @$fuelTax['tax'] }}%</td>
                                <td class="text-center">{{ @$fuelTax['count'] }}</td>
                                <td class="text-right">{{ money(@$fuelTax['incidence_price'], $currencySymbol) }}</td>
                                <td class="text-right">{{ money(@$fuelTax['incidence_vat_price'], $currencySymbol) }}</td>
                                <td class="text-right">{{ money(@$fuelTax['subtotal'], $currencySymbol) }}</td>
                                <td class="text-right">{{ money(@$fuelTax['vat'], $currencySymbol) }}</td>
                                <td class="text-right">{{ money(@$fuelTax['total'], $currencySymbol) }}</td>
                            </tr>
                            <?php
                            $fuelCount+= @$fuelTax['count'];
                            $fuelIncidence+= @$fuelTax['incidence_price'];
                            $fuelIncidenceVat+= @$fuelTax['incidence_vat_price'];
                            $fuelSubtotal+= @$fuelTax['subtotal'];
                            $fuelVat+= @$fuelTax['vat'];
                            $fuelTotal+= @$fuelTax['total'];
                            ?>
                        @endforeach
                        <tr>
                            <td class="bold" style="border: none; text-align: right">{{ translation('admin/global.word.totals', $locale) }}</td>
                            <td class="text-center bold" style="background: #f2f2f2">{{ @$fuelCount }}</td>
                            <td class="text-right bold" style="background: #f2f2f2">{{ money(@$fuelIncidence, $currencySymbol) }}</td>
                            <td class="text-right bold" style="background: #f2f2f2">{{ money(@$fuelIncidenceVat, $currencySymbol) }}</td>
                            <td class="text-right bold" style="background: #f2f2f2">{{ money(@$fuelSubtotal, $currencySymbol) }}</td>
                            <td class="text-right bold" style="background: #f2f2f2">{{ money(@$fuelVat, $currencySymbol) }}</td>
                            <td class="text-right bold" style="background: #f2f2f2">{{ money(@$fuelTotal, $currencySymbol) }}</td>
                        </tr>
                    </table>
                @endif
        @endif


        @if (@$customer)
            @if (!$customer->productsBought->isEmpty() && $billingData->total_products > 0.0)
                <h5 style="margin-bottom: 3px">{{ translation('admin/global.billing.pdf.section02', $locale) }}</h5>
                <table class="table table-bordered table-pdf m-b-5" style="font-size: 6.3pt; border: none">
                    <tr>
                        <th class="w-50px">{{ translation('admin/global.word.reference', $locale) }}</th>
                        <th>{{ translation('admin/global.word.product', $locale) }}</th>
                        <th class="w-50px text-right">{{ translation('admin/global.word.price_un', $locale) }}</th>
                        <th class="w-30px text-center">{{ translation('admin/global.word.qty', $locale) }}</th>
                        <th class="w-50px text-right">{{ translation('admin/global.word.subtotal', $locale) }}</th>
                        <th class="w-50px text-right">{{ translation('admin/global.word.vat', $locale) }}</th>
                        <th class="w-50px text-right">{{ translation('admin/global.word.total', $locale) }}</th>
                    </tr>
                    <?php $price = $qty = $subtotal = $vat = $total = 0; ?>
                    @foreach ($customer->productsBought as $product)
                        <?php
                        $price+= $product->price;
                        $qty+= $product->qty;
                        $subtotal+=$product->subtotal;
                        $vat+= $product->vat;
                        $total+= $product->total;
                        ?>
                        <tr>
                            <td>
                                {{ @$product->product->ref }}
                            </td>
                            <td>
                                {{ @$product->product->name }}
                                @if ($product->obs)
                                    <br />
                                    <i>{{ $product->obs }}</i>
                                @endif
                            </td>
                            <td class="text-right">{{ money($product->price, $currencySymbol) }}</td>
                            <td class="text-center">{{ $product->qty }}</td>
                            <td class="text-right">{{ money($product->subtotal, $currencySymbol) }}</td>
                            <td class="text-right">{{ money($product->vat, $currencySymbol) }}</td>
                            <td class="text-right">{{ money($product->total, $currencySymbol) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" class="bold" style="border: none; text-align: right">{{ translation('admin/global.word.totals', $locale) }}</td>
                        <td class="text-right bold" style="background: #f2f2f2">{{ money($price, $currencySymbol) }}</td>
                        <td class="text-center bold" style="background: #f2f2f2">{{ $qty }}</td>
                        <td class="text-right bold" style="background: #f2f2f2">{{ money($subtotal, $currencySymbol) }}</td>
                        <td class="text-right bold" style="background: #f2f2f2">{{ money($vat, $currencySymbol) }}</td>
                        <td class="text-right bold" style="background: #f2f2f2">{{ money($total, $currencySymbol) }}</td>
                    </tr>
                </table>
                <?php $documentSubtotal += $total; ?>
            @endif

            @if (@$customer && !$customer->covenants->isEmpty() && $billingData->total_covenants > 0.0)
                <h5 style="margin-bottom: 3px">{{ translation('admin/global.billing.pdf.section03', $locale) }}</h5>
                <table class="table table-bordered table-pdf m-b-5" style="font-size: 6.3pt; border: none">
                    <tr>
                        <th>{{ translation('admin/global.word.covenant', $locale) }}</th>
                        <th class="w-90px">{{ translation('admin/global.word.start_date', $locale) }}</th>
                        <th class="w-90px">{{ translation('admin/global.word.end_date', $locale) }}</th>
                        <th class="w-50px text-right">{{ translation('admin/global.word.subtotal', $locale) }}</th>
                    </tr>
                    <?php $total = 0; ?>
                    @foreach ($customer->covenants as $covenant)
                        <?php $total += $covenant->amount; ?>
                        <tr>
                            <td>
                                {{ $covenant->description }}
                                @if ($covenant->type == 'variable')
                                    (Até {{ $covenant->max_shipments }} envios)
                                @endif
                            </td>
                            <td>{{ $covenant->start_date->format('Y-m-d') }}</td>
                            <td>{{ $covenant->end_date->format('Y-m-d') }}</td>
                            <td class="text-right">{{ money($covenant->amount, $currencySymbol) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="3" class="bold" style="border: none; text-align: right">
                            {{ translation('admin/global.word.totals', $locale) }}
                        </td>
                        <td class="text-right bold" style="background: #f2f2f2">{{ money($total, $currencySymbol) }}</td>
                    </tr>
                </table>
                <?php $documentSubtotal += $total; ?>
            @endif
        @endif

        @if (!$groupByCustomer)
            <?php
            $invoicesIds = array_unique($invoicesIds);
            $invoices = \App\Models\Invoice::whereIn('id', $invoicesIds)->get();
            ?>
            <hr class="m-b-10 m-t-10" />
            <div style="margin-top: -6px">
                @if (!$invoices->isEmpty())
                    <h4 class="text-left m-t-0" style="float: left; width: 170px">
                        <small>Doc. {{ translation('admin/global.word.associate', $locale) }}</small><br />
                        @foreach ($invoices as $invoice)
                            @if ($invoice->doc_type != 'nodoc')
                                <small style="color: #000">{{ $invoice->doc_series }} {{ $invoice->doc_id }}
                                    ({{ $invoice->doc_date }})
                                </small><br />
                            @endif
                        @endforeach
                    </h4>
                @endif
                @if (@$invoice_type != 'nodoc')
                    <h4 class="text-right m-t-0" style="float: right; width: 110px;">
                        <small>{{ translation('admin/global.word.total', $locale) }}</small><br />
                        @if ($period == 'single')
                            <b class="bold">{{ money($documentSubtotal + $documentVat, $currencySymbol) }}</b>
                        @else
                            <b class="bold">{{ money($billingData->document_total, $currencySymbol) }}</b>
                        @endif
                    </h4>

                    <h4 class="text-right m-t-0" style="float: right;  width: 100px;">
                        <small>{{ strtoupper(translation('admin/global.word.vat', $locale)) }}</small><br />
                        @if ($period == 'single')
                            <b class="bold">{{ money($documentVat, $currencySymbol) }}</b>
                        @else
                            <b class="bold">{{ money($billingData->document_vat, $currencySymbol) }}</b>
                        @endif
                    </h4>
                @endif
                <h4 class="text-right m-t-0" style="float: right; width: 130px;">
                    <small>{{ translation('admin/global.word.subtotal', $locale) }}</small><br />
                    @if ($period == 'single')
                        <b class="bold">{{ money($documentSubtotal, $currencySymbol) }}</b>
                    @else
                        <b class="bold">{{ money($billingData->document_subtotal, $currencySymbol) }}</b>
                    @endif
                </h4>
            </div>
        @endif
    </div>
@endforeach

<h4><small>{{ translation('admin/global.word.subtitle', $locale) }}</small></h4>
@foreach ($services as $item)
    <span>&bull; {{ $item->display_code . ' - ' . $item->name }}</span>
    @if(!$loop->last)
        <span>&nbsp;</span>
    @endif
@endforeach