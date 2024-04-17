@if($row->stock_status == 'blocked')
    <span class="label label-danger">
        <i class="fas fa-exclamation-triangle"></i> Bloqueado
    </span>
@else
    <span class="label label-success">
        Ativo
    </span>
@endif