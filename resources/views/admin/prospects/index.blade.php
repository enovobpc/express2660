@section('title')
    Potenciais Clientes
@stop

@section('content-header')
    Potenciais Clientes
@stop

@section('breadcrumb')
    <li class="active">@trans('Potenciais Clientes')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.prospects.create') }}" data-toggle="modal" data-target="#modal-remote" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> @trans('Novo')
                        </a>
                    </li>
                    <li>
                        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                            <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                        </button>
                    </li>

                    <li class="fltr-primary w-200px">
                        <strong>@trans('Estado')</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-130px">
                            {{ Form::select('status', ['' => __('Todos')] + trans('admin/prospects.status'), Request::has('status') ? Request::get('status') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>

                    @if(!Auth::user()->hasRole([config('permissions.role.seller')]) && $sellers)
                        <li class="fltr-primary w-200px">
                            <strong>@trans('Comercial')</strong><br class="visible-xs"/>
                            <div class="pull-left form-group-sm w-130px">
                                {{ Form::select('seller', ['' => __('Todos')] + $sellers, Request::has('seller') ? Request::get('seller') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                    @endif
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        @if(count($agencies) > 1)
                            <li style="margin-bottom: 5px;"  class="col-xs-6">
                                <strong>@trans('Agência')</strong><br/>
                                <div class="w-130px">
                                    {{ Form::select('agency', ['' => __('Todos')] + $agencies, Request::has('agency') ? Request::get('agency') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                        @endif
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Categoria')</strong><br/>
                            <div class="w-130px">
                                {{ Form::select('type', ['' => __('Todos')] + $types, Request::has('type') ? Request::get('type') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Transportadora')</strong><br/>
                            <div class="w-130px">
                                {{ Form::select('courier', ['' => __('Todos')] + trans('admin/prospects.couriers'), Request::has('courier') ? Request::get('courier') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th class="w-1"></th>
                                <th class="w-1"></th>
                                <th>@trans('Nome')</th>
                                <th class="w-80px">@trans('Contactos')</th>
                                <th class="w-20">@trans('Localidade')</th>
                                @if($sellers)
                                <th>@trans('Comercial')</th>
                                @endif
                                <th>@trans('Estado')</th>
                                <th class="w-1">@trans('País')</th>
                                <th class="w-65px">@trans('Criado em')</th>
                                <th class="w-65px">@trans('Ações')</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.prospects.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
                    {{ Form::close() }}
                    {{ Form::open(array('route' => 'admin.prospects.selected.activate')) }}
                    <button class="btn btn-sm btn-default m-l-10" data-action="confirm" data-confirm-title="Converter selecionados" data-confirm-class="btn-success" data-confirm-label="Converter" data-confirm="@trans('Pretende converter para cliente todos os registos selecionados?')"><i class="fas fa-user-plus"></i> @trans('Converter em Cliente')</button>
                    {{ Form::close() }}
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
                {data: 'photo', name: 'photo', orderable: false, searchable: false},
                {data: 'id', name: 'id', 'visible': false},
                {data: 'name', name: 'name'},
                {data: 'phone', name: 'phone'},
                {data: 'city', name: 'city'},
                @if($sellers)
                {data: 'seller', name: 'seller', orderable: false, searchable: false},
                @endif
                {data: 'business_status', name: 'business_status'},
                {data: 'country', name: 'country'},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'mobile', name: 'mobile', visible: false},
                {data: 'zip_code', name: 'zip_code', visible: false},
                {data: 'email', name: 'email', visible: false},
            ],
            order: [[10, "desc"]],
            ajax: {
                url: "{{ route('admin.prospects.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.agency = $('select[name=agency]').val()
                    d.type_id = $('select[name=type]').val();
                    d.active = $('select[name=active]').val();
                    d.seller = $('select[name=seller]').val();
                    d.status = $('select[name=status]').val();
                    d.payment_method = $('select[name=payment_method]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });

</script>
@stop