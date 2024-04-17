<span class="text-yellow">

    <i class="m-r-3 fas fa-envelope {{ $row->is_mail ? : 'text-muted-light' }}" data-toggle="tooltip" title="Serviço de Correio"></i>

    <i class="m-r-3 fas fa-motorcycle {{ $row->is_courier ? : 'text-muted-light' }}" data-toggle="tooltip" title="Serviço de Estafetagem"></i>

    <i class="m-r-3 fas fa-globe {{ $row->is_internacional ? : 'text-muted-light' }}" data-toggle="tooltip" title="Serviço Internacional"></i>

    <i class="m-r-3 fas fa-plane {{ $row->is_air ? : 'text-muted-light' }}" data-toggle="tooltip" title="Serviço Aéreo"></i>

    <i class="m-r-3 fas fa-ship {{ $row->is_maritime ? : 'text-muted-light' }}" data-toggle="tooltip" title="Serviço Marítimo"></i>
</span>
<small>
    @if($row->allow_kms)
        <div>Obriga KM</div>
    @endif
    @if($row->dimensions_required)
        <div>Obriga Dimensões</div>
    @endif
    @if($row->allow_pudos)
        <div>Permite PUDO's</div>
    @endif
    @if($row->allow_out_standard)
        <div>Permite Fora Norma</div>
    @endif
    @if(!$row->allow_cod)
        <div>Não Permite COD</div>
    @endif
    @if (!$row->allow_return)
        <div>Não Permite Retorno</div>
    @endif
</small>