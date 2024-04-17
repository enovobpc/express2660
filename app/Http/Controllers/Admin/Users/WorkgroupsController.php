<?php

namespace App\Http\Controllers\Admin\Users;

use App\Models\Route;
use App\Models\Service;
use App\Models\ShippingStatus;
use Html, Response, Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\UserWorkgroup;

class WorkgroupsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'workgroups';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',users_profissional_info']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $workgroup = new UserWorkgroup();

        $formOptions = array('route' => array('admin.users.workgroups.store'), 'method' => 'POST', 'class' => 'modal-ajax-form');

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->ordered()
            ->isCollection(false)
            ->pluck('name', 'id')
            ->toArray();

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->where('is_shipment', 1)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $pickupRoutes = Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->where('type', 'pickup')
            ->orWhere('type', null)
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $deliveryRoutes = Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->where('type', 'delivery')
            ->orWhere('type', null)
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $data = [
            'workgroup',
            'action',
            'formOptions',
            'services',
            'status',
            'pickupRoutes',
            'deliveryRoutes'
        ];

        return view('admin.users.users.workgroups.index', compact($data))->render();
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
    public function edit(Request $request, $id) {

        $action = 'Editar grupo de trabalho';

        $workgroup = UserWorkgroup::filterSource()->findOrNew($id);

        $formOptions = array('route' => array('admin.users.workgroups.update', $workgroup->id), 'method' => 'PUT', 'class' => 'modal-ajax-form');

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->ordered()
            ->isCollection(false)
            ->pluck('name', 'id')
            ->toArray();

        // $selectedServices = $workgroup->services ? array_map('intval', $workgroup->services) : [];

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->where('is_shipment', 1)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $pickupRoutes = Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->where('type', 'pickup')
            ->orWhere('type', null)
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $deliveryRoutes = Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->where('type', 'delivery')
            ->orWhere('type', null)
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $data = [
            'workgroup',
            'action',
            'formOptions',
            'services',
            'status',
            'pickupRoutes',
            'deliveryRoutes'
        ];

        return view('admin.users.users.workgroups.edit', compact($data))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        UserWorkgroup::flushCache(UserWorkgroup::CACHE_TAG);

        $input = $request->all();
        
        $workgroup = UserWorkgroup::filterSource()->findOrNew($id);
        $workgroup->values = $request->except(['name', '_method', '_token']);

        if ($workgroup->validate($input)) {
            $workgroup->fill($input);
            $workgroup->source = config('app.source');
            $workgroup->save();

            $row = $workgroup;

            if($request->ajax()) {
                return Response::json([
                    'result' => true,
                    'feedback' => 'Dados gravados com sucesso.',
                    'html' => view('admin.users.users.workgroups.datatables.name', compact('row'))->render()
                ]);
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        if($request->ajax()) {
            return Response::json([
                'result'   => false,
                'feedback' => $workgroup->errors()->first()
            ]);
        }

        return Redirect::back()->with('error', $workgroup->errors()->first());

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        UserWorkgroup::flushCache(UserWorkgroup::CACHE_TAG);

        $result = UserWorkgroup::filterSource()
                                ->whereId($id)
                                ->delete();

        if (!$result) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Ocorreu um erro ao tentar remover o tipo de cliente.'
            ]);
        }

        return Response::json([
            'result'   => true,
            'feedback' => 'Tipo de cliente removido com sucesso.'
        ]);
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        UserWorkgroup::flushCache(UserWorkgroup::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = UserWorkgroup::filterSource()
                            ->whereIn('id', $ids)
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

        $data = UserWorkgroup::filterSource()->select();

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.users.users.workgroups.datatables.name', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.users.users.workgroups.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }
}
