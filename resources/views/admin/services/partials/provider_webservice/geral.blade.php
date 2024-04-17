@if(is_array(trans('admin/webservices.services.' . @$provider->webservice_method)))
<?php
    $srvList = ['' => ''] + trans('admin/webservices.services.' . @$provider->webservice_method);
?>
<table class="table table-condensed">
    <tr>
        <th class="bg-gray">País Destino</th>
        <th class="bg-gray">Serviço {{ @$provider->webservice->name }}</th>
    </tr>
    <tr>
        <td class="vertical-align-middle">Portugal</td>
        <td>
            {{ Form::select('webservice_mapping['.$provider->id.'][pt]', $srvList, @$service->webservice_mapping[$provider->id]['pt'], ['class' => 'form-control select2']) }}
        </td>
    </tr>
    <tr>
        <td class="vertical-align-middle">Espanha</td>
        <td>
            {{ Form::select('webservice_mapping['.$provider->id.'][es]', $srvList, @$service->webservice_mapping[$provider->id]['es'], ['class' => 'form-control select2']) }}
        </td>
    </tr>
    <tr>
        <td class="vertical-align-middle">Internacional</td>
        <td>
            {{ Form::select('webservice_mapping['.$provider->id.'][int]', $srvList, @$service->webservice_mapping[$provider->id]['int'], ['class' => 'form-control select2 provider-int']) }}
        </td>
    </tr>
</table>
@else
    <p class="text-yellow">Não é possível personalizar a associação com os serviços do fornecedor {{ @$provider->webservice->name }}</p>
@endif