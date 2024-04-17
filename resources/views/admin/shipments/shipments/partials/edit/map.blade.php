<div class="row">
    <div class="col-sm-3">
        <div class="row">
            <div class="col-sm-5 p-r-0">
                <h5 style="margin-top: 0">
                    <small>@trans('Distância')</small><br/>
                    <i class="fas fa-road"></i> <span class="total-distance">-.--km</span>
                </h5>
            </div>
            <div class="col-sm-7">
                <h5 style="margin-top: 0;">
                    <small>@trans('Duração Viagem')</small><br/>
                    <i class="fas fa-clock"></i> <span class="total-time">-.--</span>
                </h5>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-5">
                <h5 style="margin-top: 0">
                    <small>@trans('Viatura')</small><br/>
                    <i class="fas fa-truck"></i> <span class="dlvr-vehicle">{{ $shipment->vehicle ? $shipment->vehicle : '--' }}<br/>
                        <small style="color: #111" class="dlvr-trailer">{{ $shipment->trailer }}</small>
                    </span>
                </h5>
            </div>
            <div class="col-sm-7">
                <h5 style="margin-top: 0;">
                    <small>@trans('Previsão de custos')</small><br/>
                    <div class="shp-dlvr-fuel-yes" style="display:none;">
                        <i class="fas fa-euro-sign"></i> <span class="dlvr-fuel-price">-.--</span>€</small>
                        <br/><a href="#" class="text-blue"><small style="color: #111">@trans('Ver detalhes') <i class="fas fa-external-link-alt"></i></small></a>
                    </div>
                    <div class="shp-dlvr-fuel-no" data-toggle="tooltip" title="@trans('Não é possível calcular. Não existe viatura associada ao pedido ou a viatura não tem o valor de consumo configurado.')">
                        <i class="fas fa-euro-sign"></i> <span class="dlvr-fuel-price">-.--</span>€ <small>(<span class="dlvr-liters">-.--</span>L)</small>
                        <br/>&nbsp;
                    </div>
                    
                    <div style="display: none">
                        <div class="shp-dlvr-fuel-yes" style="display:none;">
                            <i class="fas fa-euro-sign"></i> <span class="dlvr-fuel-price">-.--</span>€ <small>(<span class="dlvr-liters">-.--</span>L)</small>
                            <br/><small style="color: #111"><span class="dlvr-price-liter">-.--</span>€/L &bull; <span class="dlvr-vehicle-consumption">-.--</span>L/100km</small>
                        </div>
                        <div class="shp-dlvr-fuel-no" data-toggle="tooltip" title="@trans('Não é possível calcular. Não existe viatura associada ao pedido ou a viatura não tem o valor de consumo configurado.')">
                            <i class="fas fa-gas-pump"></i> <span class="dlvr-fuel-price">-.--</span>€ <small>(<span class="dlvr-liters">-.--</span>L)</small>
                            <br/>&nbsp;
                        </div>
                    </div>
                </h5>
            </div>
        </div>
        <div class="shp-dlvr-route-options">
            <div class="row row-5">
                <div class="col-sm-6" style="display: none">
                    <div class="checkbox" style="padding: 0">
                        <label style="padding: 0">
                            {{ Form::checkbox('avoid_highways', 1, true, [hasModule('maps') ? '' : 'disabled']) }}
                            @trans('Autoestradas')
                        </label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="checkbox" style="padding: 0">
                        <label style="padding: 0">
                            {{ Form::checkbox('avoid_tolls', 1, true, [hasModule('maps') ? '' : 'disabled']) }}
                            @trans('Portagens')
                        </label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="checkbox" style="padding: 0">
                        <label style="padding: 0">
                            {{ Form::checkbox('return_back', 1, Setting::get('shipments_km_return_back'), [hasModule('maps') ? '' : 'disabled']) }}
                            @trans('Ida+Volta')
                        </label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="checkbox" style="padding: 0">
                        <label style="padding: 0">
                            {{ Form::checkbox('waypoint_agency', 1, false, [hasModule('maps') ? '' : 'disabled']) }}
                            <i class="fas fa-warehouse"></i> @trans('Agencia')
                        </label>
                        {!! tip(__('Ative a opção caso queira contabilizar o percurso de passagem pelo armazém de recolha e de entrega.')) !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="checkbox" style="padding: 0">
                        <label style="padding: 0">
                            {{ Form::checkbox('optimize', 1, false, [hasModule('maps') ? '' : 'disabled']) }}
                            @trans('Otimizar Rota')
                        </label>
                        {!! tip(__('Se ativo, reorganiza as moradas de acordo com a entrega mais otimizada, ignorando a ordem dos pedidos')) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="shp-dlvr-route">
                    <ul>
                        @if(hasModule('maps'))
                            <li class="dlvr-loading text-center p-t-10">
                                <i class="fas fa-spin fa-circle-notch"></i> @trans('A calcular trajeto...')
                            </li>
                        @else
                            <li class="dlvr-loading text-center p-t-10">
                                <i class="fas fa-puzzle-piece fs-20 p-b-5 p-t-10"></i><br/> @trans('A sua licença não inclui este módulo.')
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-9">
        <div id="shpmap" style="height: 465px;
    position: relative;
    margin: -15px -15px 0 -15px;
    overflow: hidden;">
        </div>
    </div>
</div>
{{ Form::hidden('recipient_latitude') }}
{{ Form::hidden('recipient_longitude') }}
{{ Form::hidden('sender_latitude') }}
{{ Form::hidden('sender_longitude') }}
