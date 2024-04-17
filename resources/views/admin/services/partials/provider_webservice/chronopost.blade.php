<table class="table table-condensed">
    <tr>
        <th class="bg-gray">País Destino</th>
        <th class="bg-gray">Conta Chronopost</th>
    </tr>
    <tr>
        <td class="vertical-align-middle">Portugal</td>
        <td>
            {{ Form::text('webservice_mapping['.$provider->id.'][pt]', @$service->webservice_mapping[$provider->id]['pt'], ['class' => 'form-control']) }}
        </td>
    </tr>
    <tr>
        <td class="vertical-align-middle">Espanha</td>
        <td>
            {{ Form::text('webservice_mapping['.$provider->id.'][es]', @$service->webservice_mapping[$provider->id]['es'], ['class' => 'form-control']) }}
        </td>
    </tr>
    <tr>
        <td class="vertical-align-middle">Internacional</td>
        <td>
            {{ Form::text('webservice_mapping['.$provider->id.'][int]', @$service->webservice_mapping[$provider->id]['int'], ['class' => 'form-control provider-int']) }}
        </td>
    </tr>
</table>