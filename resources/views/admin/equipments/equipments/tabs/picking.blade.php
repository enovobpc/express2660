<div class="row">
    <div class="col-sm-3 col-md-2">
        <div class="form-group">
            {{ Form::label('action', 'Operação') }}
            {{ Form::select('action', ['' => ''] + trans('admin/equipments.equipments.actions'), null, ['class' => 'form-control select2']) }}
        </div>
        <div class="form-group location-field">
            {{ Form::label('location_id', 'Localização Destino') }}
            {{ Form::select('location_id', ['' => ''] + $locations, null, ['class' => 'form-control select2', 'disabled']) }}
        </div>
        <div class="reception-fields" style="display: none">
            <div class="form-group">
                {{ Form::label('customer_id', 'Associar ao Cliente') }}
                {{ Form::select('customer_id', ['' => ''], null, ['class' => 'form-control select2', 'data-placeholder' => '']) }}
            </div>
            <div class="form-group">
                {{ Form::label('name', 'Nome Artigo') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => '- Não indicar -']) }}
            </div>
            <div class="form-group">
                {{ Form::label('category_id', 'Categoria') }}
                {{ Form::select('category_id', ['' => ''] + $categoriesList, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="form-group code-ot">
            <label>Ref. Pedido (OT)</label>
            {{ Form::text('ot_code', null, ['class' => 'form-control', 'disabled']) }}
        </div>
        <div class="form-group is-required">
            <label>Código de barras {!! tip('Coloque o cursor ativo na caixa de texto e utilize o leitor de códigos de barras para picar a mercadoria.') !!}</label>
            {{ Form::text('sku', null, ['class' => 'form-control nospace', 'required', 'disabled']) }}
        </div>
<!--        <hr style="margin: 5px 0 15px"/>
        <div class="form-group">
            <div class="checkbox m-t-5">
                <label style="padding-left: 0">
                    {{ Form::checkbox('autocreate', 1, true) }}
                    Criar artigos se não existir
                </label>
            </div>
        </div>-->
    </div>
    <div class="col-sm-10" style="border-left: 1px dashed #ddd; min-height: 405px; padding-left: 0; padding-right: 10px; margin-bottom: -10px; margin-top: -10px">
        <div class="nav-tabs-custom" style="box-shadow: none">
            <div class="tab-content">
                <div class="tab-pane active" id="tab-readed">
                    @include('admin.equipments.equipments.partials.readed_equipments')
                </div>
            </div>
        </div>
    </div>
</div>