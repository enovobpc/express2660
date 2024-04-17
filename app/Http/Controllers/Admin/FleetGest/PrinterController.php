<?php

namespace App\Http\Controllers\Admin\FleetGest;

use App\Models\CacheSetting;
use App\Models\FleetGest\Cost;
use App\Models\FleetGest\Reminder;
use App\Models\FleetGest\Vehicle;
use App\Models\PurchaseInvoice;
use App\Models\Shipment;
use App\Models\Statistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Mpdf\Mpdf;
use Setting, File, Response, Auth, Mail, DB;

class PrinterController extends \App\Http\Controllers\Admin\Controller {

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
    public function validities(Request $request) {

        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        $otherData = [];
        if($request->has('vehicle')) {
            $otherData['vehicle'] = $request->get('vehicle');
        }

        if($request->has('expireds')) {
            $otherData['expireds'] = $request->get('expireds');
        }

        return Vehicle::printValidities($startDate, $endDate, $otherData);
    }

    /**
     * Print list of shipments
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function costsBalance(Request $request) {

        try {
            $ids = $request->id;

            $startDate = $request->get('start_date');
            $endDate   = $request->get('end_date');

            $vehicles = Statistic::getfleetBalanceCosts($startDate, $endDate, $ids);

            $mpdf = new Mpdf([
                'format'        => 'A4-L',
                'margin_left'   => 14,
                'margin_right'  => 5,
                'margin_top'    => 25,
                'margin_bottom' => 15,
                'margin_header' => 0,
                'margin_footer' => 0,
            ]);
            $mpdf->showImageErrors = true;
            $mpdf->SetAuthor("ENOVO");
            $mpdf->shrink_tables_to_fit = 0;

            $data = [
                'vehicles'          => $vehicles,
                'startDate'         => $startDate,
                'endDate'           => $endDate,
                'documentTitle'     => 'Balanço de Custos',
                'documentSubtitle'  => 'Resumo de ' . $startDate . ' até ' . $endDate,
                'view'              => 'admin.fleet.printer.costs_balance'
            ];

            $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

            if(Setting::get('open_print_dialog_docs')) {
                $mpdf->SetJS('this.print();');
            }

            $mpdf->debug = true;
            return $mpdf->Output('Balanço de Custos.pdf', 'I'); //output to screen

            exit;

        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar ficheiro Excel. ' . $e->getMessage());
        }
    }

    /**
     * Print reminders summary
     *
     * @param type $reminderId
     * @return type
     */
    public function summary(Request $request)
    {
        try {

            if ($request->id) {
                $ids = $request->id;
            } else {

                $data = Reminder::select(['id']);

                //limit search
                $value = $request->limit_search;
                if ($request->has('limit_search') && !empty($value)) {
                    $minId = (int) CacheSetting::get('shipments_limit_search');
                    if ($minId) {
                        $data = $data->where('id', '>=', $minId);
                    }
                }

                $ids = $data->pluck('id')->toArray();
            }

            return Reminder::printReminders($ids, 'I');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }
}
