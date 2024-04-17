<small>
    @if($row->customer_customization)
        <div><i class="fas fa-check"></i> Preço por cliente</div>
    @endif
    @if($row->complementar_service)
        <div><i class="fas fa-check"></i> Menu rápido - Envios</div>
    @endif

    @if($row->collection_complementar_service)
        <div><i class="fas fa-check"></i> Menu rápido - Recolhas</div>
    @endif

    @if($row->account_complementar_service)
        <div><i class="fas fa-check"></i> Menu rápido - Área Cliente</div>
    @endif
</small>