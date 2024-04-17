<table class="table table-condensed m-0" style="margin-top: -2px">
    <tr>
        <th class="bg-dark" style="border-radius: 3px 0 0 0">@trans('Sku')</th>
        <th class="bg-dark">@trans('Artigo')</th>
        <th class="bg-dark">@trans('Cliente')</th>
        <th class="bg-dark w-100px">@trans('Localização')</th>
        <th class="bg-dark w-50px">@trans('Stock')</th>
        <th class="bg-dark w-80px">@trans('Disponivel')</th>
        <th class="bg-dark w-80px">@trans('Danificado')</th>
        @if($inventory->allow_edit)
        <th class="bg-dark w-1"></th>
        @endif
    </tr>
    @if(!$inventory->lines->isEmpty())
        <?php
        $lastItem = null;
        $borderBold = false;
        $lines = $inventory->lines;
        $lines = $lines->reverse();
        ?>
        @foreach($lines as $line)
            <?php
                if($lastItem && $lastItem != $line->product_id) {
                    $borderBold = true;
                }
             ?>
            <tr data-id="{{ $line->id }}"
                data-qty-existing="{{ $line->qty_existing }}"
                data-url="{{ route('admin.logistic.inventories.items.update', [$line->inventory_id, $line->id]) }}"
                class="{{ $line->qty_real == $line->qty_existing && $line->qty_real != 0 ? 'row-green' : ($line->qty_real == 0 ? 'row-red' : 'row-yellow') }} {{ $borderBold ? 'border-bold' : '' }}">
                <td>{{ @$line->product->sku }}</td>
                <td>
                    {{ @$line->product->name }}
                    <a href="{{ route('admin.logistic.products.edit', @$line->product_id) }}" target="_blank">
                        <i class="fas fa-external-link"></i>
                    </a>
                </td>
                <td>{{ @$line->product->customer->name }}</td>
                @if($inventory->allow_edit)
                    <td>
                        @if(@$line->location->code)
                        {{ Form::select('location_id['.$line->id.']', [@$line->location->id => @$line->location->code], null, ['class' => 'form-control p-location text-center select2']) }}
                        @else
                        {{ Form::select('location_id['.$line->id.']', ['' => ''] + $locations, null, ['class' => 'form-control p-location l-empty text-center select2']) }}
                        @endif
                    </td>
                    <td class="bold text-center vertical-align-middle">{{ $line->qty_existing }}</td>
                    <td class="w-60px">
                        {{ Form::text('qty_real['.$line->id.']', $line->qty_real, ['class' => 'form-control qty-real text-center']) }}
                    </td>
                    <td class="w-60px">
                        {{ Form::text('qty_damaged['.$line->id.']', $line->qty_damaged, ['class' => 'form-control qty-damaded text-center']) }}
                    </td>
                    <td>
                        <a href="{{ route('admin.logistic.inventories.items.destroy', [$line->inventory_id, $line->id]) }}" class="text-red btn-delete-product">
                            <i class="fas fa-times"></i>
                        </a>
                    </td>
                @else
                    <td>{{ @$line->location->code }}</td>
                    <td class="bold text-center vertical-align-middle">{{ $line->qty_existing }}</td>
                    <td class="w-60px text-center">{{ $line->qty_real }}</td>
                    <td class="w-60px text-center">{{ $line->qty_damaged }}</td>
                @endif
            </tr>
            <?php $borderBold = false; $lastItem = $line->product_id ?>
        @endforeach
    @endif
</table>