<div class="modal" id="fast-search" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(array('route' => array('admin.fast-search'), 'class' => 'form-fast-search')) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title"><i class="fas fa-search"></i> Pesquisa Rápida</h4>
            </div>
                <div class="tabbable-line m-b-15">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#tab-fs-info" data-toggle="tab" data-starget="shipments">
                                @trans('Envios')
                            </a>
                        </li>
                        <li>
                            <a href="#tab-fs-customers" data-toggle="tab" data-starget="customers">
                                @trans('Clientes')
                            </a>
                        </li>
                        <li>
                            <a href="#tab-fs-billing" data-toggle="tab" data-starget="billing">
                                @trans('Faturação')
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="modal-body p-t-0 p-b-0 modal-shipment">
                    <div class="tab-content m-b-0" style="padding-bottom: 10px;">
                        <div class="tab-pane active" id="tab-fs-info">
                            <h4>Código de envio ou recolha a procurar</h4>
                            <div class="form-group">
                                {{ Form::text('fast_search_shipment', null, array('class' => 'form-control input-lg fast-search', 'autocomplete' => 'off')) }}
                            </div>
                            <div class="checkbox-inline">
                                <label>
                                    {{ Form::radio('fs_open_mode', 'show', true) }}
                                    Consultar
                                </label>
                            </div>
                            <div class="checkbox-inline">
                                <label>
                                    {{ Form::radio('fs_open_mode', 'edit') }}
                                    Editar
                                </label>
                            </div>
                        </div>

                        <div class="tab-pane" id="tab-fs-customers">
                            <h4>Código de Cliente ou NIF</h4>
                            <div class="form-group">
                                {{ Form::text('fast_search_customer', null, array('class' => 'form-control input-lg fast-search', 'autocomplete' => 'off')) }}
                            </div>
                        </div>

                        <div class="tab-pane" id="tab-fs-billing">
                            <h4>Código de Cliente ou NIF</h4>
                            <div class="form-group">
                                {{ Form::text('fast_search_billing', null, array('class' => 'form-control input-lg fast-search', 'autocomplete' => 'off')) }}
                            </div>
                            <div class="row row-5">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        {{ Form::label('fast_search_year', 'Ano') }}
                                        {{ Form::select('fast_search_year', listNumeric(1, 2018, date('Y')), date('Y'), array('class' => 'form-control fast-search select2')) }}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {{ Form::label('fast_search_month', 'Mês') }}
                                        {{ Form::select('fast_search_month', trans('datetime.list-month') , date('m'), array('class' => 'form-control fast-search select2')) }}
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        {{ Form::label('fast_search_period', 'Periodo') }}
                                        {{ Form::select('fast_search_period', ['30d' => 'Mensal', '15d' => 'Quinzenal'] ,null, array('class' => 'form-control fast-search select2')) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="modal-footer">
                {{ Form::hidden('target', 'shipment') }}
                <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
                <button type="button" class="btn btn-primary btn-fast-search" data-loading-text="<i class='fas fa-search'></i> @trans('Aguarde...')">@trans('Procurar')</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>