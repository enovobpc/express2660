@if(@$row->operator->name)
    {{ @$row->operator->name }}
@else
    <i class="text-muted">Sem responsável</i>
@endif