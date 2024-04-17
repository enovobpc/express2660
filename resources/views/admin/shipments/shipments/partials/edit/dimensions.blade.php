<div id="modal-shipment-dimensions">
    <div>
        <div>
            <table class="table table-condensed m-b-0 shipment-dimensions">
                <thead>
                    <tr>
                        <th class="bg-gray w-40px">@trans('Qtd')</th>
                        <th class="bg-gray w-90px">@trans('Pacote')</th>
                        <th class="bg-gray">@trans('Descrição mercadoria')<i> <small>(@trans('Opcional'))</small></i></th>
                        <th class="bg-gray w-100px">@trans('Comprim.')</th>
                        <th class="bg-gray w-100px">@trans('Largura')</th>
                        <th class="bg-gray w-100px">@trans('Altura')</th>
                        <th class="bg-gray w-95px">@trans('Peso')</th>
                        <th class="bg-gray" style="width: 62px">M3</th>
                        @if(Setting::get('shp_dimensions_show_price'))
                        <th class="bg-gray w-85px">@trans('Preço Un.')</th>
                        @endif
                        @if(Setting::get('show_adr_fields'))
                            <th class="bg-gray w-50px">Class <div style="position: absolute;top: 0;border-bottom: 1px solid #333;top: -18px;width: 140px;text-align: center;">ADR</div></th>
                            <th class="bg-gray w-50px">Letter</th>
                            <th class="bg-gray w-50px">Numbr</th>
                        @endif
                        @if(Setting::get('shp_dimensions_show_mounting'))
                            <th class="bg-gray w-40px"></th>
                        @endif
                        <th class="bg-gray w-1"></th>
                        <th class="bg-gray w-1"></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $volumes = @$shipment->volumes ? ($shipment->volumes > 100 ? 100 : $shipment->volumes) : 1;
                $volumes = $volumes > 6 ? $volumes : 6;
                ?>
                @if($shipment->multiple_addresses)
                    @foreach ($shipment->multiple_addresses as $address)
                        @if(!empty($address->pack_dimensions) && !$address->pack_dimensions->isEmpty())
                            <?php
                            $dimensions = $address->pack_dimensions->toArray();
                            $volumes    = $address->pack_dimensions->count();
                            ?>
                        @endif

                        @for($key = 0 ; $key < $volumes ; $key++)
                            @include('admin.shipments.shipments.partials.edit.dimensions_row')
                        @endfor
                    @endforeach
                @else
                    @if(!empty($shipment->pack_dimensions) && !$shipment->pack_dimensions->isEmpty())
                        <?php
                        $dimensions = $shipment->pack_dimensions->toArray();
                        $volumes = $shipment->pack_dimensions->count();
                        ?>
                    @endif

                    @for($key = 0 ; $key < $volumes ; $key++)
                        @include('admin.shipments.shipments.partials.edit.dimensions_row')
                    @endfor
                @endif
                </tbody>
            </table>
            <button type="button" class="btn btn-xs btn-default m-l-0 m-b-10 btn-new-dim-row">
                <i class="fas fa-plus"></i> @trans('Adicionar nova linha')
            </button>
        </div>
        <button type="button" class="confirm-dimensions hide"></button>
        {{--<button type="button" class="cancel-dimensions hide"></button>--}}
    </div>
</div>