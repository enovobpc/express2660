@section('title')
    Agenda
@stop

@section('content-header')
    Agenda
@stop

@section('breadcrumb')
    <li class="active">@trans('Agenda')</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-calendar" data-toggle="tab">@trans('Calendário')</a></li>
                    <li><a href="#tab-list" data-toggle="tab">@trans('Vista em Lista')</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-calendar">
                        <div class="row">
                            <div class="col-md-4">
                                <a href="{{ route('admin.calendar.events.create') }}" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modal-remote">
                                    <i class="fas fa-plus"></i> @trans('Novo')
                                </a>
                                <div class="height-15"></div>
                                <div id="calendar-list">
                                    @include('admin.calendar.side_list')
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="main-calendar"></div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab-list">
                        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-events">
                            <li>
                                <a href="{{ route('admin.calendar.events.create') }}" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modal-remote">
                                    <i class="fas fa-plus"></i> @trans('Novo')
                                </a>
                            </li>
                        </ul>
                        <div class="table-responsive">
                            <table id="datatable-events" class="table table-striped table-dashed table-hover table-condensed">
                                <thead>
                                <tr>
                                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                    <th></th>
                                    <th>@trans('Evento')</th>
                                    <th class="w-70px">@trans('Começa')</th>
                                    <th class="w-70px">@trans('Termina')</th>
                                    <th class="w-70px">@trans('Ações')</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        {{--<div class="selected-rows-action hide">
                            {{ Form::open(array('route' => 'admin.calendar.events.selected.destroy')) }}
                            <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados">
                                <i class="fas fa-trash-alt"></i> Apagar Selecionados
                            </button>
                            {{ Form::close() }}
                        </div>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.calendar.destroy')
@stop

@section('plugins')
    {{ HTML::script('vendor/admin-lte/plugins/fullcalendar/fullcalendar.js') }}
@stop

@section('scripts')
    {{ HTML::style('vendor/admin-lte/plugins/fullcalendar/fullcalendar.css') }}

    <script type="text/javascript">

        $(document).ready(function () {

            var oTable = $('#datatable-events').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'title', name: 'title'},
                    {data: 'start', name: 'start'},
                    {data: 'end', name: 'end'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                ajax: {
                    url: "{{ route('admin.calendar.events.datatable') }}",
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

        $(document).ready(function () {
            $('.main-calendar').fullCalendar({
                lang: 'pt',
                aspectRatio: 2,
                displayEventTime: false,
                year: "{{ $month }}",
                month: "{{ $year }}",
                events: "{{ route('admin.calendar.events.load', ['source' => 'calendar']) }}",
                dayClick: function (date) {
                    var date = moment(date).format("YYYY-MM-DD");
                    var url = "{{ route('admin.calendar.events.create') }}?date=" + date;
                    $('#modal-remote').modal('show').find('.modal-content').load(url);
                },
                eventClick: function (calEvent) {
                    var eventId = calEvent.id;
                    var url = "{{ route('admin.calendar.events.index') }}/" + calEvent.id + "/edit";
                    $('#modal-remote').modal('show').find('.modal-content').load(url);
                },
                viewRender: function (element) {
                    var month = moment(element.intervalStart).format("MM")
                    var year = moment(element.intervalStart).format("YYYY")
                    var url = "{{ route('admin.calendar.events.load', ['source' => 'list']) }}&year=" + year + "&month=" + month;

                    var headerUrl = "{{ route('admin.calendar.events.index') }}";
                    headerUrl = Url.updateParameter(headerUrl, 'year', year);
                    headerUrl = Url.updateParameter(headerUrl, 'month', month);
                    Url.change(headerUrl)

                    $('#calendar-list').html('<i class="fas fa-spin fa-circle-notch"></i> A carregar eventos...')
                    $('#calendar-list').load(url);
                },
            });
        });

        $(document).on('click', '[data-toggle="delete-event"]',function(e){
            e.preventDefault();
            var url = $(this).attr('href');

            $('#modal-destroy-event').find('form').attr('action', url);
            $('#modal-destroy-event').modal('show');
        })
    </script>
@stop