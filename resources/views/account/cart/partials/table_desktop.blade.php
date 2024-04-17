<table class="table table-hover table-condensed table-shopping-cart hidden-xs">
    <thead>
    <tr>
        <th class="col-sm-3 col-lg-1">{{ trans('account/cart.cart.product') }}</th>
        <th class="col-sm-3 col-lg-5 text-center">Nome</th>
        <th class="col-sm-3 col-lg-3">{{ trans('account/cart.cart.qty') }}</th>
        <th class="text-center w-30px col-sm-1">{{ trans('account/cart.cart.unity') }}</th>
        <th class="text-center w-30px col-sm-1">{{ trans('account/cart.cart.total') }}</th>
        <th style="width: 1%"></th>
    </tr>
    </thead>
    <tbody>
        @foreach($products as $item)
        <tr id="row-{{ $item->id }}">
            <td class="vertical-align-middle" style="width: 1%">
                @if($item->photo)
                    <img src="{{ asset($item->photo) }}" class="height-35">
                @else
                    <img src="{{ asset('assets/img/default/default.thumb.png') }}" class="height-35">
                @endif
            </td>
            <td class="text-center vertical-align-middle">
                {{ $item->product->name }}
            </td>
            <td class="text-center vertical-align-middle">
                <form method="POST" action="{{ route('cart.row.update', $item->id) }}" accept-charset="UTF-8" class="update-quantity">
                    <div class="input-group number-spinner">
                        {{ Form::number('quantity', $item->qty, ['class' => 'form-control text-center update-product number', 'min' => 1, 'max' => 999, 'autocomplete' => 'off']) }}
                        <span class="input-group-btn data-up"></span>
                    </div>
                </form>
            </td>
            
            <td class="text-center vertical-align-middle">
                {{ money($item->price_unity, '€') }}
            </td>
            <td class="text-center vertical-align-middle bold"><span class="subtotal">{{ money($item->total, '€') }}</span></td>
            <td class="vertical-align-middle">
                <a href="{{ route('cart.row.destroy', $item->id) }}" class="text-red bigger-130 remove-product">
                    <span class="fa fa-times"></span>
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>