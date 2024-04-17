@if(@$row->user->name)
    {{ @$row->user->name }}
@else
    <span class="text-info"><i class="fas fa-info-circle"></i> @trans('Sem responsável')</span>
@endif