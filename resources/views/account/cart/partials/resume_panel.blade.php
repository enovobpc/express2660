<div class="resume-panel-content">
    <div class="spacer-30 visible-xs"></div>
    <div class="cart-resume-panel m-b-30">
        <h4 class="m-t-0 text-uppercase">{{ trans('account/cart.cart.summary') }}</h4>
        <table class="table m-t-20 m-b-0">
            <tr>
                <td>{{ trans('account/cart.cart.subtotal') }}</td>
                <td class="text-right">
                    <span class="cart-subtotal">{{ $productsCart->subtotal}}</span>
                </td>
            </tr>
            <tr>
                
            </tr>
            <tr>
                <td>{{ trans('account/cart.cart.vat') }} <small>{{ Setting::get('vat_rate_normal') }}%</small></td>
                <td class="text-right">
                    <span class="cart-vat">{{ $productsCart->vat}}</span>
                </td>
            </tr>
            
        </table>
        <table class="table margin-0">
            <tr class="font-size-22px">
                <td>{{ trans('account/cart.cart.total') }}</td>
                <td class="text-right">
                    <i class="fa fa-spin fa-circle-o-notch hide cart-total-loading"></i>
                    <span class="cart-total">{{ $productsCart->total}}</span>
                </td>
            </tr>
        </table>
        <a href="{{ route('account.cart.checkout') }}" class="btn btn-block btn-primary m-t-20" type="submit">Seguinte<i class="fa fa-chevron-right"></i></a>
        <div class="clearfix"></div>
    </div>
</div>