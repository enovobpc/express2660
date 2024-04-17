<?php

namespace App\Http\Controllers\Admin;

use App\Models\ChangeLog;
use App\Models\LoginLog;
use App\Models\Provider;
use App\Models\ShippingStatus;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Auth;
use App\Models\Service;
use App\Models\Agency;
use Cache, Response;

class ChangesLogController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware(['ability:' . config('permissions.role.admin') . ',changes_log']);
    }

    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function index() {
//    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function create() {
//    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
//    public function store(Request $request) {
//    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($sourceClass, $sourceId) {

        $changes = ChangeLog::with('user', 'customer')
                    ->where('source', $sourceClass)
                    ->where('source_id', $sourceId)
                    ->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->get();

        $status = $services = $providers = [];
        if(in_array($sourceClass, ['Shipment', 'Customer'])) {
            $status    = ShippingStatus::withTrashed()->pluck('name', 'id')->toArray();
            $services  = Service::withTrashed()->pluck('name', 'id')->toArray();
            $providers = Provider::withTrashed()->pluck('name', 'id')->toArray();
        }

        return view('admin.partials.modals.change_log', compact('changes', 'status', 'services', 'providers'))->render();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showLogin($targetClass, $sourceId) {

        $logs = LoginLog::with('user', 'customer')
            ->where('source', config('app.source'))
            ->where('target', $targetClass)
            ->where('user_id', $sourceId)
            ->orderBy('created_at', 'desc')
            ->get();

        $logsArr = [];
        foreach ($logs as $log) {
            $logsArr[] = [
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                'user'       => $log->target == 'user' ? @$log->user->name : @$log->customer->name,
                'remember'   => $log->remember ? '<i class="fas fa-check-circle"></i>' : '',
                'ip'         => $log->ip
            ];
        }

        $logs = $logsArr;

        return view('admin.partials.modals.login_logs', compact('logs'))->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function edit($id) {
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function update(Request $request, $id) {
//    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function destroy($id) {
//    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
//    public function massDestroy(Request $request) {
//    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    /*public function datatable(Request $request) {

        $data = Service::filterAgencies()
                    ->with('provider')
                    ->select();

        $agencies = Agency::get(['id','code', 'name', 'color']);
        $agencies = $agencies->groupBy('id')->toArray();

        return Datatables::of($data)
                        ->edit_column('agencies', function($row) use ($agencies) {
                            return view('admin.partials.datatables.agencies', compact('row', 'agencies'))->render();
                        })
                        ->edit_column('name', function($row) {
                            return view('admin.services.datatables.name', compact('row'))->render();
                        })
                        ->edit_column('unity', function($row) {
                            return view('admin.services.datatables.unity', compact('row'))->render();
                        })
                        ->add_column('type', function($row) {
                            return view('admin.services.datatables.type', compact('row'))->render();
                        })
                        ->edit_column('provider_id', function($row) {
                            return @$row->provider->name;
                        })
                        ->add_column('select', function($row) {
                            return view('admin.partials.datatables.select', compact('row'))->render();
                        })
                        ->add_column('actions', function($row) {
                            return view('admin.services.datatables.actions', compact('row'))->render();
                        })
                        ->make(true);
    }*/

}
