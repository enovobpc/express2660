<table class="table table-condensed m-b-0 m-t-10" style="background: #fff; max-height: 320px; overflow-y: auto; display: block;">
    <tr class="bg-gray">
        <th class="w-140px">SKU</th>
        <th>Artigo</th>
        <th class="w-110px">Localização</th>
        <th class="w-65px">Max</th>
        <th class="w-65px">Qtd</th>
        <th class="w-1"></th>
    </tr>
    <tbody>
        @forelse ($event->lines as $line)
            <tr>
                <td class="vertical-align-middle">
                    {{ $line->product->sku ?? 'N/A' }}
                    {{ Form::hidden('line_id', $line->id ?? null) }}
                </td>
                <td class="vertical-align-middle">
                    {{ $line->product->name ?? $line->name ?? 'N/A' }}
                </td>
                <td class="vertical-align-middle">
                    {{ $line->location->code ?? '' }}
                </td>
                <td class="vertical-align-middle">
                    {{ $line->product->stock_available ?? 'N/A' }}
                </td>
                <td class="vertical-align-middle">
                    {{ Form::text('product_qty[]', @$line->qty, [
                        'class' => 'form-control input-sm text-center number',
                        'maxlength' => 6,
                        'placeholder' => 'Max ' .@$line->product_location->stock,
                        'data-qty' => @$line->qty,
                        'data-max' => $line->product->stock_available ?? '',
                        'data-url' => route('admin.event-manager.line.update', [$event->id, $line->id]) // Both need to exist!!
                        ]) }}
                    <small class="text-red qty-helper" style="display: none">Max. {{ (@$line->product_location->stock + $line->qty) }}</small>
                </td>
                <td class="vertical-align-middle">
                    <a href="{{ route('admin.event-manager.line.destroy', [$event->id, $line->id]) }}" class="text-red btn-delete-line">
                        <i class="fas fa-times"></i>
                    </a>
                </td>
            </tr>
        @empty
            <tr class="tr-empty">
                <td colspan="8">
                    <p class="p-10 text-center">
                        <i class="fas fa-info-circle"></i> Não há artigos no pedido.
                    </p>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>