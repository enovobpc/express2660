<?php

namespace App\Http\Controllers\Admin\Users;

use App\Models\FileRepository;
use App\Models\FleetGest\UsageLog;
use Html, Croppa, Response, File, Redirect;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;

class AttendanceController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'users';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',users_absences']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($userId) {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $userId) {
        return $this->update($request, $userId, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($userId, $id) {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $userId, $id = null) {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($userId, $id) {

    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/features/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request, $userId) {

        $data = UsageLog::with('vehicle')
                ->where('operator_id', $userId)
                ->select();

        //filter date min
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {
            $dtMax = $dtMin;
            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }
            $data = $data->whereBetween('start_date', [$dtMin, $dtMax]);
        }

        //filter vehicle
        $value = $request->get('vehicle');
        if($request->has('vehicle')) {
            $data = $data->where('vehicle_id', $value);
        }

        $value = $request->get('type');
        if($request->has('type')){
            $data = $data->where('type', $value);
        }

        return Datatables::of($data)
            ->edit_column('vehicle', function($row) {
                return view('admin.fleet.usage_logs.datatables.vehicle', compact('row'))->render();
            })
            ->edit_column('start_date', function($row) {
                return $row->start_date ? $row->start_date->format('Y-m-d') : '';
            })
            ->add_column('start_hour', function($row) {
                return $row->start_date ? $row->start_date->format('H:i') : '';
            })
            ->edit_column('end_date', function($row) {
                return $row->end_date ? $row->end_date->format('H:i') : '';
            })
            ->edit_column('start_km', function($row) {
                return money($row->start_km, '', 0);
            })
            ->edit_column('end_km', function($row) {
                return money($row->end_km, '', 0);
            })
            ->add_column('total_km', function($row) {
                return money($row->end_km - $row->start_km, '', 0);
            })
            ->add_column('duration', function($row) {
                if($row->end_date) {
                    //$diff = $row->start_date->diff($row->end_date)->format('%H:%I:%S');
                    $diff = $row->start_date->diff($row->end_date)->format('%Hh %Im');
                    return $diff;
                }
            })
            ->edit_column('type', function($row) {
                return view('admin.fleet.usage_logs.datatables.type', compact('row'))->render(); 
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.fleet.usage_logs.datatables.actions', compact('row'))->render();
            })    
            ->make(true);
    }
}
