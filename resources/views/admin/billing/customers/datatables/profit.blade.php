<div style="white-space: nowrap">
    @if(hasModule('statistics'))
        @if($profit > 0)
            <span class="text-green">
                <i class="fas fa-caret-up"></i> {{ money($profit, Setting::get('app_currency')) }}
            </span>
        @else
            <span class="text-red">
                <i class="fa {{ $profit == 0.00 ? '' : 'fa-caret-down'}}"></i> {{ money($profit, Setting::get('app_currency')) }}
            </span>
        @endif
    @else
        <span class="text-muted" data-toggle="tooltip" title="Conheça o lucro para este cliente. Módulo de estatísticas gerais não ativo.">N/A</span>
    @endif
</div>
