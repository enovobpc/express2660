@if($row->is_scheduled == 'biweekly')
    <span class="label label-info">Quinzenalmente</span>
@elseif($row->is_scheduled == 'monthly')
    <span class="label bg-blue">Mensalmente</span>
@elseif($row->is_scheduled == 'quarterly')
    <span class="label bg-purple">Trimestral</span>
@endif