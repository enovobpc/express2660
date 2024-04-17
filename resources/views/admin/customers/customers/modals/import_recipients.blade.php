<div class="modal" id="modal-import-recipients">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.customers.recipients.import', $customer->id], 'files' => true]) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title">@trans('Importar remetentes')</h4>
            </div>
            <div class="modal-body">
                <div class="row row-10">
                    <div class="{{ !empty($departments) ? 'col-sm-8' : 'col-sm-12' }}">
                        <div class="form-group m-b-5">
                            {{ Form::label('file', __('Ficheiro a importar'), ['class' => 'control-label']) }}
                            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                <div class="form-control" data-trigger="fileinput">
                                    <i class="fas fa-file fileinput-exists"></i>
                                    <span class="fileinput-filename"></span>
                                </div>
                                <span class="input-group-addon btn btn-default btn-file">
                                    <span class="fileinput-new">@trans('Selecionar')</span>
                                    <span class="fileinput-exists">@trans('Alterar')</span>
                                    <input type="file" name="file" data-file-format="csv,xls,xlsx" required>
                                </span>
                                <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">@trans('Remover')</a>
                            </div>
                        </div>
                    </div>
                    @if(!empty($departments))
                    <div class="col-sm-4">
                        <div class="form-group m-b-5">
                            {{ Form::label('customer_id', __('Departamento'), ['class' => 'control-label']) }}
                            {{ Form::select('customer_id', ['' => __('Apenas Conta Principal')] + $departments, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    @endif
                </div>
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