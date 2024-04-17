<div class="modal" id="modal-mass-edit">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.zip-codes.agencies.selected.update']]) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Editar códigos postais em massa</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5">
                    <div class="col-sm-8">
                        <div class="form-group">
                            {{ Form::label('assign_provider_id', 'Fornecedor') }}
                            {{ Form::select('assign_provider_id', ['' => '- Não Alterar -', '-1' => 'Nenhum'] + $providersList, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('kms', 'Kms') }}
                            {!! tip('Distância desde a Agência associada.') !!}
                            {{ Form::text('kms', null, ['class' => 'form-control', 'placeholder' => '-Não alterar-']) }}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{ Form::label('assign_agency_id', 'Associar à Agência') }}
                    {{ Form::select('assign_agency_id', ['' => '- Não Alterar -','-1' => 'Não associar nenhuma Agência'] + $agenciesList, null, ['class' => 'form-control select2']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('assign_is_regional', 'Códigos Regionais') }}
                    {{ Form::select('assign_is_regional', ['-1' => '- Não Alterar -','0' => 'Não', '1' => 'Sim'], null, ['class' => 'form-control select2']) }}
                </div>
                <div class="form-group m-b-0">
                    {{ Form::label('assign_services[]', 'Serviços autorizados') }} {!! tip('Limite os serviços possíveis usar para este código postal. Por exemplo, pode impedir que serviços internacionais sejam selecionados para códigos postais nacionais.') !!}
                    {{ Form::select('assign_services[]', ['-1' => 'Todos'] + $servicesList, null, ['class' => 'form-control select2', 'multiple', 'data-placeholder' => 'Não alterar']) }}
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">Gravar</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>