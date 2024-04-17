@if($row->to)
    {{ str_limit($row->to) }}
    @if($row->cc)
    <br/>
    <small class="italic text-muted">Cópia: {{ str_limit($row->cc) }}</small>
    @endif
@elseif($row->bcc)
    <i class="fas fa-eye-slash"></i> <i>Destinatários Não Revelados</i>
@endif
