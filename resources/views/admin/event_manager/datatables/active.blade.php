<div class="text-center toggle-field">
    @if ($row->is_draft)
        <a href=""
        data-href="{{ route('admin.event-manager.status.update', $row->id) }}" 
        data-title="Finalizar evento"
        data-body="Confirma a finalização do evento?" 
        data-confirm-label="Confirmação"
        data-confirm-class="btn-success"
        class="toogle-action confirmationBootBox">
            <span class="label label-warning">Rascunho</span>
        </a>
    @else
        <a href=""
        data-href="{{ route('admin.event-manager.status.update', [$row->id]) }}" 
        data-title="Alterar estado"
        data-body="Pretende alterar o estado do evento?"
        data-confirm-label="Confirmação"
        data-confirm-class="btn-success btn-finish"
        class="text-blue toogle-action confirmationBootBox">

            @if($row->is_active)
                <i class="fas fa-check-circle text-green"></i>
            @else
                <i class="fas fa-times-circle text-muted"></i>
            @endif   
        </a>
    @endif  
</div>