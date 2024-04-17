<div class="compare-title">
    <span class="pull-right close-comparator"><i class="fas fa-times"></i></span>
    <h4 class="pull-left m-t-0 m-b-10"><i class="fas fa-coins"></i> @trans('Comparador de Custos')</h4>
    <div class="clearfix"></div>
</div>
<table>
    <tr>
        <th>@trans('Fornecedor')</th>
        <th class="text-right">@trans('Preço')</th>
        <th class="text-right">@trans('Custo')</th>
        <th class="text-right">@trans('Ganho')</th>
        <th class="w-1"></th>
    </tr>
    @foreach($providersPrices as $key => $provider)
    <tr style="{{ $provider['cost'] == 0.00 ? 'opacity:0.3;' : '' }}">
        <td>{{ $provider['name'] }}</td>

        @if($provider['cost'] == 0.00)
            <td class="text-right">-.--</td>
            <td class="text-right">-.--</td>
            <td class="text-right">-.--</td>
        @else
            <td class="text-right">
                {{ money($provider['total']) }}
            </td>
            <td class="text-right">
                {{ money($provider['cost']) }}</td>
            <td class="text-right">
                @if($provider['balance'] > 0.00)
                    <span class="text-green">
                        <i class="fas fa-caret-up"></i> {{ money($provider['balance']) }}
                    </span>
                @else
                    <span class="text-red">
                        <i class="fas fa-caret-down"></i> {{ money($provider['balance']) }}
                    </span>
                @endif
            </td>
        @endif
        <td><button class="btn btn-xs btn-default" data-provider="{{ $provider['id'] }}">@trans('Escolher')</button></td>
    </tr>
    @endforeach
</table>