<?php

namespace App\Http\Controllers\Admin\Users;

use Html, Response, Cache, Setting;;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\UserAbsenceType;
use App\Models\User;

class AttributesDeclarationsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'attributes_declarations';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',attributes_declarations']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {
    }

    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request)
    {
    }


    public function setInfoEquipment(Request $request, $id)
    {
        $userId = $id;

        $title = "Características do equipamento";

        $formOptions = array('route' => array('admin.attributes-declarations.equipment.store'), 'method' => 'PUT', 'class' => 'modal-ajax-form');

        return view('admin.users.users.declarations.attributes.partials.infoEquipment', compact('userId', 'formOptions', 'title'))->render();
    }

    public function storeEquipment(Request $request)
    {
        $input = $request->all();
        if (isset($input['save_info']) && $input['save_info'] == 1) {
            Setting::set('info_equipment', $input['equipment_info']);
            Setting::set('price_equipment', $input['price']);
            Setting::save();
        }

        $price = $request['price'];


        if (!hasModule('human_resources')) {
            return Redirect::route('admin.users.index')->with('error', 'Módulo de recursos humanos não instalado. Contacte-nos para mais informação.');
        }

        $userdata['info'] = $input;
        if ($request->has('user_id')) {
            $userdata['user'] = $request->get('user_id');
        }

        return User::printEquipmentDoc($userdata);
    }
}
