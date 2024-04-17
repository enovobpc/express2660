@if(!$webservice->exists)
    <div class="alert alert-info">
        <h4>Configurar mapeamento de serviços</h4>
        <p>Poderá configurar o mapeamento de serviços depois de gravar a ligação.</p>
    </div>
@else
    @if($webservice->method == 'ctt_expresso' || $webservice->method == 'ctt')
        @include('admin.webservices.global.partials.mapping_services_ctt')
    @elseif($webservice->method == 'chronopost')
        @include('admin.webservices.global.partials.mapping_services_chronopost')
    @elseif(in_array($webservice->method, ['dhl', 'integra2', 'lfm', 'kargo', 'via_directa', 'wexpress']))
        <div class="row">
            <div class="col-sm-12">
                <h4>Configurar ligação aos serviços {{ @$webservice->webservice_method->name }}</h4>
                <p><i class="fas fa-info-circle"></i> A configuração de serviços não é possível para este fornecedor.</p>
            </div>
        </div>
    @else
    <div class="row">
        <div class="col-sm-12">
            <h4>Configurar ligação aos serviços {{ @$webservice->webservice_method->name }}</h4>
            <table class="table table-condensed">
                <tr>
                    <th class="bg-gray">Serviço</th>
                    <th class="bg-gray w-120px">Portugal</th>
                    <th class="bg-gray w-120px">Espanha</th>
                    <th class="bg-gray w-120px">Internacional</th>
                </tr>
                @foreach($services as $service)
                    <tr>
                        <td class="vertical-align-middle">{{ $service->name }}</td>
                        <td>
                            @if($listProviderServices)
                                {{ Form::select('mapping_services['.$service->id.'][pt]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? 'disabled' : '']) }}
                            @else
                                {{ Form::text('mapping_services['.$service->id.'][pt]', null, ['class' => 'form-control', $service->is_internacional ? 'disabled' : '']) }}
                            @endif
                        </td>
                        <td>
                            @if($listProviderServices)
                                {{ Form::select('mapping_services['.$service->id.'][es]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? 'disabled' : '']) }}
                            @else
                                {{ Form::text('mapping_services['.$service->id.'][es]', null, ['class' => 'form-control', $service->is_internacional ? 'disabled' : '']) }}
                            @endif
                        </td>
                        <td>
                            @if($listProviderServices)
                                {{ Form::select('mapping_services['.$service->id.'][int]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? '' : 'disabled']) }}
                            @else
                                {{ Form::text('mapping_services['.$service->id.'][int]', null, ['class' => 'form-control', $service->is_internacional ? '' : 'disabled']) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
    @endif
@endif