<div class="modal fade" id="modal-import-prices-table">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.providers.services.import', $provider->id]]) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title">@trans('Importar tabela de preços de outra agência')</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5">
                    <div class="col-sm-8">
                        <div class="form-group is-required">
                            {{ Form::label('import_agency_id', __('Importar tabela de preços da agência:')) }}
                            {{ Form::select('import_agency_id', ['' => ''] + $agenciesList, null, ['class' => 'form-control select2', 'required']) }}
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
                    <i class="fas fa-exclamation-triangle text-yellow"></i> <b>@trans('Atenção!')</b> @trans('Ao importar, a tabela de preços do será subscrita e substituída.')
                </p>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    {{ Form::hidden('type', Request::get('type')) }}
                    {{ Form::hidden('agency', Request::get('agency')) }}
                    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A importar...">@trans('Importar')</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>