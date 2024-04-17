{{ $row->date->format('Y-m-d') }}
<br/>
<?php
$diff = $row->date->diffInDays(\Carbon\Carbon::today())
?>
@if($row->is_active)
    @if($row->date)
        @if($row->date->gt(\Carbon\Carbon::today()) && $diff > $row->days_alert)
        <small class="text-green bold">
            {{ $diff }} @trans('dias')
        </small>
        @elseif($row->date->gt(\Carbon\Carbon::today()) && $diff <= $row->days_alert)
        <small class="text-yellow bold">
            <i class="far fa-clock"></i> {{ $diff }} @trans('dias')
        </small>
        <a href="{{ route('admin.fleet.reminders.reset.edit', ['vehicle' => $row->vehicle_id]) }}"
           class="btn btn-xs btn-default"
           data-toggle="modal"
           data-target="#modal-remote-lg">
            <span data-toggle="tooltip"
                  title="@trans('Remarque este lembrete para outra data ou km.')">
                <i class="fas fa-undo-alt"></i> @trans('Reset')
            </span>
        </a>
        <a href="{{ route('admin.fleet.reminders.conclude', [$row->id]) }}"
           data-method="post"
           data-confirm="@trans('Confirma a conclusão deste lembrete?')"
           data-confirm-title="@trans('Concluir lembrete')"
           data-confirm-label="@trans('Concluir')"
           data-confirm-class="btn-success"
           class="btn btn-xs btn-default">
            <i class="fas fa-check"></i>
        </a>
        @else
        <small class="text-red bold">
            <i class="fas fa-exclamation-triangle"></i> {{ $diff }} @trans('dias atrás')
        </small>
        <a href="{{ route('admin.fleet.reminders.reset.edit', ['vehicle' => $row->vehicle_id]) }}"
           class="btn btn-xs btn-default"
           data-toggle="modal"
           data-target="#modal-remote-lg">
            <span data-toggle="tooltip"
                  title="@trans('Remarque este lembrete para outra data ou km.')">
                <i class="fas fa-undo-alt"></i> @trans('Reset')
            </span>
        </a>
        <a href="{{ route('admin.fleet.reminders.conclude', [$row->id]) }}"
           data-method="post"
           data-confirm="@trans('Confirma a conclusão deste lembrete?')"
           data-confirm-title="@trans('Concluir lembrete')"
           data-confirm-label="@trans('Concluir')"
           data-confirm-class="btn-success"
           class="btn btn-xs btn-default">
            <i class="fas fa-check"></i>
        </a>
        @endif
    @endif
@endif
