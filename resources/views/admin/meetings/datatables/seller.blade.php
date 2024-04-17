@if(@$row->seller->name)
    {{ @$row->seller->name }}<br/>
@else
    <i>@trans('Sem comercial associado')</i>
@endif
@if($row->duration)
<i class="text-muted">Estimado: {{ trans('admin/meetings.durations.'.$row->duration) }}</i>
@endif