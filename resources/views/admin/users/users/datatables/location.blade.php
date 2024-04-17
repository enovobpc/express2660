<div class="text-center">
    @if($row->location_enabled)
        <span class="label label-success" data-toggle="tooltip" title="Os serviços de localização estão ativos na aplicação móvel.">
            <i class="fas fa-location-arrow"></i> @trans('Ativo')
        </span>
    @elseif($row->location_denied)
        <span class="label label-danger"  data-toggle="tooltip" title="Não é possível usar os serviços de localização porque estão definidos como bloqueados nas definições do telemóvel.">
            <i class="fas fa-location-arrow"></i> @trans('Bloqueado')
        </span>
    @else
        <span class="label label-default"  data-toggle="tooltip" title="O utilizador desligou a sua localização na aplicação móvel">
            <i class="fas fa-location-arrow"></i> @trans('Inativo')
        </span>
    @endif
    <br/>
    <small>{{ $row->location_last_update ? timeElapsedString($row->location_last_update) : 'Nunca ativo' }}</small>
</div>