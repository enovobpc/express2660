@if(!$row->provider_id)
    <a href="{{ route('admin.billing.providers.show', ['999999999', 'month' => $row->month, 'year' => $row->year]) }}"
       class="text-blue">
        <i class="text-red"><i class="fas fa-exclamation-circle"></i> Envios sem fornecedor associado</i>
    </a>
@else
    <a href="{{ route('admin.billing.providers.show', [$row->provider_id, 'month' => $row->month, 'year' => $row->year]) }}"
       class="text-blue">
        <i class="fas fa-square" style="color: {{ @$row->color }}"></i> {{ @$row->name }}
    </a>
@endif