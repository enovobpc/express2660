<?php
    $documentTotal = 0;
    $totalShipments = 0;
?>
<div>
    @if(!$shipments->isEmpty())
        <h4>
            <span class="bold">{{ $customer->code. ' - '. $customer->name }}</span><br/>
            <small>{{ trans('datetime.month.'.$month) }} de {{ $year }}</small>
        </h4>
    <h5>Envios e Encargos Associados</h5>
    <table class="table table-bordered table-pdf m-b-5" style="font-size: 6.3pt;">
        <tr>
            <th>Envio</th>
            <th style="width: 30px">Serv.</th>
            <th>Referência</th>
            <th>Remetente</th>
            <th>Destinatário</th>
            <th>Remessa</th>
            <th>Cobrança</th>
            <th class="w-150px">Obs.</th>
            <th class="w-40px">Preço</th>
        </tr>
        <?php
            $countTotal = 0;
            $totalExpenses = 0;
            $hasPaymentAtRecipient = false;
            $totalWithoutVat = 0;
            $totalExpensesWithoutVat = 0;
        ?>
        @foreach($shipments as $shipment)
            @if(($shipment->is_collection && $shipment->status_id == 18) || !$shipment->is_collection)
                <?php
                if(!$shipment->ignore_billing && !$shipment->payment_at_recipient) {
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

                        if(!$collection->ignore_billing && !$collection->payment_at_recipient) {
                            $totalShipments += $collection->total_price;

                            if($collection->isExport()) {
                                $totalWithoutVat+= $collection->total_price;
                            }
                        }
                    }
                }
                ?>
            <tr>
                @if(!$shipment->expenses->isEmpty())
                <td rowspan="2">
                @else
                <td>
                @endif
                    <b class="bold">{{ $shipment->tracking_code }}</b><br/>
                    <i>{{ $shipment->date }}</i>
                </td>
                <td class="text-center">
                    {{ @$services[$shipment->service_id] }}<br/>
                    {{ strtoupper($shipment->recipient_country) }}
                </td>
                <td>
                    @if($shipment->provider_id != 3)
                        {{ $shipment->reference }}
                    @endif
                </td>
                <td>{{ str_limit($shipment->sender_name, 25) }}<br/>{{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}</td>
                <td>{{ str_limit($shipment->recipient_name, 25) }}
                    <br/>{{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}</td>
                <td>
                    {{ $shipment->volumes }} vol.<br/>
                    {{ $shipment->weight > $shipment->volumetric_weight ? $shipment->weight : $shipment->volumetric_weight }} kg
                </td>
                <td>{{ money($shipment->charge_price, Setting::get('app_currency')) }}</td>
                <td>
                    @if($shipment->status_id == '18')
                        ### RECOLHA FALHADA ###
                    @endif
                    {{ $shipment->obs }}
                </td>
                <td>
                    @if(!$shipment->ignore_billing)
                        {{ money($shipment->total_price, Setting::get('app_currency')) }}
                        @if($shipment->payment_at_recipient)
                            <?php $hasPaymentAtRecipient = true; ?>
                            *
                        @endif
                    @endif
                </td>
            </tr>
            @if(!$shipment->expenses->isEmpty() || $hasCollectionExpense)
                <tr>
                    <td colspan="9" style="padding: 0; border: none">
                        <table class="w-100" style="border:none">
                            @if($hasCollectionExpense)
                                <tr>
                                    <td style="width: 30px; text-align: center">REC</td>
                                    <td>
                                        Taxa de Recolha. Recolha N.º {{ $shipment->collection_tracking_code }}
                                    </td>
                                    <td class="w-40px">{{ money($shipment->collection_price, Setting::get('app_currency')) }}</td>
                                </tr>

                            @endif

                            @foreach($shipment->expenses as $expense)
                                <?php $totalExpenses+= $expense->pivot->subtotal ?>
                                <tr>
                                    <td style="width: 30px">{{ $expense->code }}</td>
                                    <td>
                                        {{ $expense->name }}
                                        (<i>Quantidade:</i> {{ $expense->pivot->qty }})
                                    </td>
                                    <td class="w-40px">{{ money($expense->pivot->subtotal, Setting::get('app_currency')) }}</td>
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
            <div style="width: 14%" class="pull-left">*Pag. no destino</div>
        @endif
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
                    <small style="width: 100px; float: left">Encargos: <br/>
                        <b class="bold" style="color: #000;">{{ money($totalExpenses, Setting::get('app_currency')) }}</b></small>
                </div>
                <div style="width: 100px; float: right">
                    <small style="width: 100px; float: left">Env./Rec.: <br/><b class="bold" style="color: #000;">{{ money($totalShipments, Setting::get('app_currency')) }}</b></small>
                </div>
                <div style="width: 100px; float: right">
                    <small>N.º Env./Rec: <br/><b class="bold" style="color: #000;">{{ $countTotal }}</b></small>
                </div>
            </h4>
    </div>
    <div class="clearfix"></div>
    <?php $documentTotal+= ($totalShipments + $totalExpenses);?>
    @endif

    @if(!$customer->products->isEmpty())
        <hr class="m-t-0 m-b-0"/>
        <h5>Compras e Artigos</h5>
        <table class="table table-bordered table-pdf m-b-5" style="font-size: 6.3pt">
            <tr>
                <th>Artigo</th>
                <th class="w-90px">Preço Un.</th>
                <th class="w-90px">Quantidade</th>
                <th class="w-90px">Subtotal</th>
            </tr>
            <?php $total = 0; ?>
            @foreach($customer->products as $product)
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
            <small>Total:</small>
            <b class="bold">{{ money($total, Setting::get('app_currency')) }}</b>
        </h4>
        <?php $documentTotal+= $total;?>
    @endif

    @if(!$customer->covenants->isEmpty())
        <hr class="m-t-0 m-b-0"/>
        <h5>Avenças Mensais</h5>
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
            <small>Total:</small>
            <b class="bold">{{ money($total, Setting::get('app_currency')) }}</b>
        </h4>
        <?php $documentTotal+= $total;?>
    @endif
    <hr class="m-b-10 m-t-10"/>
        <h3 class="text-right m-t-0">
            <small>Total a Pagar:</small>
            <b class="bold">{{ money($documentTotal, Setting::get('app_currency')) }}</b>
        </h3>
</div>