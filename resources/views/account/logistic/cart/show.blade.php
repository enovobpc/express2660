<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Consulta de Pedido <b>{{$reference}}</b></h4>
</div>
<div class="modal-body">
    @if($products->isEmpty())
        <p class="text-muted text-center m-t-50 m-b-50">
            <i class="fas fa-info-circle"></i> Não existem artigos
        </p>
    @else
        
        @if(@$products->first()->status == 'pending' || @$products->first()->status == 'PENDING' | @$products->first()->status == 'refused' | @$products->first()->status == 'REFUSED')
                <p> <span style="font-weight: bold">Locais da encomenda: </span> 
                @if(@$products->first()->origin_name || @$products->first()->origin_address || @$products->first()->origin_city || @$products->first()->origin_zip_code || @$products->first()->origin_country || @$products->first()->destination_name || @$products->first()->destination_address || @$products->first()->destination_zip_code || @$products->first()->destination_city || @$products->first()->destination_country)
                    <a style="cursor: pointer;" id="show_info_local">Consultar</a></p>
                @else
                    Não especificado</p>
                @endif
        @endif
        
        <div id="locals">
            <div class="row row-5">
                <div class="col-md-4">
                    <h4 style="font-weight: bold;">Origem:</h4>
                    <p><b>Nome:</b> {{ @$products->first()->origin_name}} </p>
                    <p><b>Nº Telefone:</b> {{ @$products->first()->origin_phone_number}} </p>
                    <p><b>Morada:</b> {{ @$products->first()->origin_address}} </p>
                    <p><b>Código Postal:</b> {{ @$products->first()->origin_zip_code}} </p>
                    <p><b>Cidade:</b> {{ @$products->first()->origin_city}} </p>
                    <p><b>País:</b> {{ @$products->first()->origin_country}} </p>

                </div>

                <div class="col-md-4">
                    <h4 style="font-weight: bold;">Destino:</h4>
                    <p><b>Nome:</b> {{ @$products->first()->destination_name}} </p>
                    <p><b>Nº Telefone:</b> {{ @$products->first()->destination_phone_number}} </p>
                    <p><b>Morada:</b> {{ @$products->first()->destination_address}} </p>
                    <p><b>Código Postal:</b> {{ @$products->first()->destination_zip_code}} </p>
                    <p><b>Cidade:</b> {{ @$products->first()->destination_city}} </p>
                    <p><b>País:</b> {{ @$products->first()->destination_country}} </p>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-md-12">
                    <b>Observações: </b> {{ @$products->first()->obs}}
                </div>
            </div>
            <hr>
        </div>
        
        <table id="datatable-history" class="table table-condensed table-hover">
            <thead>
            <tr>
                <th class="w-1"></th>
                <th class="w-100px">Ref.</th>
                <th>Artigo</th>
                <th class="w-1">Stock</th>
                <th class="w-55px">Qtd</th>
                
            </tr>
            </thead>
            <tbody>
            @foreach($products as $product)
                <tr>
                    <td>
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
                    <td>{{ @$product->product->sku }}</td>
                    <td>{{ @$product->product->name }}</td>
                    <td>{{ @$product->product->stock_available }}</td>
                    <td><input name="qty" value="{{ @$product->qty }}" class="form-control input-xs text-center" style="background-color: #999; pointer-events:none;"></td>
                    
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    @if(Auth::guard('customer')->user()->is_commercial)
        @if(@$product->status != 'refused' && @$product->status != 'REFUSED' && @$product->status != 'ACCEPT' &&  @$product->status != 'accept')
            <button type="button" class="btn btn-danger btn-refuse" data-loading-text="A anular..."><i class="fas fa-times"></i> Recusar</button>
        @endif

        
        @if(@$product->status != 'accept' && @$product->status != 'ACCEPT')
            <a href="{{ route('account.logistic.cart.accept', $reference) }}"
            class="btn btn-success"
            data-toggle="modal"
            data-target="#modal-remote-xl"
            onclick="$('#modal-remote-lg').modal('hide');">
                <i class="fas fa-plus"></i> Aceitar
            </a>
            {{-- <button type="button" class="btn btn-success btn-accept" data-loading-text="A submeter..."><i class="fas fa-check"></i> Aceitar</button> --}}
        @endif
    @endif
</div>

<script>
        $('.modal .btn-refuse').on('click', function(data) {
        $this = $(this);
        $this.button('loading');
        $.post("{{ route('account.logistic.cart.refuse', ['id' => $reference]) }}", function(data){
            Growl.success(data.feedback);
            $('#modal-remote-lg').modal('hide');
            oTableCartOrders.draw();
        }).fail(function(){
            Growl.error500();
        }).always(function (){
            $this.button('reset');
        })
    });

    $('.modal .btn-accept').on('click', function(data) {

        $this = $(this);
        $this.button('loading');
        $.get("{{ route('account.logistic.cart.accept', ['id' => $reference]) }}", function(data){
            if(data.result) {
                Growl.success(data.feedback);
                $('#modal-remote-lg').modal('hide');
                oTableCartOrders.draw();
            } else {
                Growl.error(data.feedback);
            }

        }).fail(function(){
            Growl.error500();
        }).always(function (){
            $this.button('reset');
        })
    });

    $(document).ready(function() {
        $('#show_info_local').click(function() {
            $('#locals').toggle();
            if ($('#locals').is(":visible")) {
            $('#show_info_local').text('Esconder');
            } else {
            $('#show_info_local').text('Consultar');
            }
        });
            $('#locals').hide();
        });
    
</script>
