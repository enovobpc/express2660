<table class="table table-condensed m-b-5">
    <tr>
        <th class="bg-gray">@trans('Artigo')</th>
        <th class="w-80px bg-gray">@trans('Qtd')</th>
        <th class="w-100px bg-gray">@trans('Destino')</th>
        @if($allowEdit)
        <th class="w-1 bg-gray"></th>
        @endif
    </tr>
    @if(@$receptionOrder->lines->isEmpty())
        <tr>
            <td class="vertical-align-middle" colspan="4">
                @trans('Nenhum artigo foi confirmado na recepção')
            </td>
        </tr>
    @else
        @foreach($receptionOrder->confirmation as $line)
            <tr data-url="{{ route('admin.logistic.reception-orders.confirmation.line.update', [$receptionOrder->id, $line->id]) }}">
                <td class="vertical-align-middle">
                    {{ @$line->product->name }}
                    <br/>
                    <small class="text-muted">{{ @$line->product->sku }}</small>
                </td>
                <td class="vertical-align-middle text-center">
                    @if($allowEdit)
                        <input type="text" name="qty" value="{{ $line->qty_received }}" class="form-control text-center number input-sm" style="padding: 2px"/>
                    @else
                        {{ $line->qty_received }}
                    @endif
                </td>
                <td class="vertical-align-middle">{{ @$line->location->code }}</td>
                @if($allowEdit)
                <td class="vertical-align-middle">
                    <a href="{{ route('admin.logistic.reception-orders.confirmation.line.destroy', [$receptionOrder->id, $line->id]) }}"
                       data-ajax-confirm="@trans('Confirma a remoção da linha selecionada?')"
                       data-ajax-method="delete"
                       data-confirm-callback="removeItem"
                       class="vertical-align-middle text-red">
                        <i class="fas fa-times"></i>
                    </a>
                </td>
                @endif
            </tr>
        @endforeach
    @endif
</table>