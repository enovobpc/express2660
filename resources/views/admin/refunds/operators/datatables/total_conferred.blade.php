@if($row->total_conferred)
    <span class="text-green bold" data-toggle="tooltip" title="Valor total conferido nesta data">
        {{ money($row->total_conferred, Setting::get('app_currency')) }}
    </span>
@endif

