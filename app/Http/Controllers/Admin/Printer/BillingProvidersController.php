<?php

namespace App\Http\Controllers\Admin\Printer;

use App\Models\ProviderBilling;
use Illuminate\Http\Request;
use Setting, File, Response, Auth, Mail;

class BillingProvidersController extends \App\Http\Controllers\Admin\Controller {

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
        $this->middleware(['ability:' . config('permissions.role.admin') . ',billing_providers']);
    }


    /**
     * Print list of shipments
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printProviderSummary(Request $request, $providerId) {

        $year   = $request->has('year')  ? $request->year : date('Y');
        $month  = $request->has('month') ? $request->month : date('n');
        $period = $request->has('period') ? $request->period : '30d';

        return ProviderBilling::printShipments($providerId, $month, $year, 'pdf', null, $period);
    }
}
