@if(hasModule('statistics'))
    @if($row->gain_money >= 0.00)
        <span class="text-green">
            <i class="fas fa-caret-up"></i> <b>{{ money($row->gain_money, Setting::get('app_currency')) }}</b>
            ({{ money($row->gain_percent, '%') }})
        </span>
    @else
        <span class="text-red">
            <i class="fas fa-caret-down"></i> <b>{{ money($row->gain_money, Setting::get('app_currency')) }}</b>
            ({{ money($row->gain_percent, '%') }})
        </span>
    @endif
@else
    <span class="text-muted" data-toggle="tooltip" title="Conheça o lucro para este envio. Módulo de estatísticas gerais não ativo.">N/A</span>
@endif