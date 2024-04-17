@if(!hasModule('statistics'))
    @include('admin.partials.denied_message')
@else
<div class="box no-border">
    <div class="box-body">
        <div class="col-sm-12">
            <h4 class="text-blue text-uppercase fs-15 m-t-5">Importações e Exportações (Sem Encargos)</h4>
        </div>
        <div class="col-sm-4">
            <div style="height: 145px; border: 1px solid #ddd; overflow-y: scroll;">
            <table class="table table-dashed table-hover table-condensed m-0">
                <thead>
                    <tr>
                        <th class="bg-gray">Origem</th>
                        <th class="bg-gray w-80px text-center">Total</th>
                        <th class="bg-gray w-80px text-center">Nº Vol.</th>
                        <th class="bg-gray w-80px text-center">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Nacional</td>
                        <td class="text-center">{{ $customer->count_shipments_nacional }}</td>
                        <td class="text-center">{{ $customer->count_shipments_nacional_volumes }}</td>
                        <td class="text-center">{{ money($customer->total_shipments_nacional, Setting::get('app_currency')) }}</td>
                    </tr>
                    <tr>
                        <td>Importações</td>
                        <td class="text-center">{{ $customer->count_shipments_import }}</td>
                        <td class="text-center">{{ $customer->count_shipments_import_volumes }}</td>
                        <td class="text-center">{{ money($customer->total_shipments_import, Setting::get('app_currency')) }}</td>
                    </tr>
                    <tr>
                        <td>Exportações</td>
                        <td class="text-center">{{ $customer->count_export }}</td>
                        <td class="text-center">{{ $customer->count_export_volumes }}</td>
                        <td class="text-center">{{ money($customer->total_export, Setting::get('app_currency')) }}</td>
                    </tr>
                    <tr>
                        <td class="bold">TOTAL</td>
                        <td class="bold text-center">{{ $customer->count_shipments }}</td>
                        <td class="bold text-center">{{ $customer->count_shipments_volumes }}</td>
                        <td class="bold text-center">{{ money($customer->total_shipments, Setting::get('app_currency')) }}</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
        <div class="col-sm-4">
            <div style="height: 145px; border: 1px solid #ddd; overflow-y: scroll;">
            <table class="table table-dashed table-hover table-condensed m-0">
                <thead>
                    <tr>
                        <th class="bg-gray">ORIGEM IMPORTAÇÕES</th>
                        <th class="bg-gray w-80px text-center">Total</th>
                        <th class="bg-gray w-80px text-center">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($importShipments as $country => $shipment)
                    <tr>
                        <td><i class="flag-icon flag-icon-{{$country}}"></i> {{ trans('country.' . $country) }}</td>
                        <td class="text-center">{{ $shipment->count() }}</td>
                        <td class="text-center">{{ money($shipment->sum('total_price'), Setting::get('app_currency')) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3">Sem importações</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
        <div class="col-sm-4">
            <div style="height: 145px; border: 1px solid #ddd; overflow-y: scroll;">
            <table class="table table-dashed table-condensed table-hover m-0">
                <thead>
                    <tr>
                        <th class="bg-gray">DESTINO EXPORTAÇÕES</th>
                        <th class="bg-gray w-80px text-center">Total</th>
                        <th class="bg-gray w-80px text-center">Valor</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($exportShipments as $country => $shipment)
                    <tr>
                        <td><i class="flag-icon flag-icon-{{$country}}"></i> {{ trans('country.' . $country) }}</td>
                        <td class="text-center">{{ $shipment->count() }}</td>
                        <td class="text-center">{{ money($shipment->sum('total_price'), Setting::get('app_currency')) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Sem exportações</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            </div>
        </div>
        <div class="col-sm-12">
            <h4 class="form-divider">Envios por tipo de serviço</h4>
            <table class="table table-dashed table-condensed">
                <thead>
                    <tr>
                        <th rowspan="2"></th>
                        <th colspan="2" class="text-center">A cobrar IVA</th>
                        <th colspan="2" class="text-center">Isento de IVA</th>
                    </tr>
                    <tr>
                        <th class="text-center">Total</th>
                        <th class="text-center">Valor</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($typeOfServices as $serviceName => $values)
                    <tr>
                        <td>{{ $serviceName }}</td>
                        <td class="text-center">{{ $values['count_vat'] }}</td>
                        <td class="text-center">{{ money($values['total_vat'], Setting::get('app_currency')) }}</td>
                        <td class="text-center">{{ $values['count_no_vat'] }}</td>
                        <td class="text-center">{{ money($values['total_no_vat'], Setting::get('app_currency')) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif