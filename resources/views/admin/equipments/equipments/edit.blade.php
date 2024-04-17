{{ Form::model($equipment, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-md-3">
            <div class="w-85 pull-left">
                {{ Form::label('image', 'Fotografia', array('class' => 'form-label')) }}<br/>
                <div class="fileinput {{ $equipment->filepath ? 'fileinput-exists' : 'fileinput-new'}}" data-provides="fileinput">
                    <div class="fileinput-new thumbnail">
                        <img src="{{ asset('assets/img/default/default.thumb.png') }}" class="img-responsive">
                    </div>
                    <div class="fileinput-preview fileinput-exists thumbnail">
                        @if($equipment->filepath)
                            <img src="{{ $equipment->filehost }}/{{ $equipment->getCroppa(200, 200) }}" onerror="this.src = '{{ img_broken(true) }}'" class="img-responsive">
                        @endif
                    </div>
                    <div>
                        <span class="btn btn-default btn-block btn-sm btn-file">
                            <span class="fileinput-new">Procurar...</span>
                            <span class="fileinput-exists"><i class="fas fa-sync-alt"></i> Alterar</span>
                            <input type="file" name="image">
                        </span>
                        <a href="#" class="btn btn-danger btn-block btn-sm fileinput-exists" data-dismiss="fileinput">
                            <i class="fas fa-close"></i> Remover
                        </a>
                    </div>
                </div>
            </div>
            <div class="w-15 pull-left">&nbsp;</div>
        </div>
        <div class="col-sm-9">
            <h4 class="form-divider no-border" style="margin-top: -15px">Dados do artigo</h4>
            <div class="row row-5">
                <div class="col-sm-3">
                    <div class="form-group is-required">
                        {{ Form::label('sku', 'SKU/Ref. Artigo') }}
                        {{ Form::text('sku', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group is-required">
                        {{ Form::label('name', 'Designação artigo') }}
                        {{ Form::text('name', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('status', 'Estado') }}
                        {{ Form::select('status', trans('admin/equipments.equipments.status'), null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('customer_id', 'Proprietário') }}
                        {{ Form::select('customer_id', [@$equipment->customer->id => @$equipment->customer->name], null, ['class' => 'form-control', 'data-placeholder' => '']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('location_id', 'Localização') }}
                        {{ Form::select('location_id', ['' => ''] + $locations, null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('category_id', 'Categoria') }}
                        {{ Form::select('category_id', ['' => ''] + $categories, null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        {{ Form::label('ot_code', 'N.º OT') }}
                        {{ Form::text('ot_code', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-7" style="padding-right: 30px">
                    <h4 class="form-divider">Peso e Dimensões</h4>
                    <div class="row row-5">
                        <div class="col-md-3">
                            <div class="form-group">
                                {{ Form::label('stock_total', 'Stock') }}
                                {{ Form::text('stock_total', null, ['class' => 'form-control decimal']) }}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {{ Form::label('weight', 'Peso') }}
                                <div class="input-group">
                                    {{ Form::text('weight', null, ['class' => 'form-control decimal']) }}
                                    <div class="input-group-addon">kg</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                {{ Form::label('unity', 'Unidade') }}
                                {{ Form::select('unity',  trans('admin/global.measure-units'), null, ['class' => 'form-control select2']) }}
                            </div>
                        </div>
                    </div>
                    <div class="row row-5">
                        <div class="col-sm-4">
                            <div class="form-group">
                                {{ Form::label('width', 'Comprimento') }}
                                <div class="input-group">
                                    {{ Form::text('width', null, ['class' => 'form-control decimal']) }}
                                    <div class="input-group-addon">cm</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {{ Form::label('height', 'Altura') }}
                                <div class="input-group">
                                    {{ Form::text('height', null, ['class' => 'form-control decimal']) }}
                                    <div class="input-group-addon">cm</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {{ Form::label('length', 'Largura') }}
                                <div class="input-group">
                                    {{ Form::text('length', null, ['class' => 'form-control decimal']) }}
                                    <div class="input-group-addon">cm</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row row-5">
                        <div class="col-sm-4 hidden">
                            <div class="checkbox">
                                <label style="padding: 0 0 0 10px">
                                    {{ Form::checkbox('is_active', 1, true) }}
                                    Ativo
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <h4 class="form-divider">Variáveis Logísticas</h4>
                    <div class="form-group">
                        {{ Form::label('serial_no', 'Nº Série') }}
                        {{ Form::text('serial_no', null, ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('lote', 'Lote') }}
                        {{ Form::text('lote', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
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
        ajax: Init.select2Ajax("{{ route('admin.equipments.search.customer') }}")
    });

</script>

