{{ Form::model($budget, array('route' => array('admin.budgets.update', $budget->id), 'method' => 'PUT')) }}
<div class="form-group form-group-sm m-b-5">
    {{ Form::label('status', 'Estado') }}
    {{ Form::select('status', trans('admin/budgets.status'), null, ['class' => 'form-control select2']) }}
</div>
<div class="form-group form-group-sm m-b-5">
    {{ Form::label('email', 'E-mail de resposta') }}
    {{ Form::text('email', null, ['class' => 'form-control']) }}
</div>
<div class="form-group form-group-sm m-b-5">
    {{ Form::label('tracking_code', 'TRK do Envio Associado') }}
    {{ Form::text('tracking_code', @$budget->shipment->tracking_code, ['class' => 'form-control']) }}
</div>
<div class="row row-5">
    <div class="col-sm-6">
        <div class="form-group form-group-sm m-b-5">
            {{ Form::label('provider', 'Fornecedor') }}
            {{ Form::text('provider', @$budget->shipment_id ? @$budget->shipment->provider->name : null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group form-group-sm m-b-5">
            {{ Form::label('total', 'Valor') }}
            {{ Form::text('total', null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>
<div class="form-group">
    {{ Form::label('obs', 'Observações Internas') }}
    {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 4]) }}
</div>
<div class="form-group form-group-sm">
    {{ Form::label('user_id', 'Responsável') }}
    {{ Form::select('user_id', $operators, null, ['class' => 'form-control select2']) }}
</div>
<button type="submit" class="btn btn-sm btn-primary pull-right">Gravar</button>
<button type="button" class="btn btn-sm btn-default btn-edit-cancel pull-right m-r-10">Cancelar</button>
{{ Form::close() }}