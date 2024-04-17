{{ Form::model($workgroup, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('name', __('Designação')) }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => 'Ex: Gestores de Operações']) }}
            </div>
        </div>

        <div class="col-sm-7">
            <div class="form-group">
                {{ Form::label('services[]', __('Serviços')) }}
                {{ Form::select('services[]', $services, array_map('intval', $workgroup->values['services'] ?? []), ['class' => 'form-control select2', 'multiple', 'data-placeholder' => 'Todos']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group">
                {{ Form::label('status[]', __('Estados')) }}
                {{ Form::select('status[]', $status, array_map('intval', $workgroup->values['status'] ?? []), ['class' => 'form-control select2', 'multiple', 'data-placeholder' => 'Todos']) }}
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('sender_countries[]', __('Países Origem')) }}
                {{ Form::select('sender_countries[]', trans('country'), $workgroup->values['sender_countries'] ?? [], ['class' => 'form-control select2', 'multiple', 'data-placeholder' => 'Todos']) }}
            </div>
        </div>
        <div class="col-sm-6">
            {{ Form::label('recipient_countries[]', __('Países Destino')) }}
            {{ Form::select('recipient_countries[]', trans('country'), $workgroup->values['recipient_countries'] ?? [], ['class' => 'form-control select2', 'multiple', 'data-placeholder' => 'Todos']) }}
        </div>

        <div class="col-sm-12"></div>
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('pickup_routes[]', __('Rotas Recolha')) }}
                {{ Form::select('pickup_routes[]', $pickupRoutes, array_map('intval', $workgroup->values['pickup_routes'] ?? []), ['class' => 'form-control select2', 'multiple', 'data-placeholder' => 'Todas']) }}
            </div>
        </div>
        <div class="col-sm-6">
            {{ Form::label('delivery_routes[]', __('Rotas Entrega')) }}
            {{ Form::select('delivery_routes[]', $deliveryRoutes, array_map('intval', $workgroup->values['delivery_routes'] ?? []), ['class' => 'form-control select2', 'multiple', 'data-placeholder' => 'Todas']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}
<script>
    $('#modal-remote-xs .select2').select2(Init.select2());
</script>
