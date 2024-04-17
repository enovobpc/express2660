<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Detalhes Produto</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-3">
            <img src="{{ $product->photo_url ? $product->photo_url : asset('assets/img/default/default.thumb.png')}}" onerror="this.src='{{ asset('assets/img/default/broken.thumb.png') }}'" class="img-responsive"/>
        </div>
        <div class="col-sm-9">
            <div class="row row-5">
                <div class="col-sm-10">
                    <h4 class="m-0">
                        <b>{{ $product->name }}</b><br/>
                        <small>SKU {{ $product->sku }}</small>
                    </h4>
                </div>
                <div class="col-sm-2">
                    <h4 class="m-0 pull-right">
                        <i class="fas fa-circle {{ $product->getStockLabel() }}"></i>
                        {{ $product->stock_total }} {{ trans('admin/global.measure-units.' . ($product->unity ?? 'unity')) }}
                        <br/>
                        <small>Stock Min: {{ $product->stock_min ? $product->stock_min : 0 }}</small>
                    </h4>
                </div>
                <div class="col-sm-12">
                    <p>{{ $product->description }}</p>
                    <hr/>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-7">
                    <div class="row row-5">
                        <div class="col-sm-8">
                            <p>
                                <span class="text-muted">Dimensões (CxAxL)</span><br/>
                                <b>{{ money($product->width) }} x {{ money($product->height) }} x {{ money($product->length) }}</b>
                            </p>
                        </div>
                        <div class="col-sm-4">
                            <p>
                                <span class="text-muted">Peso</span><br/>
                                <b>{{ money($product->weight) }}kg</b>
                            </p>
                        </div>
                        <div class="col-sm-8">
                            <p>
                                <span class="text-muted">Preço</span><br/>
                                <b>{{ money($product->price, '€') }} (IVA {{ $product->vat }}%)</b>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    @if($product->lote)
                    <p>
                        <span class="text-muted">Lote</span><br/>
                        <b>{{ $product->lote ?: 'N/A' }}</b>
                    </p>
                    @else
                    <p>
                        <span class="text-muted">Nº Série</span><br/>
                        <b>{{ $product->serial_no ?: 'N/A' }}</b>
                    </p>
                    @endif
                    <p>
                        <span class="text-muted">Validade</span><br/>
                        <b>{{ $product->expiration_date }}</b>
                    </p>
                </div>

            </div>
            <hr/>
            <div class="row row-5">
                <div class="col-sm-4">
                    <p>
                        <span class="text-muted">Marca</span><br/>
                        <b>{{ $product->brand->name ?? 'N/A' }}&nbsp;</b>
                    </p>
                </div>
                <div class="col-sm-4">
                    <p>
                        <span class="text-muted">Modelo</span><br/>
                        <b>{{ $product->brand_model->name ?? 'N/A' }}&nbsp;</b>
                    </p>
                </div>
                <div class="col-sm-4">
                    <p>
                        <span class="text-muted">Família</span><br/>
                        <b>{{ $product->family->name ?? 'N/A' }}&nbsp;</b>
                    </p>
                </div>
                <div class="col-sm-4">
                    <p>
                        <span class="text-muted">Categoria</span><br/>
                        <b>{{ $product->category->name ?? 'N/A' }}&nbsp;</b>
                    </p>
                </div>
                <div class="col-sm-4">
                    <p>
                        <span class="text-muted">Sub-Categoria</span><br/>
                        <b>{{ $product->subcategory->name ?? 'N/A' }}&nbsp;</b>
                    </p>
                </div>
                <div class="col-sm-4">
                    <p>
                        <span class="text-muted">Referência</span><br/>
                        <b>{{ $product->customer_ref ?: 'N/A' }}</b>
                    </p>
                </div>
            </div>
            <hr class="m-t-5 m-b-10"/>

            <div class="row row-5">
                <div class="col-sm-4">
                    <p>
                        <span class="text-muted">Stock Atual</span><br/>
                        <b><i class="fas fa-circle"></i> {{ $product->stock_total }} {{ trans('admin/global.measure-units.' . ($product->unity ?? 'unity')) }}</b>
                    </p>
                </div>
                <div class="col-sm-4">
                    <p>
                        <span class="text-muted">Stock Mínimo</span><br/>
                        <b>{{ $product->stock_min ?: 0 }} {{ trans('admin/global.measure-units.' . ($product->unity ?? 'unity')) }}</b>
                    </p>
                </div>
                <div class="col-sm-4">
                    <p>
                        <span class="text-muted">Unidade</span><br/>
                        <b>{{ trans('admin/global.measure-units.' . ($product->unity ?? 'unity')) }}</b>
                    </p>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-4">
                    <p>
                        <span class="text-muted">Unidades por pacote</span><br/>
                        {{ $product->unities_by_pack ?: '--' }}
                    </p>
                </div>
                <div class="col-sm-4">
                    <p>
                        <span class="text-muted">Unidades por caixa</span><br/>
                        {{ $product->packs_by_box ?: '--' }}
                    </p>
                </div>
                <div class="col-sm-4">
                    <p>
                        <span class="text-muted">Unidades por palete</span><br/>
                        {{ $product->boxes_by_pallete ?: '--' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>
