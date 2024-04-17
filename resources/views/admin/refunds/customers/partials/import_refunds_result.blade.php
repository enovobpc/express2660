@if($totalErrors)
<p class="text-red">
    <i class="fas fa-exclamation-triangle"></i> <b>{{ $totalSuccess }}</b> registos lidos com sucesso. <b>{{ $totalErrors }}</b> registos não encontrados em sistema.
</p>
@else
<p class="text-green">
    <i class="fas fa-check-circle"></i> <b>{{ $totalSuccess }}</b> registos encontrados com sucesso.
</p>
@endif

<div style="height: 235px; overflow: scroll; border: 1px solid #ddd;">
    <table class="table table-condensed">
        <tr>
            <th class="w-1">#</th>
            <th class="w-5">TRK</th>
            <th>TRK {{ $provider }}</th>
            <th>Informação adicional</th>
            <th class="w-95px">Data Recb.</th>
            <th class="w-150px">Forma Recebimento</th>
        </tr>
        @foreach($rows as $key => $item)
            <tr style="{{ $item['success'] ? 'color: #00a623' : 'color: red' }}">
                <td style="vertical-align: middle">{{ $key + 1 }}</td>
                <td style="vertical-align: middle">
                    @if($item['success'])
                    {{ $item['trk'] }}
                    @else
                        N/A
                    @endif
                </td>
                <td style="vertical-align: middle">{{ $item['provider_trk'] }}</td>
                <td style="vertical-align: middle">{{ $item['message'] }}</td>
                <td style="vertical-align: middle">
                    @if($item['success'])
                        {{ $item['date'] }}
                    @endif
                </td>
                <td class="input-sm" style="vertical-align: middle">
                    @if($item['success'])
                    {{ Form::select('method['.$item['shipment'].']', [''=>'', 'money' => 'Numerário', 'transfer' => 'Transferência', 'check' => 'Cheque', 'mb' => 'Multibanco'], $item['method'], ['class' => 'form-control select2']) }}
                    {{ Form::hidden('date['.$item['shipment'].']', $item['date']) }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
</div>
