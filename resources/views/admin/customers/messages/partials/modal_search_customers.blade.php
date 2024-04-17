<div class="modal" id="modal-filter-customers" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => array('admin.customers.messages.store'), 'method' => 'POST', 'class' => 'form-filter-customers']) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title">@trans('Filtrar clientes')</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {{ Form::label('type[]', __('Tipo(s) de Cliente')) }}
                    {{ Form::selectMultiple('type[]', $types, null, ['class' => 'form-control select2', 'data-placeholder' => 'Todos']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('agency[]', __('Agência(s)')) }}
                    {{ Form::selectMultiple('agency[]', $agencies, null, ['class' => 'form-control select2', 'data-placeholder' => 'Todos']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('route[]', __('Rota(s)')) }}
                    {{ Form::selectMultiple('route[]', $routes, null, ['class' => 'form-control select2', 'data-placeholder' => 'Todos']) }}
                </div>
                {{--<div class="form-group">
                    {{ Form::label('seller[]', 'Vendedor(s)') }}
                    {{ Form::selectMultiple('seller[]', $sellers, null, ['class' => 'form-control select2', 'data-placeholder' => 'Todos']) }}
                </div>--}}
                <div class="form-group">
                    {{ Form::label('prices[]', __('Tabela(s) de Preço')) }}
                    {{ Form::selectMultiple('prices[]', $pricesTables, null, ['class' => 'form-control select2', 'data-placeholder' => 'Todos']) }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default filter-cancel">@trans('Fechar')</button>
                <button type="button" class="btn btn-primary filter-customers">@trans('Filtrar')</button>
            </div>
            {{ Form::hidden('filter', 1) }}
            {{ Form::close() }}
        </div>
    </div>
</div>