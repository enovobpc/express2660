<div class="text-center">
    {{ $row->code }}
    @if($row->provider_code)
    <br/>
    <small class="text-muted" data-toggle="tooltip" title="Código correspondente no fornecedor"><i class="fas fa-plug"></i>{{ $row->provider_code }}</small>
    @endif
</div>