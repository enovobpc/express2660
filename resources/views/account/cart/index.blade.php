@section('title')
    {{ trans('account/cart.title') }} |
@stop

@section('metatags')
    <meta name="description" content="">
    <meta property="og:title" content="{{ trans('global.menu.cart') }}">
    <meta property="og:description" content="">
    <meta property="og:image" content="{{ asset('assets/img/og_image.png') }}">
@stop

@section('content')
    <div class="container">
        <div class="col-sm-12">
            <ol class="breadcrumb">
                <br>
            </ol>
        </div>
        <div class="col-sm-12">
            <h1 class="title pull-left">{{ trans('account/cart.cart.info') }}</h1>
        </div>
    
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row row-5">
                        @if(empty($productsCart))
                            <div class="col-sm-12">
                                <div class="clearfix"></div>
                                <div class="spacer-50"></div>
                                <div class="text-center text-muted">
                                    <i class="fa fa-shopping-bag bigger-300"></i>
                                </div>
                                <h3 class="text-muted text-center">Carrinho Vazio</h3>
                                <h4 class="text-muted text-center font-weight-normal m-b-20">Não foram adicionados productos ao carrinho</h4>
                                <div class="height-50"></div>
                            </div>
                        @else 
                            <div class="col-sm-9">
                                @include('account.cart.partials.table_desktop')
                                @include('account.cart.partials.table_mobile')
                                <a href="{{ route('account.cart.destroy', $productsCart->id) }}" class="btn btn-sm btn-default destroy-cart"><i class="fas fa-trash-alt"></i> {{ trans('account/cart.cart.destroy') }}</a>
                                <div class="pull-right">
                                    @if(Setting::get('shipping_charge_enabled'))
                                        @if($freeShippingLimit > Cart::instance('shopping')->total())
                                            <p class="text-info free-shipping-remaining">
                                                <i class="fa fa-truck"></i> {!! trans('global.cart.shipping.remaining', ['value' => money($freeShippingLimit - Cart::instance('shopping')->total(), '€')]) !!}
                                            </p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-3">
                                @include('account.cart.partials.resume_panel')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="spacer-30"></div>
@stop

@section('scripts')
<script>
    /**
     * Remove product
     */
    $(document).on('click', '.remove-product', function (e) {

        e.preventDefault();
        var url = $(this).attr('href');
        var $tr  = $(this).closest('tr');
        
        swal({
            title: "{{ trans('global.cart.product.remove.title') }}",
            text: "{{ trans('global.cart.product.remove.message') }}",
            type: "info",
            animation: false,
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "{{ trans('global.word.remove') }}",
            cancelButtonText: "{{ trans('global.word.cancel') }}",
            closeOnConfirm: true
        },
        function () {

            $.ajax({
                url: url,
                type: 'DELETE',
                success: function (data) {
                    if (data.result) {

                        if(data.count > 0) {
                            $('.resume-panel-content').replaceWith(data.html);
                            $('.cart-total').html(data.total)
                            $('.cart-subtotal').html(data.subtotal)
                            $('.cart-count').html(data.count)
                        } else {
                            location.reload();
                        }
                        swal("{{ trans('global.cart.product.remove.success') }}", data.feedback, "success");
                        $tr.remove();
                    } else {
                        swal("{{ trans('global.cart.product.remove.error') }}", data.feedback, "error");
                    }

                },
            }).error(function () {
                swal({
                    title: "{{ trans('global.error.500.ajax.title') }}",
                    text: "{{ trans('global.error.500.ajax.title') }}",
                    type: "error",
                    animation: false,
                });
            });
        });
    })
    
    /**
     * Update product
     */
    $(document).on('change', '.update-product', function (e) {
        e.preventDefault();
        updateProduct($(this));
    });

    $(document).on('click', '.number-spinner button', function (e) {
        e.preventDefault();
        var $input = $(this).closest('.number-spinner').find('input');
        updateProduct($input);
    });

    $('.update-quantity').on('submit', function(e) {
        e.preventDefault();
    })

    function updateProduct($input) {
        var url = $input.closest('form').attr('action');
        var value = $input.val();

        var newWindowWidth = $(window).width();

        if (newWindowWidth < 767) { //mobile
            var $subtotal = $input.closest('.row').find('.subtotal');
            $subtotal.html('<span class="text-muted"><i class="fa fa-spin fa-circle-o-notch"></i></span>');
        } else {
            var $subtotal = $input.closest('td').next().next();
            $subtotal.html('<span class="text-muted"><i class="fa fa-spin fa-circle-o-notch"></i> {{ trans('global.word.loading') }}...</span>');
        }

        $input.css('border-color', '#ccc').css('color', '#555')

        $.ajax({
            url: url,
            type: 'POST',
            data: {quantity: value},
            success: function (data) {

                if (data.result) {
                    $('.resume-panel-content').replaceWith(data.html);
                    $subtotal.html(data.product_subtotal);
                    $input.val(data.quantity)
                } else {
                    $input.css('border-color', 'red').css('color', 'red').css('position', 'relative').css('z-index', '11');
                    $subtotal.html('<span class="text-red"><i class="fa fa-warning"></i></span>');
                }

                if(data.alertFeeback) {
                    swal("", data.feedback, 'warning');
                    $input.val(data.quantity)
                }
            },
        }).error(function () {
            swal({
                title: "{{ trans('global.error.500.ajax.title') }}",
                text: "{{ trans('global.error.500.ajax.title') }}",
                type: "error",
                animation: false,
            });
        });
    }

</script>
@stop