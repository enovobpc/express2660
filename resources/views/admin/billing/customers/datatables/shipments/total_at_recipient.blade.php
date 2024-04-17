<span class="label bg-orange" data-toggle="tooltip" title="Pagamento no Destino">
        <i class="fas fa-arrow-right"></i> {{ money($row->total_price_for_recipient, Setting::get('app_currency')) }}
    </span>
<br/>
<span class="text-muted italic" data-toggle="tooltip" title="Preço de custo">
    {{ money($row->cost_price, Setting::get('app_currency')) }}
</span>