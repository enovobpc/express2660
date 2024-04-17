<h4 class="m-t-0"><b>@trans('Pré-visualização')</b> @if(isset($previewRows) && !empty($previewRows) )| {{ count($previewRows) }} @trans('registos encontrados.')@endif</h4>
@if($hasErrors)
    <h4 class="text-red"><i class="fas fa-exclamation-triangle"></i> @trans('Há') {{ $hasErrors }} @trans('erros necessários corrigir antes de importar.')</h4>
@endif

@if($previewRows > 300)

    <div style="overflow: scroll; border: 1px solid #999; height: 365px;">
        @if(isset($previewRows) && !empty($previewRows))
            <table class="table table-condensed table-responsive table-hover table-bordered table-preview m-0">
                <tr class="bg-gray">
                    @foreach($headerRow as $header)
                        <th>{{ trans('admin/importer.'.$importType.'.'.$header.'.name') }}</th>
                    @endforeach
                </tr>
                @foreach($previewRows as $rowId => $row)
                    @if(isset($row['errors']))
                        <tr style="background: red; color: #fff">
                            @foreach($headerRow as $header)
                                <td>
                                    @if($header == 'service_id')
                                        {{ @$services[@$row[$header]] }}
                                    @elseif($header == 'customer_id')
                                        @if(isset($customers[@$row[$header]]))
                                            {{ @$customers[@$row[$header]] }}
                                        @else
                                            {!! '<i class="text-muted">Sem cliente</i>' !!}
                                        @endif
                                    @elseif($header == 'provider_id')
                                        {{ @$providers[@$row[$header]] }}
                                    @elseif($header == 'status_id')
                                        {{ @$shipmentStatus[@$row[$header]] }}
                                    @elseif($header == 'operator_id')
                                        {{ @$operators[@$row[$header]] }}
                                    @elseif(in_array($header, ['recipient_country','sender_country', 'country']))
                                        {!! '<i class="flag-icon flag-icon-'.@$row[$header].'"></i> ' . strtoupper(@$row[$header]) !!}
                                    @elseif(trans('admin/importer.'.$importType.'.'.$header.'.checkbox') == 'true')
                                        <div class="text-center">
                                            <input type="checkbox" class="preview-checkbox" data-field="{{ $header }}" data-row-id="{{ $rowId }}" {{ @$row[$header] == '1' ? 'checked' : '' }} value="1">
                                        </div>
                                    @else
                                        {{ @$row[$header] }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @include('admin.files_importer.partials.row_dimensions')
                        <tr style="color: red;">
                            <td colspan="{{ count($headerRow) }}" style="border-top: none; background: #fcdbdb; padding: 0 5px 5px;">
                                @foreach($row['errors'] as $error)
                                    <p class="m-0"><i class="fas fa-exclamation-triangle"></i> {{ $error }}</p>
                                @endforeach
                            </td>
                        </tr>
                    @else
                        <tr class="item-row" style="{{ isset($row['dimensions']) ? 'background: #cecece; border-top: 2px solid #222' : '' }}">
                            @foreach($headerRow as $header)
                                <td>
                                    @if($header == 'service_id')
                                        {{ @$services[@$row[$header]] }}
                                    @elseif($header == 'customer_id')
                                        @if(isset($customers[@$row[$header]]))
                                            {{ @$customers[@$row[$header]] }}
                                        @else
                                            {!! '<i class="text-muted">Sem cliente</i>' !!}
                                        @endif
                                    @elseif($header == 'provider_id' || $header == 'provider_code')
                                        {{ @$providers[@$row['provider_id']] }}
                                    @elseif($header == 'status_id')
                                        {{ @$shipmentStatus[@$row[$header]] }}
                                    @elseif(in_array($header, ['recipient_country','sender_country', 'country']))
                                        {!! '<i class="flag-icon flag-icon-'.@$row[$header].'"></i> ' . strtoupper(@$row[$header]) !!}
                                    @elseif(trans('admin/importer.'.$importType.'.'.$header.'.checkbox') == 'true')
                                        <div class="text-center">
                                            <input type="checkbox" class="preview-checkbox" data-field="{{ $header }}" data-row-id="{{ $rowId }}" {{ @$row[$header] == '1' ? 'checked' : '' }} value="1">
                                        </div>
                                    @elseif($header == 'vehicle_id' || $header == 'license_plate')
                                        {{ @$vehicles[@$row['vehicle_id']] ?? @$row['license_plate'] }}
                                    @elseif($header == 'price_table_id')
                                        {{ @$pricesTables[@$row['price_table_id']] }}
                                    @else
                                        {{ @$row[$header] }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @include('admin.files_importer.partials.row_dimensions')
                    @endif
                @endforeach
            </table>
        @endif
    </div>
@else
<div style="overflow: scroll; border: 1px solid #999; height: 365px;">
    @if(isset($previewRows) && !empty($previewRows))
        <table class="table table-condensed table-responsive table-hover table-bordered table-preview m-0">
            <tr class="bg-gray">
                @foreach($headerRow as $header)
                    <th>{{ trans('admin/importer.'.$importType.'.'.$header.'.name') }}</th>
                @endforeach
            </tr>
            @foreach($previewRows as $rowId => $row)
                @if(isset($row['errors']))
                    <tr style="background: red; color: #fff">
                        @foreach($headerRow as $header)

                            <td>
                                @if($header == 'service_id')
                                    {{ @$services[@$row[$header]] }}
                                @elseif($header == 'customer_id')
                                    @if(isset($customers[@$row[$header]]))
                                        {{ @$customers[@$row[$header]] }}
                                    @else
                                        {!! '<i class="text-muted">Sem cliente</i>' !!}
                                    @endif
                                @elseif($header == 'provider_id')
                                    {{ @$providers[@$row[$header]] }}
                                @elseif($header == 'status_id')
                                    {{ @$shipmentStatus[@$row[$header]] }}
                                @elseif(in_array($header, ['recipient_country','sender_country', 'country']))
                                    {!! '<i class="flag-icon flag-icon-'.@$row[$header].'"></i> ' . strtoupper(@$row[$header]) !!}
                                @else
                                    {{ @$row[$header] }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @if(isset($row['dimensions']))
                        <tr style="color: red;">
                            <td colspan="{{ count($headerRow) }}" style="border-top: none; background: #fcdbdb; padding: 0 5px 5px;">
                                <table class="w-100">
                                    <tr>
                                        <th>@trans('Qtd')</th>
                                        <th>@trans('SKU')</th>
                                        <th>@trans('Nº Série')</th>
                                        <th>@trans('Lote')</th>
                                    </tr>
                                @foreach($row['dimensions'] as $dimension)
                                    <tr>
                                        <td>{{ $dimension['qty'] }}</td>
                                        <td>{{ $dimension['sku'] }}</td>
                                        <td>{{ $dimension['serial_no'] }}</td>
                                        <td>{{ $dimension['lote'] }}</td>
                                    </tr>
                                @endforeach
                                </table>
                            </td>
                        </tr>
                    @endif
                    <tr style="color: red;">
                        <td colspan="{{ count($headerRow) }}" style="border-top: none; background: #fcdbdb; padding: 0 5px 5px;">
                            @foreach($row['errors'] as $error)
                                <p class="m-0"><i class="fas fa-exclamation-triangle"></i> {{ $error }}</p>
                            @endforeach
                        </td>
                    </tr>
                @else
                    <tr class="item-row">
                        @foreach($headerRow as $header)
                            <td>
                                @if($header == 'service_id')
                                    {{ @$services[@$row[$header]] }}
                                @elseif($header == 'customer_id')
                                    @if(isset($customers[@$row[$header]]))
                                        {{ @$customers[@$row[$header]] }}
                                    @else
                                        {!! '<i class="text-muted">Sem cliente</i>' !!}
                                    @endif
                                @elseif($header == 'provider_id')
                                    {{ @$providers[@$row[$header]] }}
                                @elseif($header == 'status_id')
                                    {{ @$shipmentStatus[@$row[$header]] }}
                                @elseif(in_array($header, ['recipient_country','sender_country', 'country']))
                                    {!! '<i class="flag-icon flag-icon-'.@$row[$header].'"></i> ' . strtoupper(@$row[$header]) !!}
                                @elseif(trans('admin/importer.'.$importType.'.'.$header.'.checkbox') == 'true')
                                    <div class="text-center">
                                        <input type="checkbox" class="preview-checkbox" data-field="{{ $header }}" data-row-id="{{ $rowId }}" {{ @$row[$header] == '1' ? 'checked' : '' }} value="1">
                                    </div>
                                @else
                                    {{ @$row[$header] }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endif
            @endforeach
        </table>
    @endif
</div>
@endif