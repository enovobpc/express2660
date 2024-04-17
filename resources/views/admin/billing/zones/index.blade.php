@section('title')
    Zonas de Faturação
@stop

@section('content-header')
    Zonas de Faturação
@stop

@section('breadcrumb')
<li class="active">Configurações</li>
<li class="active">Zonas de Faturação</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.billing.zones.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-lg">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                    <li class="fltr-primary w-200px">
                        <strong>Tipo</strong><br class="visible-xs"/>
                        <div class="w-140px pull-left form-group-sm">
                            {{ Form::select('unity', ['' => 'Todos'] + trans('admin/shipments.billing-zones-types'), Request::has('prices') ? Request::get('prices') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-1">Código</th>
                                <th class="w-200px">Descrição</th>
                                <th class="w-120px">Unidade</th>
                                <th class="w-1">País</th>
                                <th>País/Códigos Postais</th>
                                <th class="w-20px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.billing.zones.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@if(Request::get('zoneid'))
    <a href="{{ route('admin.billing.zones.edit', Request::get('zoneid'))}}" data-toggle="modal" data-target="#modal-remote-lg" class="zoneid">OLAAAA</a>
@endif
@stop

@section('scripts')
<script type="text/javascript">

    $(document).ready(function () {

        @if(Request::has('zoneid'))
        $('.zoneid').trigger('click');
        @endif

        var oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'code', name: 'code'},
                {data: 'name', name: 'name'},
                {data: 'unity', name: 'unity'},
                {data: 'country', name: 'country', orderable: false, searchable: false},
                {data: 'mapping', name: 'mapping'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.billing.zones.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.unity = $('select[name=unity]').val();
                },
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