@section('title')
Timeline
@stop

@section('content-header')
    Timeline
@stop

@section('breadcrumb')
    <li class="active">Timeline</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-calendar" data-toggle="tab">Vista Geral</a></li>
                    {{--<li><a href="#tab-map" data-toggle="tab">Exportações</a></li>--}}
                    {{--<li><a href="#tab-map" data-toggle="tab">Importações</a></li>--}}
                    {{--<li><a href="#tab-map" data-toggle="tab">Nacional</a></li>--}}
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-calendar">
                        @include('admin.timeline.partials.calendar')
                    </div>
                    {{--<div class="tab-pane" id="tab-map">
                        @include('admin.cargo_planning.partials.country')
                    </div>--}}
                </div>
            </div>
        </div>
    </div>
@stop

@section('styles')
    {{ HTML::style('vendor/fullcalendar-scheduler-5.0.1/lib/main.min.css') }}
    <style>
        .fc-header-toolbar {
            margin: 0 !important;
            padding: 0 0 5px 0;
        }

        .fc-event {
            margin-bottom: 5px;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 2px;
            background: #e2e2e2;
            cursor: pointer;
        }

        .fc-event:hover {
            background: #ccc;
        }

        .fc-h-event .fc-event-main {
            color: #333 !important;
            font-weight: normal;
            font-size: 13px;
        }

        .event-type-actions {
            display: none;
            position: absolute;
            right: 0;
            background: rgba(0,0,0,0.7);
            margin: -6px -11px;
            height: 28px;
            padding: 6px 10px 5px;
            border-radius: 0 3px 3px 0;
        }

        .fc-event:hover .event-type-actions {
            display: block;
        }

        .fc-day-mon {
            border-right: 2px solid #000 !important;
        }


        .fc-theme-standard th.fc-timeline-slot-label:nth-child(5n+5) {
            border-right: 2px solid #333 !important;
        }
        .fc-theme-standard .fc-timeline-header-row:first-child th.fc-timeline-slot-label {
           /* background: red;*/
            border-right: 2px solid #333 !important;
        }

        .fc-theme-standard td:nth-child(5n+5) {
            border-right: 2px solid #333 !important;
        }

    </style>
@stop

@section('plugins')
    {{ HTML::script('/vendor/fullcalendar-scheduler-5.0.1/lib/main.min.js') }}
    {{ HTML::script('/vendor/fullcalendar-scheduler-5.0.1/lib/locales/pt.js') }}
@stop

@section('scripts')

    <script type="text/javascript">
        var calendar;
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            calendar = new FullCalendar.Calendar(calendarEl, {
                schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
                timeZone: 'UTC',
                locale: 'pt',
                slotDuration: '04:00:00',
                slotMinTime: '06:00:00',
                initialView: 'resourceTimelineMonth',
                aspectRatio: 1.5,
                headerToolbar: {
                    left: 'prev,today,next',
                    center: 'title',
                    right: 'resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth'
                },
                editable: true,
                resourceAreaHeaderContent: 'Viaturas',
                resourceAreaWidth: '15%',
                resources: {!! $calendarResources !!},
                events: '{!! route('admin.timeline.events', Request::all()) !!}',
                initialDate: '{!! $startDate !!}',
                nowIndicator: true,
                scrollDate: '{{ date('Y-m-d') }}',

                resourcesSet: function(resources) {
                    resources.forEach(function(item){
                        if(item.id) {
                            var html = item.extendedProps.html;
                            $('[data-resource-id=' + item.id + ']').find('.fc-datagrid-cell-main').after(html);
                        }
                    })
                },

                datesSet: function(dateInfo) {
                    var start = dateInfo.start.toISOString();
                    var end   = dateInfo.end.toISOString();

                    var url = Url.updateParameter(Url.current(), 'start', start);
                    url = Url.updateParameter(url, 'end', end);
                    Url.change(url);

                    $('[name="start"]').val(start);
                    $('[name="end"]').val(end);
                },

                eventsSet: function(events) {

                   $('.fc-event-title').each(function(item) {
                       $(this).html($(this).text());
                   })

                /*    $('.fc-datagrid-cell-main').each(function(item) {
                        $(this).html($(this).text());
                    })*/

                },

                eventMouseEnter: function (event) {
                    //console.log(event.event.extendedProps)
                },

                //ao clicar evento
                eventClick: function(info) {
                    var targetType = info.event.extendedProps.target;
                    var targetUrl  = info.event.extendedProps.target_url;
                    var eventId    = info.event._def.id; //info.event.id;
                    if(typeof eventId == 'undefined' || eventId == '') {
                        var eventId = info.event.id;
                    }

                    var eventEditUrl = '{{ route("admin.timeline.index") }}/' +eventId+ '/edit';

                    if(targetType == 'shipment') {
                        $('#modal-remote-xl').modal('show').find('.modal-content').load(targetUrl);
                    } else {
                        $('#modal-remote').modal('show').find('.modal-content').load(eventEditUrl);
                    }
                },

                //ao movimentar evento
                eventDrop: function (info) {

                    var eventId    = info.event.id; //info.event.id;
                    var actionUrl  = info.event.extendedProps.target_url;
                    var updateUrl  = info.event.extendedProps.update_url;
                    var startDate  = info.event.start.toISOString();
                    var endDate    = info.event.end.toISOString();
                    var resources  = info.event.getResources()
                    var resourceId = resources[0].id;

                    var data = {
                        source: 'timeline',
                        event_id:   eventId,
                        resource:   resourceId,
                        start_date: startDate,
                        end_date:   endDate,
                    }

                    $.ajax({
                        type: 'PUT',
                        url: updateUrl,
                        data: data,
                        success: function(data) {

                        }
                    }).fail(function () {
                        Growl.error500('Não foi possível gravar o evento.');
                    })
                },

                //ao redimensionar o evento
                eventResize: function (info) {

                    var eventId    = info.event.id; //info.event.id;
                    var actionUrl  = info.event.extendedProps.target_url;
                    var updateUrl  = info.event.extendedProps.update_url;
                    var startDate  = info.event.start.toISOString();
                    var endDate    = info.event.end.toISOString();
                    var resources  = info.event.getResources()
                    var resourceId = resources[0].id;

                    if(eventId) {
                        Growl.warning('Não pode alterar a data e hora de carga e descarga. Para o fazer, deve editar a ordem de carga.')
                    }

                    /* var data = {
                        source:     'timeline',
                        event_id:   eventId,
                        resource:   resourceId,
                        start_date: startDate,
                        end_date:   endDate,
                    }

                    $.ajax({
                        type: 'PUT',
                        url: updateUrl,
                        data: data,
                        success: function(data) {

                        }
                    }).fail(function () {
                        Growl.error500('Não foi possível gravar o evento.');
                    }) */
                },

                //apos movimentar evento externo
                eventReceive: function (info) {

                    var startDate  = info.event.start.toISOString();
                    var typeId     = info.event.extendedProps.typeId;
                    var resources  = info.event.getResources()
                    var resourceId = resources[0].id;

                    var data = {
                        type_id:    typeId,
                        start_date: startDate,
                        resource:   resourceId
                    }

                    $.post('{{ route('admin.timeline.store') }}', data, function (response) {
                        info.event.setProp('id', response.id);
                        info.event.setExtendedProp('target_url', response.target_url)
                    }).fail(function () {
                        Growl.error500('Não foi possível gravar o evento.');
                    })
                },
            });

            calendar.render();
        });

        document.addEventListener('DOMContentLoaded', function() {
            var Draggable = FullCalendar.Draggable;
            var containerEl = document.getElementById('external-events');

            new Draggable(containerEl, {
                itemSelector: '.fc-event',
                eventData: function(eventEl) {
                    return {
                        typeId: eventEl.getAttribute('id'),
                        title:  eventEl.innerText,
                        color:  eventEl.getAttribute('color'),
                        target: 'custom'
                    };
                }
            });

            calendar.render();
        });

        //change filter
        $('.datatable-filters select, .datatable-filters input, .datatable-filters-extended select, .datatable-filters-extended input').on('change', function() {
            calendar.render();
        })
    </script>
@stop