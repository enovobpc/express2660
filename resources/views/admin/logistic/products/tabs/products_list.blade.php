<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
    @if(config('app.source') != 'activos24')
        <li>
            <a href="{{ route('admin.logistic.products.create') }}"
               class="btn btn-success btn-sm"
               data-toggle="modal"
               data-target="#modal-remote-xl">
                <i class="fas fa-plus"></i> @trans('Novo')
            </a>
        </li>
        <li>
            <div class="btn-group btn-group-sm" role="group">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-boxes"></i> @trans('Gerir Stock') <i class="fas fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ route('admin.logistic.inventories.create') }}"
                               data-toggle="modal"
                               data-target="#modal-remote-xl">
                                <i class="fas fa-fw fa-file-alt"></i> @trans('Criar Inventário')
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="{{ route('admin.logistic.products.stock.add') }}"
                               data-toggle="modal"
                               data-target="#modal-remote-xs">
                                <i class="fas fa-fw fa-plus"></i> @trans('Adicionar Stock')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.logistic.products.adjustment.edit') }}"
                                data-toggle="modal"
                                data-target="#modal-remote">
                                <i class="fas fa-fw fa-check"></i> @trans('Corrigir Stock')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.logistic.products.move.edit') }}"
                               data-toggle="modal"
                               data-target="#modal-remote">
                                <i class="fas fa-fw fa-exchange-alt"></i> @trans('Transferir Stock')
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </li>
    @else
        <li>
            <button class="btn btn-success btn-sm" disabled>
                <i class="fas fa-plus"></i> @trans('Novo')
            </button>
        </li>
    @endif
    <li>
        <div class="btn-group btn-group-sm" role="group">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-wrench"></i> @trans('Ferramentas') <i class="fas fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('admin.importer.index') }}" target="_blank">
                            <i class="fas fa-fw fa-upload"></i> @trans('Importar produtos massivo')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.logistic.products.export', Request::all()) }}" data-toggle="export-url">
                            <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar Listagem atual')
                        </a>
                    </li>
                    @if(config('app.source') == 'activos24')
                    <li class="divider"></li>
                    <li>
                        <a href="{{ route('admin.logistic.products.sync') }}"
                           class="btn-sync">
                            <i class="fas fa-sync-alt"></i> @trans('Sincronizar Agora')
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-print"></i> @trans('Relatórios') <i class="fas fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modal-map-print-stocks">
                            <i class="fas fa-fw fa-print"></i> @trans('Mapa Existências')
                        </a>
                    </li>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modal-map-export-stocks">
                            <i class="fas fa-fw fa-file-excel"></i> @trans('Mapa Existências')
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
        <strong>@trans('Cliente')</strong><br class="visible-xs"/>
        <div class="w-190px pull-left form-group-sm">
            {{ Form::select('dt_customer',  Request::has('dt_customer') ? [''=>'', Request::get('dt_customer') => Request::get('dt_customer-text')] : [''=>''], Request::has('dt_customer') ? Request::get('dt_customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => __('Todos'), 'data-query-text' => 'true')) }}
        </div>
    </li>
</ul>
<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
    <ul class="list-inline pull-left m-b-5">
        <li style="margin-bottom: 5px;" class="form-group-sm hidden-xs col-xs-12">
            <strong>@trans('Filtar Data')</strong><br/>
            <div class="w-130px m-r-4" style="position: relative; z-index: 5;">
                {{ Form::select('date_unity', ['' => 'Últ. movimento', '3' => 'Data Validade', '4' => 'Data registo'], fltr_val(Request::all(), 'date_unity'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li class="shp-date col-xs-12">
            <strong>@trans('Data')</strong><br/>
            <div class="input-group input-group-sm w-240px">
                {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início'), 'autocomplete' => 'field-1']) }}
                <span class="input-group-addon">@trans('até')</span>
                {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim'), 'autocomplete' => 'field-1']) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>@trans('Armazém')</strong><br/>
            <div class="w-130px">
                {{ Form::selectMultiple('warehouse', $warehouses, fltr_val(Request::all(), 'warehouse'), ['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>@trans('Localização')</strong><br/>
            <div class="w-130px">
                {{ Form::selectMultiple('location', $locations, fltr_val(Request::all(), 'location'), ['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
            </div>
        </li>
        {{--<li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Cliente</strong><br/>
            <div class="w-250px">
                {{ Form::select('dt_customer',  Request::has('dt_customer') ? [''=>'', Request::get('dt_customer') => Request::get('dt_customer-text')] : [''=>''], Request::has('dt_customer') ? Request::get('dt_customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
            </div>
        </li>--}}
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>@trans('Unidade')</strong><br/>
            <div class="w-80px">
                {{ Form::select('unity', array('' => __('Todos')) + trans('admin/global.measure-units'), Request::has('unity') ? Request::get('unity') : null, array('class' => 'form-control select2 input-sm filter-datatable')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>@trans('Marca')</strong><br/>
            <div class="w-130px">
                {{ Form::selectMultiple('brand', $brands, fltr_val(Request::all(), 'brand'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>@trans('Modelo')</strong><br/>
            <div class="w-130px">
                {{ Form::selectMultiple('model', $models, fltr_val(Request::all(), 'model'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>@trans('Família')</strong><br/>
            <div class="w-150px">
                {{ Form::selectMultiple('family', $families, fltr_val(Request::all(), 'group'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>@trans('Categoria')</strong><br/>
            <div class="w-130px">
                {{ Form::selectMultiple('category', $categories, fltr_val(Request::all(), 'category'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>@trans('Subcategoria')</strong><br/>
            <div class="w-130px">
                {{ Form::selectMultiple('subcategory', $subcategories, fltr_val(Request::all(), 'subcategory'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>@trans('Imagens')</strong><br/>
            <div class="w-80px">
                {{ Form::select('images', array('' => __('Todos'), '1' => __('Sim'), '0' => __('Não')), fltr_val(Request::all(), 'images'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>@trans('Lote')</strong><br/>
            <div class="w-80px">
                {{ Form::select('lote', array('' => __('Todos'), '1' => __('Sim'), '0' => __('Não')), fltr_val(Request::all(), 'lote'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>@trans('N.º Série')</strong><br/>
            <div class="w-80px">
                {{ Form::select('serial_no', array('' => __('Todos'), '1' => __('Sim'), '0' => __('Não')), fltr_val(Request::all(), 'serial_no'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-6">
            <strong>@trans('Estado')</strong><br/>
            <div class="w-80px">
                {{ Form::select('status', array('' => __('Todos')) + trans('admin/logistic.products.status'), fltr_val(Request::all(), 'status'), array('class' => 'form-control input-sm filter-datatable select2')) }}
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
            <th class="w-1">@trans('SKU')</th>
            <th>@trans('Artigo')</th>
            <th class="w-120px">@trans('Nº Série/Lote')</th>
            <th class="w-75px">@trans('Validade')</th>
            <th class="w-65px">@trans('Stock')</th>
            <th class="w-1"><i class="fas fa-pallet"></i></th>
            <th class="w-1">@trans('PVP')</th>
            <th class="w-150px">@trans('Localização')</th>
            <th class="w-65px">@trans('Ult. Mov.')</th>
            <th class="w-1">@trans('Estado')</th>
            <th class="w-75px">@trans('Ações')</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{--{{ Form::open(array('route' => 'admin.logistic.products.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
    {{ Form::close() }}--}}
    <div>
        {{--<div class="btn-group btn-group-sm dropup">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-print"></i> Bloquear/Desbloquear <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('admin.printer.refunds.customers.proof') }}" data-toggle="datatable-action-url" target="_blank">
                        Comprovativo Reembolso
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.printer.refunds.customers.summary') }}" data-toggle="datatable-action-url" target="_blank">
                        Listagem de Resumo
                    </a>
                </li>
            </ul>
        </div>--}}
        <a href="{{ route('admin.logistic.products.export') }}"
           data-toggle="modal"
           data-target="#modal-block"
           class="btn btn-sm btn-default">
            <i class="fas fa-ban"></i> @trans('Bloquear/Desbloquear')
        </a>
        <a href="{{ route('admin.logistic.products.export') }}"
           data-toggle="datatable-action-url"
           target="_blank"
           class="btn btn-sm btn-default">
            <i class="fas fa-file-excel"></i> @trans('Exportar')
        </a>
    </div>
</div>
<div class="clearfix"></div>