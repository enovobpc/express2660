@section('title')
    Tabelas Preço Gerais
@stop

@section('content-header')
    @trans('Tabelas Preço Gerais')
@stop

@section('breadcrumb')
    <li class="active">@trans('Configurações')</li>
    <li class="active">@trans('Tabelas Preço Gerais')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.prices-tables.create') }}" class="btn btn-success btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote-xs">
                            <i class="fas fa-plus"></i> @trans('Novo')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.prices-tables.mass.edit') }}" class="btn btn-default btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote">
                           @trans('Atualizar preços em massa')
                        </a>
                    </li>
                    @if(count($agencies) > 1)
                        <li class="fltr-primary w-240px">
                            <strong>@trans('Agência')</strong><br class="visible-xs"/>
                            <div class="pull-left form-group-sm w-170px">
                                {{ Form::selectMultiple('agency', $agencies, fltr_val(Request::all(), 'agency'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                    @endif
                </ul>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th>@trans('Tabela')</th>
                                <th class="w-20">@trans('Agências')</th>
                                <th class="w-80px">@trans('Clientes')</th>
                                <th class="w-1">@trans('Ativo')</th>
                                <th class="w-70px">@trans('Criado em')</th>
                                <th class="w-1">@trans('Ações')</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.prices-tables.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar')</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    var oTable

    $(document).ready(function () {
        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'name', name: 'name'},
                {data: 'agencies', name: 'agencies'},
                {data: 'customers', name: 'customers', class: 'text-center', searchable: false},
                {data: 'active', name: 'active',  class: 'text-center'},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.prices-tables.datatable') }}",
                type: "POST",
                data: function(d) {
                    d.agency = $('select[name=agency]').val();
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