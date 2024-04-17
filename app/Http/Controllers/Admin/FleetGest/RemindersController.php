<?php

namespace App\Http\Controllers\Admin\FleetGest;

use App\Models\FleetGest\Reminder;
use App\Models\FleetGest\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use File, Response;

class RemindersController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_reminders';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',fleet_reminders']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $reminders = Reminder::getNotifications();

        $vehicles = Vehicle::remember(config('cache.query_ttl'))
            ->cacheTags(Vehicle::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->orderBy('name', 'desc')
            ->pluck('name', 'id')
            ->toArray();

        return $this->setContent('admin.fleet.reminders.index', compact('vehicles', 'reminders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $action = 'Criar lembrete';
        
        $reminder = new Reminder;
                
        $formOptions = array('route' => array('admin.fleet.reminders.store'), 'method' => 'POST');

        $vehicles = Vehicle::remember(config('cache.query_ttl'))
            ->cacheTags(Vehicle::CACHE_TAG)
            ->filterSource()
            ->filterAgencies()
            ->isActive()
            ->pluck('name', 'id')
            ->toArray();

        if($request->has('vehicle')) {
            $vehicles = [$request->vehicle => @$vehicles[$request->vehicle]];
        }

        $data = compact(
            'reminder',
            'vehicles',
            'action',
            'formOptions'
        );

        return view('admin.fleet.reminders.edit', $data)->render();
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function show($id) {
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $reminder = Reminder::with('vehicle')
                    ->filterSource()
                    ->findOrfail($id);

        $vehicles = [$reminder->vehicle_id => @$reminder->vehicle->name];

        $action = 'Editar Lembrete';

        $formOptions = array('route' => array('admin.fleet.reminders.update', $reminder->id), 'method' => 'PUT', 'files' => true);

        $data = compact(
            'reminder',
            'vehicles',
            'action',
            'formOptions'
        );

        return view('admin.fleet.reminders.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        Reminder::flushCache(Reminder::CACHE_TAG);

        $input = $request->all();

        $reminder = Reminder::filterSource()
                        ->findOrNew($id);

        if ($reminder->validate($input)) {
            $reminder->fill($input);
            $reminder->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $reminder->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Reminder::flushCache(Reminder::CACHE_TAG);

        $result = Reminder::filterSource()
                    ->find($id)
                    ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o lembrete.');
        }

        return Redirect::back()->with('success', 'Lembrete removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        Reminder::flushCache(Reminder::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $rows = Reminder::filterSource()
                    ->whereIn('id', $ids)
                    ->get();

        foreach($rows as $row) {
            $result = $row->delete();
        }
        
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

        $data = Reminder::filterSource()
                        ->with('vehicle')
                        ->select();

        //filter vehicle
        $value = $request->get('vehicle');
        if($request->has('vehicle')) {
            $data = $data->where('vehicle_id', $value);
        }

        //filter active
        $value = $request->get('active');
        if($request->has('active')) {

            if($value <= 1) {
                $data = $data->where('is_active', $value);
            } else {

                $reminders = Reminder::getNotifications();

                if($value == '2' && @$reminders['expireds']) {
                    $ids = array_column($reminders['expireds'], 'id');
                } elseif($value == '3' && @$reminders['warnings']) {
                    $ids = array_column($reminders['warnings'], 'id');
                }

                $data = $data->whereIn('id', $ids);
            }

        }

        return Datatables::of($data)
            ->edit_column('vehicle_id', function($row) {
                return view('admin.fleet.reminders.datatables.vehicle', compact('row'))->render();
            })
            ->edit_column('is_active', function($row) {
                return view('admin.fleet.reminders.datatables.is_active', compact('row'))->render();;
            })
            ->edit_column('date', function($row) {
                return view('admin.fleet.reminders.datatables.date', compact('row'))->render();;
            })
            ->edit_column('km', function($row) {
                return view('admin.fleet.reminders.datatables.km', compact('row'))->render();;
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.fleet.reminders.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Show modal for reset edition
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resetEdit(Request $request) {

        $reminders = Reminder::getNotifications($request->vehicle);

        $params = ['expireds' => true];
        $vehicle = Vehicle::getNotifications($request->vehicle, $params);

        if(@$vehicle->notifications) {

            foreach ($vehicle->notifications as $notification) {
                if(in_array($notification['type'], ['ipo', 'iuc', 'insurance', 'tachograph'])) {
                    if($notification['status'] == 'expired') {
                        $reminders['expireds'][] = [
                            "vehicle_id" => $request->vehicle,
                            "title"      => $notification['title'],
                            "days_alert" => $notification['days_alert'],
                            "date"       => $notification['date'],
                            "type"       => $notification['type'],
                        ];
                    } else {
                        $reminders['warnings'][] = [
                            "vehicle_id" => $request->vehicle,
                            "title"      => $notification['title'],
                            "days_alert" => $notification['days_alert'],
                            "date"       => $notification['date'],
                            "type"       => $notification['type'],
                        ];
                    }
                }
            }
        }

        return view('admin.fleet.reminders.reset', compact('reminders', 'vehicle'))->render();
    }


    /**
     * Show modal for reset edition
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resetStore(Request $request) {

        Reminder::flushCache(Reminder::CACHE_TAG);

        $input = $request->all();

        try {
            foreach ($input['action'] as $key => $action) {

                if (empty($action)) {
                    continue;
                }

                $reminder = Reminder::filterSource()->find($input['id'][$key]);
                if (empty($input['id'][$key]) && in_array($input['type'][$key], ['ipo', 'iuc', 'insurance', 'tachograph'])) {

                    $type = $input['type'][$key];
                    $vehicle = Vehicle::filterSource()->findOrFail($input['vehicle'][$key]);

                    if($type == 'ipo') {
                        $vehicle->ipo_date = $input['date'][$key];
                    }

                    if($type == 'iuc') {
                        $vehicle->iuc_date = $input['date'][$key];
                    }

                    if($type == 'insurance') {
                        $vehicle->insurance_date = $input['date'][$key];
                    }

                    $vehicle->save();

                } else if ($action == 'conclude') {
                    $reminder->is_active = 0;
                    $reminder->save();
                } else if ($action == 'reset') {
                    $reminder->is_active = 0;
                    $reminder->save();

                    $cloneReminder              = $reminder->replicate();
                    $cloneReminder->title       = @$input['title'][$key] ? $input['title'][$key] : $cloneReminder->title;
                    $cloneReminder->date        = $input['date'][$key];
                    $cloneReminder->km          = $input['km'][$key];
                    $cloneReminder->days_alert  = $input['days_alert'][$key];
                    $cloneReminder->km_alert    = $input['km_alert'][$key];
                    $cloneReminder->is_active   = 1;
                    $cloneReminder->save();
                }
            }
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Não foi possível gravar as alterações');
        }

        return Redirect::back()->with('success', 'Alterações gravadas com sucesso.');
    }

    /**
     * Set conclude confirm
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function conclude(Request $request, $id) {

        Reminder::flushCache(Reminder::CACHE_TAG);

        $reminder = Reminder::filterSource()->find($id);
        $reminder->is_active = 0;
        $result = $reminder->save();


        if($result) {
            return Redirect::back()->with('success', 'Alterações gravadas com sucesso.');
        }

        return Redirect::back()->with('error', 'Não foi possível gravar as alterações');
    }
}
