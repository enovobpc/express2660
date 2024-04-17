<?php

namespace App\Http\Controllers\Admin\Printer;

use App\Models\Equipment\Equipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Setting, File, Response, Auth, Mail;

class EquipmentsController extends \App\Http\Controllers\Admin\Controller {

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
        $this->middleware(['ability:' . config('permissions.role.admin') . ',equipments']);
    }


    /**
     * Print list of shipments
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inventory(Request $request, $groupResults = null) {

        if(!hasModule('equipments')) {
            return Redirect::route('admin.equipments.index')->with('error', 'Módulo de equipamentos não instalado. Contacte-nos para mais informação.');
        }

        $ids = $request->get('id');
        $groupResults    = $request->get('group') ? $request->get('group') : $groupResults;
        $categorySummary = $request->get('show-category', true);
        $showDetails     = $request->get('show-details', true);

        return Equipment::printInventory($ids, $groupResults, $categorySummary, $showDetails);
    }

        /**
     * Print list of shipments
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function labels(Request $request) {

        if(!hasModule('equipments')) {
            return Redirect::route('admin.equipments.index')->with('error', 'Módulo de equipamentos não instalado. Contacte-nos para mais informação.');
        }

        $ids = $request->get('id');
        if(!is_array($ids)) {
            $ids = [$ids];
        }
    
        return Equipment::printLabels($ids);
    }
}
