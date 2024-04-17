@if($row->doc_type != 'payment-note')
    @if($row->is_settle)
        <span class="label label-success">Pago</span>
    @else
        <span class="label label-danger">Não Pago</span>
    @endif
@endif