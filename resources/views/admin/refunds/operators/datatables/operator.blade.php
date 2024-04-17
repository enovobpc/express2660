<a href="{{ route('admin.operator-refunds.edit', [$row->operator_id, 'date_min' => $dtMin, 'date_max' => $dtMax]) }}" data-toggle="modal" data-target="#modal-remote-xl">
    @if($row->operator_id)
        {{ @$row->operator->name }}
    @else
        Sem operador
    @endif
</a>

