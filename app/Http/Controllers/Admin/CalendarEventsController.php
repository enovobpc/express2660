<?php

namespace App\Http\Controllers\Admin;

use App\Models\CalendarEvent;
use App\Models\CalendarEventParticipant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use View, Response, Auth;

class CalendarEventsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'calendar_events';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',calendar_events']);

        //https://developers.google.com/calendar/create-events#php
    }

    /**
     * Colors
     * @var array
     */
    private $colors = [
        '#34495e' => '#34495e',
        //green
        '#1abc9c' => '#1abc9c',
        '#16a085' => '#16a085',
        '#2ecc71' => '#2ecc71',
        '#48AD01' => '#48AD01',

        //blue
        '#3498db' => '#3498db',
        '#2980b9' => '#2980b9',

        //purple
        '#9b59b6' => '#9b59b6',
        '#8e44ad' => '#8e44ad',

        //orange
        '#FEC606' => '#FEC606',
        '#f39c12' => '#f39c12',
        '#e67e22' => '#e67e22',
        '#d35400' => '#d35400',

        //red
        '#e74c3c' => '#e74c3c',
        '#c0392b' => '#c0392b',
        '#E3000E' => '#E3000E',

        //gray
        '#bdc3c7' => '#bdc3c7',
        '#7f8c8d' => '#7f8c8d',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $month = $request->month ? $request->month : date('n');
        $year  = $request->year ? $request->year : date('Y');

        $calendarEvents = CalendarEvent::filterEvents()
                            ->whereRaw('MONTH(start) = '.$month)
                            ->whereRaw('YEAR(start) = '.$year)
                            ->orderBy('start', 'asc')
                            ->get();

        return $this->setContent('admin.calendar.index', compact('calendarEvents', 'month', 'year'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        
        $action = 'Novo Evento';

        $calendarEvent = new CalendarEvent();
        $calendarEvent->start = $request->date ? $request->date : date('Y-m-d');

        $formOptions = array('route' => array('admin.calendar.events.store'), 'method' => 'POST', 'class' => 'form-event');

        $colors = $this->colors;

        $hours = listHours(5, 1, 6, 0, 23);
        $lastStartHour = lastHour();

        $endHour = new Carbon(date('Y-m-d').' '.$lastStartHour.':00');
        $endHour->addHour(1);
        $endHour = $endHour->format('H:i');

        $operators = User::filterAgencies()
            ->whereHas('roles.perms', function($query) {
                $query->whereName('calendar_events');
            })
            ->whereNotIn('id', [1, Auth::user()->id])
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        return view('admin.calendar.edit', compact('calendarEvent', 'action', 'formOptions', 'colors', 'hours', 'lastStartHour', 'endHour', 'operators'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        return $this->update($request, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        $action = 'Editar Evento';

        $calendarEvent = CalendarEvent::filterEvents()->findOrfail($id);

        if(config('app.source') != 'corridadotempo' && $calendarEvent->created_by != Auth::user()->id) {
            return view('admin.calendar.show', compact('calendarEvent'))->render();
        }

        $participants = $calendarEvent->participants->pluck('id')->toArray();

        $formOptions = array('route' => array('admin.calendar.events.update', $calendarEvent->id), 'method' => 'PUT', 'class' => 'form-event');

        $colors = $this->colors;

        $hours = listHours(5, 1, 6, 0, 23);
        $lastStartHour = lastHour();

        $endHour = new Carbon(date('Y-m-d').' '.$lastStartHour.':00');
        $endHour->addHour(1);
        $endHour = $endHour->format('H:i');

        $operators = User::filterAgencies()
            ->whereHas('roles.perms', function($query) {
                $query->whereName('calendar_events');
            })
            ->whereNotIn('id', [1, Auth::user()->id])
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        return view('admin.calendar.edit', compact('calendarEvent', 'action', 'formOptions', 'colors', 'hours', 'lastStartHour', 'endHour', 'operators', 'participants'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        CalendarEvent::flushCache(CalendarEvent::CACHE_TAG);

        $user = Auth::user();
        $input = $request->all();

        $input['alert_at']      = null;
        $input['user_id']       = null;
        $input['agencies']      = $user->agencies;
        $input['participants']  = empty($input['participants']) ? [] : $input['participants'];
        $input['type']          = $request->get('type');
        $input['customer_id']   = $request->get('customer_id');


        $input['start'] = $request->start.' ' . ($request->start_hour ? $request->start_hour : '00:00').':00';
        $input['end']   = $request->end.' ' . ($request->end_hour ? $request->end_hour : '23:59').':00';


        $calendarEvent = CalendarEvent::filterMyEvents()
                                ->findOrNew($id);

        if(!empty($input['repeat_period'])) {
            $oldHash = $calendarEvent->repeat_hash;
            $input['repeat_hash'] = str_random(8);
        }

        if(!empty($input['alert_period'])) {
            $start = new Carbon($input['start']);
            $input['alert_at'] = $start->subMinute($input['alert_period']);
        }

        if ($calendarEvent->validate($input)) {
            $calendarEvent->fill($input);
            $calendarEvent->save();

            $calendarEvent->participants()->sync($input['participants']);

            if(!empty($calendarEvent->alert_at)) {
                $calendarEvent->setNotification();
            }

            if(!empty($input['repeat_period'])) {
                $this->createRepetions($calendarEvent, $input, $oldHash);
            }

            return Redirect::back()->with('success', 'Evento gravado com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $calendarEvent->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {

        CalendarEvent::flushCache(CalendarEvent::CACHE_TAG);

        if($request->destroy_repetions) {
            $event = CalendarEvent::filterMyEvents()
                                ->find($id);

            $repetions = CalendarEvent::where('repeat_hash', $event->repeat_hash)->get();
            foreach ($repetions as $repetion) {
                $repetion->deleteNotification();
                $result = $repetion->delete();
            }

        } else {
            $event = CalendarEvent::filterMyEvents()
                            ->find($id);

            $event->deleteNotification();
            $result = $event->delete();
        }

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o evento.');
        }

        return Redirect::back()->with('success', 'Evento removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        CalendarEvent::flushCache(CalendarEvent::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = CalendarEvent::whereIn('id', $ids)
                        ->delete();
        
        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {

        $data = CalendarEvent::filterMyEvents()
                            ->select();

        return Datatables::of($data)
                        ->edit_column('title', function($row) {
                            return view('admin.calendar.datatables.title', compact('row'))->render();
                        })
                        ->edit_column('start', function($row) {
                            return @$row->start->format('Y-m-d');
                        })
                        ->edit_column('end', function($row) {
                            return $row->end ? @$row->end->format('Y-m-d') : null;
                        })
                        ->add_column('select', function($row) {
                            return view('admin.partials.datatables.select', compact('row'))->render();
                        })
                        ->add_column('actions', function($row) {
                            return view('admin.calendar.datatables.actions', compact('row'))->render();
                        })
                        ->make(true);
    }


    /**
     * Load calendar events
     * GET /admin/calendar/events/load
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function loadEvents(Request $request) {

        if($request->source == 'calendar') {
            return $this->loadEventsCalendar($request);
        } else {
            return $this->loadEventsSideList($request);
        }
    }

    /**
     * Load events to calendar
     * @param Request $request
     */
    public function loadEventsCalendar($request) {

        $bindings = array(
            'id',
            'title',
            'start',
            'end',
            'color'
        );

        $events = CalendarEvent::filterEvents()
                        ->whereBetween('start', [$request->start,  $request->end])
                        ->get($bindings);

        return Response::json($events);
    }

    /**
     * Load events to calendar
     * @param Request $request
     */
    public function loadEventsSideList($request) {

        $year  = $request->year ? $request->year : date('Y');
        $month = $request->month ? $request->month : date('m');

        $start = $year . '-' . $month . '-01 00:00:00';
        $end   = $year . '-' . $month . '-31 23:59';

        $bindings = array(
            'id',
            'title',
            'start',
            'end',
            'color',
            'created_by'
        );

        $calendarEvents = CalendarEvent::filterEvents()
                                ->whereBetween('start', [$start, $end])
                                ->get($bindings);

        return view('admin.calendar.side_list', compact('calendarEvents'))->render();
    }

    /**
     * Mark notification as concluded or not
     *
     * @return \Illuminate\Http\Response
     */
    public function conclude($id) {

        $calendarEvent = CalendarEvent::filterEvents()
            ->findOrFail($id);

        $calendarEvent->concluded = !$calendarEvent->concluded;
        $calendarEvent->save();

        return Response::json([
            'concluded' => $calendarEvent->concluded
        ]);
    }

    /**
     * Create calendar event repetions
     */
    public function createRepetions($calendarEvent, $input, $oldHash) {

        if(!empty($oldHash)) {
            $lastEvents = CalendarEvent::where('repeat_hash', $oldHash)
                                ->where('start', '>=', $calendarEvent->start)
                                ->get();

            foreach($lastEvents as $event) {
                $event->deleteNotification();
                $event->forceDelete();
            }
        }

        $startDates = CalendarEvent::createRepetition($calendarEvent->start, $input['repeat_period']);
        $endDates   = CalendarEvent::createRepetition($calendarEvent->end, $input['repeat_period']);

        if(!empty($input['alert_period'])) {
            $alertDates  = CalendarEvent::createRepetition($calendarEvent->alert_at, $input['repeat_period']);
        }

        unset($startDates[0]);

        foreach($startDates as $key => $date) {

            $calendarEvent = new CalendarEvent();
            $calendarEvent->fill($input);
            $calendarEvent->alert_at = @$alertDates[$key];
            $calendarEvent->start    = $date;
            $calendarEvent->end      = $endDates[$key];
            $calendarEvent->save();

            if(!empty($calendarEvent->alert_at)) {
                $calendarEvent->setNotification();
            }

        }
    }
}
