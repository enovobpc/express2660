<div class="modal" id="modal-import-global-prices-table">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.customers.services.import', $customer->id, 'source' => 'prices-table']]) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title">@trans('Importar a partir de tabela de preços global')</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5">
                    <div class="col-sm-8">
                        <div class="form-group is-required">
                            {{ Form::label('import_global_prices_id', __('Importar tabela de preços global:')) }}
                            {{ Form::select('import_global_prices_id', ['' => ''] + $pricesTables, null, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('import_target', __('Importar Grupo')) }}
                            {{ Form::select('import_target', [''=>'Todos'] + $servicesGroupsList, null, ['class' => 'form-control input-sm select2']) }}
                        </div>
                    </div>
                </div>
                <p class="m-0">
                    <i class="fas fa-info-circle text-info"></i> <b>@trans('Atenção!')</b> @trans('Ao importar, a tabela de preços do atual cliente será subscrita e substituída.')
                </p>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A importar...">@trans('Importar')</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>