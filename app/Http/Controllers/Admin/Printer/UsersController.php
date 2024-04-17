<?php

namespace App\Http\Controllers\Admin\Printer;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Setting, File, Response, Auth, Mail;

class UsersController extends \App\Http\Controllers\Admin\Controller
{

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
        $this->middleware(['ability:' . config('permissions.role.admin') . ',users']);
    }


    /**
     * Print list of shipments
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function validities(Request $request)
    {

        if (!hasModule('human_resources')) {
            return Redirect::route('admin.users.index')->with('error', 'Módulo de recursos humanos não instalado. Contacte-nos para mais informação.');
        }

        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        $otherData = [];
        if ($request->has('user')) {
            $otherData['customer'] = $request->get('user');
        }

        return User::printValidities($startDate, $endDate, $otherData);
    }

    /**
     * Print SIM Document
     *
     * @param $input
     */
    public function SIMCommunications(Request $request)
    {
        if (!hasModule('human_resources')) {
            return Redirect::route('admin.users.index')->with('error', 'Módulo de recursos humanos não instalado. Contacte-nos para mais informação.');
        }

        $userdata = [];
        if ($request->has('user')) {
            $userdata['customer'] = $request->get('user');
        }

        return User::printSIMCommunications($userdata);
    }

    /**
     * Print uniform Document
     *
     * @param $input
     */
    public function uniform(Request $request)
    {
        if (!hasModule('human_resources')) {
            return Redirect::route('admin.users.index')->with('error', 'Módulo de recursos humanos não instalado. Contacte-nos para mais informação.');
        }

        $userdata = [];
        if ($request->has('user')) {
            $userdata['user'] = $request->get('user');
        }

        return User::printUniformDoc($userdata);
    }

    /**
     * Print activity Document
     *
     * @param $input
     */
    public function activity(Request $request)
    {
        if (!hasModule('human_resources')) {
            return Redirect::route('admin.users.index')->with('error', 'Módulo de recursos humanos não instalado. Contacte-nos para mais informação.');
        }

        $userdata = [];
        if ($request->has('user')) {
            $userdata['user'] = $request->get('user');
        }

        return User::printInternationalActivityDoc($userdata);
    }
}
