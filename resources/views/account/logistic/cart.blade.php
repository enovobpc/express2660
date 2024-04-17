<?php
    $customer = Auth::guard('customer')->user();

    $products = App\Models\Logistic\CartProduct::with('product')
    ->where('customer_id', $customer->id)->where(function ($q) {
    $q->where('reference', NULL)->orWhere('closed', 0);
    })
    ->get();

    $need_validate = false;
    foreach ($products as $product) {
        if ($product->product->need_validation) {
            $need_validate = true;
            break;
        }
    }
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Consulta de Pedido</h4>
</div>
<div class="modal-body">

    @if($products->isEmpty())
        <p class="text-muted text-center m-t-50 m-b-50">
            <i class="fas fa-info-circle"></i> Não existem artigos
        </p>
    @else
        <table id="datatable-history" class="table table-condensed table-hover" >
            <thead>
            <tr>
                <th class="w-1"></th>
                <th class="w-100px">Ref.</th>
                <th>Artigo</th>
                <th class="w-1">Stock</th>
                <th class="w-55px">Qtd</th>
                <th class="w-5">Elim.</th>
            </tr>
            </thead>
            <tbody>
            @foreach($products as $product)
                <tr>
                    <td style="vertical-align: middle;">
                        @if(@$product->product->filepath)
                            <a href="{{ asset(@$product->product->filepath) }}"
                               data-src="{{ asset(@$product->product->filepath) }}"
                               data-thumb="{{ asset(@$product->product->getCroppa(200,200)) }}"
                               class="preview-img">
                                <img src="{{ asset(@$product->product->getCroppa(200,200)) }}" style="height: 40px; width: 40px" onerror="this.src='{{ asset('assets/img/default/broken.thumb.png') }}'"/>
                            </a>
                        @else
                            <img src="{{ asset('assets/img/default/default.thumb.png') }}" style="height: 40px; width: 40px"/>
                        @endif
                    </td>
                    <td style="vertical-align: middle;">{{ @$product->product->sku }}</td>
                    <td style="vertical-align: middle;">{{ @$product->product->name }}</td>
                    <td style="vertical-align: middle;">{{ @$product->product->stock_available }}</td>
                    <td style="vertical-align: middle;"><input name="qty" value="{{ @$product->qty }}" class="form-control input-xs text-center"></td>
                    <td style="vertical-align: middle;" class="text-center delete-product" data-id="{{@$product->product->id}}"><i class="fas fa-trash" style="color:red"></i></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    @if(!$products->isEmpty())
        <button type="button" class="btn btn-danger btn-destroy" data-loading-text="A anular..."><i class="fas fa-trash"></i> Anular</button>
        
       
         
        @if($need_validate)
            <a href="{{ route('account.logistic.cart.set.locations') }}"
                class="btn btn-success generate-envio"
                data-toggle="modal"
                data-target="#modal-remote"
                data-loading-text="A submeter..."
                onclick="$('#modal-remote-lg').modal('hide');">
                    <i class="fas fa-check"></i> Submeter
                </a>
        @else
            <a href="{{ route('account.logistic.cart.create.shipment') }}"
            class="btn btn-success generate-envio"
            data-toggle="modal"
            data-target="#modal-remote-xl"
            onclick="$('#modal-remote-lg').modal('hide');">
                <i class="fas fa-plus"></i> Gerar Envio
            </a>
        @endif
            
    @endif
</div>

<script>
    $('.modal .btn-destroy').on('click', function(data) {
        $this = $(this);
        $this.button('loading');
        $.post("{{ route('account.logistic.cart.destroy') }}", function(data){
            Growl.success(data.feedback);
            $('#modal-remote-lg').modal('hide');
            $('.cart-logistic-total').html(0);
        }).fail(function(){
            Growl.error500();
        }).always(function (){
            $this.button('reset');
        })
    });

    $('.modal .btn-submit').on('click', function(data) {
        // $this = $(this);
        // $this.button('loading');
        // var local = document.getElementById("local").value;
        // if(local == ''){
        //     local = 0;
        // }
        // $.get("{{ route('account.logistic.cart.conclude', ['local' => ':local']) }}".replace(':local', local), function(data){
        //     if(data.result) {
        //         Growl.success(data.feedback);
        //         $('#modal-remote-lg').modal('hide');
        //         $('.cart-logistic-total').html(0);
        //     } else {
        //         Growl.error(data.feedback);
        //     }

        // }).fail(function(){
        //     Growl.error500();
        // }).always(function (){
        //     $this.button('reset');
        // })

    });



    $('.modal .delete-product').on('click', function(data) {
        $this = $(this);
        $this.button('loading');
        
        $.post("{{ route('account.logistic.cart.deleteProduct')}}", {
            'id' : $this.data('id')
        }, function(data){
            if(data.result) {
                $this.closest("tr").remove();
                $('.cart-logistic-total').html(data.cart_total)
                Growl.success(data.feedback);
                
            } else {
                Growl.error(data.feedback);
            }

        }).fail(function(){
            Growl.error500();
        }).always(function (){
            $this.button('reset');
        })
    });
</script>
