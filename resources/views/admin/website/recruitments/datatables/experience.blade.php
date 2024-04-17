<span class="text-muted">Experiência:</span> 
@if($row->has_experience)
Sim
@else
Não
@endif

<br/>
<span class="text-muted">Sit. Prof.:</span> 
@if($row->professional_situation)
Empregado
@else
Desempregado
@endif

<br/>
<span class="text-muted">Anterior:</span> 
{{ str_limit($row->company, 30) }}