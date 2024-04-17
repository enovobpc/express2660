@if($shipmentHistory->isEmpty())
    <div class="text-center text-muted p-5">
        <h4><i class="fas fa-info-circle"></i> Não há histórico de estados para este envio.</h4>
    </div>
@else
    <table class="table m-b-0">
        <tr>
            <th class="w-90px bg-gray-light" style="border-top: none">Data</th>
            <th class="w-55px bg-gray-light" style="border-top: none">Hora</th>
            <th class="w-160px bg-gray-light" style="border-top: none">Estado</th>
            <th class="w-80px bg-gray-light" style="border-top: none">Armazém</th>
            <th class="w-180px bg-gray-light" style="border-top: none">Motorista/Viatura</th>
            @if(in_array(Setting::get('app_mode'), ['cargo', 'freight']))
                <th class="bg-gray-light" style="border-top: none">Localização</th>
            @endif
            <th class="bg-gray-light" style="border-top: none">Observações</th>
            <th class="w-180px bg-gray-light" style="border-top: none">Registado Por</th>
            <th class="w-1 bg-gray-light"></th>
        </tr>
        @foreach($shipmentHistory as $history)
        <tr style="{{ $history->deleted_at ? 'color: red; opacity:0.6; background:#ffe8e8' : '' }} {{ !$history->status_id ? 'opacity:0.6; background:#f2f2f2' : '' }}">
            <td>{{ $history->created_at->format('Y-m-d') }}</td>
            <td>{{ $history->created_at->format('H:i') }}</td>
            <td>
                @if(@$history->status_id)
                    <span class="label" style="background: {{ @$history->status->color }}">
                        {{ @$history->status->name }}
                    </span>
                @else
                    <span class="label" style="color: #000; border: 1px solid #999;">
                        Nota Interna
                    </span>
                @endif
            </td>
            <td>
                @if($history->provider_agency_code)
                <div data-toggle="popover"
                     title="{{ @$history->provider_agency->code }} - {{ @$history->provider_agency->name }} <span class='label label-provider' style='background: {{ $shipment->provider->color }}'>{{ $shipment->provider->name }}</span>"
                     data-html="true"
                     data-content="{{ agencyTip($history->provider_agency) }}">
                     {{ @$history->provider_agency_code }}
                     <i class="fas fa-external-link-square-alt"></i>
                 </div>
                @elseif(@$history->agency_id)
                <div data-toggle="popover"
                     title="{{ @$history->agency->name }} <span class='label label-provider' style='background: {{ @$shipment->provider->color }}'>{{ @$shipment->provider->name }}</span>"
                     data-html="true"
                     data-content="{{ agencyTip($history->agency) }}">
                     {{ @$history->agency->code }}
                     <i class="fas fa-external-link-square-alt"></i>
                </div>
                @endif
            </td>
            <td>
                <div>{{ split_name(@$history->operator->name) }}</div>
                @if($history->vehicle)
                    {{ $history->vehicle }}
                @endif
                @if($history->trailer)
                    + {{ $history->trailer }}
                @endif
            </td>
            @if(in_array(Setting::get('app_mode'), ['cargo', 'freight']))
            <td>
                @if($history->city)
                    <a href="https://www.google.com/maps/search/?api=1&query={{ str_replace(' ', '+', $history->city) }}" target="_blank">
                        <i class="fas fa-map-marker-alt"></i> {!! $history->city !!}
                    </a>
                @endif
            </td>
            @endif
            <td>
                {!! $history->obs !!}

                @if($history->receiver)
                    @if($history->obs)
                    <br/>
                    @endif
                    @if($history->status_id == \App\Models\ShippingStatus::DELIVERED_ID)
                         Recebido Por: {{ $history->receiver }}{{ $history->vat ? ' - ' . $history->vat : '' }}
                    @else
                         Responsável: {{ $history->receiver }}{{ $history->vat ? ' - ' . $history->vat : '' }}
                    @endif
                @endif

                @if(@$history->incidence)
                    @if($history->obs)
                    <br/>
                    @endif
                    Motivo de Incidência: {{ @$history->incidence->name }}
                @endif

                @if(($history->signature || $history->receiver) && !in_array($shipment->webservice_method, ['envialia', 'tipsa']))
                    <div>
                        <a href="{{ route('admin.shipments.get.pod', [$shipment->id, $history->id]) }}" target="_blank">
                
                            @if($history->status_id == \App\Models\ShippingStatus::DELIVERED_ID)
                                @if (Setting::get('app_mode') == 'cargo')
                                <i class="fas fa-fw fa-signature"></i> Prova entrega digital - eCMR
                                @else
                                <i class="fas fa-fw fa-signature"></i> Download Prova Entrega Digital
                                @endif
                            @else
                                @if (Setting::get('app_mode') == 'cargo')
                                <i class="fas fa-fw fa-signature"></i> Prova recolha digital - eCMR
                                @else
                                <i class="fas fa-fw fa-signature"></i> Download Prova Recolha Digital
                                @endif
                            @endif
                        </a>
                    </div>
                @endif

                @if($history->status_id == \App\Models\ShippingStatus::DELIVERED_ID && in_array($shipment->webservice_method, ['envialia', 'tipsa']))
                    <div>
                        <a href="{{ route('admin.shipments.get.pod', $shipment->id) }}"
                           target="_blank">                             
                            <i class="fas fa-fw fa-signature"></i> Download Prova Entrega Digital
                        </a>
                    </div>
                @endif

                @php
                    $attachamentHistory = $attachamentHistory->where('shipment_history_id', $history->id)->pluck('filepath', 'name');
                @endphp

                @if($attachamentHistory->isEmpty() && $history->filepath)
                    <div>
                        <a href="{{ asset($history->filepath) }}" target="_blank">
                            <i class="fas fa-fw fa-image"></i> Consultar prova fotográfica (<?php try { echo human_filesize(filesize(public_path($history->filepath))); } catch (\Exception $e) {} ?>)
                        </a>
                    </div>
                @endif

                @if($history->latitude && $history->longitude)
                    <div>
                        <a href="http://maps.google.com/maps?q={{ $history->latitude }},{{ $history->longitude }}" target="_blank">
                            <i class="fas fa-fw fa-map-marker-alt"></i> Consultar Localização
                        </a>
                    </div>
                @endif

                @if(Setting::get('mobile_app_photo_multiple') && count($attachamentHistory) != 0)
                <div class="accordion" id="accordionExample">
                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <a class="btn-header-link fs-13" data-toggle="collapse" data-target="#collapseOne" style="padding: 0%; box-shadow: none; cursor:pointer;" > <i class="fas fa-fw fa-image"></i> Consultar Digitalização<i class="fas fa-angle-down rotate-icon" style="margin-left: 10px"></i></a>
                        </div>
                        <div id="collapseOne" class="collapse" data-parent="#accordionExample">
                            <div class="card-body">
                                <ul class="list-unstyled p-l-18">
                                @foreach($attachamentHistory as $key => $file)
                                    <li class="p-3">
                                        <a href="{{ asset($file) }}" target="_blank" class="btn-header-link fs-13" style="padding: 0%; box-shadow: none;">
                                                {{$key}}&nbsp(<?php try { echo human_filesize(filesize(public_path($file))); } catch (\Exception $e) {} ?>)
                                        </a>
                                    </li>
                                @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </td>
            <td>
                @if($history->api)
                    Cliente via <span class="label bg-blue"><i class="fas fa-plug"></i> API</span>
                @elseif($history->user_id)
                    {{ @$history->user->name }}
                @else
                    Sistema
                @endif
                @if($history->submited_at)
                <div><small class="text-muted" data-toggle="tooltip" title="Sincronizado com fornecedor"><i class="fas fa-plug"></i> {{ $history->submited_at }}</small></div>
                @endif
                @if($history->deleted_at)
                    <div class="italic">
                        <small data-toggle="tooltip"
                               data-placement="left"
                               title="Apagado por {{ @$history->deleted_user->name }} em {{ $history->deleted_at }}">
                            <i class="fas fa-trash-alt"></i> {{ @$history->deleted_user->name }}
                        </small>
                    </div>
                @endif
            </td>
            <td>
                @if($history->deleted_at)
                    <a href="{{ route('admin.shipments.history.restore', [$history->shipment_id, $history->id]) }}"
                       class="text-green"
                       data-toggle="ajax-confirm"
                       data-ajax-method="post"
                       data-confirm-title="Restaurar estado do envio"
                       data-ajax-confirm="Confirma o restauro do estado selecionado?"
                       data-confirm-label="Restaurar"
                       data-confirm-class="btn-success">
                        <i class="fas fa-undo"></i>
                    </a>
                @else
                    <a href="{{ route('admin.shipments.history.destroy', [$history->shipment_id, $history->id]) }}"
                       class="text-red"
                       data-toggle="ajax-confirm"
                       data-ajax-method="delete"
                       data-ajax-confirm="Confirma a anulação do estado selecionado?<br/><small>O estado ficará invisível para os clientes, mas continuará visivel para administradores.</small>">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
@endif