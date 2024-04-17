<div class="form-group">
    <label>@trans('Agência') <i class="fas fa-info-circle" data-toggle="tooltip" title="@trans('Todos os clientes vão ser associados à agência escolhida.')"></i></label>
    {{ Form::select('agency_id',  ['' => ''] + $agencies, null, ['class' => 'form-control select2']) }}
</div>
