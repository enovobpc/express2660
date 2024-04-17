<div class="modal" id="modal-import-tolls">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => 'admin.fleet.tolls.import', 'class' => 'import-form','files' => true]) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title">@trans('Importar Ficheiro Portagens')</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5 import-inputs-area">
                    <div class="col-sm-9">
                        <div class="form-group m-b-0">
                            {{ Form::label('file', __('Ficheiro de portagens'), ['class' => 'control-label']) }}
                            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                <div class="form-control" data-trigger="fileinput">
                                    <i class="fas fa-file fileinput-exists"></i>
                                    <span class="fileinput-filename"></span>
                                </div>
                                <span class="input-group-addon btn btn-default btn-file">
                                    <span class="fileinput-new">@trans('Selecionar')</span>
                                    <span class="fileinput-exists">@trans('Alterar')</span>
                                    <input type="file" name="file" data-file-format="csv,xls,xlsx" required />
                                </span>
                                <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">
                                    @trans('Remover')
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group is-required">
                            {{ Form::label('provider_id', __('Fornecedor'))  }}
                            {{ Form::select('provider_id', @$tollsProviders, null, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                </div>

                <div class="import-results-area"></div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
                    <button type="submit" class="btn btn-primary"
                            data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A importar...">@trans('Importar')
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>