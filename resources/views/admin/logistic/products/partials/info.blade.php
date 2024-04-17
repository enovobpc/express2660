{{ Form::model($product, array('route' => array('admin.logistic.products.update', $product->id), 'method' => 'PUT', 'class' => 'form-product', 'files' => true)) }}
<div class="box no-border">
    <div class="box-body">
        <div class="row row-5">
            <div class="col-sm-9">
                <div class="row row-5">
                    <div class="col-sm-9">
                        <div class="row row-5">
                            <div class="col-sm-3">
                                <div class="form-group is-required">
                                    {{ Form::label('sku', __('SKU')) }}
                                    {{ Form::text('sku', null, ['class' => 'form-control', 'required']) }}
                                </div>
                            </div>
                            <div class="col-sm-9">
                                <div class="form-group is-required">
                                    {{ Form::label('name', __('Designação artigo')) }}
                                    {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
                                </div>
                            </div>
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-6">
                                <div class="form-group is-required">
                                    {{ Form::label('customer_id', __('Proprietário')) }}
                                    @if($product->exists)
                                        {{ Form::text('', @$product->customer->code . ' - ' .@$product->customer->name, ['class' => 'form-control', 'disabled']) }}
                                    @else
                                        {{ Form::select('customer_id', [$product->customer_id => @$product->customer->code . ' - ' .@$product->customer->name], null, ['class' => 'form-control', 'required']) }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('customer_ref', __('Ref. Cliente')) }}
                                    {{ Form::text('customer_ref', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('barcode', __('Código Barras')) }}
                                    {{ Form::text('barcode', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            @if(hasPermission('logistic_brands'))
                            <small class="pull-right">
                                <a href="{{ route('admin.logistic.brands.index', ['tab' => 'brands', 'dt_customer' => $product->customer_id]) }}" target="_blank">@trans('Gerir Marcas')</a>
                            </small>
                            @endif
                            {{ Form::label('brand_id', __('Marca')) }}
                            {{ Form::select('brand_id', ['' => ''] + $brands, null, ['class' => 'form-control select2', 'data-child' => 'models']) }}
                        </div>
                        <div class="form-group">
                            @if(hasPermission('logistic_brands'))
                                <small class="pull-right">
                                    <a href="{{ route('admin.logistic.brands.index', ['tab' => 'models', 'dt_customer' => $product->customer_id]) }}" target="_blank">@trans('Gerir Modelos')</a>
                                </small>
                            @endif
                            {{ Form::label('model_id', __('Modelo')) }} <i class="fas fa-spin fa-circle-notch bloading" style="display: none"></i>
                            {{ Form::select('model_id', ['' => ''] + $models, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
                <div class="row row-5">
                    <div class="col-sm-6" style="padding-right: 30px">
                        <h4 class="form-divider">@trans('Peso e Dimensões')</h4>
                        <div class="row row-5">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('unity', __('Unidade')) }}
                                    {{ Form::select('unity',  trans('admin/global.measure-units'), null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('price', __('Preço')) }}
                                    <div class="input-group">
                                        {{ Form::text('price', null, ['class' => 'form-control decimal']) }}
                                        <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('weight', __('Peso')) }}
                                    <div class="input-group">
                                        {{ Form::text('weight', null, ['class' => 'form-control decimal']) }}
                                        <div class="input-group-addon">kg</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('width', __('Comprimento')) }}
                                    <div class="input-group">
                                        {{ Form::text('width', null, ['class' => 'form-control decimal']) }}
                                        <div class="input-group-addon">cm</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('height', __('Altura')) }}
                                    <div class="input-group">
                                        {{ Form::text('height', null, ['class' => 'form-control decimal']) }}
                                        <div class="input-group-addon">cm</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::label('length', __('Largura')) }}
                                    <div class="input-group">
                                        {{ Form::text('length', null, ['class' => 'form-control decimal']) }}
                                        <div class="input-group-addon">cm</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-6">
                        <h4 class="form-divider">@trans('Variáveis Logísticas')</h4>
                        <div class="row row-5">
                            <div class="col-sm-5">
                                <div class="form-group m-b-20">
                                    <div class="m-b-10">
                                        {{ Form::label('has_serial', __('Lote ou Número série?')) }}
                                    </div>
                                    <label class="radio-inline">
                                        {{ Form::radio('has_serial', 'serial', $product->serial_no ? true : false) }}
                                        @trans('N.º Série')
                                    </label>
                                    <label class="radio-inline">
                                        {{ Form::radio('has_serial', 'lote', $product->lote ? true : false) }}
                                        @trans('Lote')
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="form-group has-lote" style="{{ $product->lote ? '' : 'display:none'}}">
                                    {{ Form::label('lote', __('Lote')) }}
                                    {{ Form::text('lote', null, ['class' => 'form-control']) }}
                                </div>
                                <div class="form-group has-serial" style="{{ $product->serial_no ? '' : 'display:none'}}">
                                    {{ Form::label('serial_no', __('Nº Série')) }}
                                    {{ Form::text('serial_no', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    {{ Form::label('production_date', __('Data Produção')) }}
                                    <div class="input-group">
                                        {{ Form::text('production_date', null, ['class' => 'form-control datepicker']) }}
                                        <div class="input-group-addon">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    {{ Form::label('volumes', __('Volumes')) }}
                                    <div class="input-group">
                                        {{ Form::text('volumes', null, ['class' => 'form-control']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="form-group expiration-date"  style="{{ $product->lote ? '' : 'display:none'}}">
                                    {{ Form::label('expiration_date', __('Data Validade')) }}
                                    <div class="input-group">
                                        {{ Form::text('expiration_date', null, ['class' => 'form-control datepicker']) }}
                                        <div class="input-group-addon">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-5">
                    <div class="col-sm-7" style="padding-right: 30px">
                        <h4 class="form-divider">@trans('Catalogação')</h4>
                        <div class="form-group">
                            @if(hasPermission('logistic_brands'))
                                <small class="pull-right">
                                    <a href="{{ route('admin.logistic.brands.index', ['tab' => 'families', 'dt_customer' => $product->customer_id]) }}" target="_blank">@trans('Gerir Familias')</a>
                                </small>
                            @endif
                            {{ Form::label('family_id', __('Grupo/Família Artigos')) }}
                            {{ Form::select('family_id', ['' => ''] + $families, null, ['class' => 'form-control select2', 'data-child' => 'categories']) }}
                        </div>
                        <div class="row row-5">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    @if(hasPermission('logistic_brands'))
                                        <small class="pull-right">
                                            <a href="{{ route('admin.logistic.brands.index', ['tab' => 'categories', 'dt_customer' => $product->customer_id]) }}" target="_blank">@trans('Gerir Categorias')</a>
                                        </small>
                                    @endif
                                    {{ Form::label('category_id', __('Categoria')) }} <i class="fas fa-spin fa-circle-notch bloading" style="display: none"></i>
                                    {{ Form::select('category_id', ['' => ''] + $categories, null, ['class' => 'form-control select2', 'data-child' => 'subcategories']) }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    @if(hasPermission('logistic_brands'))
                                        <small class="pull-right">
                                            <a href="{{ route('admin.logistic.brands.index', ['tab' => 'subcategories', 'dt_customer' => $product->customer_id]) }}" target="_blank">@trans('Gerir Subcategorias')</a>
                                        </small>
                                    @endif
                                    {{ Form::label('subcategory_id', __('Subcategoria')) }} <i class="fas fa-spin fa-circle-notch bloading" style="display: none"></i>
                                    {{ Form::select('subcategory_id', ['' => ''] + $subcategories, null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <h4 class="form-divider">@trans('Stock')</h4>
                        <div class="row row-5">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('stock_total', __('Stock Total')) }}
                                    {{ Form::text('stock_total', null, ['class' => 'form-control number', @$product->locations->isEmpty() ? '' : 'disabled']) }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('stock_min', __('Stock  Min')) }}
                                    {{ Form::text('stock_min', null, ['class' => 'form-control number']) }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('stock_status', __('Bloqueado')) }}
                                    {{ Form::select('stock_status', ['available' => __('Não'), 'blocked' => __('Sim')], null, ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                        </div>
                        <div class="row row-5">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('unities_by_pack', __('Un./Pack')) }}
                                    {{ Form::text('unities_by_pack', null, ['class' => 'form-control number']) }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('packs_by_box', __('Un./Caixa')) }}
                                    {{ Form::text('packs_by_box', null, ['class' => 'form-control number']) }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('boxes_by_pallete', __('Un./Palete')) }}
                                    {{ Form::text('boxes_by_pallete', null, ['class' => 'form-control number']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-5">
                    <div class="col-sm-9">
                        <h4 class="form-divider">@trans('Descrição artigo')</h4>
                        <div class="form-group">
                            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3]) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <h4 class="form-divider">@trans('Opções Adicionais')</h4>
                        <div class="checkbox">
                            <label style="padding: 0 0 0 10px">
                                {{ Form::checkbox('is_active', 1) }}
                                @trans('Ativo')
                            </label>
                        </div>
                        <div class="checkbox">
                            <label style="padding: 0 0 0 10px">
                                {{ Form::checkbox('is_obsolete', 1) }}
                                @trans('Obsoleto')
                            </label>
                        </div>
                        <div class="checkbox">
                            <label style="padding: 0 0 0 10px">
                                {{ Form::checkbox('need_validation', 1) }}
                                @trans('Necessita Validação')
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="w-15 pull-left">&nbsp;</div>
                <div class="w-85 pull-left">
                    {{ Form::label('image', __('Fotografia'), array('class' => 'form-label')) }}<br/>
                    {{ Form::hidden('delete_photo') }}
                    <div class="fileinput {{ $product->filepath ? 'fileinput-exists' : 'fileinput-new'}}" data-provides="fileinput">
                        <div class="fileinput-new thumbnail">
                            <img src="{{ asset('assets/img/default/default.thumb.png') }}" class="img-responsive">
                        </div>
                        <div class="fileinput-preview fileinput-exists thumbnail">
                            <a href="{{ asset($product->filepath) }}" target="_blank" class="preview-img">
                            @if($product->filepath)
                                <img src="{{ asset($product->getCroppa(200, 200)) }}" onerror="this.src='{{ img_broken(true) }}'" class="img-responsive">
                            @endif
                            </a>
                        </div>
                        <div>
                            <span class="btn btn-default btn-block btn-sm btn-file">
                                <span class="fileinput-new">@trans('Procurar...')</span>
                                <span class="fileinput-exists"><i class="fas fa-sync-alt"></i> @trans('Alterar')</span>
                                <input type="file" name="image">
                            </span>
                            <a href="#" class="btn btn-danger btn-block btn-sm fileinput-exists" data-dismiss="fileinput">
                                <i class="fas fa-close"></i> @trans('Remover')
                            </a>
                        </div>
                    </div>
                    <h4 class="form-divider">@trans('Anotações')</h4>
                    <div class="form-group m-0">
                        {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 17]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <button class="btn btn-primary">@trans('Gravar')</button>
    </div>
</div>
{{ Form::close() }}