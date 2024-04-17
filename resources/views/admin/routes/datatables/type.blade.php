@if($row->type == 'pickup')
    Só Recolhas
@elseif($row->type == 'delivery')
    Só Entregas
@else
    Recolhas e Entregas
@endif
