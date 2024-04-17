@section('title')
    Destinatários
@stop

@section('content-header')
    Destinatários
@stop

@section('breadcrumb')
<li class="active">@trans('Destinatários')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    {{--<li>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-filter-datatable btn-default">
                                <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                            </button>
                        </div>
                    </li>--}}
                    <li class="fltr-primary w-270px">
                        <strong>@trans('Cliente')</strong><br class="visible-xs"/>
                        <div class="w-220px pull-left form-group-sm" style="position: relative">
                            {{ Form::select('dt_customer',  Request::has('dt_customer') ? [''=>'', Request::get('dt_customer') => Request::get('dt_customer-text')] : [''=>''], Request::has('dt_customer') ? Request::get('dt_customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-160px">
                        <strong>@trans('País')</strong><br class="visible-xs"/>
                        <div class="w-120px pull-left form-group-sm" style="position: relative">
                            {{ Form::select('country', ['' => 'Todos'] + trans('country'), Request::has('country') ? Request::get('country') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-125px">
                        <strong>@trans('E-mail')</strong><br class="visible-xs"/>
                        <div class="w-75px pull-left form-group-sm" style="position: relative">
                            {{ Form::select('email', ['' => __('Todos'), '1' => __('Sim'), '0' => __('Não')], Request::has('email') ? Request::get('email') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-125px">
                        <strong>@trans('Telem')</strong><br class="visible-xs"/>
                        <div class="w-75px pull-left form-group-sm" style="position: relative">
                            {{ Form::select('phone', ['' => __('Todos'), '1' => __('Sim'), '0' => __('Não')], Request::has('phone') ? Request::get('phone') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-125px">
                        <strong>@trans('NIF')</strong><br class="visible-xs"/>
                        <div class="w-75px pull-left form-group-sm" style="position: relative">
                            {{ Form::select('vat', ['' => __('Todos'), '1' => __('Sim'), '0' => __('Não')], Request::has('vat') ? Request::get('vat') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                </ul>
                {{--<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>País</strong><br/>
                            <div class="w-110px">
                                {{ Form::select('country', ['' => 'Todos'] + trans('country'), Request::has('country') ? Request::get('country') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>Cliente</strong><br/>
                            <div class="w-250px">
                                {{ Form::select('dt_customer',  Request::has('dt_customer') ? [''=>'', Request::get('dt_customer') => Request::get('dt_customer-text')] : [''=>''], Request::has('dt_customer') ? Request::get('dt_customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
                            </div>
                        </li>
                    </ul>
                </div>--}}
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-1">@trans('Código')</th>
                                <th>@trans('Destinatário')</th>
                                <th>@trans('Morada')</th>
                                <th class="w-200px">@trans('Contactos')</th>
                                <th class="w-1"><i class="fas fa-flag"></i></th>
                                <th class="w-150px">@trans('Cliente')</th>
                                <th class="w-65px">@trans('Criado em')</th>
                                <th class="w-1">@trans('Ações')</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.recipients.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger"
                            data-action="confirm"
                            data-title="Apagar selecionados"
                            data-confirm-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
                    {{ Form::close() }}
                    <button class="btn btn-sm btn-default m-l-5" data-toggle="modal" data-target="#modal-mass-update">
                        <i class="fas fa-pencil-alt"></i> @trans('Editar em Massa')
                    </button>
                    @include('admin.customers.recipients.modals.mass_update')
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
                {data: 'name', name: 'name'},
                {data: 'address', name: 'address'},
                {data: 'contacts', name: 'contacts', orderable: false, searchable: false},
                {data: 'country', name: 'country', class: 'text-center'},
                {data: 'customer_id', name: 'customer_id'},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'mobile', name: 'mobile', visible: false},
                {data: 'email', name: 'email', visible: false},
                {data: 'zip_code', name: 'zip_code', visible: false},
                {data: 'vat', name: 'vat',  visible: false},
            ],
            order: [[8, "desc"]],
            ajax: {
                type: "POST",
                url: "{{ route('admin.recipients.datatable') }}",
                data: function (d) {
                    d.customer = $('select[name=dt_customer]').val();
                    d.country  = $('select[name=country]').val();
                    d.zip_code = $('select[name=zip_code]').val();
                    d.email    = $('select[name=email]').val();
                    d.phone    = $('select[name=phone]').val();
                    d.vat      = $('select[name=vat]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            },
        });


        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });

        //enable option to search with enter press
        Datatables.searchOnEnter(oTable);
    });


    $("select[name=dt_customer]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.customers.search') }}")
    });

    $("#modal-mass-update select[name=assign_assigned_customer_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.customers.search') }}")
    });
</script>
@stop