@if($row->source_type == 'Shipment')
    <span class="label label-info">
        Envios/Recolhas
    </span>
@else
    <span class="label bg-blue">
        Gestor SMS
    </span>
@endif