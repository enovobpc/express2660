<div class="row row-5">
    <div class="col-xs-12">
        <div class="alert alert-warning m-0 text-black alert-optimize-route" style="border-radius: 3px 3px 0 0; {{ $trip->is_route_optimized ? 'display:none' : ''}}">
            <i class="fas fa-info-circle"></i> @trans('O percurso não está otimizado porque a ordem dos serviços foi definida manualmente. Pretende calcular a melhor rota para entrega?')
            <a href="{{ route('admin.trips.shipments.optimize.edit', $trip->id) }}"
                data-toggle="modal"
                data-target="#modal-remote-xs"
                class="btn btn-xs btn-primary pull-right m-r-5" style="text-decoration: none; margin-top: -3px;">
                 <i class="fas fa-route"></i>
                    @trans('Calcular rota otimizada')
             </a>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="map-trace-details">
            <button class="btn btn-sm btn-block btn-default trace-delivery-route"><i class="fas fa-route"></i> @trans('Ver trajeto sugerido')</button>
            <h4>
                <small>@trans('Distância total')</small><br/>
                <span class="total-distance">--</span>
            </h4>
            <h4>
                <small>@trans('Tempo previsto')</small><br/>
                <span class="total-time">--</span>
            </h4>
            <div class="row row-5" style="margin-top: 5px; border-top: 1px solid #ccc">
                {{-- <div class="col-xs-6">
                    <p class="m-0">
                        <small>Combustível</small><br/>
                        <span class="total-fuel">--</span>
                    </h4>
                </div>
                <div class="col-xs-6">
                    <p class="m-0">
                        <small>Motorista</small><br/>
                        <span class="total-salary">--</span>
                    </h4>
                </div> --}}
            </div>
            <div class="m-t-5">
                <a href="#"
                    data-toggle="modal"
                    data-target="#modal-remote" style="display: none" 
                    class="route-details">
                    @trans('Detalhe da viagem') <i class="fas fa-external-link-alt"></i>
                </a>
            </div>

        </div>
        <div id="deliveryMap" style="width: 100%; height: 700px; border-radius: 2px"></div>
    </div>
</div>