<div class="text-center toggle-field">
    @if ($row->is_draft)
        <span class="label label-warning">Rascunho</span>
    @else
        <a href=""
        data-href="{{ route('account.event-manager.status.update', [$row->id]) }}"
        data-confirm-label="Confirmação"
        data-confirm-class="btn-success"
        data-title="Alterar estado"
        data-body="Pretende alterar o estado do evento?"
        class="text-blue toogle-action confirmationBootBox">

            @if($row->is_active)
                <i class="fas fa-check-circle text-green"></i>
            @else
                <i class="fas fa-times-circle text-muted"></i>
            @endif   
        </a>
    @endif  
</div>