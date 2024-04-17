<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
    <li>
        <a href="{{ route('admin.equipments.create') }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-lg">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    <li>
        <div class="btn-group btn-group-sm" role="group">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-wrench"></i> Ferramentas <i class="fas fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('admin.importer.index') }}" target="_blank">
                            <i class="fas fa-fw fa-upload"></i> Importar Artigos
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            data-toggle="modal"
                            data-target="#modal-file-conference">
                            <i class="fas fa-fw fa-upload"></i> Conferência por Ficheiro
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="{{ route('admin.equipments.categories.index') }}"
                           data-toggle="modal"
                           data-target="#modal-remote">
                            <i class="fas fa-fw fa-list"></i> Gerir categorias
                        </a>
                    </li>
                </ul>
            </div>
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-print"></i> Imprimir <i class="fas fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('admin.equipments.printer.inventory') }}" target="_blank">
                            Inventário simples
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.equipments.printer.inventory', ['group' => 'location']) }}" target="_blank">
                            Inventário por localização
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.equipments.printer.inventory', ['group' => 'category']) }}" target="_blank">
                            Inventário por categoria
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.equipments.printer.inventory', ['group' => 'location-category']) }}" target="_blank">
                            Inventário por Localização + categoria
                        </a>
                    </li>
                </ul>
            </div>
            <button type="button" class="btn btn-filter-datatable btn-sm btn-default">
                <i class="fas fa-filter"></i> <i class="fas fa-angle-down"></i>
            </button>
        </div>
    </li>
    <li class="fltr-primary w-240px">
        <strong>Cliente</strong><br class="visible-xs"/>
        <div class="w-190px pull-left form-group-sm">
            {{ Form::select('dt_customer',  Request::has('dt_customer') ? [''=>'', Request::get('dt_customer') => Request::get('dt_customer-text')] : [''=>''], Request::has('dt_customer') ? Request::get('dt_customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
        </div>
    </li>
    <li class="fltr-primary w-230px">
        <strong>Localização</strong><br class="visible-xs"/>
        <div class="w-150px pull-left form-group-sm">
            {{ Form::selectMultiple('location', ['-1' => 'Sem localização'] + $locations, fltr_val(Request::all(), 'location'), ['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
        </div>
    </li>
</ul>
<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
    <ul class="list-inline pull-left m-b-5">
        <li style="margin-bottom: 5px;" class="form-group-sm hidden-xs col-xs-12">
            <strong>Filtar Data</strong><br/>
            <div class="w-130px m-r-4" style="position: relative; z-index: 5;">
                {{ Form::select('date_unity', ['' => 'Últ. movimento', 'creation' => 'Data criação'] + trans('admin/equipments.equipments.actions'), fltr_val(Request::all(), 'date_unity'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li class="shp-date col-xs-12">
            <strong>Data</strong><br/>
            <div class="input-group input-group-sm w-240px">
                {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                <span class="input-group-addon">até</span>
                {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Categoria</strong><br/>
            <div class="w-130px">
                {{ Form::selectMultiple('category', $categoriesList, fltr_val(Request::all(), 'category'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Imagens</strong><br/>
            <div class="w-80px">
                {{ Form::select('images', array('' => 'Todos', '1' => 'Sim', '0' => 'Não'), fltr_val(Request::all(), 'images'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Estado</strong><br/>
            <div class="w-80px">
                {{ Form::select('status', array('' => 'Todos') + trans('admin/equipments.equipments.status'), fltr_val(Request::all(), 'status'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Movimentos</strong><br/>
            <div class="w-130px">
                {{ Form::select('action', array('' => 'Todos') + trans('admin/equipments.equipments.actions'), fltr_val(Request::all(), 'action'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li> 
         <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>Tipo de Ficheiro</strong><br/>
            <div class="w-130px">
                {{ Form::select('type_file', array('' => 'Todos', 'Némesis' => 'Némesis', 'Click' => 'Click'), fltr_val(Request::all(), 'type_file'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
    </ul>
</div>
<div class="table-responsive">
    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-1"></th>
            <th class="w-1">Referência</th>
            <th>Equipamento</th>
            <th class="w-160px">Nº Série/Lote</th>
            <th class="w-120px">Categoria</th>
            <th class="w-40px">Qtd</th>
            <th class="w-200px">Localização</th>
            <th class="w-1">Estado</th>
            <th class="w-65px">N.º OT</th>
            <th class="w-65px">Ult. Mov.</th>
            <th class="w-75px">Ações</th>
        </tr>
        </thead>
        <tbody></tbody> 
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.equipments.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
    {{ Form::close() }}
    <div>
        <div class="btn-group btn-group-sm dropup m-l-5">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-fw fa-file-excel"></i>  Exportar <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('admin.equipments.export') }}" target="_blank">
                        Listagem de Equipamentos
                    </a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="{{ route('admin.equipments.filter.export', ['group' => 'consumption']) }}" data-toggle="modal" data-target="#modal-remote-xs">
                        Inventário Consumo por Localização + Categoria
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.equipments.export.file', ['group' => 'warehouse']) }}" target="_blank">
                        Inventário por Armazém + Categoria
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.equipments.export.file', ['group' => 'stock-location-category']) }}" target="_blank">
                        Inventário por Localização + Categoria
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.equipments.filter.export', ['group' => 'resume-movements']) }}" data-toggle="modal" data-target="#modal-remote-xs">
                        Resumo de Movimentos por Categoria
                    </a>
                </li>
            </ul>
        </div>
        <div class="btn-group btn-group-sm dropup m-l-5">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-print"></i> Imprimir <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('admin.equipments.printer.labels') }}"
                       data-toggle="datatable-action-url"
                       target="_blank">
                       Etiquetas Artigo
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.equipments.printer.inventory') }}"
                       data-toggle="datatable-action-url"
                       target="_blank">
                        Inventário simples
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.equipments.printer.inventory', ['group' => 'location']) }}"
                       data-toggle="datatable-action-url"
                       target="_blank">
                        Inventário por localização
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.equipments.printer.inventory', ['group' => 'category']) }}"
                       data-toggle="datatable-action-url"
                       target="_blank">
                        Inventário por categoria
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.equipments.printer.inventory', ['group' => 'location-category']) }}"
                       data-toggle="datatable-action-url"
                       target="_blank">
                        Inventário por localização e categoria
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="clearfix"></div>