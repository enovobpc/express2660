<table class="table table-condensed m-b-5 table-products">
    <tr>
        <th class="bg-gray">@trans('Artigo')</th>
        <th class="w-1 bg-gray">@trans('Qtd')</th>
        <th class="w-1 bg-gray">@trans('Rcb')</th>
        @if($allowEdit)
        <th class="w-1 bg-gray">
            {{--<button type="button" class="btn btn-sm btn-default btn-auto-read">
                <i class="fas fa-angle-right"></i>
            </button>--}}
        </th>
        @endif
    </tr>
    @if(@$receptionOrder->lines->isEmpty())
        <tr>
            <td class="vertical-align-middle" colspan="3">
                @trans('Não selecionou uma ordem de recepção')
            </td>
        </tr>
    @else
        @foreach(@$receptionOrder->lines as $line)
            <tr class="{{ @$line->qty_received > 0 ? (@$line->qty_received == @$line->qty ? 'rw-green' : 'rw-red') : '' }}">
                <td class="vertical-align-middle">
                    {{ @$line->product->name }}
                    <br/>
                    <small class="text-muted">{{ @$line->product->sku }}</small>
                </td>
                <td class="vertical-align-middle text-center">{{ @$line->qty }}</td>
                <td class="vertical-align-middle text-center bold {{ @$line->qty == @$line->qty_received ? 'text-green' : 'text-red' }}">{{ @$line->qty_received ? @$line->qty_received : 0 }}</td>
                @if($allowEdit)
                <td class="vertical-align-middle">
                    <button type="button" class="btn btn-sm btn-default btn-auto-read"
                            data-id="{{ @$line->product->id }}"
                            data-sku="{{ @$line->product->sku }}">
                        <i class="fas fa-angle-right"></i>
                    </button>
                </td>
                @endif
            </tr>
        @endforeach
    @endif
</table>