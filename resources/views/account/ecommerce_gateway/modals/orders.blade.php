<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">{{ trans('account/global.word.close') }}</span>
    </button>
    <h4 class="modal-title">{{ trans('account/global.word.orders') }}</h4>
</div>
<div class="modal-body">
    <div class="row row-15 orders-top-header">
        <div class="col-xs-12">
            <div class="form-group">
                {{ Form::label('ecommerce_gateway_id', 'Gateway') }}
                {{ Form::select('ecommerce_gateway_id', ['' => ''] + $gateways, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="text-center m-t-30 m-b-30" id="table-orders-loading" style="display: none">
                A carregar encomendas... <i class="fas fa-spin fa-spinner"></i>
            </div>

            <div class="table-responsive w-100" id="table-orders">
                @include('account.ecommerce_gateway.partials.orders.table', ['orders' => []])
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account/global.word.close') }}</button>
</div>

<style>
    .orders-top-header {
        background: #f9f9f9;
        margin: -16px -15px 15px;
        padding: 7px 0 0 10px;
        border-bottom: 1px solid #ddd;
    }
</style>

<script>
    $('.select2').select2(Init.select2());

    var modalEcommerceGatewayOrders = {
        remove: function (code) {
            $('.tr-order[data-code="'+ code +'"]').remove();
        }
    };

    $('.modal [name="ecommerce_gateway_id"]').on('change', function () {
        const ORDERS_TABLE_URL = '{!! route('account.ecommerce-gateway.orders', [':id']) !!}';

        var $loading = $('#table-orders-loading');
        var $table   = $('#table-orders');

        var $this = $(this);
        if ($this.val() == '') {
            return;
        }

        $table.hide();
        $loading.show();

        $.get(ORDERS_TABLE_URL.replace(':id', $this.val()))
        .done(function (html) {
            $('#table-orders').html(html);
        })
        .fail(function (error) {
            // TODO: Handle error
        })
        .always(function () {
            $table.show();
            $loading.hide();
        });
    });

    @if (count($gateways) == 1)
    $('.modal [name="ecommerce_gateway_id"]').val({{ key($gateways) }}).trigger('change');
    @endif
</script>