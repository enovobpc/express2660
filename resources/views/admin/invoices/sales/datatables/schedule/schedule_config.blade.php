@if($row->send_email)
    <div data-toggle="tooltip" title="Enviar e-mail ao cliente"><i class="fas fa-check-circle text-green"></i> E-mail</div>
@else
    <div data-toggle="tooltip" title="Enviar e-mail ao cliente"><i class="fas fa-times-circle text-muted"></i> E-mail</div>
@endif

@if($row->is_draft)
    <div data-toggle="tooltip" title="Criar como rascunho"><i class="fas fa-check-circle text-green"></i> Rascunho</div>
@else
    <div data-toggle="tooltip" title="Criar como rascunho"><i class="fas fa-times-circle text-muted"></i> Rascunho</div>
@endif