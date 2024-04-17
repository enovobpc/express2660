{{ Form::model($product, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-3 col-md-2">
            <div class="w-85 pull-left">
                {{ Form::label('image', __('Fotografia'), array('class' => 'form-label')) }}<br/>
                <div class="fileinput {{ $product->filepath ? 'fileinput-exists' : 'fileinput-new'}}" data-provides="fileinput">
                    <div class="fileinput-new thumbnail">
                        <img src="{{ asset('assets/img/default/default.thumb.png') }}" class="img-responsive">
                    </div>
                    <div class="fileinput-preview fileinput-exists thumbnail">
                        @if($product->filepath)
                            <img src="{{ $product->filehost }}/{{ $product->getCroppa(200, 200) }}" onerror="this.src = '{{ img_broken(true) }}'" class="img-responsive">
                        @endif
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
            </div>
            <div class="w-15 pull-left">&nbsp;</div>
        </div>
        <div class="col-sm-9 col-md-6">
            <h4 class="form-divider no-border" style="margin-top: -15px">@trans('Dados do artigo')</h4>
            <div class="row row-5">
                <div class="col-sm-3">
                    <div class="form-group is-required">
                        {{ Form::label('sku', __('SKU/Cód. Artigo')) }}
                        {{ Form::text('sku', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="form-group is-required">
                        {{ Form::label('name', __('Designação artigo')) }}
                        {{ Form::text('name', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-9">
                    <div class="form-group is-required">
                        {{ Form::label('customer_id', __('Proprietário')) }}
                        {{ Form::select('customer_id', [], null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('barcode', __('Código Barras')) }}
                        {{ Form::text('barcode', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
            <div class="row row-5">
                <h4 class="form-divider">@trans('Catalogação')</h4>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('family_id', __('Família/Grupo')) }}
                            {{ Form::select('family_id', ['' => ''] + $families, null, ['class' => 'form-control select2', 'data-child' => 'categories']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('category_id', __('Categoria')) }}
                            {{ Form::select('category_id', ['' => ''] + $categories, null, ['class' => 'form-control select2', 'data-child' => 'categories']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('subcategory_id', __('SubCategoria')) }}
                            {{ Form::select('subcategory_id', ['' => ''] + $subcategories, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-7" style="padding-right: 30px">
                    <h4 class="form-divider">@trans('Peso e Dimensões')</h4>
                    <div class="row row-5">
                        <div class="col-md-8">
                            <div class="form-group">
                                {{ Form::label('unity', __('Unidade')) }}
                                {{ Form::select('unity',  trans('admin/global.measure-units'), null, ['class' => 'form-control select2']) }}
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
                    <div class="row row-5">
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('unities_by_pack', __('Un./Pack')) }}
                                {{ Form::text('unities_by_pack', null, ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('packs_by_box', __('Un./Caixa')) }}
                                {{ Form::text('packs_by_box', null, ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('boxes_by_pallete', __('Un./Palete')) }}
                                {{ Form::text('boxes_by_pallete', null, ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-sm-4 hidden">
                            <div class="checkbox">
                                <label style="padding: 0 0 0 10px">
                                    {{ Form::checkbox('is_active', 1, true) }}
                                    @trans('Ativo')
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <h4 class="form-divider">@trans('Variáveis Logísticas')</h4>
                    <div class="form-group">
                        {{ Form::label('lote', __('Lote')) }}
                        {{ Form::text('lote', null, ['class' => 'form-control']) }}
                    </div>
                    <div class="row row-5">
                        <div class="col-sm-6">
                            <div class="form-group">
                                {{ Form::label('expiration_date', __('Data Produção')) }}
                                <div class="input-group">
                                    {{ Form::text('expiration_date', null, ['class' => 'form-control datepicker']) }}
                                    <div class="input-group-addon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
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
                    <div class="form-group">
                        {{ Form::label('serial_no', __('Nº Série')) }}
                        {{ Form::text('serial_no', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-9 col-sm-offset-3 col-md-4 col-md-offset-0">
            <h4 class="form-divider no-border" style="margin-top: -15px">@trans('Stock e Localizações')</h4>
            <div style="overflow-y: auto; max-height: 257px;">
                <table class="table table-condensed" style="border-top: 0; margin-top: -10px">
                    <tr>
                        <th style="border-top: none;">@trans('Localização')</th>
                        <th style="border-top: none;" class="w-80px">@trans('Stock')</th>
                        {{--<th class="w-80px">Alocado</th>--}}
                    </tr>
                    @for($i=0; $i<10 ; $i++)
                        <tr>
                            <td>
                                {{ Form::select('location[]', ['' => ''] + $locations, null, ['class' => 'form-control location-field select2']) }}
                            </td>
                            <td>
                                {{ Form::text('qty[]', null, ['class' => 'form-control number text-center']) }}
                            </td>
                            {{--<td>
                                {{ Form::text('allocated[]', null, ['class' => 'form-control number']) }}
                            </td>--}}
                        </tr>
                    @endfor
                </table>
            </div>
            <h4 class="form-divider">@trans('Anotações')</h4>
            <div class="form-group">
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 3]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}


{{ Html::script('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.js') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.css') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker-fontawesome.css') }}
<script>
    $('.modal .select2').select2(Init.select2());
    $('.modal .datepicker').datepicker(Init.datepicker());

    $("select[name=customer_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.logistic.products.search.customer') }}")
    });


    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-product').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                oTable.draw(); //update datatable
                $('#modal-remote-xl').modal('hide');
                Growl.success(data.feedback);
            } else {
                Growl.error(data.feedback);
            }

        }).fail(function () {
            Growl.error500();
        }).always(function(){
            $button.button('reset');
        })
    });
</script>

