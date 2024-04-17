<?php

namespace App\Http\Controllers\Admin\Printer;

use App\Models\ProductSale;
use App\Models\Shipment;
use App\Models\ShippingStatus;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use LynX39\LaraPdfMerger\PdfManage;
use Setting, File, Response, Auth, Mail;
use Illuminate\Http\Request;
use \Mpdf\Mpdf;
use App\Models\CustomerBilling;
use App\Models\CustomerCovenant;
use App\Models\Customer;
use App\Models\Billing;
use App\Models\Agency;

class BillingCustomersController extends \App\Http\Controllers\Admin\Controller {

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
        $this->middleware(['ability:' . config('permissions.role.admin') . ',customers']);
    }


    /**
     * Print list of shipments
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printCustomerSummary(Request $request, $customerId) {

        $year   = $request->has('year')  ? $request->year : date('Y');
        $month  = $request->has('month') ? $request->month : date('n');
        $period = $request->has('period') ? $request->period : '30d';

        CustomerBilling::printShipments($customerId, $month, $year, 'pdf', null, $period);
    }

    /**
     * Print billing summary of all customers
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function massPrintCustomerSummary(Request $request) {

        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 0);

        $year      = $request->has('year')  ? $request->year : date('Y');
        $month     = $request->has('month') ? $request->month : date('m');
        $period    = $request->has('period') ? $request->period : '30d';
        $customers = $this->getCustomersIds($year, $month, $period);

        try {
            $tempFiles = [];

            $basePath = public_path() . '/uploads/billing/';
            if(!File::exists($basePath)) {
                File::makeDirectory($basePath);
            }


            foreach ($customers as $customerId) {
                $file = CustomerBilling::printShipments($customerId, $month, $year, 'string', null, $period);
                $outputFile = $basePath . config('app.source') . '_' . $year . $month . '_' . $customerId . '.pdf';
                $tempFiles[] = $outputFile;
                File::put($outputFile, $file);
            }

            /**
             * Merge files
             */
            $pdfMerger = new PdfManage();
            foreach ($tempFiles as $filepath) {
                $pdfMerger->addPDF($filepath, 'all');
            }

            /**
             * Save merged file
             */
            $outputFile = '/uploads/billing/' . config('app.source') . '_' . $year . $month . '.pdf';
            $pdfMerger->merge('file', public_path() . $outputFile);

            /**
             * Destroy all temporary_files
             */
            File::delete($tempFiles);

            /**
             * Send email
             */
            $progress = '100';
            Mail::send('emails.billing.mass_summary', compact('input', 'outputFile', 'year', 'month', 'progress'), function ($message) {
                $message->to(Auth::user()->email)
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Download - Resumo Mensal de Faturação');
            });

            return Response::json([
                'result'   => true,
                'feedback' => 'Ficheiro mensal de faturação gerado com sucesso.'
            ]);

        } catch (\Exception $e) {

            $progress = 'error';
            Mail::send('emails.billing.mass_summary', compact('input', 'year', 'month', 'progress'), function ($message) {
                $message->to(Auth::user()->email)
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Download - Resumo Mensal de Faturação (Erro)');
            });

            File::delete($tempFiles);

            $message = 'Execução abortada: ' . $e->getMessage();
            if(Auth::user()->isAdmin()) {
                $message.= ' on file ' . $e->getFile(). ' Line '. $e->getLine();
            }

            return Response::json([
                'result'   => false,
                'feedback' => $message
            ]);
        }
    }

    /**
     * Print summary of billing
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printCustomersMonthValues(Request $request) {

        $year       = $request->has('year')  ? $request->year : date('Y');
        $month      = $request->has('month') ? $request->month : date('m');
        $period     = $request->has('period') ? $request->period : '30d';
        $periodName = Billing::getPeriodName($year, $month, $period);
        $customers  = $this->getCustomersIds($year, $month, $period);

        $mpdf = new Mpdf([
            'format'        => 'A4',
            'margin_left'   => 5,
            'margin_right'  => 5,
            'margin_top'    => 28,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->showImageErrors = true;
        $mpdf->SetAuthor("ENOVO");
        $mpdf->shrink_tables_to_fit = 0;

        $data = [
            'customers'         => $customers,
            'month'             => $month,
            'year'              => $year,
            'period'            => $period,
            'documentTitle'     => 'Listagem de Valores a Faturar',
            'documentSubtitle'  => $periodName,
            'view'              => 'admin.printer.billing.customers.month_values'
        ];

        $mpdf->WriteHTML(view('admin.layouts.pdf', $data)->render()); //write

        if(Setting::get('open_print_dialog_docs')) {
            $mpdf->SetJS('this.print();');
        }

        $outputFile = public_path().'/uploads/billing/'.config('app.source').'_'.$year.$month.'.pdf';
        $mpdf->Output($outputFile, 'I'); //save on server
    }

    /**
     * Assign selected resources to same customer
     * GET /admin/billing/shipments/selected/assign-customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massAssignCustomers(Request $request) {

        $ids = explode(',', $request->ids);

        $customer = Customer::findOrFail($request->assign_customer_id);

        foreach($ids as $id) {
            $shipment = Shipment::find($id);
            $shipment->customer_id = $customer->id;

            if($request->get('calc_prices')) {
                $shipment->updatePrices();
            }

            $shipment->customer_id = $customer->id;

            if($request->get('update_name') == 'sender') {
                $shipment->sender_name      = $customer->name;
                $shipment->sender_address   = $customer->address;
                $shipment->sender_zip_code  = $customer->zip_code;
                $shipment->sender_city      = $customer->city;
                $shipment->sender_country   = $customer->country;
                $shipment->sender_phone     = $customer->phone;
            } elseif($request->get('update_name') == 'recipient') {
                $shipment->recipient_name      = $customer->name;
                $shipment->recipient_address   = $customer->address;
                $shipment->recipient_zip_code  = $customer->zip_code;
                $shipment->recipient_city      = $customer->city;
                $shipment->recipient_country   = $customer->country;
                $shipment->recipient_phone     = $customer->phone;
            }

            $shipment->save();
        }

        return Redirect::back()->with('success', 'Registos selecionados associados com sucesso ao cliente '.$customer->code.'.');
    }

    /**
     * Return customers ids to selected period
     *
     * @param $periodFirstDay
     * @param $periodLastDay
     * @param $month
     * @param $year
     * @param $period
     * @param $myAgencies
     * @return mixed
     */
    public function getCustomersIds($year, $month, $period) {

        $myAgencies = Agency::filterSource()
            ->filterAgencies()
            ->pluck('id')
            ->toArray();

        $periodDates    = Billing::getPeriodDates($year, $month, $period);
        $periodFirstDay = $periodDates['first'];
        $periodLastDay  = $periodDates['last'];


        $covenantsCustomers = CustomerCovenant::leftJoin('customers', 'customers.id', '=', 'customers_covenants.customer_id')
            ->where('start_date', '<=', $periodFirstDay)
            ->where('end_date', '>=', $periodLastDay)
            ->where('type', 'fixed')
            ->whereIn('customers.agency_id', $myAgencies)
            ->pluck('customers.id')
            ->toArray();

        $customers = Customer::leftJoin('shipments', function($q) use($periodFirstDay, $periodLastDay) {
                $q->on('customers.id', '=', 'shipments.customer_id');
                $q->whereBetween('shipments.billing_date', [$periodFirstDay, $periodLastDay]);
                $q->whereNull('shipments.deleted_at');
                $q->where('shipments.status_id', '<>', ShippingStatus::CANCELED_ID);
            })
            ->with(['shipments' => function ($q) use($periodFirstDay, $periodLastDay)  {
                $q->whereBetween('billing_date', [$periodFirstDay, $periodLastDay]);
                $q->where('status_id', '<>', ShippingStatus::CANCELED_ID);
                $q->where('is_collection', 0);
            }])
            ->with(['productsBought' => function ($q) use ($periodFirstDay, $periodLastDay) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(ProductSale::CACHE_TAG);
                $q->whereBetween('date', [$periodFirstDay, $periodLastDay]);
            }])
            ->with(['covenants' => function ($q) use ($periodFirstDay, $periodLastDay) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(CustomerCovenant::CACHE_TAG);
                $q->filterBetweenDates($periodFirstDay, $periodLastDay);
            }])
            ->with(['billing' => function ($q) use ($year, $month, $period) {
                $q->where('year', $year);
                $q->where('month', $month);
                $q->where('period', $period);
            }])
            ->where(function($q) use($periodFirstDay, $periodLastDay, $covenantsCustomers) {
                $q->whereBetween('billing_date', [$periodFirstDay, $periodLastDay]);
                $q->orWhereIn('customers.id', $covenantsCustomers);
            })
            ->filterSeller()
            ->whereIn('customers.agency_id', $myAgencies)
            ->groupBy('customers.name')
            ->orderBy('code', 'asc')
            ->pluck('customers.id')
            ->toArray();

        return $customers;
    }
}
