<div class="row">
    <div class="col-sm-12">
        <div>
            <div class="box-header bg-gray">
                <h3 class="box-title">Comissões por Comercial</h3>
            </div>
            <div class="box-body p-0">
                <table class="table table-hover m-0">
                    <thead>
                    <tr>
                        <th class="bg-gray-light">Comercial</th>
                        <th class="w-70px text-right bg-gray-light">Nº Env.</th>
                        <th class="w-70px text-right bg-gray-light">Nº Vol.</th>
                        <th class="w-85px text-right bg-gray-light" style="border-left: 1px solid #ccc;">Envios</th>
                        <th class="w-85px text-right bg-gray-light">Avenças</th>
                        <th class="w-70px text-right bg-gray-light">Outros</th>
                        <th class="w-90px text-right bg-gray-light" style="border-left: 1px solid #ccc;">Faturação</th>
                        <th class="w-90px text-right bg-gray-light">Recibos</th>
                        <th class="w-80px text-right bg-gray-light" style="border-left: 1px solid #ccc;">Comissão</th>
                        <th class="w-60px text-right bg-gray-light">Total</th>
                        <th class="w-100px text-center bg-gray-light">Ações</th>
                    </tr>
                    </thead>
                </table>
                <div class="table-responsive" style="border: 1px solid #eee; overflow: scroll;">
                    <table class="table table-hover m-0">
                        <tbody>
                        <?php
                        $count = 0;
                        $volumes = 0;
                        $totalPrice = 0;
                        $totalCovenants = 0;
                        $totalProducts = 0;
                        $total = 0;
                        $totalReceipts = 0;
                        $totalCommissions = 0;
                        ?>
                        @foreach($salesCommercial as $sellerId => $shipment)
                            <?php
                            $count+= @$shipment['count'];
                            $volumes+= @$shipment['volumes'];
                            $totalPrice+= @$shipment['price'];
                            $totalCovenants+= @$shipment['covenants'];
                            $totalProducts+= @$shipment['products'];
                            $total+= @$shipment['total'];
                            $totalReceipts+= @$shipment['receipts'];
                            $totalCommissions+= @$shipment['comission_total'];

                            ?>
                            <tr data-vol="{{ @$shipment['volumes'] }}" data-count="{{ @$shipment['count'] }}" data-total="{{ @$shipment['total'] }}">
                                <td>{!! $shipment['name'] ? $shipment['name'] : '<i>Sem comercial associado.</i>' !!}</td>
                                <td class="w-70px text-right">{{ @$shipment['count'] }}</td>
                                <td class="w-70px text-right">{{ @$shipment['volumes'] }}</td>
                                <td class="w-85px text-right" style="border-left: 1px solid #ccc;">{{ money(@$shipment['price']) }}</td>
                                <td class="w-85px text-right">{{ money(@$shipment['covenants']) }}</td>
                                <td class="w-70px text-right">{{ money(@$shipment['products']) }}</td>
                                <td class="w-90px text-right bold" style="border-left: 1px solid #ccc;">{{ money(@$shipment['total']) }}</td>
                                <td class="w-90px text-right">{{ money(@$shipment['receipts']) }}</td>
                                <td class="w-80px text-right" style="border-left: 1px solid #ccc;">{{ money(@$shipment['comission_percent'], '%') }}</td>
                                <td class="w-60px text-right bold text-blue">{{ money(@$shipment['comission_total']) }}</td>
                                <td class="w-100px text-right bold text-blue">
                                    <a href="{{ route('admin.export.billing', ['start_date' => $startDate, 'end_date' => $endDate, 'seller' => $sellerId]) }}"
                                       class="btn btn-xs btn-default"
                                       target="_blank">
                                        <i class="fas fa-file-excel"></i> Exportar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <table class="table table-hover m-0">
                    <thead>
                    <tr>
                        <th class="bg-gray-light">Total</th>
                        <th class="w-70px text-right bg-gray-light">{{ $count }}</th>
                        <th class="w-70px text-right bg-gray-light">{{ $volumes }}</th>
                        <th class="w-85px text-right bg-gray-light" style="border-left: 1px solid #ccc;">{{ money($totalPrice) }}</th>
                        <th class="w-85px text-right bg-gray-light">{{ money($totalCovenants) }}</th>
                        <th class="w-70px text-right bg-gray-light">{{ money($totalProducts) }}</th>
                        <th class="w-90px text-right bg-gray-light" style="font-weight: bold; border-left: 1px solid #ccc;">{{ money($total) }}</th>
                        <th class="w-90px text-right bg-gray-light">{{ money($totalReceipts) }}</th>
                        <th class="w-80px text-right bg-gray-light" style="border-left: 1px solid #ccc;"></th>
                        <th class="w-60px text-right bg-gray-light text-blue" style="font-weight: bold">{{ money($totalCommissions) }}</th>
                        <th class="w-100px text-right bg-gray-light"></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="m-t-10 row">
    <div class="col-sm-12">
        <div>
            <div class="box-header bg-gray">
                <h3 class="box-title">Comissões por Motorista</h3>
            </div>
            <div class="box-body p-0">
                <table class="table table-hover m-0">
                    <thead>
                    <tr>
                        <th class="bg-gray-light">Motorista</th>
                        <th class="w-70px text-right bg-gray-light">Nº Env.</th>
                        <th class="w-70px text-right bg-gray-light">Nº Vol.</th>
                        {{-- <th class="w-85px text-right bg-gray-light" style="border-left: 1px solid #ccc;">Envios</th>
                        <th class="w-85px text-right bg-gray-light">Avenças</th>
                        <th class="w-70px text-right bg-gray-light">Outros</th> --}}
                        <th class="w-90px text-right bg-gray-light" style="border-left: 1px solid #ccc;">Faturação</th>
                        {{-- <th class="w-90px text-right bg-gray-light">Recibos</th> --}}
                        <th class="w-80px text-right bg-gray-light" style="border-left: 1px solid #ccc;">Comissão</th>
                        <th class="w-60px text-right bg-gray-light">Total</th>
                        <th class="w-100px text-center bg-gray-light">Ações</th>
                    </tr>
                    </thead>
                </table>
                <div class="table-responsive" style="border: 1px solid #eee; overflow: scroll;">
                    <table class="table table-hover m-0">
                        <tbody>
                         <?php
                        $count = 0;
                        $volumes = 0;
                        $totalPrice = 0;
                        $totalCovenants = 0;
                        $totalProducts = 0;
                        $total = 0;
                        $totalReceipts = 0;
                        $totalCommissions = 0;
                        ?>
                        @foreach($salesOperators as $operatorId => $shipment)
                            @php

                            $count+= @$shipment['count'];
                            $volumes+= @$shipment['volumes'];
                            $totalPrice+= @$shipment['price'];
                            $totalCovenants+= @$shipment['covenants'];
                            $totalProducts+= @$shipment['products'];
                            $total+= @$shipment['total'];
                            $totalReceipts+= @$shipment['receipts'];
                            $totalCommissions+= @$shipment['comission_total'];

                            @endphp
                            <tr data-vol="{{ @$shipment['volumes'] }}" data-count="{{ @$shipment['count'] }}" data-total="{{ @$shipment['total'] }}">
                                <td>{!! $shipment['name'] ? $shipment['name'] : '<i>Sem comercial associado.</i>' !!}</td>
                                <td class="w-70px text-right">{{ @$shipment['count'] }}</td>
                                <td class="w-70px text-right">{{ @$shipment['volumes'] }}</td>
                                {{-- <td class="w-85px text-right" style="border-left: 1px solid #ccc;">{{ money(@$shipment['price']) }}</td>
                                <td class="w-85px text-right">{{ money(@$shipment['covenants']) }}</td>
                                <td class="w-70px text-right">{{ money(@$shipment['products']) }}</td> --}}
                                <td class="w-90px text-right bold" style="border-left: 1px solid #ccc;">{{ money(@$shipment['total']) }}</td>
                                {{-- <td class="w-90px text-right">{{ money(@$shipment['receipts']) }}</td> --}}
                                <td class="w-80px text-right" style="border-left: 1px solid #ccc;">{{ money(@$shipment['comission_percent'], '%') }}</td>
                                <td class="w-60px text-right bold text-blue">{{ money(@$shipment['comission_total']) }}</td>
                                <td class="w-100px text-right bold text-blue">
                                    <a href="{{ route('admin.export.billing.operators.shipments', ['operator' => $operatorId, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                                       class="btn btn-xs btn-default"
                                       target="_blank">
                                        <i class="fas fa-file-excel"></i> Exportar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <table class="table table-hover m-0">
                    <thead>
                    <tr>
                        <th class="bg-gray-light">Total</th>
                        <th class="w-70px text-right bg-gray-light">{{ $count }}</th>
                        <th class="w-70px text-right bg-gray-light">{{ $volumes }}</th>
                        {{-- <th class="w-85px text-right bg-gray-light" style="border-left: 1px solid #ccc;">{{ money($totalPrice) }}</th>
                        <th class="w-85px text-right bg-gray-light">{{ money($totalCovenants) }}</th>
                        <th class="w-70px text-right bg-gray-light">{{ money($totalProducts) }}</th> --}}
                        <th class="w-90px text-right bg-gray-light" style="font-weight: bold; border-left: 1px solid #ccc;">{{ money($total) }}</th>
                        {{-- <th class="w-90px text-right bg-gray-light">{{ money($totalReceipts) }}</th> --}}
                        <th class="w-80px text-right bg-gray-light" style="border-left: 1px solid #ccc;"></th>
                        <th class="w-60px text-right bg-gray-light text-blue" style="font-weight: bold">{{ money($totalCommissions) }}</th>
                        <th class="w-100px text-right bg-gray-light"></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@if(!empty($prospectHistory))
<hr/>
<div class="row">
    <div class="col-sm-12">
        <div>
            <div class="box-header bg-gray">
                <h3 class="box-title"><i class="fas fa-lines-chart"></i> Prospeção do mês</h3>
            </div>
            <div class="box-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover m-0">
                        <tr>
                            <th class="w-180px bg-gray-light" style="border-right: 1px solid #999">Comercial</th>
                            <th class="bg-gray-light" style="border-right: 1px solid #999">Reuniões</th>
                            @foreach(trans('admin/prospects.status') as $key => $status)
                                <th class="bg-gray-light text-center">{{ $status }}</th>
                            @endforeach
                        </tr>
                        <?php $totals = []; $totalMeetings = 0; ?>
                        @foreach($prospectHistory as $commercialName => $histories)
                            <?php $totalMeetings+= $histories['meetings']; ?>
                            <tr>
                                <td style="border-right: 1px solid #999">{!! $commercialName ? $commercialName : '<i>Sem comercial associado.</i>' !!}</td>
                                <td class="text-center" style="border-right: 1px solid #999">{{ @$histories['meetings'] }}</td>
                                @foreach(trans('admin/prospects.status') as $key => $status)
                                    <?php $totals[$key] = @$totals[$key] ? $totals[$key] + @$histories[$key] : @$histories[$key] ?>
                                <td class="text-center {{ @$histories[$key] ? '' : 'text-muted' }}">{{ @$histories[$key] ? $histories[$key] : 0 }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        <tr>
                            <th class="w-180px bg-gray-light" style="border-right: 1px solid #999">TOTAIS</th>
                            <th class="text-center bg-gray-light" style="border-right: 1px solid #999">{{ $totalMeetings }}</th>
                            @foreach(trans('admin/prospects.status') as $key => $status)
                                <th class="bg-gray-light text-center">{{ @$totals[$key] ? $totals[$key] : 0 }}</th>
                            @endforeach
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
