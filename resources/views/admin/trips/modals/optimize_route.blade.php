{{ Form::open(['route' => ['admin.trips.shipments.optimize.store', $trip->id], 'method' => 'post']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title"><i class="fas fa-route"></i> @trans('Otimizar Entrega')</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div style="background: #f2f2f2; border-bottom: 1px solid #ddd; margin: -15px -15px 15px; padding: 15px;">
                <h4 class="bold m-t-0">@trans('Pretende otimizar a rota para entrega?')</h4>
                <p class="text-muted m-0">@trans('Todos os destinos serão organizados da forma mais eficiente.')
                <br/>@trans('Serão calculadas as horas previstas de entrega.')</p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group is-required">
                {{ Form::label('start_location', __('Local Início')) }} {!! tip('Ex: Lisboa / 1500 Lisboa / 46001 Valencia, Espanha') !!}
                {{ Form::text('start_location', $trip->start_location, ['class' => 'form-control', 'required', 'maxlength' => 35]) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group is-required">
                {{ Form::label('end_location', __('Local Termo')) }} {!! tip('Ex: Lisboa / 1500 Lisboa / 46001 Valencia, Espanha') !!}
                {{ Form::text('end_location', $trip->end_location, ['class' => 'form-control required', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group m-b-0">
                {{ Form::label('start_hour', __('Hora Início'), ['class' => 'control-label']) }}
                {{ Form::select('start_hour', [''=>''] + listHours(5), $trip->start_hour, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group m-b-0">
                {{ Form::label('end_hour', __('Hora Termo'), ['class' => 'control-label']) }}
                {{ Form::select('end_hour', [''=>''] + listHours(5), $trip->end_hour, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group m-b-0">
                {{ Form::label('avg_delivery_time', __('Tempo Descarga'), ['class' => 'control-label']) }}
                {!! tip(__('Estabelece o tempo médio de descarga/entrega por cada serviço.')) !!}
                {{ Form::select('avg_delivery_time', [''=>''] + $deliveryTimes, $trip->avg_delivery_time, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    @if(hasModule('maps') || hasModule('route-optimizer'))
    <button type="submit" class="btn btn-success"><i class="fas fa-route"></i> @trans('Otimizar Rota')</button>
    @endif
</div>
{{ Form::close() }}
<script>
    $('.modal .select2').select2(Init.select2())
</script>
