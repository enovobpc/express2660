@section('title')
    Gestão de Incidências
@stop

@section('content-header')
    Gestão de Incidências
@stop

@section('breadcrumb')
    <li class="active">Gestão de Incidências</li>
@stop


@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box no-border">
                <div class="box-body">
                    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                        {{--<li>
                            <a href="{{ route('admin.shipments.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-xl">
                                <i class="fas fa-plus"></i> Novo
                            </a>
                        </li>--}}
                        <li>
                            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                                <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                            </button>
                        </li>
                        <li class="fltr-primary w-150px">
                            <strong>Resolvido</strong><br class="visible-xs"/>
                            <div class="w-80px pull-left form-group-sm">
                                {{ Form::select('resolved', [''=>'Todos','1' => 'Sim', '0' => 'Não'], fltr_val(Request::all(), 'resolved', 0), ['class' => 'form-control input-sm filter-datatable select2']) }}
                            </div>
                        </li>
                        <li class="fltr-primary w-140px">
                            <strong>Solução</strong><br class="visible-xs"/>
                            <div class="w-80px pull-left form-group-sm">
                                {{ Form::select('solution', [''=>'Todos','1' => 'Sim', '0' => 'Não'], fltr_val(Request::all(), 'solution'), ['class' => 'form-control input-sm filter-datatable select2']) }}
                            </div>
                        </li>
                        <li class="fltr-primary w-200px">
                            <strong>Fornecedor</strong><br class="visible-xs"/>
                            <div class="w-120px pull-left form-group-sm">
                                {{ Form::selectMultiple('provider', $providers, fltr_val(Request::all(), 'provider'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                    </ul>
                    <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                        <ul class="list-inline pull-left m-b-5">
                            <li class="col-xs-12">
                                <strong>Data Incidência</strong><br/>
                                <div class="input-group input-group-sm w-220px">
                                    {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                                    <span class="input-group-addon">até</span>
                                    {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;" class="col-xs-12">
                                <strong>Serviço</strong><br/>
                                <div class="w-120px">
                                    {{ Form::selectMultiple('service', $services, fltr_val(Request::all(), 'service'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;" class="col-xs-6">
                                <strong>Motorista</strong><br/>
                                <div class="w-120px">
                                    {{ Form::selectMultiple('operator', array('not-assigned' => 'Sem operador') + $operators, fltr_val(Request::all(), 'operator'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;" class="col-xs-6">
                                <strong>Motivo Incidência</strong><br/>
                                <div class="w-170px">
                                    {{ Form::selectMultiple('incidence', $incidences, fltr_val(Request::all(), 'incidence'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;"  class="col-xs-6">
                                <strong>Cliente</strong><br/>
                                <div class="w-250px">
                                    {{ Form::select('dt_customer',  Request::has('dt_customer') ? [''=>'', Request::get('dt_customer') => Request::get('dt_customer-text')] : [''=>''], Request::has('dt_customer') ? Request::get('dt_customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
                                </div>
                            </li>
                        </ul>
                        <ul class="list-inline pull-left m-b-5">
                            @if(Setting::get('shipments_limit_search'))
                                <li style="width: 130px" class="m-t-20 m-l-5">
                                    <div class="checkbox">
                                        <label>
                                            {{ Form::checkbox('limit_search', 1, Request::has('limit_search') ? Request::get('limit_search') : true) }}
                                            Últimos {{ Setting::get('shipments_limit_search') }} meses
                                        </label>
                                    </div>
                                </li>
                            @endif
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped table-dashed table-hover table-condensed table-shipments">
                            <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-90px">TRK</th>
                                <th>Remetente</th>
                                <th>Destinatário</th>
                                <th class="w-80px">Serviço</th>
                                <th class="w-55px">Remessa</th>
                                <th class="w-1">Incidencia</th>
                                <th class="w-230px">Motivo</th>
                                <th class="w-230px">Última Solução</th>
                                <th class="w-1"><i class="fas fa-check-circle" data-toggle="tooltip" title="Resolvido?"></i></th>
                                <th class="w-65px">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="selected-rows-action hide">
                        <div>
                            <button class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-resolve-all">
                                <i class="far fa-check-square"></i> Resolver Todos
                            </button>
                            @include('admin.shipments.incidences.modals.mass_resolve')
                            <a href="{{ route('admin.export.incidences') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
                                <i class="fas fa-fw fa-file-excel"></i> Exportar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('styles')
    <style>
        .brdr-lft {
            border-left: 3px solid #555 !important;
        }
    </style>
@stop

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ getGoogleMapsApiKey() }}"></script>
    <script type="text/javascript">
        var oTable, oTableIncidences;

        $(document).ready(function () {

            oTable = oTableIncidences =
                $('#datatable').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'tracking_code', name: 'shipments.tracking_code', visible: false},
                    {data: 'id', name: 'id'},
                    {data: 'sender_name', name: 'shipments.sender_name'},
                    {data: 'recipient_name', name: 'shipments.recipient_name'},

                    {data: 'service_id', name: 'service_id', searchable: false},
                    {data: 'volumes', name: 'volumes', searchable: false},
                    {data: 'created_at', name: 'created_at', 'class': 'brdr-lft', searchable: false},

                    {data: 'reason', name: 'reason', orderable: false, searchable: false},
                    {data: 'solution', name: 'solution', orderable: false, searchable: false},
                    {data: 'resolved', name: 'resolved', class:'text-center', orderable: false, searchable: false},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    {data: 'reference', name: 'shipments.reference', visible: false},
                    {data: 'provider_tracking_code', name: 'shipments.provider_tracking_code', visible: false},
                ],
                order: [[7, "desc"]],
                ajax: {
                    url: "{{ route('admin.incidences.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.resolved           = $('select[name=resolved]').val();
                        d.solution           = $('select[name=solution]').val();
                        d.zone               = $('select[name=zone]').val();
                        d.status             = $('select[name=status]').val();
                        d.source             = $('select[name=source]').val();
                        d.service            = $('select[name=service]').val();
                        d.provider           = $('select[name=provider]').val();
                        d.incidence          = $('select[name=incidence]').val();
                        d.operator           = $('select[name=operator]').val();
                        d.customer           = $('select[name=dt_customer]').val();
                        d.date_min           = $('input[name=date_min]').val();
                        d.date_max           = $('input[name=date_max]').val();
                        d.date_unity         = $('select[name=date_unity]').val();
                        d.limit_search       = $('input[name=limit_search]:checked').length;
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            //enable option to search with enter press
            Datatables.searchOnEnter(oTable);

            $('.filter-datatable').on('change', function (e) {
                e.preventDefault();
                oTable.draw();
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

        $(document).on('change', '#modal-export-selected [name=provider]', function(){
            var exportUrl = Url.updateParameter($('[data-toggle="export-selected"]').attr('href'), 'provider', $(this).val());
            $('[data-toggle="export-selected"]').attr('href', exportUrl);
        })

        $("select[name=dt_customer]").select2({
            minimumInputLength: 2,
            allowClear: true,
            ajax: Init.select2Ajax("{{ route('admin.shipments.search.customer') }}")
        });

        $(document).on('change', '[name="resolved"]', function(e){
            e.preventDefault();
            var $form      = $(this).closest('form');
            var formAction = $form.attr('action');
            var resolved   = $(this).is(':checked');
            var historyId  = $form.find('[name="history_id"]').val();


            $form.find('.fas').show();
            $.post(formAction, {historyId:historyId, resolved: resolved}, function(data){
                Growl.success(data.feedback);
            }).fail(function(){
                Growl.error500();
            }).always(function(){
                $form.find('.fas').hide();
            });
        })
    </script>
@stop