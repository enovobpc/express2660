@if(@$statusStatistics)
    <table class="table table-condensed m-0" style="margin-top: -1px">
        <tr>
            <th class="bg-gray">@trans('Serviço')</th>
            <th class="text-center bg-gray w-40px">
                <i class="fas fa-clock" data-toggle="tooltip" title="Pendentes/Não vistos"></i>
            </th>
            <th class="text-center bg-yellow w-40px">
                <i class="fas fa-check" data-toggle="tooltip" title="Aceites/Aguardam Execução"></i>
            </th>
            <th class="text-center bg-purple w-40px">
                <i class="fas fa-dolly" data-toggle="tooltip" title="A recolher"></i>
            </th>
            <th class="text-center bg-blue w-40px">
                <i class="fas fa-truck" data-toggle="tooltip" title="Em Transporte/Distribuição"></i>
            </th>
            <th class="text-center bg-green w-40px">
                <i class="fas fa-clipboard-check" data-toggle="tooltip" title="Entregues"></i>
            </th>
            <th class="text-center bg-red w-40px">
                <i class="fas fa-exclamation-triangle" data-toggle="tooltip" title="Incidências"></i>
            </th>
        </tr>
    </table>

    <div class="nicescroll" style="height: 147px; overflow-y: scroll">
        <table class="table table-condensed m-0">
            <?php $totals = [
                'pending' => 0,
                'accepted' => 0,
                'accepted' => 0,
                'pickup' => 0,
                'transit' => 0,
                'delivered' => 0,
                'incidence' => 0
            ];
            ?>
            @foreach($statusStatistics as $serviceId => $counters)
            <?php
                $service = $services->filter(function($item) use($serviceId) { return $item->id == $serviceId; })->first();
                $totals['pending']+= @$counters['pending'];
                $totals['accepted']+= @$counters['accepted'];
                $totals['pickup']+= @$counters['pickup'];
                $totals['transit']+= @$counters['transit'];
                $totals['delivered']+= @$counters['delivered'];
                $totals['incidence']+= @$counters['incidence'];
            ?>
            <tr>
                <td>
                    @if($service)
                    <span data-toggle="tooltip" title="{{ @$service->name }}">
                    {!! @$service->display_code ? $service->display_code : '<i>Sem serviço</i>' !!}
                    </span>
                    @else
                        <i>@trans('Sem serviço')</i>
                    @endif
                </td>
                <td class="text-center w-40px" style="background: #f2f2f2; {{ $counters['pending'] ? : 'opacity: 0.4' }}">{{ $counters['pending'] }}</td>
                <td class="text-center w-40px" style="background: #fff6db; {{ $counters['accepted'] ? : 'opacity: 0.4' }}">{{ $counters['accepted'] }}</td>
                <td class="text-center w-40px" style="background: #f1edff; {{ $counters['pickup'] ? : 'opacity: 0.4' }}">{{ $counters['pickup'] }}</td>
                <td class="text-center w-40px" style="background: #c6e8ff; {{ $counters['transit'] ? : 'opacity: 0.4' }}">{{ $counters['transit'] }}</td>
                <td class="text-center w-40px" style="background: #d7f4dc; {{ $counters['delivered'] ? : 'opacity: 0.4' }}">{{ $counters['delivered'] }}</td>
                <td class="text-center w-40px" style="background: #ffe5e5; {{ $counters['incidence'] ? : 'opacity: 0.4' }}">{{ $counters['incidence'] }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    <table class="table table-condensed m-0">
        <tr>
            <td class="bg-gray bold">@trans('TOTAL')</td>
            <td class="text-center w-40px bg-gray bold">{{ @$totals['pending'] }}</td>
            <td class="text-center w-40px bg-gray bold">{{ @$totals['accepted'] }}</td>
            <td class="text-center w-40px bg-gray bold">{{ @$totals['pickup'] }}</td>
            <td class="text-center w-40px bg-gray bold">{{ @$totals['transit'] }}</td>
            <td class="text-center w-40px bg-gray bold">{{ @$totals['delivered'] }}</td>
            <td class="text-center w-40px bg-gray bold">{{ @$totals['incidence'] }}</td>
        </tr>
    </table>
    <div class="row row-5">
        <div class="col-xs-3">
            <h4 class="text-center m-t-5 text-yellow" style="line-height: 21px; margin: 0; height: 51px;">
                <small>@trans('Aceites')</small><br/>
                <b><i class="fas fa-check"></i> {{ @$totals['accepted'] }}</b>
            </h4>
        </div>
        <div class="col-xs-3">
            <h4 class="text-center m-t-5 text-purple" style="line-height: 21px; margin: 0; height: 51px;">
                <small>@trans('Em Recolha')</small><br/>
                <b><i class="fas fa-dolly"></i> {{ @$totals['pickup'] }}</b>
            </h4>
        </div>
        <div class="col-xs-3">
            <h4 class="text-center m-t-5 text-blue" style="line-height: 21px; margin: 0; height: 51px;">
                <small>@trans('Transporte')</small><br/>
                <b><i class="fas fa-shipping-fast"></i> {{ @$totals['transit'] }}</b>
            </h4>
        </div>
        <div class="col-xs-3">
            <h4 class="text-center m-t-5 text-red" style="line-height: 21px; margin: 0; height: 51px;">
                <small>@trans('Incidência')</small><br/>
                <b><i class="fas fa-exclamation-triangle"></i> {{ @$totals['incidence'] }}</b>
            </h4>
        </div>
    </div>
@else
    <div class="text-center m-t-100" style="color: #bbb;">
        <i class="fas fa-thumbs-down fs-25 m-b-5"></i><br/>@trans('Não há serviços a decorrer')
    </div>
@endif