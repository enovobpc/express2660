@if($row->has_prices || $row->price_table_id)
    <i class="fas fa-check-circle text-green"
       data-toggle="tooltip"
       title="Este cliente tem definida a tabela de preços {{ $row->price_table_id ? @$row->price_table->name : 'Personalizada' }}.">
    </i>
@endif