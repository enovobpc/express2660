@if($row->doc_type != 'payment-note')
    {!! @$row->type->name ? @$row->type->name : '<small class="text-red"><i class="fas fa-exclamation-triangle"></i> ELIMINADO</small>' !!}
    <br/>
    <small class="text-muted italic">
        {{ $row->description }}
    </small>
@endif

