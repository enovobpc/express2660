@if($zipCodes->isEmpty())
    <div class="helper text-info">
        <i class="fas fa-info-circle"></i>
        Não foram encontrados códigos postais, ou todos os códigos postais já se
        encontram associados a uma agência.
    </div>
@else
    <table class="table import-search-results">
        <tr>
            <th>{{ Form::checkbox('select-all-zip-codes') }}</th>
            <th>Código Postal</th>
            <th>Localidade</th>
            <th>Distrito/Região/Provincia</th>
            <th>Concelho/Município</th>
        </tr>
        @foreach($zipCodes as $zipCode)
            <tr>
                <td>
                    {{ Form::checkbox('zip_code[]', $zipCode->zip_code, null, ['class' => 'select-zip-code']) }}
                </td>
                <td>{{ $zipCode->zip_code }}</td>
                <td>{{ $zipCode->postal_designation }}</td>
                <td>{{ trans('districts_codes.districts.' . $zipCode->country . '.' .$zipCode->district_code) }}</td>
                <td>
                    @if(!in_array($zipCode->country, ['es']))
                    {{ trans('districts_codes.counties.' . $zipCode->country .'.' . $zipCode->district_code .'.' . $zipCode->county_code) }}
                    @else
                    {{ trans('districts_codes.districts.' . $zipCode->country . '.' .$zipCode->district_code) }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
@endif