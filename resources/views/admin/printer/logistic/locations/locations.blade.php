
<table class="table table-bordered table-pdf m-b-5" style="font-size: 6.3pt; border: none">
    <tr>
        <th>Armazém</th>
        
        <th style="width: 30px">Localização</th>
        <th>Código de barras</th>
        <th style="width: 150px">Tipologia</th>
        <th>Peso Max</th>
        <th class="w-100px">Paletes</th>
        <th style="width: 150px">Estado</th>
    </tr>
@foreach($locations as $location)

    <tr>
        <td>
            {{@$location->warehouse->name }}
        </td>
        <td>
            {{@$location->code }}
        </td>
        <td>
            {{ @$location->barcode }}
        </td>
        <td>
            {{ @$location->type->name }}
        </td>
        <td>
            {{ @$location->max_weight }}
        </td>
        <td>
            {{ @$location->max_pallets }}
        </td>
        <td>
            {{  trans('admin/logistic.locations.status.' . $location->status) }}
        </td>
    </tr>
    
@endforeach
</table>
