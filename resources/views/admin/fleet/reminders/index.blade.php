@section('title')
    Lembretes
@stop

@section('content-header')
    Lembretes
@stop

@section('breadcrumb')
    <li class="active">@trans('Gestão de Frota')</li>
    <li class="active">@trans('Lembretes')</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box no-border">
                <div class="box-body">
                    @if($reminders)
                    <div class="alert {{ @$reminders['expireds'] ? 'alert-danger' : 'alert-warning' }}">
                        <h4 class="m-b-0">
                            @if(@$reminders['warnings'])
                            @trans('Tem') {{ count($reminders['warnings']) }} @trans('lembretes prestes a expirar.')
                            @endif
                            @if(@$reminders['expireds'])
                                {{ count($reminders['expireds']) }} @trans('lembretes já ultrapassados.')
                            @endif
                            <small>
                                <a href="{{ route('admin.fleet.reminders.reset.edit') }}"
                                   data-toggle="modal"
                                   data-target="#modal-remote-lg"
                                   class="btn btn-xs btn-default" style="color: #333; text-decoration: none; margin-top: -10px; margin-bottom: -5px;">
                                   @trans('Reiniciar ou Concluir')
                                </a>
                            </small>
                        </h4>
                    </div>
                    @endif
                    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                        <li>
                            <a href="{{ route('admin.fleet.reminders.create') }}"
                               class="btn btn-success btn-sm"
                               data-toggle="modal"
                               data-target="#modal-remote">
                                <i class="fas fa-plus"></i> @trans('Novo')
                            </a>
                        </li>
                        <li>
                            <a href="#" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-print-validities">
                                <i class="fas fa-file-pdf"></i> @trans('Resumo validades')
                            </a>
                        </li>
                        <li class="fltr-primary w-180px">
                            <strong>Viatura</strong><br class="visible-xs"/>
                            <div class="pull-left form-group-sm w-125px">
                                {{ Form::select('vehicle', ['' => __('Todas')] + $vehicles, fltr_val(Request::all(), 'vehicle'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li class="fltr-primary w-260px">
                            <strong>@trans('Data')</strong><br class="visible-xs"/>
                            <div class="w-150px pull-left form-group-sm">
                                <div class="input-group input-group-sm w-220px">
                                    {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início'), 'autocomplete' => 'field-1']) }}
                                    <span class="input-group-addon">@trans('até')</span>
                                    {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim'), 'autocomplete' => 'field-1']) }}
                                </div>
                            </div>
                        </li>
                        <li class="fltr-primary w-140px">
                            <strong>@trans('Estado')</strong><br class="visible-xs"/>
                            <div class="pull-left form-group-sm w-85px">
                                {{ Form::select('active', ['' => __('Todos'), '2' => __('Expirados'), '3' => __('Prestes Expirar'), '1'=> __('Ativo'), '0' => __('Concluídos')], fltr_val(Request::all(), 'active', 1), array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                    </ul>
                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                            <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-20">@trans('Viatura')</th>
                                <th>@trans('Lembrete')</th>
                                <th class="w-80px">@trans('Data Limite')</th>
                                <th class="w-80px">@trans('Km Limite')</th>
                                <th class="w-65px">@trans('Aviso Dias')</th>
                                <th class="w-60px">@trans('Aviso Km')</th>
                                <th class="w-1">@trans('Ativo')</th>
                                <th class="w-80px">@trans('Ações')</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="selected-rows-action hide">
                        {{ Form::open(array('route' => 'admin.fleet.incidences.selected.destroy')) }}
                        <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
                        {{ Form::close() }}
                        <a href="{{ route('admin.fleet.printer.reminders') }}" data-toggle="datatable-action-url" target="_blank" class="btn btn-sm btn-default m-l-5">
                            <i class="fas fa-file-pdf"></i> @trans('Imprimir')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.fleet.vehicles.modals.validities')
@stop

@section('scripts')
    <script type="text/javascript">
        $('#modal-print-validities [type=submit]').on('click', function(e){
            e.preventDefault();
            $(this).closest('form').submit();
            $(this).closest('.modal').modal('hide');
            $(this).button('reset');
        })

        var oTable;
        $(document).ready(function () {

            oTable = $('#datatable').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'vehicle_id', name: 'vehicle_id'},
                    {data: 'title', name: 'title'},
                    {data: 'date', name: 'date', class:'text-center'},
                    {data: 'km', name: 'km', class:'text-center'},
                    {data: 'days_alert', name: 'days_alert', class:'text-center'},
                    {data: 'km_alert', name: 'km_alert', class:'text-center'},
                    {data: 'is_active', name: 'is_active', class:'text-center'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                ajax: {
                    url: "{{ route('admin.fleet.reminders.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.vehicle    = $('select[name=vehicle]').val();
                        d.date_min   = $('input[name=date_min]').val();
                        d.date_max   = $('input[name=date_max]').val();
                        d.active     = $('select[name=active]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('.filter-datatable').on('change', function (e) {
                oTable.draw();
                e.preventDefault();

                /*var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
                exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                $('[data-toggle="export-url"]').attr('href', exportUrl);*/
            });
        });

        //export selected
        $(document).on('change', '.row-select',function(){
            var queryString = '';
            $('input[name=row-select]:checked').each(function(i, selected){
                queryString+=  (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()
            });

            /*var exportUrl = Url.removeQueryString($('[data-toggle="export-selected"]').attr('href'));
            $('[data-toggle="export-selected"]').attr('href', exportUrl + '?' + queryString);*/
        });
    </script>
@stop