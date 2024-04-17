<div class="modal" id="modal-import-from-agency">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            {{ Form::open(['route' => 'admin.zip-codes.agencies.import.agency']) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Importar Códigos de Outra Agência</h4>
            </div>
            <div class="modal-body">
                <h4 class="m-0 text-blue">Importar de</h4>
                <div class="row row-5">
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('source', 'Plataforma Origem') }}
                            {{ Form::select('source', ['' => ''] + $sources, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group is-required">
                            {{ Form::label('source_agency', 'Importar códigos da agência') }}
                            {{ Form::select('source_agency', ['' => 'Todas'] + $agenciesList, null, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('source_provider', 'Forncedor') }}
                            {{ Form::select('source_provider', ['' => 'Qualquer'] + $allProviders, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
                <h4 class="m-0 text-blue">Importar Para</h4>
                <div class="row row-5">
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('recipient_source', 'Plaraforma Destino') }}
                            {{ Form::select('recipient_source', ['' => ''] + $sources, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('provider_id', 'Fornecedor Destino') }}
                            {{ Form::select('provider_id', ['' => '- Nenhum Fornecedor -'] + $providersList, isset($provider) ? $provider->id : '', ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A importar...">Importar</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>