<div class="box no-border">
    <div class="box-body">
        <div class="row row-5">
            <div class="col-sm-12">
                <a href="{{ route('admin.logistic.products.stock.add', ['product' => $product->id]) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-xs"
                   class="btn btn-xs btn-success m-b-5">
                    <i class="fas fa-plus"></i> @trans('Adicionar')
                </a>
                {{--<h4 class="form-divider no-border pull-left" style="margin-top: -8px; margin-bottom: 20px; width: auto; float: left">
                    <i class="fas fa-fw fa-warehouse"></i> Localizações do Artigo
                </h4>--}}
                <div class="clearfix"></div>
                @include('admin.logistic.products.partials.locations_table')
            </div>
        </div>
    </div>
</div>