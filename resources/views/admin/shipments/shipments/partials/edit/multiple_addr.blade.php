<div class="col-sm-12">
    @if(Setting::get('shipment_adicional_addr_mode') != 'pro_fixed')
    <button type="button" class="btn btn-xs btn-default pull-right {{ $shipment->multiple_addresses ? 'hide' : '' }}" data-action="rem-addr"><i class="fas fa-times"></i> Cancelar</button>
    @endif
    <h5 class="m-b-5 m-t-5 text-uppercase text-left text-blue bold">Multiplas cargas e descargas / CMR</h5>
    <div class="clearfix"></div>
    <div class="addrs-container input-group-xs">
        <table class="table-addrs">
            <tr>
                <th>Local Carga</th>
                <th></th>
                <th>Local Descarga</th>
                <th></th>
                <th class="w-95px" style="border-left: 2px solid #333">Referência</th>
                <th class="w-100px">Data</th>
                <th class="w-20px text-right">Vols</th>
                <th class="w-80px text-right">Peso</th>
                <th class="w-50px text-right">LDM</th>
                <th class="w-50px text-right">M3</th>
                <th class="w-40px"></th>
            </tr>
            @if($shipment->multiple_addresses)
                @foreach ($shipment->multiple_addresses as $key => $shipmentAddress)
                    <?php 
                        $hash = $shipmentAddress->id == $shipment->id ? 'main' : str_random(5);
                        $triggerPrice = $hash == 'main' ? 'trigger-price' : '';
                    ?>
                    @include('admin.shipments.shipments.partials.edit.multiple_addr_row')
                @endforeach
            @else
                <?php 
                    $hash = 'main';
                    $triggerPrice    = 'trigger-price';
                    $shipmentAddress = $shipment;
                ?>
                @include('admin.shipments.shipments.partials.edit.multiple_addr_row')
            @endif
        </table>
        <button type="button" class="btn btn-xs btn-default m-l-5 m-t-5" data-action="add-addr-row"><i class="fas fa-plus"></i> Nova Carga / Descarga</button>
        <button type="button" class="btn btn-xs btn-default m-l-5 m-t-5" data-action="edit-addr-row"><i class="fas fa-pencil-alt"></i> Editar dados</button>
    </div>
</div>