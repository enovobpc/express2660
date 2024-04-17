<div class="ontime-tracking m-b-30">
    <table class="table table-history m-0">
        <tr>
            <th class="">
                <i class="fas fa-map-marker-alt"></i> Seguimento <i>On Time</i>
                <span class="pull-right">Localização Ativa</span>
            </th>
        </tr>
    </table>
    <div class="tracking-history">
        <div class="operator-avatar">
            <img src="{{ @$shipment->operator->filepath }}" onerror="this.src='{{ asset('assets/img/default/avatar.png') }}'"/>
            <div class="pull-left">
                <h4>
                    <small>Operador</small><br/>
                    @if(1 && @$shipment->operator->name)
                        {{ @$shipment->operator->name }}
                    @else
                        Nome Indisponível
                    @endif
                    <br/>
                    @if($shipment->vehicle)
                        <small class="vehicle">
                            <i class="fas fa-car"></i> {{ $shipment->vehicle }}
                        </small>
                    @endif
                </h4>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="navegation-status">
            {{-- <i class=""></i> Em execução. <i class="fas fa-info-circle" data-toggle="tooltip" title="A localização é atualizada a cada 10 minutos."></i>
--}}
            <h4 class="last-status" style="color: {{ @$shipment->status->color }}">
                <small class="status-date">{{ @$shipment->last_history->created_at->format('Y-m-d H:i') }}</small>
                <small>Estado</small><br/>
                {{ @$shipment->status->name }}
            </h4>
        </div>
        <div class="navegation-history">
            @if(0 && $shipment->live_tracking == 'active')
                {{-- <table>
                     <tr>
                         <td class="w-50px">15:15</td>
                         <td>Rua Maria Casimira</td>
                     </tr>
                     <tr>
                         <td class="w-50px">15:15</td>
                         <td>Rua Maria Casimira</td>
                     </tr>
                     <tr>
                         <td class="w-50px">15:15</td>
                         <td>Rua Maria Casimira</td>
                     </tr>
                     <tr>
                         <td class="w-50px">15:15</td>
                         <td>Rua Maria Casimira</td>
                     </tr>
                     <tr>
                         <td class="w-50px">15:15</td>
                         <td>Rua Maria Casimira</td>
                     </tr>
                     <tr>
                         <td class="w-50px">15:15</td>
                         <td>Rua Maria Casimira</td>
                     </tr>
                     <tr>
                         <td class="w-50px">15:15</td>
                         <td>Rua Maria Casimira</td>
                     </tr>
                     <tr>
                         <td class="w-50px">15:15</td>
                         <td>Rua Maria Casimira</td>
                     </tr><tr>
                         <td class="w-50px">15:15</td>
                         <td>Rua Maria Casimira</td>
                     </tr>
                     <tr>
                         <td class="w-50px">15:15</td>
                         <td>Rua Maria Casimira</td>
                     </tr><tr>
                         <td class="w-50px">15:15</td>
                         <td>Rua Maria Casimira</td>
                     </tr>
                     <tr>
                         <td class="w-50px">15:15</td>
                         <td>Rua Maria Casimira</td>
                     </tr>
                     <tr>
                         <td class="w-50px">15:15</td>
                         <td>Rua Maria Casimira</td>
                     </tr>
                     <tr>
                         <td class="w-50px">15:15</td>
                         <td>Rua Maria Casimira</td>
                     </tr>
                     <tr>
                         <td class="w-50px">15:15</td>
                         <td>Rua Maria Casimira</td>
                     </tr>


                 </table>--}}
            @else
                <span class="disabled-history">
                                        <i class="fas fa-location-arrow"></i>
                                        <br/>
                                        Seguimento Indisponível
                                        <br/>
                                        <small>
                                            De momento não é possível
                                            acompanhar a sua entrega
                                            em tempo real.
                                        </small>
                                    </span>
            @endif
        </div>
    </div>
    <div class="" id="map" style="height: 350px"></div>
</div>