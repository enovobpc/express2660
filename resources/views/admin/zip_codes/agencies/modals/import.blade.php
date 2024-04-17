<div class="modal" id="modal-import-zip-codes">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            {{ Form::open(['route' => 'admin.zip-codes.agencies.import']) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Carregar Códigos Postais em Massa</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5 modal-filters" style="margin: -15px -15px 10px;
    background: #eee;
    padding: 6px 10px 0px;
    border-bottom: 1px solid #ddd;">
                    @include('admin.zip_codes.agencies.partials.filters')
                </div>
                <div class="import-search-results">
                    <div class="helper">
                        <i class="fas fa-search"></i>
                        Escolha um distrito ou concelho para procurar códigos postais.
                    </div>
                </div>
                <p class="bold m-t-10 text-blue">Associar os códigos selecionados na lista a:</p>
                <div class="row row-5">
                    <div class="col-sm-2">
                        <div class="form-group">
                            {{ Form::label('zone', 'País') }}
                            {{ Form::select('zone', trans('country'), Setting::get('app_country'), ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {{ Form::label('agency_id', 'Agência/Armazém') }} {!! tip('Todos os envios para estes códigos postais serão associados à agência indicada.') !!}
                            {{ Form::select('agency_id', ['' => '- Nenhuma -'] + $agenciesList, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            {{ Form::label('provider_id', 'Fornecedor') }} {!! tip('Todos os envios para estes códigos postais serão canalizados para o fornecedor indicado.') !!}
                            {{ Form::select('provider_id', ['' => '- Nenhum -'] + $providersList, isset($provider) ? $provider->id : '', ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('services[]', 'Serviços Autorizados') }} {!! tip('Selecione os serviços que estão disponíveis para estes códigos postais. Se não selecionar nenhum são considerados todos.') !!}
                            {{ Form::select('services[]', $servicesList, null, ['class' => 'form-control select2', 'multiple', 'data-placeholder' => 'Todos']) }}
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <div class="form-group">
                            {{ Form::label('kms', 'Kms') }} {!! tip('Especifique o total de KM entre a agência e estes códigos postais.') !!}
                            {{ Form::text('kms', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group m-0">
                            <div class="checkbox m-0">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('is_regional', 1) }}
                                    Os códigos selecionados são locais/regionais
                                </label>
                                {!! tip('Os códigos postais logais/regionais são todos os codigos postais abrangidos pelas suas próprias viaturas sem necessidade de subcontratação.') !!}
                            </div>
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