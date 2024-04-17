<div class="box no-border">
    <div class="box-body">
        {{ Form::open(array('route' => array('admin.logistic.products.images.store', $product->id), 'files' => true, 'class' => "dropzone product-dropzone", 'id' => "image-dropzone")) }}
        {{ Form::close() }}
        <hr/>
        <h4 class="m-t-0 m-b-15">
            <i class="fas fa-file-image"></i> @trans('Imagens do produto') <span id="foto-count" class="badge">{{ count($product->images) }}</span>
        </h4>
        <ul class="ace-thumbnails uploaded-images sortable">
            @forelse ($product->images->sortBy('sort') as $image)
            @include('admin.logistic.products.partials.photo')
            @empty
            <p class="text-muted no-images">@trans('Este produto não tem nenhuma imagem.')</p>
            @endforelse
        </ul>
        <div class="clearfix"></div>

    </div>
    <div class="box-footer">
        <button class="btn btn-sm btn-primary pull-left" id="save-images-order" disabled>@trans('Gravar ordenação')</button>
        <span class="selected-images-action hide pull-left m-l-5">
            {{ Form::open(['route' => ['admin.logistic.products.images.selected.destroy', $product->id]]) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
            {{ Form::close() }}
        </span>
    </div>
</div>