@section('title')
    Artigos Faturação e Taxas IVA
@stop

@section('content-header')
    Artigos Faturação e Taxas IVA
@stop

@section('breadcrumb')
    <li class="active">Faturação</li>
    <li class="active">Artigos Faturação e Taxas IVA</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs tabs-filter">
                    <li class="active"><a data-toggle="tab" href="#tab-items">Artigos Faturação</a></li>
                    {{-- <li><a href="#tab-products" data-toggle="tab">Peças e Produtos</a></li> --}}
                    @if (Auth::user()->hasRole([config('permissions.role.admin')]))
                        <li><a data-toggle="tab" href="#tab-vatrates">Taxas IVA</a></li>
                    @endif
                    @if (Auth::user()->hasRole([config('permissions.role.admin')]))
                        <li><a data-toggle="tab" href="#tab-apikey">API Faturação</a></li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-items">
                        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-items">
                            <li>
                                <a class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote" href="{{ route('admin.billing.items.create') }}">
                                    <i class="fas fa-plus"></i> Novo
                                </a>
                            </li>
                            <li>
                                <div class="btn-group" role="group">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" type="button">
                                            <i class="fas fa-wrench"></i> Ferramentas <i class="fas fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{ route('admin.importer.index') }}">
                                                    <i class="fas fa-fw fa-upload"></i> Importador de Ficheiros Excel
                                                </a>
                                            </li>
                                            <div class="divider"></div>
                                            <li>
                                                <a data-toggle="modal" data-target="#modal-remote" href="{{ route('admin.billing.items.sort') }}">
                                                    <i class="fas fa-fw fa-sort-amount-down"></i> Ordenar
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="btn-group" role="group">
                                        <button class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" type="button">
                                            <i class="fas fa-print"></i> Relatórios <i class="fas fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a data-toggle="export-url" href="{{ route('admin.printer.billing.items.list', Request::all()) }}" target="_blank">
                                                    <i class="fas fa-fw fa-print"></i> Imprimir listagem atual
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    <button class="btn btn-filter-datatable btn-default btn-sm" type="button">
                                        <i class="fas fa-filter"></i> <i class="fas fa-angle-down"></i>
                                    </button>
                                </div>
                            </li>

                            @if (Auth::user()->isAdmin())
                                | <li>
                                    <a class="btn btn-default btn-sm" data-method="get" data-confirm="Confirma a inserção automática de artigos no programa de faturação?" data-confirm-title="Adicionar artigos" data-confirm-label="Adicionar"
                                        data-confirm-class="btn-success" href="{{ route('admin.billing.items.sync', ['install' => 1]) }}">
                                        <i class="fas fa-plus"></i> Instalar
                                    </a>
                                </li>
                                <li>
                                    <a class="btn btn-default btn-sm" data-method="get" data-confirm="Confirma a sincronização de artigos com o programa de faturação?" data-confirm-title="Sincronizar artigos" data-confirm-label="Sincronizar"
                                        data-confirm-class="btn-success" href="{{ route('admin.billing.items.sync') }}">
                                        <i class="fas fa-sync-alt"></i> Sincronizar
                                    </a>
                                </li>
                            @endif
                        </ul>
                        <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-items">
                            <ul class="list-inline pull-left m-b-5">
                                <li>
                                    <strong>Tipo</strong>
                                    <div class="w-140px">
                                        {{ Form::select('type', ['' => 'Todos', 'services' => 'Serviços', 'products' => 'Produtos', 'stocks' => 'Produtos (Com Stocks)'], fltr_val(Request::all(), 'type'), ['class' => 'form-control input-sm filter-datatable select2']) }}
                                    </div>
                                </li>

                                <li>
                                    <strong>Marca</strong>
                                    <div class="w-130px">
                                        {{ Form::selectMultiple('brand', $brands, fltr_val(Request::all(), 'brand'), ['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
                                    </div>
                                </li>

                                <li>
                                    <strong>Modelo</strong>
                                    <div class="w-130px">
                                        {{ Form::selectMultiple('model', $models, fltr_val(Request::all(), 'model'), ['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
                                    </div>
                                </li>

                                <li>
                                    <strong>Ativo</strong>
                                    <div class="w-70px">
                                        {{ Form::select('is_active', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], fltr_val(Request::all(), 'is_active'), ['class' => 'form-control input-sm filter-datatable select2']) }}
                                    </div>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-dashed table-hover table-condensed" id="datatable-items">
                                <thead>
                                    <tr>
                                        <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                        <th></th>
                                        <th class="w-120px">Referência</th>
                                        <th>Designação</th>
                                        <th class="w-100px">Fornecedor</th>
                                        <th class="w-90px">Preço Compra</th>
                                        <th class="w-90px">Preço Venda</th>
                                        <th class="w-1">IVA</th>
                                        <th class="w-1">Serviço</th>
                                        <th class="w-70px">Stock</th>
                                        <th class="w-1">Ativo</th>
                                        <th class="w-1"><i class="fas fa-sort-amount-up"></i></th>
                                        <th class="w-65px">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="selected-rows-action hide">
                            {{ Form::open(['route' => 'admin.billing.items.selected.destroy']) }}
                            <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
                            {{ Form::close() }}
                        </div>
                    </div>
                    @if (hasPermission('vat_rates'))
                        <div class="tab-pane" id="tab-vatrates">
                            @include('admin.billing.vat_rates.index')
                        </div>
                    @endif
                    @if (Auth::user()->hasRole([config('permissions.role.admin')]))
                        <div class="tab-pane" id="tab-apikey">
                            @include('admin.billing.api_keys.index')
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        var oTableItems, oTableVatRates, oTableApiKeys;
        $(document).ready(function() {

            oTableItems = $('#datatable-items').DataTable({
                columns: [{
                        data: 'select',
                        name: 'select',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id',
                        name: 'id',
                        visible: false
                    },
                    {
                        data: 'reference',
                        name: 'reference'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'provider_id',
                        name: 'provider_id'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'sell_price',
                        name: 'sell_price'
                    },
                    {
                        data: 'tax_rate',
                        name: 'tax_rate',
                        class: 'text-center'
                    },
                    {
                        data: 'is_service',
                        name: 'is_service',
                        class: 'text-center'
                    },
                    {
                        data: 'stock_total',
                        name: 'stock_total',
                        class: 'text-center'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        class: 'text-center'
                    },
                    {
                        data: 'sort',
                        name: 'sort',
                        class: 'text-center'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [
                    [11, "desc"]
                ],
                ajax: {
                    url: "{{ route('admin.billing.items.datatable') }}",
                    type: "POST",
                    data: function(d) {
                        d.agency = $('#tab-items select[name=agency]').val()
                        d.type = $('#tab-items select[name=type]').val()
                        d.is_active = $('#tab-items select[name=is_active]').val()
                        d.brand = $('#tab-items select[name=brand]').val();
                        d.model = $('#tab-items select[name=model]').val();
                    },
                    complete: function() {
                        Datatables.complete();
                    },
                    error: function() {
                        Datatables.error();
                    }
                }
            });

            $('#tab-items .filter-datatable').on('change', function(e) {
                oTableItems.draw();
                e.preventDefault();
            });

            $('#tab-items .filter-datatable').on('change', function(e) {
                e.preventDefault();
                oTableItems.draw();

                $('#tab-items [data-toggle="export-url"]').each(function() {
                    var exportUrl = Url.removeQueryString($(this).attr('href'));
                    exportUrl = exportUrl + '?' + Url.getQueryString(Url.current());
                    $(this).attr('href', exportUrl);
                });
            });


            @if (hasPermission('vat_rates'))
                oTableVatRates = $('#datatable-vatrates').DataTable({
                    columns: [{
                            data: 'select',
                            name: 'select',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'id',
                            name: 'id',
                            visible: false
                        },
                        {
                            data: 'company_id',
                            name: 'company_id'
                        },
                        {
                            data: 'code',
                            name: 'code',
                            class: 'text-center'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'name_abrv',
                            name: 'name_abrv'
                        },
                        {
                            data: 'class',
                            name: 'class',
                            class: 'text-center'
                        },
                        {
                            data: 'subclass',
                            name: 'subclass'
                        },
                        {
                            data: 'zone',
                            name: 'zone'
                        },
                        {
                            data: 'value',
                            name: 'value',
                            class: 'text-right'
                        },
                        {
                            data: 'exemption_reason',
                            name: 'exemption_reason',
                            class: 'text-center'
                        },
                        {
                            data: 'is_sales',
                            name: 'is_sales',
                            class: 'text-center'
                        },
                        {
                            data: 'is_purchases',
                            name: 'is_purchases',
                            class: 'text-center'
                        },
                        {
                            data: 'is_active',
                            name: 'is_active',
                            class: 'text-center'
                        },
                        {
                            data: 'billing_code',
                            name: 'billing_code',
                            class: 'text-center'
                        },
                        {
                            data: 'sort',
                            name: 'sort',
                            class: 'text-center'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    order: [
                        [15, "asc"]
                    ],
                    ajax: {
                        url: "{{ route('admin.billing.vat-rates.datatable') }}",
                        type: "POST",
                        data: function(d) {
                            d.company = $('#tab-vatrates select[name=company]').val()
                        },
                        complete: function() {
                            Datatables.complete();
                        },
                        error: function() {
                            Datatables.error();
                        }
                    }
                });

                $('#tab-vatrates .filter-datatable').on('change', function(e) {
                    oTableVatRates.draw();
                    e.preventDefault();
                });
            @endif

            @if (Auth::user()->isAdmin())
                oTableApiKeys = $('#datatable-apikeys').DataTable({
                    columns: [{
                            data: 'select',
                            name: 'select',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'id',
                            name: 'id',
                            visible: false
                        },
                        {
                            data: 'company_id',
                            name: 'company_id'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'token',
                            name: 'token'
                        },
                        {
                            data: 'start_date',
                            name: 'start_date'
                        },
                        {
                            data: 'end_date',
                            name: 'end_date'
                        },
                        {
                            data: 'is_active',
                            name: 'is_active',
                            class: 'text-center'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    order: [
                        [8, "asc"]
                    ],
                    ajax: {
                        url: "{{ route('admin.billing.api-keys.datatable') }}",
                        type: "POST",
                        data: function(d) {
                            d.active = $('#tab-apikey select[name=is_active]').val()
                        },
                        complete: function() {
                            Datatables.complete();
                        },
                        error: function() {
                            Datatables.error();
                        }
                    }
                });

                $('#tab-apikey .filter-datatable').on('change', function(e) {
                    oTableApiKeys.draw();
                    e.preventDefault();
                });
            @endif
        });
    </script>
@stop
