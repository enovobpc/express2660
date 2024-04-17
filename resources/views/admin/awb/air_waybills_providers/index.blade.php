@section('title')
    Fornecedores Aéreos
@stop

@section('content-header')
    Fornecedores Aéreos
@stop

@section('breadcrumb')
<li class="active">Cartas de Porte Aéreo</li>
<li class="active">Fornecedores Aéreos</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.air-waybills.providers.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable" class="table table-condensed table-striped table-dashed table-hover">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-1">NºIATA</th>
                                <th class="w-1">IATA</th>
                                <th class="w-1">ICAO</th>
                                <th>Designação</th>
                                <th>Morada</th>
                                <th>País</th>
                                <th class="w-20px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.air-waybills.providers.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {

        var oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'iata_no', name: 'iata_no'},
                {data: 'iata_code', name: 'iata_code'},
                {data: 'icao_code', name: 'icao_code'},
                {data: 'name', name: 'name'},
                {data: 'address', name: 'address'},
                {data: 'country', name: 'country'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.air-waybills.providers.datatable') }}",
                type: "POST",
                data: function (d) {},
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