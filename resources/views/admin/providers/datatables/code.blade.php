{{ $row->code }}
@if($row->type == 'carrier')
    <div data-toggle="tooltip" title="Fornecedor de Transportes">
    <span class="label bg-blue">
        <i class="fas fa-truck"></i>
    </span>
    </div>
@endif