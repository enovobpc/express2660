<div class="modal" id="budgeter-details-{{ $service->id }}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Detalle de costes</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        @if($service->filepath)
                            <div class="cost-detail-img" style="background-image: url('{{ asset($service->filepath) }}')"></div>
                        @else
                            <div class="cost-detail-img" style="background-image: url('https://app.baltransurgente.com/assets/img/logo/logo.svg')"></div>
                        @endif
                        <div class="pull-left p-l-15">
                            <h4 class="m-0 bold">
                                <small>Servicio</small><br/>
                                {{ $service->name }}
                            </h4>

                            <p>
                                {{ @$service->provider->name }}
                            </p>
                            @if($service->is_air)
                                <div class="service-vehicle" style="color: #ff6000;text-align: left">
                                    <i class="fas fa-plane"></i> {{ trans('account/global.word.aerial') }}
                                </div>
                            @elseif($service->is_maritime)
                                <div class="service-vehicle" style="color: #0290f0;text-align: left">
                                    <i class="fas fa-ship"></i> {{ trans('account/global.word.maritime') }}
                                </div>
                            @else
                                <div class="service-vehicle" style="text-align: left">
                                    <i class="fas fa-truck"></i> {{ trans('account/global.word.terrestrial') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <h4 class="bold"><i class="fas fa-calendar-check"></i> {{ trans('account/global.word.pickup') }}</h4>
                        <p>
                            {{ @$service->pickup_date }}
                            @if($service->pickup_hour)
                                <br/>
                                {{ trans('account/budgeter.results.pickup_hour', ['hour' => $service->pickup_hour]) }}
                            @endif
                        </p>
                    </div>
                    <div class="col-sm-3">
                        <h4 class="bold"><i class="fas fa-home"></i> {{ trans('account/global.word.delivery') }}</h4>
                        <p>
                            {{ @$service->delivery_date }}
                            @if($service->delivery_hour)
                                <br/>
                                {{ trans('account/budgeter.results.delivery_hour', ['hour' => $service->delivery_hour]) }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="row m-t-20">
                    <div class="col-sm-12">
                        <table class="table table-condensed m-b-0">
                            <thead>
                                <tr>
                                    <th class="bg-gray">Tasa</th>
                                    <th class="bg-gray text-right w-70px">Precio</th>
                                    <th class="bg-gray text-right w-40px">Ctd</th>
                                    <th class="bg-gray text-right w-70px">Subtotal</th>
                                    <th class="bg-gray text-right w-70px">IVA</th>
                                    <th class="bg-gray text-right w-70px">Total</th>
                                    <th class="bg-gray text-right w-70px" style="border-left: 2px solid #333">Coste</th>
                                    <th class="bg-gray text-right w-70px">Subtotal</th>
                                    <th class="bg-gray text-right w-70px">IVA</th>
                                    <th class="bg-gray text-right w-70px">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        Envio {{ $service->name }} ({{ strtoupper(@$service->details['prices']['zone']) }}, {{ @$service->details['parcels']['taxable_weight'] }}kg)
                                    </td>
                                    <td class="text-right">{{ money(@$service->details['prices_details']['shipping']['subtotal'], '€') }}</td>
                                    <td class="text-right">1</td>
                                    <td class="text-right">{{ money(@$service->details['prices_details']['shipping']['subtotal']) }}</td>
                                    <td class="text-right">{{ money(@$service->details['prices_details']['shipping']['vat']) }}</td>
                                    <td class="text-right">{{ money(@$service->details['prices_details']['shipping']['total']) }}</td>
                                    <td class="text-right" style="border-left: 2px solid #333">{{ money(@$service->details['prices_details']['shipping']['cost_subtotal']) }}</td>
                                    <td class="text-right">{{ money(@$service->details['prices_details']['shipping']['cost_subtotal']) }}</td>
                                    <td class="text-right">{{ money(@$service->details['prices_details']['shipping']['cost_vat']) }}</td>
                                    <td class="text-right">{{ money(@$service->details['prices_details']['shipping']['cost_total']) }}</td>
                                </tr>
                                @if(!empty(@$service->details['prices']['fuel_tax']))
                                    <tr>
                                        <td>
                                            Taja Combustible
                                        </td>
                                        <td class="text-right">{{ money(@$service->details['prices']['fuel_tax'], '%') }}</td>
                                        <td class="text-right">1</td>
                                        <td class="text-right">{{ money(@$service->details['prices_details']['fuel']['subtotal']) }}</td>
                                        <td class="text-right">{{ money(@$service->details['prices_details']['fuel']['vat']) }}</td>
                                        <td class="text-right">{{ money(@$service->details['prices_details']['fuel']['total']) }}</td>
                                        <td class="text-right" style="border-left: 2px solid #333">{{ money(@$service->details['prices_details']['fuel']['cost_subtotal']) }}</td>
                                        <td class="text-right">{{ money(@$service->details['fuel']['shipping']['cost_subtotal']) }}</td>
                                        <td class="text-right">{{ money(@$service->details['fuel']['shipping']['cost_vat']) }}</td>
                                        <td class="text-right">{{ money(@$service->details['fuel']['shipping']['cost_total']) }}</td>
                                    </tr>
                                @endif
                                @if(@$service->details['expenses'])
                                    @foreach(@$service->details['expenses'] as $expense)
                                        <tr>
                                            <td>{{ $expense['name'] }}</td>
                                            <td class="text-right">{{ money($expense['price'], $expense['unity'] == 'euro' ? '€' : '%') }}</td>
                                            <td class="text-right">{{ $expense['qty'] }}</td>
                                            <td class="text-right">{{ money($expense['subtotal']) }}</td>
                                            <td class="text-right">{{ money($expense['vat']) }}</td>
                                            <td class="text-right">{{ money($expense['total']) }}</td>
                                            <td  class="text-right" style="border-left: 2px solid #333">{{ money($expense['cost_price']) }}</td>
                                            <td class="text-right">{{ money($expense['cost_subtotal']) }}</td>
                                            <td class="text-right">{{ money($expense['cost_vat']) }}</td>
                                            <td class="text-right">{{ money($expense['cost_total']) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr>
                                    <td></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right bold">{{ money(@$service->details['billing']['subtotal']) }}</td>
                                    <td class="text-right bold">{{ money(@$service->details['billing']['vat']) }}</td>
                                    <td class="text-right bold">{{ money(@$service->details['billing']['total']) }}</td>
                                    <td  class="text-right" style="border-left: 2px solid #333"></td>
                                    <td class="text-right bold">{{ money(@$service->details['costs']['subtotal']) }}</td>
                                    <td class="text-right bold">{{ money(@$service->details['costs']['vat']) }}</td>
                                    <td class="text-right bold">{{ money(@$service->details['costs']['total']) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<style>
    table .bg-gray { background-color: #e7e7e7; }
    .cost-detail-img {
        height: 80px;
        width: 80px;
        background-position: center;
        background-repeat: no-repeat;
        background-size: 90%;
        border: 1px solid #ccc;
        border-radius: 3px;
        float: left;
    }
</style>
