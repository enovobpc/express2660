@if($row->target_type == 'Vehicle')
    Viatura
@elseif($row->target_type == 'User')
    Colaborador
@elseif($row->target_type == 'Shipment')
    Envio
@endif