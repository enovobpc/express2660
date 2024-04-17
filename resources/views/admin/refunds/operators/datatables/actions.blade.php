<a href="{{ route('admin.operator-refunds.edit', [$row->operator_id ? $row->operator_id : 0, 'date_min' => $dtMin, 'date_max' => $dtMax, 'date_unity' => $dtUnity]) }}" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-remote-xl">
    Conferir
</a>