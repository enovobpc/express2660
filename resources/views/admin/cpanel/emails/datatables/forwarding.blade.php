<div class="text-center">
    @if($row->forwarding_active)
        <span class="label label-success">
        <i class="fas fa-check-circle"></i> @trans('Ativo')
    </span>
    @else
        <span class="label label-default">
        <i class="fas fa-times-circle"></i> @trans('Inativo')
    </span>
    @endif
</div>