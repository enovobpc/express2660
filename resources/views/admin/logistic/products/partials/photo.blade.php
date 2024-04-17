<li data-id="{{ $image->id }}">
    <label class="delete-checkbox">
        {{ Form::checkbox('image-select', $image->id, null, array('class' => 'flat-blue image-select')) }}
    </label>
    <a href="{{ asset($image->filepath) }}" class="image-item" data-sub-html="{{{ $image->caption }}}">
        <img src="{{ asset($image->getCroppa(150,150)) }}" alt="{{ $image->filename }}" width="150px">
    </a>
    <div class="tags">
        <span class="label-holder">
            @if($image->is_cover)
            <span class="label bg-green">@trans('IMAGEM PRINCIPAL')</span>
            @endif
        </span>
    </div>
    <div class="tools tools-right">    
        @if($image->is_cover)
        <a href="javascript:">
            <i class="fas fa-star-o text-muted"></i>
        </a>
        @else
        <a href="{{ route('admin.logistic.products.images.cover', [$image->product_id, $image->id]) }}" data-method="post" data-confirm-class="btn btn-success" data-confirm-label="@trans('Definir como principal')" data-confirm-title="@trans('Marcar imagem como principal')" data-confirm="@trans('Pretende definir esta imagem como imagem principal?')">
            <i class="fas fa-star text-yellow"></i>
        </a>
        @endif
        <a href="{{ route('admin.logistic.products.images.destroy', [$image->product_id, $image->id]) }}" data-method="delete" data-confirm="@trans('Tem a certeza que prentende remover esta imagem?')">
            <i class="fas fa-trash-alt text-red"></i>
        </a>
    </div>
</li>