<?php
$srvList = ['' => ''] + trans('admin/webservices.services.ctt_expresso');
?>
<table class="table table-condensed">
    <tr>
        <th class="bg-gray">País Destino</th>
        <th class="bg-gray w-100px">1 volume</th>
        <th class="bg-gray w-100px">Multiplos vol</th>
    </tr>
    <tr>
        <td>Portugal</td>
        <td>
            {{ Form::select('webservice_mapping['.$provider->id.'][pt]', $srvList, @$service->webservice_mapping[$provider->id]['pt'], ['class' => 'form-control select2']) }}
        </td>
        <td>
            {{ Form::select('webservice_mapping['.$provider->id.'][ptm]', $srvList, @$service->webservice_mapping[$provider->id]['ptm'], ['class' => 'form-control select2']) }}
        </td>
    </tr>
    <tr>
        <td>Espanha</td>
        <td>
            {{ Form::select('webservice_mapping['.$provider->id.'][es]', $srvList, @$service->webservice_mapping[$provider->id]['es'], ['class' => 'form-control select2']) }}
        </td>
        <td>
            {{ Form::select('webservice_mapping['.$provider->id.'][esm]', $srvList, @$service->webservice_mapping[$provider->id]['esm'], ['class' => 'form-control select2']) }}
        </td>
    </tr>
    <tr>
        <td>Internacional</td>
        <td>
            {{ Form::select('webservice_mapping['.$provider->id.'][int]', $srvList, @$service->webservice_mapping[$provider->id]['int'], ['class' => 'form-control select2 provider-int']) }}
        </td>
        <td>
            {{ Form::select('webservice_mapping['.$provider->id.'][intm]', $srvList, @$service->webservice_mapping[$provider->id]['intm'], ['class' => 'form-control select2 provider-int']) }}
        </td>
    </tr>
</table>