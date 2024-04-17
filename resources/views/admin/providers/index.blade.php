@section('title')
    Fornecedores
@stop

@section('content-header')
    Fornecedores
@stop

@section('breadcrumb')
    <li class="active">@trans('Entidades')</li>
    <li class="active">@trans('Fornecedores')</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box no-border">
                <div class="box-body">
                    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                        <li>
                            <a href="{{ route('admin.providers.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> @trans('Novo')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.providers.sort', ['type' => 'carrier']) }}"
                                class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-remote">
                                <i class="fas fa-sort-amount-down"></i> @trans('Ordenar')
                            </a>
                        </li>
                        <li>
                            <div class="btn-group btn-group-sm" role="group">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-wrench"></i> @trans('Ferramentas') <i class="fas fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('admin.export.providers', Request::all()) }}"
                                                data-toggle="export-url">
                                                <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar listagem atual')
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.importer.index', ['type' => 'providers']) }}"
                                                target="_blank">
                                                <i class="fas fa-fw fa-upload"></i> @trans('Importar Fornecedores')
                                            </a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="{{ route('admin.providers-types.index') }}" data-toggle="modal"
                                                data-target="#modal-remote">
                                                <i class="fas fa-fw fa-list"></i> @trans('Gerir categorias')
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <button type="button" class="btn btn-filter-datatable btn-default">
                                    <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                                </button>
                            </div>
                        </li>
                        @if (count($agencies) > 1)
                            <li class="fltr-primary w-200px">
                                <strong>@trans('Agência')</strong><br class="visible-xs" />
                                <div class="pull-left form-group-sm w-130px">
                                    {{ Form::select('agency', ['' => __('Todos')] + $agencies, Request::has('agency') ? Request::get('agency') : null, ['class' => 'form-control input-sm filter-datatable select2']) }}
                                </div>
                            </li>
                        @endif
                        <li class="fltr-primary w-120px">
                            <strong>@trans('Tipo')</strong><br class="visible-xs" />
                            <div class="pull-left form-group-sm w-80px">
                                {{ Form::select('type',['' => __('Todos'), 'carrier' => __('Transportadores'), 'others' => __('Outros')],Request::has('type') ? Request::get('type') : null,['class' => 'form-control input-sm filter-datatable select2']) }}
                            </div>
                        </li>
                        <li class="fltr-primary w-200px">
                            <strong>@trans('Categoria')</strong><br class="visible-xs" />
                            <div class="pull-left form-group-sm w-130px">
                                {{ Form::selectMultiple('category', $categories, Request::has('category') ? Request::get('category') : null, ['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
                            </div>
                        </li>
                        <li class="fltr-primary w-110px">
                            <strong>@trans('Código')</strong><br class="visible-xs" />
                            <div class="w-50px pull-left form-group-sm" style="position: relative">
                                {{ Form::text('code', fltr_val(Request::all(), 'code'), ['class' => 'form-control input-sm filter-datatable','style' => 'width: 100%;']) }}
                            </div>
                        </li>
                    </ul>
                    <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}"
                        data-target="#datatable">
                        <ul class="list-inline pull-left">
                            <li style="margin-bottom: 5px;" class="col-xs-6">
                                <strong>@trans('País Faturação')</strong><br />
                                <div class="w-110px">
                                    {{ Form::select('country',['' => __('Todos')] + trans('country'),Request::has('country_billing') ? Request::get('country_billing') : null,['class' => 'form-control input-sm filter-datatable select2']) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;" class="col-xs-6">
                                <strong>@trans('Pagamento')</strong><br />
                                <div class="w-150px">
                                    {{ Form::selectMultiple('payment_method',['-1' => __('Sem condição definida')] + $paymentConditions,Request::has('payment_method') ? Request::get('payment_method') : null,['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;" class="col-xs-6">
                                <strong>@trans('Estado')</strong><br />
                                <div class="w-100px">
                                    {{ Form::select('active',['' => __('Todos'), '1' => __('Ativo'), '0' => __('Inativo')],Request::has('active') ? Request::get('active') : '1',['class' => 'form-control input-sm filter-datatable select2']) }}
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="table-responsive">
                        <table id="datatable" class="table table-condensed table-striped table-dashed table-hover">
                            <thead>
                                <tr>
                                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                    <th></th>
                                    <th class="w-1">@trans('Nº')</th>
                                    <th class="w-120px">@trans('Nome Sistema')</th>
                                    <th>@trans('Designação Social')</th>
                                    <th>@trans('Localidade')</th>
                                    <th class="w-150px">@trans('Contactos')</th>
                                    <th class="w-60px">@trans('Saldo')</th>
                                    <th class="w-1"><span data-toggle="tooltip" title="Ativo"><i
                                                class="fas fa-check-circle"></i></span></th>
                                    <th class="w-1"><i class="fas fa-flag"></i></th>
                                    <th class="w-20px">@trans('Ações')</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="selected-rows-action hide">
                        {{ Form::open(['route' => 'admin.providers.selected.destroy']) }}
                        <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i
                                class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
                        {{ Form::close() }}
                        {{ Form::open(['route' => 'admin.providers.selected.inactivate']) }}
                        <button class="btn btn-sm btn-default m-l-5" data-action="confirm"
                            data-confirm-title="Inativar selecionados" data-confirm-class="btn-success"
                            data-confirm-label="Inativar" data-confirm="Pretende inativar os fornecedores selecionados?">
                            <i class="fas fa-user-times"></i> @trans('Inativar')
                        </button>
                        {{ Form::close() }}
                        <a href="{{ route('admin.export.providers') }}" class="btn btn-sm btn-default m-l-5"
                            data-action-url="datatable-action-url">
                            <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar')
                        </a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        var oTable;

        $(document).ready(function() {
            oTable = $('#datatable').DataTable({
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
                        data: 'code',
                        name: 'code',
                        class: 'text-center'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'company',
                        name: 'company'
                    },
                    {
                        data: 'city',
                        name: 'city'
                    },
                    /*{data: 'category_id', name: 'category_id', searchable: false},*/
                    {
                        data: 'contacts',
                        name: 'contacts',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'balance_total_unpaid',
                        name: 'balance_total_unpaid'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        class: 'text-center'
                    },
                    {
                        data: 'country',
                        name: 'country',
                        class: 'text-center'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'address',
                        name: 'address',
                        visible: false
                    },
                    {
                        data: 'email',
                        name: 'email',
                        visible: false
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        visible: false
                    },
                    {
                        data: 'mobile',
                        name: 'mobile',
                        visible: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        visible: false
                    },
                    {
                        data: 'vat',
                        name: 'vat',
                        visible: false
                    },
                ],
                order: [
                    [2, "desc"]
                ],
                ajax: {
                    url: "{{ route('admin.providers.datatable') }}",
                    type: "POST",
                    data: function(d) {
                        d.type = $('[data-target="#datatable"] select[name=type]').val();
                        d.agency = $('[data-target="#datatable"] select[name=agency]').val();
                        d.country = $('[data-target="#datatable"] select[name=country]').val();
                        d.category = $('[data-target="#datatable"] select[name=category]').val();
                        d.payment_method = $('[data-target="#datatable"] select[name=payment_method]')
                            .val();
                        d.code = $('[data-target="#datatable"] input[name=code]').val();
                        d.active = $('[data-target="#datatable"] select[name=active]').val();
                    },
                    beforeSend: function() {
                        Datatables.cancelDatatableRequest(oTable)
                    },
                    complete: function() {
                        Datatables.complete()
                    }
                }
            });

            $('[data-target="#datatable"] .filter-datatable').on('change', function(e) {
                oTable.draw();
                e.preventDefault();

                var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
                exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                $('#tab-others [data-toggle="export-url"]').attr('href', exportUrl);
            });
        });
    </script>
@stop
