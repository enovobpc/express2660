{{ Form::model($accessory, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-8">
            <div class="form-group is-required">
                {{ Form::label('vehicle_id', __('Viatura')) }}
                {{ Form::select('vehicle_id', count($vehicles) > 1 ? ['' => ''] + $vehicles : $vehicles, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('type', __('Tipo Acessório')) }}
                {{ Form::select('type', ['' => ''] + trans('admin/fleet.accessories.types'), null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('code', __('Referência')) }}
                {{ Form::text('code', null, ['class' => 'form-control number', 'required', 'maxlength' => 6]) }}
            </div>
        </div>
        <div class="col-sm-9">
            <div class="form-group is-required">
                {{ Form::label('name', __('Nome acessório')) }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>

    </div>
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('brand', __('Marca')) }}
                {{ Form::text('brand', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('model', __('Modelo')) }}
                {{ Form::text('model', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('buy_date', __('Data Compra')) }}
                <div class="input-group">
                    {{ Form::text('buy_date', $accessory->exists && $accessory->buy_date ? $accessory->buy_date->format('Y-m-d') : date('Y-m-d'), ['class' => 'form-control datepicker']) }}
                    <span class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('validity_date', __('Data Validade')) }}
                <div class="input-group">
                    {{ Form::text('validity_date', $accessory->exists && $accessory->validity_date ? $accessory->validity_date->format('Y-m-d') : null, ['class' => 'form-control datepicker']) }}
                    <span class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('obs', __('Observações')) }}
                {{ Form::textarea('obs',null, ['class' => 'form-control', 'rows' => 2]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());
    $('.datepicker').datepicker(Init.datepicker())
</script>

