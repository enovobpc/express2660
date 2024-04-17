{{ Form::model($ticket, array('route' => array('admin.customer-support.update', $ticket->id), 'method' => 'PUT')) }}
<div class="form-group form-group-sm m-b-5">
    {{ Form::label('status', __('Estado')) }}
    {{ Form::select('status', trans('admin/customers_support.status'), null, ['class' => 'form-control select2']) }}
</div>
<div class="form-group form-group-sm">
    {{ Form::label('user_id', __('Responsável')) }}
    {{ Form::select('user_id', ['' => __('- Sem Responsável -')] + $operators, null, ['class' => 'form-control select2']) }}
</div>
<div class="form-group form-group-sm m-b-5">
    {{ Form::label('email', __('E-mail para resposta')) }}
    {{ Form::text('email', null, ['class' => 'form-control']) }}
</div>
<div class="form-group form-group-sm m-b-5">
    {{ Form::label('customer_id', __('Cliente associado')) }}
    {{ Form::select('customer_id', @$ticket->customer_id ? [ @$ticket->customer_id => @$ticket->customer->name] : [], null, ['class' => 'form-control', 'data-placeholder' => '']) }}
</div>
<div class="form-group form-group-sm m-b-5">
    {{ Form::label('tracking_code', __('TRK do Envio Associado')) }}
    {{ Form::text('tracking_code', @$ticket->shipment->tracking_code, ['class' => 'form-control']) }}
</div>
<div class="form-group">
    {{ Form::label('obs', __('Observações Internas')) }}
    {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 4]) }}
</div>
<button type="submit" class="btn btn-sm btn-primary pull-right">@trans('Gravar')</button>
<button type="button" class="btn btn-sm btn-default btn-edit-cancel pull-right m-r-5">@trans('Cancelar')</button>
{{ Form::close() }}