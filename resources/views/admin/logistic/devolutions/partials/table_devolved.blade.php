<table class="table table-condensed m-b-5">
    <tr>
        <th class="bg-gray">@trans('Artigo')</th>
        <th class="w-80px bg-gray">@trans('Qtd')</th>
        <th class="w-100px bg-gray">@trans('Destino')</th>
        <th class="w-115px bg-gray">@trans('Estado')</th>
        @if($allowEdit)
        <th class="w-1 bg-gray"></th>
        @endif
    </tr>
    @if(@$devolution->items->isEmpty())
        <tr>
            <td class="vertical-align-middle" colspan="4">
                @trans('Nenhum artigo adicionado à devolução')
            </td>
        </tr>
    @else
        @foreach($devolution->items as $line)
            <tr data-url="{{ route('admin.logistic.devolutions.items.update', [$line->devolution_id, $line->id]) }}">
                <td class="vertical-align-middle">
                    {{ @$line->product->name }}
                    <br/>
                    <small class="text-muted">{{ @$line->product->sku }}</small>
                </td>
                <td class="vertical-align-middle text-center">
                    @if($allowEdit)
                    <input type="text" name="qty" value="{{ $line->qty }}" class="form-control text-center number" style="padding: 2px"/>
                    @else
                        {{ $line->qty }}
                    @endif
                </td>
                <td class="vertical-align-middle">{{ @$line->location->code }}</td>
                <td class="vertical-align-middle">
                    @if($allowEdit)
                    <select name="status" class="form-control select2">
                        <option value="">@trans('Intacto')</option>
                        <option value="damaged" {{ $line->status == 'damaged' ? 'selected' : '' }}>@trans('Danificado')</option>
                    </select>
                    @else
                        {{ $line->status == 'damaged' ? __('Danificado') : __('Intacto') }}
                    @endif
                </td>
                @if($allowEdit)
                <td class="vertical-align-middle">
                    <a href="{{ route('admin.logistic.devolutions.items.destroy', [$devolution->id, $line->id]) }}"
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