<div class="row">
    <div class="col-sm-12">
        <h4>Configurar ligação aos serviços DPD</h4>
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
            <tr>
                <td class="vertical-align-middle">Entrega Ponto Pickup</td>
                <td>
                    @if($listProviderServices)
                        {{ Form::select('mapping_services['.$service->id.'][ptpudo]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? 'disabled' : '']) }}
                    @else
                        {{ Form::text('mapping_services['.$service->id.'][ptpudo]', null, ['class' => 'form-control', $service->is_internacional ? 'disabled' : '']) }}
                    @endif
                </td>
                <td>
                    @if($listProviderServices)
                        {{ Form::select('mapping_services['.$service->id.'][espudo]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? 'disabled' : '']) }}
                    @else
                        {{ Form::text('mapping_services['.$service->id.'][espudo]', null, ['class' => 'form-control', $service->is_internacional ? 'disabled' : '']) }}
                    @endif
                </td>
                <td>
                    @if($listProviderServices)
                        {{ Form::select('mapping_services['.$service->id.'][intpudo]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? '' : 'disabled']) }}
                    @else
                        {{ Form::text('mapping_services['.$service->id.'][intpudo]', null, ['class' => 'form-control', $service->is_internacional ? '' : 'disabled']) }}
                    @endif
                </td>
            </tr>
            <tr>
                <td class="vertical-align-middle">Entrega ao Sábado</td>
                <td>
                    @if($listProviderServices)
                        {{ Form::select('mapping_services['.$service->id.'][ptsab]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? 'disabled' : '']) }}
                    @else
                        {{ Form::text('mapping_services['.$service->id.'][ptsab]', null, ['class' => 'form-control', $service->is_internacional ? 'disabled' : '']) }}
                    @endif
                </td>
                <td>
                    @if($listProviderServices)
                        {{ Form::select('mapping_services['.$service->id.'][essab]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? 'disabled' : '']) }}
                    @else
                        {{ Form::text('mapping_services['.$service->id.'][essab]', null, ['class' => 'form-control', $service->is_internacional ? 'disabled' : '']) }}
                    @endif
                </td>
                <td>
                    @if($listProviderServices)
                        {{ Form::select('mapping_services['.$service->id.'][intsab]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? '' : 'disabled']) }}
                    @else
                        {{ Form::text('mapping_services['.$service->id.'][intsab]', null, ['class' => 'form-control', $service->is_internacional ? '' : 'disabled']) }}
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>