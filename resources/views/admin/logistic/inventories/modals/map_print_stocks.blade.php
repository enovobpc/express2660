<div class="modal modal-filter-dates" id="modal-map-print-stocks">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.logistic.inventories.print.maps', 'stocks'], 'method' => 'GET', 'target' => '_blank']) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title"><i class="fas fa-print"></i> @trans('Imprimir mapa de existências')</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5">
                    <div class="col-sm-12">
                        <div class="form-group is-required">
                            {{ Form::label('customer', __('Cliente'), ['class' => 'control-label']) }}
                            {{ Form::select('customer', [], null, ['class' => 'form-control', 'data-placeholder' => 'Todos']) }}
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group is-required">
                            {{ Form::label('date', __('Existências à data'), ['class' => 'control-label']) }}
                            <div class="input-group">
                                {{ Form::text('date', date('Y-m-d'), ['class' => 'form-control datepicker']) }}
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="form-group is-required">
                            {{ Form::label('warehouse', __('Armazém'), ['class' => 'control-label']) }}
                            {{ Form::select('warehouse', ['' => __('Todos')] + $warehouses, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group is-required m-t-0 m-b-5">
                            <div class="checkbox m-0">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('group_customer', 1, true) }}
                                    @trans('Agrupar por cliente')
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group is-required m-t-0 m-b-5">
                            <div class="checkbox m-0">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('group_product', 1, true) }}
                                    @trans('Agrupar por produto (ignorar detalhe por localização)')
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-left">
                    <p class="text-red m-t-5 m-b-0" id="modal-feedback"></p>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
                <button type="submit" class="btn btn-primary btn-submit"><i class="fas fa-print"></i> @trans('Imprimir')</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>