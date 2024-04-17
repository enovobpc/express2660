<?php
$currency = Setting::get('app_currency');
$docDays        = $manifest->duration_days + @$returnManifest->duration_days;
$docKms         = $manifest->kms + @$returnManifest->kms;
$docKmsEmpty    = $manifest->kms_empty + @$returnManifest->kms_empty;

$docSubtotal = $manifest->shipments->sum('billing_subtotal')
    + @$returnManifest->shipments->sum('billing_subtotal');

$docCostSubtotal= $manifest->shipments->sum('cost_billing_subtotal')
    + @$returnManifest->shipments->sum('cost_billing_subtotal');

$docBalance     = $docSubtotal - $docCostSubtotal;
?>
<style>
    .tblabel {
        width: 51px;
        padding: 2px 3px 2px 0;
        text-align: right;
        font-size: 10px;
    }

    .tbinput {
        font-weight: bold;
        padding: 1px 2px;
        font-size: 10px;
        border-radius: 4px;
        font-size: 11px;
    }

    h5 {
        margin: 0;
        font-size: 13px;
    }

    h5 small {
        font-size: 9px;
    }

    .global-summary h5 {
        margin-top: 7px;
        font-size: 15px;
    }
    
    .global-summary h4 {
        margin: 1px 0;
        margin-top: 3px;
    }
</style>

<div class="global-summary" style="padding: 2px 2px; margin-bottom: 15px; font-size: 11px; margin-top: -30px; margin-left: 0px; border: 1px solid #ddd; border-radius: 5px;  background: #ddd">
    <div style="width: 39%; float: left;">
        <h5 style="margin-top: 5px; font-size: 12px">
            @if(!$returnManifest->exists)
                <div style="height: 7px"></div>
            @endif
            {{ strtoupper($manifest->start_location) }} - {{ strtoupper($manifest->end_location) }}
            @if($returnManifest->exists)
                <br/>
                {{ strtoupper($returnManifest->start_location) }} - {{ strtoupper($returnManifest->end_location) }}
            @endif
        </h5>
    </div>
    <div style="width: 5%; float: left;">
        <h5>
            <small>Duração</small><br/>
            {{ $docDays }}d
        </h5>
    </div>
    <div style="width: 8%; float: left; text-align: right">
        <h5>
            <small>KM Carga</small><br/>
            {{ number($docKms, 0) }}km
        </h5>
    </div>
    <div style="width: 8%; float: left; text-align: right">
        <h5>
            <small>KM Vazio</small><br/>
            {{ number($docKmsEmpty, 0) }}km
        </h5>
    </div>
    <div style="width: 13%; float: left; text-align: right">
        <h5>
            <small>Faturação</small><br/>
            {{ money($docSubtotal, $currency) }}
        </h5>
    </div>
    <div style="width: 12%; float: left; text-align: right">
        <h5>
            <small>Custos</small>
            <br/>
            {{ money($docCostSubtotal, $currency) }}
        </h5>
    </div>
    <div style="width: 14%; float: left; text-align: right; font-weight: bold;">
        <h4>
            <small>Resultado</small><br/>
            <span style="font-weight: bold">{{ $docBalance > 0.00 ? '+' :'' }}{{ money($docBalance, $currency) }}</span>
        </h4>
    </div>
</div>


<h4 style="font-weight: bold; float: left; width: 200px;">VIAGEM INICIAL</h4>
<div style="float: right; width: 450px; font-size: 13px; margin-top: 0px; text-align: right;; color: #555">
    {{ $manifest->duration_days }} dias &nbsp;&bull;&nbsp;
    {{ money($manifest->kms ? $manifest->kms : 0) }}km viagem &nbsp;&bull;&nbsp;
    {{ money($manifest->kms_empty ? $manifest->kms_empty : 0) }}km vazio &nbsp;&bull;&nbsp;
    <span style="font-weight: bold; font-size: 18px; color: black">{{ money($manifest->shipments->sum('billing_subtotal') - $manifest->shipments->sum('cost_billing_subtotal'), $currency) }}</span>
</div>
<div style="clear: both; margin-top: 15px"></div>
<div style="padding: 2px 2px; margin-bottom: 5px; font-size: 11px; margin-top: -20px; margin-left: 0px; border: 1px solid #ddd; border-radius: 5px;">
    <div style="width: 30%; float: left;">
        <table style="width: 100%; margin-bottom: 3px">
            <tr>
                <td class="tblabel">Origem</td>
                <td class="tbinput">{{ strtoupper($manifest->start_location) }}</td>
            </tr>
        </table>
        <table style="width: 100%">
            <tr>
                <td class="tblabel">Destino</td>
                <td class="tbinput">{{ strtoupper($manifest->end_location) }}</td>
            </tr>
        </table>
    </div>
    <div style="width: 30%; float: left">
        <table style="width: 100%; margin-bottom: 3px">
            <tr>
                <td class="tblabel">Motorista</td>
                <td class="tbinput">{{ @$manifest->operator->name }}</td>
            </tr>
        </table>
        <table style="width: 100%">
            <tr>
                <td class="tblabel">Acomp.</td>
                <td class="tbinput">&nbsp;{{ @$manifest->assistants_names }}</td>
            </tr>
        </table>
    </div>
    <div style="width: 18%; float: left">
        <table style="width: 100%; margin-bottom: 3px">
            <tr>
                <td class="tblabel">Viatura</td>
                <td class="tbinput">{{ $manifest->vehicle }}</td>
            </tr>
        </table>
        <table style="width: 100%">
            <tr>
                <td class="tblabel">Reboque</td>
                <td class="tbinput">{{ $manifest->trailer ? $manifest->trailer : '&nbsp;' }}</td>
            </tr>
        </table>
    </div>
    <div style="width: 20%; float: left;">
        <table style="width: 100%; margin-bottom: 3px">
            <tr>
                <td class="tblabel">Dt Início</td>
                <td class="tbinput">{{ $manifest->pickup_date ? $manifest->pickup_date->format('Y-m-d') . ' ' . $manifest->start_hour : '&nbsp;'  }}</td>
            </tr>
        </table>
        <table style="width: 100%; margin-bottom: 3px">
            <tr>
                <td class="tblabel">Data Fim</td>
                <td class="tbinput">
                    {{ $manifest->delivery_date ? $manifest->delivery_date->format('Y-m-d') . ' ' . $manifest->end_hour : '&nbsp;'  }}
                </td>
            </tr>
        </table>
    </div>
</div>
<table class="table table-bordered table-pdf font-size-7pt" style="margin-bottom: 0px; margin-top: 0">
    <tr>
        <th style="width: 50px">Ordem Carga</th>
        <th>Cliente</th>
        <th style="width: 80px">Referência</th>
        <th style="width: 80px">Mercadoria</th>
        <th style="width: 60px">Entrega</th>
        <th style="width: 50px">Preço</th>
        <th style="width: 50px">Custo</th>
    </tr>
    <?php $billingSubtotal = 0; $billingVat = 0; $billingTotal = $billingCostSubtotal = 0; ?>
    @foreach($manifest->shipments as $shipment)
        <?php

        $billingSubtotal+= $shipment->billing_subtotal;
        $billingVat+= $shipment->billing_vat;
        $billingTotal+= $shipment->billing_total;
        $billingCostSubtotal+= $shipment->cost_billing_subtotal;
        ?>
        <tr>
            <td>
                <strong style="font-weight: bold; font-size: 12px">{{ $shipment->tracking_code }}</strong><br/>
                {{ $shipment->date }} {{ $shipment->start_hour }}
            </td>
            <td>
                <b style="font-weight: bold">{{ @$shipment->customer->code }} - {{ @$shipment->customer->name }}</b><br/>
                Carga: ({{ $shipment->sender_intcode }}) {{ $shipment->sender_name }} <br/>
                Desc.: ({{ $shipment->recipient_intcode }}) {{ substr($shipment->recipient_name, 0, 30) }}
            </td>
            <td>
               {{-- @if($shipment->reference)
                    {{ $shipment->reference }}<br/>
                @endif--}}
                @if($shipment->reference2)
                    {{ $shipment->reference2 }}
                @endif
            </td>
            <td>
                @if(is_array($shipment->packaging_type) && !empty($shipment->packaging_type))
                    @foreach($shipment->packaging_type as $type => $qty)
                        <div>{{ $qty }} {{ substr(@$packTypes[$type], 0, 10) }}</div>
                    @endforeach
                @elseif($shipment->volumes)
                    @if($shipment->conferred_volumes)
                        <div class="bold" data-toggle="tooltip" title="Conferido pelo operador: {{ $shipment->conferred_volumes }}">{{ $shipment->volumes }} Vol.</div>
                    @else
                        <div>{{ $shipment->volumes }} Vol.</div>
                    @endif
                @else
                    <div class="text-muted">--- Vol.</div>
                @endif
                <div>{{ $shipment->weight }} KG</div>
            </td>
            <td>
                {{ $shipment->delivery_date->format('d-m-Y H:i:s') }}
            </td>
            <td style="text-align: right">
                <b style="font-weight: bold; font-size: 11px">{{ money($shipment->billing_subtotal, $currency) }}</b>
                @if(!$shipment->history->isEmpty())
                    <small style="font-weight: bold">CMR OK</small>
                @endif
            </td>
            <td style="text-align: right">
                {{ money($shipment->cost_billing_subtotal, $currency) }}<br/>
               {{-- <small>+{{ money($shipment->billing_subtotal - $shipment->cost_billing_subtotal, $currency) }}</small>--}}
            </td>
        </tr>
    @endforeach
</table>

<div style="width: 260px; height: 5mm;font-size: 7px; float: left;">
    <table style="margin: 0">
        <tr>
            <td>
                <h5 style="width: 100px; padding-right: 15px">
                    <small>Serv.</small><br/>
                    <span style="font-weight: bold">{{ @$manifest->shipments->count() }}</span>
                </h5>
            </td>
            <td>
                <h5 style="width: 100px; padding-right: 15px">
                    <small>Vols</small><br/>
                    <span style="font-weight: bold">{{ @$manifest->shipments->sum('volumes') }}</span>
                </h5>
            </td>
            <td style="text-align: right;">
                <h5 style="width: 100px; padding-right: 15px">
                    <small>LDM</small><br/>
                    <span style="font-weight: bold">{{ money(@$manifest->shipments->sum('ldm'), 'm') }}</span>
                </h5>
            </td>
            <td style="text-align: right;">
                <h5 style="width: 100px; padding: 0">
                    <small>Peso</small><br/>
                    <span style="font-weight: bold">{{ money(@$manifest->shipments->sum('weight'), 'kg', 0) }}</span>
                </h5>
            </td>
        </tr>
    </table>
</div>

<div style="width: 200px; height: 9mm; font-size: 10px; float: left;  margin-left: 118px;">
    <table style="text-align: right; width: 100%; float: right">
        <tr>
            <td>
                <h5 style="width: 100px; padding: 0">
                    <small>Subtotal</small><br/>
                    <span style="font-weight: bold">{{ money($billingSubtotal, $currency) }}</span>
                </h5>
            </td>

          {{--  <td>
                <h5 style="width: 100px; padding: 0">
                    <small>IVA</small><br/>
                    <span style="font-weight: bold">{{ money($billingVat, $currency) }}</span>
                </h5>
            </td>
            <td>
                <h5 style="width: 100px; padding: 0">
                    <small>Total</small><br/>
                    <span style="font-weight: bold">{{ money($billingTotal, $currency) }}</span>
                </h5>
            </td>--}}
        </tr>
    </table>
</div>
<div style="width: 150px; height: 9mm; font-size: 10px; float: left; padding: 0px 0px 0px 5px;">
    <table style="text-align: right; width: 100%; float: right">
        <tr>
            <td>
                <h5 style="width: 110px; padding: 0">
                    <small>Custo</small><br/>
                    <span style="font-weight: bold">{{ money($billingCostSubtotal, $currency) }}</span>
                </h5>
            </td>
            <td>
                <h5 style="width: 100px; padding: 0">
                    <small>Saldo</small><br/>
                    <span style="font-weight: bold">{{ money(($billingTotal - $billingCostSubtotal), $currency) }}</span>
                </h5>
            </td>
        </tr>
    </table>
</div>

@if(@$returnManifest->exists)
<br/>
<div style="clear: both"></div>
<h4 style="font-weight: bold; float: left; width: 200px">VIAGEM RETORNO</h4>
<div style="float: right; width: 450px; font-size: 13px; text-align: right; color: #555">
    {{ $returnManifest->duration_days }} dias &nbsp;&bull;&nbsp;
    {{ money($returnManifest->kms ? $returnManifest->kms : 0) }}km viagem &nbsp;&bull;&nbsp;
    {{ money($returnManifest->kms_empty ? $returnManifest->kms_empty : 0) }}km vazio &nbsp;&bull;&nbsp;
    <span style="font-weight: bold; font-size: 18px; color: black">{{ money($returnManifest->shipments->sum('billing_subtotal') - $returnManifest->shipments->sum('cost_billing_subtotal'), $currency) }}</span>
</div>
<div style="clear: both; margin-top: 15px"></div>
<div style="padding: 2px 2px; margin-bottom: 3px; font-size: 11px; margin-top: -20px; margin-left: 0px; border: 1px solid #ccc; border-radius: 5px;">
    <div style="width: 30%; float: left;">
        <table style="width: 100%; margin-bottom: 3px">
            <tr>
                <td class="tblabel">Origem</td>
                <td class="tbinput">{{ strtoupper($returnManifest->start_location) }}</td>
            </tr>
        </table>
        <table style="width: 100%">
            <tr>
                <td class="tblabel">Destino</td>
                <td class="tbinput">{{ strtoupper($returnManifest->end_location) }}</td>
            </tr>
        </table>
    </div>
    <div style="width: 30%; float: left">
        <table style="width: 100%; margin-bottom: 3px">
            <tr>
                <td class="tblabel">Motorista</td>
                <td class="tbinput">{{ @$returnManifest->operator->name }}</td>
            </tr>
        </table>
        <table style="width: 100%">
            <tr>
                <td class="tblabel">Acomp.</td>
                <td class="tbinput">&nbsp;{{ @$returnManifest->assistants_names }}</td>
            </tr>
        </table>
    </div>
    <div style="width: 18%; float: left">
        <table style="width: 100%; margin-bottom: 3px">
            <tr>
                <td class="tblabel">Viatura</td>
                <td class="tbinput">{{ $returnManifest->vehicle }}</td>
            </tr>
        </table>
        <table style="width: 100%">
            <tr>
                <td class="tblabel">Reboque</td>
                <td class="tbinput">{{ $returnManifest->trailer ? $returnManifest->trailer : '&nbsp;' }}</td>
            </tr>
        </table>
    </div>
    <div style="width: 20%; float: left;">
        <table style="width: 100%; margin-bottom: 3px">
            <tr>
                <td class="tblabel">Dt Início</td>
                <td class="tbinput">{{ $returnManifest->pickup_date ? $returnManifest->pickup_date->format('Y-m-d') . ' ' . $returnManifest->start_hour : '&nbsp;'  }}</td>
            </tr>
        </table>
        <table style="width: 100%; margin-bottom: 3px">
            <tr>
                <td class="tblabel">Data Fim</td>
                <td class="tbinput">
                    {{ $returnManifest->delivery_date ? $returnManifest->delivery_date->format('Y-m-d') . ' ' . $returnManifest->end_hour : '&nbsp;'  }}
                </td>
            </tr>
        </table>
    </div>
</div>
<table class="table table-bordered table-pdf font-size-7pt" style="margin-bottom: 0px; margin-top: 0">
    <tr>
        <th style="width: 50px">Ordem Carga</th>
        <th>Cliente</th>
        <th style="width: 80px">Referência</th>
        <th style="width: 80px">Mercadoria</th>
        <th style="width: 60px">Entrega</th>
        <th style="width: 50px">Preço</th>
        <th style="width: 50px">Custo</th>
    </tr>
    <?php $billingSubtotal = 0; $billingVat = 0; $billingTotal = 0; $billingCostSubtotal = 0; ?>
    @foreach($returnManifest->shipments as $shipment)
        <?php

        $billingSubtotal+= $shipment->billing_subtotal;
        $billingVat+= $shipment->billing_vat;
        $billingTotal+= $shipment->billing_total;
        $billingCostSubtotal+= $shipment->cost_billing_subtotal;
        ?>
        <tr>
            <td>
                <strong style="font-weight: bold; font-size: 12px">{{ $shipment->tracking_code }}</strong><br/>
                {{ $shipment->date }} {{ $shipment->start_hour }}
            </td>
            <td>
                <b style="font-weight: bold">{{ @$shipment->customer->code }} - {{ @$shipment->customer->name }}</b><br/>
                Carga: ({{ $shipment->sender_intcode }}) {{ $shipment->sender_name }} <br/>
                Desc.: ({{ $shipment->recipient_intcode }}) {{ substr($shipment->recipient_name, 0, 30) }}
            </td>
            <td>
                {{-- @if($shipment->reference)
                     {{ $shipment->reference }}<br/>
                 @endif--}}
                @if($shipment->reference2)
                    {{ $shipment->reference2 }}
                @endif
            </td>
            <td>
                @if(is_array($shipment->packaging_type) && !empty($shipment->packaging_type))
                    @foreach($shipment->packaging_type as $type => $qty)
                        <div>{{ $qty }} {{ substr(@$packTypes[$type], 0, 10) }}</div>
                    @endforeach
                @elseif($shipment->volumes)
                    @if($shipment->conferred_volumes)
                        <div class="bold" data-toggle="tooltip" title="Conferido pelo operador: {{ $shipment->conferred_volumes }}">{{ $shipment->volumes }} Vol.</div>
                    @else
                        <div>{{ $shipment->volumes }} Vol.</div>
                    @endif
                @else
                    <div class="text-muted">--- Vol.</div>
                @endif
                <div>{{ $shipment->weight }} KG</div>
            </td>
            <td>
                {{ $shipment->delivery_date->format('d-m-Y H:i:s') }}
            </td>
            <td style="text-align: right">
                <b style="font-weight: bold; font-size: 11px">{{ money($shipment->billing_subtotal, $currency) }}</b>
                @if(!$shipment->history->isEmpty())
                    <small style="font-weight: bold">CMR OK</small>
                @endif
            </td>
            <td style="text-align: right">
                {{ money($shipment->cost_billing_subtotal, $currency) }}<br/>
               {{-- <small>+{{ money($shipment->billing_subtotal - $shipment->cost_billing_subtotal, $currency) }}</small>--}}
            </td>
        </tr>
    @endforeach
</table>

<div style="width: 260px; height: 5mm;font-size: 7px; float: left;">
    <table style="margin: 0">
        <tr>
            <td>
                <h5 style="width: 100px; padding-right: 15px">
                    <small>Serv.</small><br/>
                    <span style="font-weight: bold">{{ @$returnManifest->shipments->count() }}</span>
                </h5>
            </td>
            <td>
                <h5 style="width: 100px; padding-right: 15px">
                    <small>Vols</small><br/>
                    <span style="font-weight: bold">{{ @$returnManifest->shipments->sum('volumes') }}</span>
                </h5>
            </td>
            <td style="text-align: right;">
                <h5 style="width: 100px; padding-right: 15px">
                    <small>LDM</small><br/>
                    <span style="font-weight: bold">{{ money(@$returnManifest->shipments->sum('ldm'), 'm') }}</span>
                </h5>
            </td>
            <td style="text-align: right;">
                <h5 style="width: 100px; padding: 0">
                    <small>Peso</small><br/>
                    <span style="font-weight: bold">{{ money(@$returnManifest->shipments->sum('weight'), 'kg', 0) }}</span>
                </h5>
            </td>
        </tr>
    </table>
</div>

<div style="width: 200px; height: 9mm; font-size: 10px; float: left;  margin-left: 118px;">
    <table style="text-align: right; width: 100%; float: right">
        <tr>
           {{-- <td>
                <h5 style="width: 100px; padding: 0">
                    <small>Subtotal</small><br/>
                    <span style="font-weight: bold">{{ money($billingSubtotal, $currency) }}</span>
                </h5>
            </td>

            <td>
                <h5 style="width: 100px; padding: 0">
                    <small>IVA</small><br/>
                    <span style="font-weight: bold">{{ money($billingVat, $currency) }}</span>
                </h5>
            </td>--}}
            <td>
                <h5 style="width: 100px; padding: 0">
                    <small>Total</small><br/>
                    <span style="font-weight: bold">{{ money($billingTotal, $currency) }}</span>
                </h5>
            </td>
        </tr>
    </table>
</div>
<div style="width: 150px; height: 9mm; font-size: 10px; float: left; padding: 0px 0px 0px 5px;">
    <table style="text-align: right; width: 100%; float: right">
        <tr>
            <td>
                <h5 style="width: 110px; padding: 0">
                    <small>Custo</small><br/>
                    <span style="font-weight: bold">{{ money($billingCostSubtotal, $currency) }}</span>
                </h5>
            </td>
            <td>
                <h5 style="width: 100px; padding: 0">
                    <small>Saldo</small><br/>
                    <span style="font-weight: bold">{{ money(($billingTotal - $billingCostSubtotal), $currency) }}</span>
                </h5>
            </td>
        </tr>
    </table>
</div>
@endif

<br/>
<div style="clear: both"></div>
<h4 style="font-weight: bold; float: left; width: 300px">OUTROS CUSTOS E DESPESAS</h4>
<div style="clear: both"></div>
<p style="font-size: 10px">Sem despesas emitidas</p>
{{--
<table class="table table-bordered table-pdf font-size-7pt">
    <tr>
        <th>Despesa</th>
        <th style="width: 80px">Custo</th>
    </tr>
    <tr>
        <td>Custo transporte em vazio</td>
        <td>0,00€</td>
    </tr>
    <tr>
        <td>Ordenado Motorista</td>
        <td>0,00€</td>
    </tr>
    <tr>
        <td>Ajuda de Custos</td>
        <td>0,00€</td>
    </tr>
</table>--}}
