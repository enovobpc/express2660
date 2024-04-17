@section('title')
    {{ trans('account/global.menu.sales-products') }} -
@stop

@section('account-content')
<div class="row">
    @foreach ($products as $product)
    <div class="col-sm-6">
        <div class="media-left">
            @if ($product->filepath)
                <img src="{{ asset($product->filepath) }}" class="media-object"
                    onerror="this.src='{{ asset('assets/img/default/img_broken.png') }}'" style="width:175px; height:175px"/>
            @else
                <img src="{{ asset('assets/img/default/default.thumb.png') }}" class="media-object" style="width:175px; height:175px"/>
            @endif
        </div>
        <div class="media-body">
            <h4 class="media-heading">{{ $product->name }}</h4>
            @if(@$customer->product_price[$product->id])
                <p>{{ money(@$customer->product_price[$product->id], '€') }}</p>
            @else 
                @if(!empty($product->promo_price))
                    <strike>{{ money($product->price, '€') }}</strike>
                    <p>{{ money($product->promo_price, '€') }}</p>
                @else
                    <p>{{ money($product->price, '€') }}</p>
                @endif
            @endif
            <div class="btn-buy buy-show hidden-xs" data-href="{{ route('account.products.buy', $product->id) }}">
                <a href="{{ route('account.products.buy', $product->id) }}">Comprar</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@stop