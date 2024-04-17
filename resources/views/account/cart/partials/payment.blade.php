{{ Form::model($auth, array('route' => 'cart.checkout.finish')) }}
<div class="cart-payment-options">
    @foreach($paymentMethods as $method)
        <div class="checkbox margin-0 {{ $method->is_default ? 'active' : '' }}">
            <div class="media media-middle">
                <div class="pull-left">
                    {{ Form::radio('payment_method', $method->id, @$cart->payment_method == $method->id ? true : $method->is_default, ['data-price' => $method->price > 0.00 ? '1' : '', 'required', 'class' => 'hide']) }}
                    <div class="radio-circle"><span></span></div>
                </div>
                <div class="media-left hidden-xs">
                    <img class="media-object" src="{{ asset($method->filepath) }}" alt="{{ $method->name }}" onerror="this.src='{{ asset("assets/img/default/img_broken.png") }}'" style="width: 45px;">
                </div>
                <div class="media-body">
                    <h4 class="media-heading">
                        {{ $method->name }}
                        @if($method->price)
                        <span class="label label-default">+ {{ money($method->price, '€') }}</span>
                        @endif
                    </h4>
                    <p class="hidden-xs">{{ $method->instructions }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
<hr/>
<button class="btn btn-success pull-right" type="submit" data-loading-text="<i class='fa fa-spin fa-circle-o-notch'></i> {{ trans('global.word.submiting') }}...">
    <i class="fa fa-check"></i> Finalizar
</button>
{{ Form::close() }}

<div class="row">
    <div class="col-md-12">
        <a href="{{ route('account.cart.checkout', 'shipping') }}#tab-shipping" class="btn btn-default pull-left"><i class="fa fa-chevron-righ"></i> Anterior</a>
    </div>
</div>