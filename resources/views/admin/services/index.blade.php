@section('title')
    Serviços de Transporte
@stop

@section('content-header')
    Serviços de Transporte
@stop

@section('breadcrumb')
    <li class="active">Configurações</li>
    <li class="active">Serviços de Transporte</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.services.create') }}" class="btn btn-success btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote-xl">
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
                                        <a href="{{ route('admin.services.sort') }}" data-toggle="modal" data-target="#modal-remote">
                                            <i class="fas fa-fw fa-sort-amount-down"></i> Ordenar lista serviços
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="{{ route('admin.services.groups.index') }}" data-toggle="modal" data-target="#modal-remote">
                                            <i class="fas fa-fw fa-list"></i> Gerir grupos serviços
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.services.groups.sort') }}" data-toggle="modal" data-target="#modal-remote">
                                            <i class="fas fa-fw fa-sort-amount-down"></i> Ordenar grupos serviço
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="{{ route('admin.transport-types.index') }}" target="_blank">
                                            <i class="fas fa-fw fa-list"></i> Gerir tipos transporte
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li>
                        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                            <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                        </button>
                    </li>
                    <li class="fltr-primary w-180px">
                        <strong>Grupo</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-130px">
                            {{ Form::select('group', ['' => 'Todos'] + $servicesGroups, Request::has('group') ? Request::get('group') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-200px">
                        <strong>Categoria</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-130px">
                            {{ Form::select('features', ['' => 'Todos'] + $features, Request::has('features') ? Request::get('features') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>Regra Cálculo</strong><br/>
                            <div class="w-160px">
                                {{ Form::select('unity', ['' => 'Todos'] + $types, Request::has('unity') ? Request::get('unity') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        @if(count($agencies) > 1)
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>Agência</strong><br/>
                            <div class="w-160px">
                                {{ Form::selectMultiple('agency', $agencies, fltr_val(Request::all(), 'agency'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                        @endif
                        {{--<li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>Zona Faturação</strong><br/>
                            <div class="w-160px">
                                {{ Form::selectMultiple('zone', $billingZones->pluck('name', 'id')->toArray(),fltr_val(Request::all(), 'zone'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>--}}
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>Fornecedor</strong><br/>
                            <div class="w-160px">
                                {{ Form::selectMultiple('provider', $providers, fltr_val(Request::all(), 'provider'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
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
                                <th class="w-1">Cód.</th>
                                <th>Serviço</th>
                                <th class="w-120px">Tabela Preços</th>
                                <th class="w-80px">Peso/Volumes</th>
                                <th class="w-1">Transito</th>
                                <th class="w-80px">Horário</th>
                                {{--<th class="w-110px">Agências</th>--}}
                                <th class="w-110px">Características</th>
                                <th class="w-70px">Clientes</th>
                                <th class="w-1">Fornecedor</th>
                                <th class="w-1"><i class="fas fa-eye" data-toggle="tooltip" title="Serviço Visivel"></i></th>
                                <th class="w-1"><i class="fas fa-sort-amount-up" data-toggle="tooltip" title="Ordenação"></i></th>
                                <th class="w-1">Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.services.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
                    {{ Form::close() }}
                    <div class="btn-group btn-group-sm dropup m-l-5">
                        <button type="button" class="btn btn-default"
                                data-toggle="modal"
                                data-target="#modal-mass-update">
                            <i class="fas fa-fw fa-pencil-alt"></i> Editar em massa
                        </button>
                    </div>
                    <div class="btn-group btn-group-sm dropup m-l-5">
                        <button type="button" class="btn btn-default"
                                data-toggle="modal"
                                data-target="#modal-mass-replicate">
                            <i class="fas fa-fw fa-copy"></i> Duplicar
                        </button>
                    </div>
                    @include('admin.services.modals.mass_replicate')
                    @include('admin.services.modals.mass_update')
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
                {data: 'display_code', name: 'display_code', class: 'text-center'},
                {data: 'name', name: 'name'},
                /*{data: 'group', name: 'group'},*/
                {data: 'unity', name: 'unity'},
                {data: 'max_weight', name: 'max_weight'},
                {data: 'transit_time', name: 'transit_time', class: 'text-center'},
                {data: 'min_hour', name: 'min_hour'},
                /*{data: 'agencies', name: 'agencies', searchable: false},*/
                {data: 'features', name: 'features', orderable: false, searchable: false},
                {data: 'customers', name: 'customers', searchable: false},
                {data: 'provider_id', name: 'provider_id', class: 'text-center'},
                {data: 'custom_prices', name: 'custom_prices', class: 'text-center'},
                {data: 'sort', name: 'sort', class: 'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[12, "asc"]],
            ajax: {
                url: "{{ route('admin.services.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.agency   = $('select[name=agency]').val()
                    d.group    = $('select[name=group]').val()
                    d.unity    = $('select[name=unity]').val()
                    d.zone     = $('select[name=zone]').val()
                    d.provider = $('select[name=provider]').val()
                    d.feature  = $('select[name=feature]').val()
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                //error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });

    $('.select-all-weekdays').on('click', function(e){
        e.preventDefault();

        $('[name="pickup_weekdays[]"]').find('option').each(function(){
            $(this).prop('selected', 1);
        }).trigger('change');
    })

    $('.select-all-zones').on('click', function(e){
        e.preventDefault();

        if($('.row-zone:checked').length) {
            $('.row-zone').prop('checked', false);
        } else {
            $('.row-zone').prop('checked', true);
        }
    })

    $(document).on('change', '#modal-mass-update .row-zone', function(e){
        e.preventDefault();
        var length = $('#modal-mass-update .row-zone:checked').length
        $('#modal-mass-update .count-selected').html(length);
    });

    $('#modal-mass-update [name="filter_box"]').on('keyup', function(){
        var value = $(this).val().toLowerCase();

        var regex = new RegExp('\\b\\w*' + value + '\\w*\\b');
        $('[data-filter-text]').hide().filter(function () {
            return regex.test($(this).data('filter-text'))
        }).show()

        $('#modal-mass-update [data-label="zip_code"]').show();
        $('#modal-mass-update [data-label="country"]').show();

        if($('#modal-mass-update [data-unity="zip_code"]:visible').length == 0) {
            $('#modal-mass-update [data-label="zip_code"]').hide();
        }

        if($('#modal-mass-update [data-unity="country"]:visible').length == 0) {
            $('#modal-mass-update [data-label="country"]').hide();
        }
    })
</script>
@stop