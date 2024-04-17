{{ Html::link(route('admin.customers.edit', $row->id), $row->name, ['class' => 'text-uppercase']) }}

@if($row->is_particular)
    <i class="fas fa-user-tag text-yellow" data-toggle="tooltip" title="@trans('Cliente Particular')"></i>
@endif

@if(!$row->is_validated)
    <span class="label label-warning" data-toggle="tooltip" title="@trans('Cliente pendente de validação')"><i class="fas fa-clock"></i> APROVAÇÃO PENDENTE</span>
@endif

@if(!$row->is_active)
    <span class="label label-danger" data-toggle="tooltip" title="@trans('Cliente inativo')"><i class="fas fa-user-times"></i> INATIVO</span>
@endif

@if(!$row->active)
<i class="fas fa-ban text-red" data-toggle="tooltip" title="@trans('Cliente bloqueado')"></i>
@endif

<br/>
<i class="text-muted">@trans('NIF:')' {{ $row->vat }}
    @if(isset($types[$row->type_id]))
    - {{ $types[$row->type_id] }}
    @endif
</i>