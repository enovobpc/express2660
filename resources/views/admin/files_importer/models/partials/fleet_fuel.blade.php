<div class="form-group">
    <label>@trans('Fornecedor')</label>
    {{ Form::select('provider_id', ['' => ''] + $providersGasStations, null, ['class' => 'form-control select2']) }}
</div>
<div class="form-group">
    {{ Form::label('date_format', __('Formato de Data'), ['class' => 'control-label']) }}
    {{ Form::select('date_format',  trans('admin/importer.date_formats'), null, ['class' => 'form-control select2']) }}
</div>
{{--
<div class="form-group">
    <label>Tipo Cliente <i class="fas fa-info-circle" data-toggle="tooltip" title="Todos os clientes vão ser associados ao tipo escolhido."></i></label>
    {{ Form::select('type_id',  ['' => ''] + $customerTypes, null, ['class' => 'form-control select2']) }}
</div>--}}
