<?php

namespace App\Http\Controllers\Admin\Printer;

use App\Models\Trip\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Setting, File, Response, Auth, Mail;

class TripsController extends \App\Http\Controllers\Admin\Controller {

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
        $this->middleware(['ability:' . config('permissions.role.admin') . ',trips']);
    }


    /**
     * Print list of shipments
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activityDeclaration(Request $request) {

        try {
            $data = $request->toArray();

            $data['start_date'] = $data['last_date'];
            $data['start_hour'] = $data['last_hour'];
            $data['end_date']   = $data['next_date'];
            $data['end_hour']   = $data['next_hour'];

            return Trip::printActivityDeclaration([$data]);
        } catch (\Exception $e) {
            dd($e->getMessage().' '. $e->getFile(). ' line '. $e->getLine());
            return Redirect::back()->with('error', 'Erro ao gerar documento. ' . $e->getMessage());
        }

    }
}
