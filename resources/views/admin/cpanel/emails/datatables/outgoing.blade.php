<div class="text-center">
    @if($row->outgoing_suspended)
        <span class="text-red" data-toggle="tooltip" title="@trans('Receção de E-mails bloqueada.')">
            <i class="fas fa-times-circle"></i>
        </span>
    @else
        <span class="text-green">
            <i class="fas fa-check-circle"></i>
        </span>
    @endif
</div>