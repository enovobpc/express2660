@section('title')
    Caixa de Valores
@stop

@section('content-header')
    Caixa de Valores
@stop

@section('breadcrumb')
    <li class="active">Caixa de Valores</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.cashier.create') }}"
                           class="btn btn-success btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote">
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
                                        <a href="{{ route('admin.printer.cashier.movements', Request::all()) }}" target="_blank" data-toggle="export-url">
                                            <i class="fas fa-fw fa-print"></i> Imprimir listagem
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.printer.cashier.movements', Request::all() + ['grouped' => 'operator'] ) }}" target="_blank" data-toggle="export-url">
                                            <i class="fas fa-fw fa-print"></i> Imprimir listagem (agr. colaborador)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <button type="button" class="btn btn-filter-datatable btn-default">
                                <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                            </button>
                        </div>
                    </li>
                    <li class="fltr-primary w-170px">
                        <strong>Operação</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-100px">
                            {{ Form::select('sense', ['' => 'Todos', 'debit' => 'Débitos', 'credit' => 'Créditos'], Request::has('sense') ? Request::get('sense') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-200px">
                        <strong>Tipo</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-140px">
                            {{ Form::select('type', ['' => 'Todos'] + $purchasesTypes, Request::has('type') ? Request::get('type') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        <li class="col-xs-12">
                            <strong>Data</strong><br/>
                            <div class="input-group input-group-sm w-220px">
                                {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                                <span class="input-group-addon">até</span>
                                {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-6">
                            <strong>Pago</strong><br/>
                            <div class="w-70px">
                                {{ Form::select('paid', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('paid') ? Request::get('paid') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-6">
                            <strong>Método</strong><br/>
                            <div class="w-130px">
                                {{ Form::select('payment_method', ['' => 'Todos'] + $paymentMethods, Request::has('payment_method') ? Request::get('payment_method') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>Cliente</strong><br/>
                            <div class="w-200px">
                                {{ Form::select('customer',  Request::has('customer') ? [''=>'', Request::get('customer') => Request::get('customer-text')] : [''=>''], Request::has('customer') ? Request::get('customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>Fornecedor</strong><br/>
                            <div class="w-200px">
                                {{ Form::select('provider',  Request::has('provider') ? [''=>'', Request::get('provider') => Request::get('provider-text')] : [''=>''], Request::has('provider') ? Request::get('provider') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-6">
                            <strong>Colaborador</strong><br/>
                            <div class="w-150px">
                                {{ Form::select('operator', ['' => 'Todos'] + $operators, Request::has('operator') ? Request::get('operator') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-6">
                            <strong>Criado Por</strong><br/>
                            <div class="w-150px">
                                {{ Form::select('created_by', ['' => 'Todos'] + $operators, Request::has('created_by') ? Request::get('created_by') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
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
                            <th class="w-1">Movimento</th>
                            <th class="w-60px">Data</th>
                            <th>Colaborador</th>
                            <th class="w-120px">Tipo</th>
                            <th>Designação</th>
                            <th class="w-120">Cliente ou Fornecedor</th>
                            <th class="w-70px">Valor</th>
                            <th class="w-80px">Método</th>
                            <th class="w-1">Pago</th>
                            {{--<th class="w-120px">Criado Por</th>--}}
                            <th class="w-55px">Ações</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.cashier.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger"
                            data-action="confirm"
                            data-title="Apagar selecionados">
                        <i class="fas fa-trash-alt"></i> Apagar Selecionados
                    </button>
                    {{ Form::close() }}
                    <a href="{{ route('admin.printer.cashier.movements') }}" target="_blank" class="btn btn-sm btn-default m-l-5 hide" data-toggle="export-selected">
                        <i class="fas fa-fw fa-print"></i> Imprimir
                    </a>
                    <div class="btn-group btn-group-sm dropup m-l-5">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-print"></i> Imprimir <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.printer.cashier.movements') }}" data-toggle="datatable-action-url" target="_blank">
                                    Listagem simples
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.printer.cashier.movements', ['grouped' => 'operator']) }}" data-toggle="datatable-action-url" target="_blank">
                                    Listagem por colaborador
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
    <script type="text/javascript">
        var oTable;

        $(document).ready(function () {
            oTable = $('#datatable').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'code', name: 'code'},
                    {data: 'date', name: 'date'},
                    {data: 'operator_id', name: 'operator_id', orderable: false, searchable: false},
                    {data: 'type_id', name: 'type_id', orderable: false, searchable: false},
                    {data: 'description', name: 'description'},
                    {data: 'customer', name: 'customer', orderable: false, searchable: false},
                    {data: 'amount', name: 'amount', class: 'text-right'},
                    {data: 'payment_method', name: 'payment_method'},
                    {data: 'is_paid', name: 'is_paid', class: 'text-center'},
                    /*{data: 'created_by', name: 'created_by', orderable: false, searchable: false},*/
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[2, "asc"]],
                ajax: {
                    url: "{{ route('admin.cashier.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.date_min       = $('input[name=date_min]').val();
                        d.date_max       = $('input[name=date_max]').val();
                        d.sense          = $('select[name=sense]').val();
                        d.customer       = $('select[name=customer]').val();
                        d.provider       = $('select[name=provider]').val();
                        d.operator       = $('select[name=operator]').val();
                        d.type           = $('select[name=type]').val();
                        d.created_by     = $('select[name=created_by]').val();
                        d.payment_method = $('select[name=payment_method]').val();
                        d.paid           = $('select[name=paid]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('.filter-datatable').on('change', function (e) {
                e.preventDefault();
                oTable.draw();

                var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
                exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                $('[data-toggle="export-url"]').attr('href', exportUrl);
            });
        });

        //export selected
        $(document).on('change', '.row-select',function(){
            var queryString = '';
            $('input[name=row-select]:checked').each(function(i, selected){
                queryString+=  (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()
            });

            var exportUrl = Url.removeQueryString($('[data-toggle="export-selected"]').attr('href'));
            $('[data-toggle="export-selected"]').attr('href', exportUrl + '?' + queryString);
        });

        $("select[name=customer]").select2({
            minimumInputLength: 2,
            allowClear: true,
            ajax: Init.select2Ajax("{{ route('admin.cashier.search.customer') }}")
        });

        $("select[name=provider]").select2({
            minimumInputLength: 2,
            allowClear: true,
            ajax: Init.select2Ajax("{{ route('admin.cashier.search.provider') }}")
        });
    </script>
@stop