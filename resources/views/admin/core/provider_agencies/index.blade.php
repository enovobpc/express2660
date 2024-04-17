@section('title')
    Agências Fornecedores
@stop

@section('content-header')
    Agências Fornecedores
@stop

@section('breadcrumb')
    <li class="active">Core</li>
    <li class="active">Agências Fornecedores</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('core.provider.agencies.create') }}"
                           class="btn btn-success btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote-lg">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                    <li>
                        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                            <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                        </button>
                    </li>
                    <li class="fltr-primary w-140px">
                        <strong>Rede</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-100px">
                            {{ Form::selectMultiple('provider', $providers, fltr_val(Request::all(), 'provider'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-170px">
                        <strong>Estado</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-120px">
                            {{ Form::select('status', ['' => 'Todos'] + $status, fltr_val(Request::all(), 'status'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-120px">
                        <strong>Ativo</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-80px">
                            {{ Form::select('is_active', ['' => '', '1'=>'Sim', '0'=>'Não'], fltr_val(Request::all(), 'is_active', 1), array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-130px">
                        <strong>Oculto</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-80px">
                            {{ Form::select('is_hidden', ['' => '', '1'=>'Sim', '0'=>'Não'], fltr_val(Request::all(), 'is_hidden', 0), array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>País</strong><br/>
                            <div class="w-160px">
                                {{ Form::select('country', [''=>''] + trans('country'), fltr_val(Request::all(), 'country', 'pt'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>Operador</strong><br/>
                            <div class="w-160px">
                                {{ Form::selectMultiple('operator2', [], fltr_val(Request::all(), 'operato2r'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
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
                                <th class="w-1">Rede</th>
                                <th class="w-1">Agência</th>
                                <th>Nome</th>
                                <th>Empresa</th>
                                <th>Contactos</th>
                                <th>Responsavel</th>
                                <th class="w-1">Estado</th>
                                <th class="w-1">Ativa</th>
                                <th class="w-65px">Registo</th>
                                <th class="w-65px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'core.provider.agencies.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar</button>
                    {{ Form::close() }}
                    <div class="btn-group btn-group-sm dropup m-l-5">
                        <button type="button" class="btn btn-default"
                                data-toggle="modal"
                                data-target="#modal-mass-update">
                            <i class="fas fa-fw fa-pencil-alt"></i> Editar em massa
                        </button>
                    </div>
                    @include('admin.core.provider_agencies.modals.mass_update')
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('styles')
    <style>
    tr.nointerest {
        background: #Eee !important;
        color: #bbb;
    }

    tr.nointerest a {
        color: #ccc;
    }

    tr.nointerest .providerlbl{
        opacity: 0.6;
    }

    tr.customer {
        background-color: #d5fcc2 !important;
    }
    </style>
@stop

@section('scripts')
<script type="text/javascript">
    var oTable;

    $(document).ready(function () {
        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'provider', name: 'provider', class: 'text-center'},
                {data: 'code', name: 'code', class: 'text-center'},
                {data: 'name', name: 'name'},
                {data: 'company', name: 'company'},
                {data: 'email', name: 'email'},
                {data: 'responsable', name: 'responsable'},
                {data: 'status', name: 'status', class: 'text-center'},
                {data: 'is_active', name: 'is_active', class: 'text-center'},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'phone', name: 'phone', visible: false},
                {data: 'responsable', name: 'responsable', visible: false},
            ],
            pageLength: 100,
            order: [[8, "asc"],[10, "desc"]],
            ajax: {
                url: "{{ route('core.provider.agencies.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.is_active = $('select[name=is_active]').val();
                    d.is_hidden = $('select[name=is_hidden]').val();
                    d.provider  = $('select[name=provider]').val();
                    d.status    = $('select[name=status]').val();
                    d.country   = $('select[name=country]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () {
                    Datatables.complete();
                    $('#datatable .customer').each(function(row){
                        $(this).closest('tr').addClass('customer')
                    })
                    $('#datatable .no-interest').each(function(row){
                        $(this).closest('tr').addClass('nointerest')
                    })
                },
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