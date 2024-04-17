<div class="text-center">
    @if($row->login_suspended)
        <span class="text-red" data-toggle="tooltip" title="@trans('O login na conta de e-mail está bloqueado.')">
            <i class="fas fa-times-circle"></i>
        </span>
    @else
        <span class="text-green">
            <i class="fas fa-check-circle"></i>
        </span>
    @endif
</div>