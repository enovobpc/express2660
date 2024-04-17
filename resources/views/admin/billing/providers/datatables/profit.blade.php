<div style="white-space: nowrap">
    @if(hasModule('statistics'))
        @if(@$profit >= 0)
            <span class="text-green">
                <i class="fas fa-caret-up"></i> <b>{{ money(@$profit, Setting::get('app_currency')) }}</b>
                @if($row->total != 0.00)
                    ({{ money((@$profit * 100) / $row->total, '', 0) }}%)
                @else
                (0%)
                @endif
            </span>
        @else
            <span class="text-red">
                <i class="fas fa-caret-down"></i> <b>{{ money(@$profit, Setting::get('app_currency')) }}</b>
                @if($row->total != 0.00)
                    ({{ money(($row->total * 100) / @$profit, '', 0) }}%)
                @else
                (0%)
                @endif
            </span>
        @endif
    @else
            <span class="text-muted" data-toggle="tooltip" title="Conheça o lucro para este fornecedor. Módulo de estatísticas gerais não ativo.">N/A</span>
    @endif
</div>
