@section('title')
    Marcas e Modelos
@stop

@section('content-header')
Marcas e Modelos
@stop

@section('breadcrumb')
    <li class="active">Marcas e Modelos</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs tabs-filter">
                <li class="active"><a href="#tab-brands" data-toggle="tab">Marcas</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-brands">
                    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-brands">
                        <li>
                            <a href="{{ route('admin.brands.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                                <i class="fas fa-plus"></i> Novo
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.brands.sort') }}" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-remote">
                                <i class="fas fa-sort-amount-down"></i> Ordenar
                            </a>
                        </li>
                    </ul>
                    <div class="table-responsive">
                        <table id="datatable-brands" class="table table-striped table-dashed table-hover table-condensed">
                            <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th>Designação</th>
                                <th class="w-280px">Observações</th>
                                <th class="w-1">Modelos</th>
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
                        {{ Form::open(array('route' => 'admin.brands.selected.destroy')) }}
                        <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    var oTableBrands;
    $(document).ready(function () {
        oTableBrands = $('#datatable-brands').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'name', name: 'name'},
                {data: 'obs', name: 'obs'},
                {data: 'models', name: 'models', orderable: false, searchable: false},
                {data: 'is_active', name: 'is_active', class:'text-center'},
                {data: 'sort', name: 'sort', class: 'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[6, "asc"]],
            ajax: {
                url: "{{ route('admin.brands.datatable') }}",
                type: "POST",
                data: function (d) {
                    
                },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('#tab-brands .filter-datatable').on('change', function (e) {
            oTableBrands.draw();
            e.preventDefault();
        });
    });

</script>
@stop