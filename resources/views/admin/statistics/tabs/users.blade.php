<div class="row">
    <div class="col-sm-12">
        <div>
            <div class="box-header bg-gray">
                <h3 class="box-title">Valores por colaborador</h3>
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
                        @foreach($salesUsers as $sellerId => $shipment)
                            <?php
                            $count+= @$shipment['count'];
                            $volumes+= @$shipment['volumes'];
                            $totalPrice+= @$shipment['price'];
                            $totalCovenants+= @$shipment['covenants'];
                            $totalProducts+= @$shipment['products'];
                            $total+= @$shipment['total'];
                            $totalReceipts+= @$shipment['receipts'];

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