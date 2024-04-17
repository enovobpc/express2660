@if(empty($row->id))
    <a href="{{ route('admin.billing.customers.show', [999999999, 'month' => $row->month, 'year' => $row->year, 'period' => $period]) }}"
       class="text-blue">
        Envios sem cliente associado
    </a>
@else
    <a href="{{ route('admin.billing.customers.show', [$row->id, 'month' => $row->month, 'year' => $row->year, 'period' => $period]) }}" class="text-blue dt-title">
        @if(!$row->name)
        <i class="text-red"><i class="fas fa-exclamation-circle"></i> Cliente eliminado</i>
        @else
        {{ @$row->code }} - {{ @$row->name }}
        @endif
    </a>
    <small class="text-muted">{{ @$agencies[$row->agency_id] }}</small>
@endif