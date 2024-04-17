<h4 class="m-t-0"><b>Pré-visualização</b> @if(isset($previewRows) && !empty($previewRows) )| {{ count($previewRows) }} registos encontrados.@endif</h4>
@if($hasErrors)
    <h4 class="text-red"><i class="fas fa-exclamation-triangle"></i> Há {{ $hasErrors }} erros necessários corrigir antes de importar.</h4>
@elseif(@$previewRows)
    <h4 class="text-green">
        <a href="{{ route('account.importer.index') }}" class="btn btn-default pull-right m-l-5" style="margin-top: -17px;">Cancelar</a>
        <button class="btn btn-success pull-right btn-conclude" type="button" style="margin-top: -17px;"><i class="fas fa-check"></i> Concluir importação</button>
        <i class="fas fa-check"></i> Não foram encontrados erros.
    </h4>
@endif
<div style="overflow: scroll; border: 1px solid #999; height: 365px;">
    @if(isset($previewRows) && !empty($previewRows))
        <table class="table table-condensed table-responsive table-hover table-bordered table-preview m-0">
            <tr class="bg-gray">
                @foreach($headerRow as $header)
                    <th style="white-space: nowrap">{{ trans('admin/importer.'.$importType.'.'.$header.'.name') }}</th>
                @endforeach
            </tr>
            @foreach($previewRows as $rowId => $row)
                @if(!empty(@$row['errors']))
                    <tr style="background: red; color: #fff">
                        @foreach($headerRow as $header)
                            <td style="white-space: nowrap">
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
                            <td style="white-space: nowrap">
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
                                @elseif($header == 'assembly')
                                    {{@$row['dimensions'][0]['assembly']}}
                                @elseif($header == 'sku')
                                    {{@$row['dimensions'][0]['sku']}}
                                @elseif($header == 'qty')
                                    {{@$row['dimensions'][0]['qty']}}
                                @elseif($header == 'article_name')
                                    {{@$row['dimensions'][0]['article_name']}}
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