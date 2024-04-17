<div class="row">
    <div class="col-sm-12">
        <h4>Configurar ligação aos serviços CTT Expresso</h4>
        <table class="table table-condensed">
            <tr>
                <th class="bg-gray">Serviço</th>
                <th class="bg-gray w-160px">Portugal</th>
                <th class="bg-gray w-160px">Espanha</th>
                <th class="bg-gray w-160px">Internacional</th>
            </tr>
            @foreach($services as $service)
                <tr>
                    <td class="vertical-align-middle">{{ $service->name }}</td>
                    <td>
                        <div class="input-group">
                            <div class="input-group-addon" style="padding: 5px">1 <i class="fas fa-box-open"></i>&nbsp;</div>
                            {{ Form::select('mapping_services['.$service->id.'][pt]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? 'disabled' : '']) }}
                        </div>
                        <div class="input-group">
                            <div class="input-group-addon" style="padding: 5px">+1<i class="fas fa-box-open"></i></div>
                            {{ Form::select('mapping_services['.$service->id.'][ptm]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? 'disabled' : '']) }}
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <div class="input-group-addon" style="padding: 5px">1 <i class="fas fa-box-open"></i>&nbsp;</div>
                            {{ Form::select('mapping_services['.$service->id.'][es]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? 'disabled' : '']) }}
                        </div>
                        <div class="input-group">
                            <div class="input-group-addon" style="padding: 5px">+1<i class="fas fa-box-open"></i></div>
                            {{ Form::select('mapping_services['.$service->id.'][esm]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? 'disabled' : '']) }}
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <div class="input-group-addon" style="padding: 5px">1 <i class="fas fa-box-open"></i>&nbsp;</div>
                            {{ Form::select('mapping_services['.$service->id.'][int]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? '' : 'disabled']) }}
                        </div>
                        <div class="input-group">
                            <div class="input-group-addon" style="padding: 5px">+1<i class="fas fa-box-open"></i></div>
                            {{ Form::select('mapping_services['.$service->id.'][intm]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? '' : 'disabled']) }}
                        </div>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td class="vertical-align-middle">Serviço com Seguro</td>
                <td>
                    <div class="input-group">
                        <div class="input-group-addon" style="padding: 5px">1 <i class="fas fa-box-open"></i>&nbsp;</div>
                        {{ Form::select('mapping_services['.$service->id.'][ptseg]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? '' : 'disabled']) }}
                    </div>
                    <div class="input-group">
                        <div class="input-group-addon" style="padding: 5px">+1<i class="fas fa-box-open"></i></div>
                        {{ Form::select('mapping_services['.$service->id.'][ptsegm]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? '' : 'disabled']) }}
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <div class="input-group-addon" style="padding: 5px">1 <i class="fas fa-box-open"></i>&nbsp;</div>
                        {{ Form::select('mapping_services['.$service->id.'][esseg]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? '' : 'disabled']) }}
                    </div>
                    <div class="input-group">
                        <div class="input-group-addon" style="padding: 5px">+1<i class="fas fa-box-open"></i></div>
                        {{ Form::select('mapping_services['.$service->id.'][essegm]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? '' : 'disabled']) }}
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <div class="input-group-addon" style="padding: 5px">1 <i class="fas fa-box-open"></i>&nbsp;</div>
                        {{ Form::select('mapping_services['.$service->id.'][intseg]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? '' : 'disabled']) }}
                    </div>
                    <div class="input-group">
                        <div class="input-group-addon" style="padding: 5px">+1<i class="fas fa-box-open"></i></div>
                        {{ Form::select('mapping_services['.$service->id.'][intsegm]', $listProviderServices, null, ['class' => 'form-control select2', $service->is_internacional ? '' : 'disabled']) }}
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>