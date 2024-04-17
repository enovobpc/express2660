<b>{{ money($row->subtotal, Setting::get('app_currency')) }}</b>
<br/>
<i class="text-muted" data-toggle="tooltip" title="Preço de custo">{{ money($row->cost_price * $row->qty, Setting::get('app_currency')) }}</i>