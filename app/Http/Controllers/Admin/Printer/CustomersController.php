<?php

namespace App\Http\Controllers\Admin\Printer;

use App\Models\ServiceGroup;
use App\Models\ShipmentHistory;
use Setting, Auth, Mail;
use \Mpdf\Mpdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\ShippingExpense;
use App\Models\Customer;
use App\Models\Service;

class CustomersController extends \App\Http\Controllers\Admin\Controller
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
        $this->middleware(['ability:' . config('permissions.role.admin') . ',customers']);
    }

    /**
     * Print prices table
     * @param $customerId
     * @return mixed
     */
    public function pricesTable(Request $request, $customerId, $outputFormat = 'I')
    {

        try {
            ini_set("pcre.backtrack_limit", "5000000");

            $sendEmail    = $request->get('send_email', false);

            $email = validateNotificationEmails($request->email);
            $email = @$email['valid'];
            $title = !empty($request->title) ? $request->title : 'Tabela de Preços';

            $cover = $request->get('cover', false);
            $cover = $cover == '1' ? true : false;

            $presentation = $request->get('presentation', false);
            $presentation = $presentation == '1' ? true : false;

            $conditions = $request->get('conditions', false);
            $conditions = $conditions == '1' ? true : false;

            $customer = Customer::find($customerId);

            $locale = 'pt';
            $serviceIds = $request->service;
            $complementarServiceIds = $request->get('complementar_service', []);

            if (empty($serviceIds)) {
                return Redirect::back()->with('error', 'Tem de selecionar pelo menos um serviço a ser impresso.');
            }

            $services = Service::filterAgencies()
                ->showOnPricesTable()
                ->whereIn('id', $serviceIds)
                ->ordered()
                ->get();

            $servicesGroups = ServiceGroup::filterSource()
                ->ordered()
                ->get();

            $servicesGroupsList = ServiceGroup::filterSource()
                ->pluck('name', 'code')
                ->toArray();

            $pricesTableData = new \App\Http\Controllers\Admin\Customers\CustomersController();
            $pricesTableData = $pricesTableData->getPricesTableData($services, $customer, $servicesGroupsList);
            $rowsWeight = @$pricesTableData['rows'];
            $pricesTableData = $pricesTableData['prices'];

            $complementarServices = ShippingExpense::remember(config('cache.query_ttl'))
                ->cacheTags(ShippingExpense::CACHE_TAG)
                ->filterSource()
                ->whereIn('id', $complementarServiceIds)
                ->isCustomerCustomization()
                ->get();

            if ($presentation && Setting::get('prices_table_presentation_' . $locale)) {
                $presentation = Setting::get('prices_table_presentation_' . $locale);
            }

            //construct pdf
            ini_set("memory_limit", "-1");

            if (Setting::get('billing_customers_pdf_position') == 'v') {
                $mpdf = new Mpdf([
                    'format'        => 'A4',
                    'margin_left'   => 11,
                    'margin_right'  => 5,
                    'margin_top'    => 28,
                    'margin_bottom' => 20,
                    'margin_header' => 0,
                    'margin_footer' => 0,
                ]);

                $layout = 'pdf';
            } else {
                $mpdf = new Mpdf([
                    'format'        => 'A4-L',
                    'margin_left'   => 14,
                    'margin_right'  => 5,
                    'margin_top'    => 25,
                    'margin_bottom' => 15,
                    'margin_header' => 0,
                    'margin_footer' => 0,
                ]);
                $layout = 'pdf_h';
            }

            $mpdf->showImageErrors = true;
            $mpdf->SetAuthor("Paulo Costa");
            $mpdf->shrink_tables_to_fit = 0;

            $data = [
                'locale'                => $locale,
                'page'                  => 3,
                'cover'                 => $cover,
                'presentation'          => $presentation,
                'conditions'            => $conditions,
                'customer'              => $customer,
                'rowsWeight'            => $rowsWeight,
                'pricesTableData'       => $pricesTableData,
                'servicesGroups'        => $servicesGroups,
                'complementarServices'  => $complementarServices,
                'documentTitle'         => $title,
                'title'                 => $request->get('title'),
                'subtitle'              => $request->get('subtitle'),
                'request'               => $request,
                'date'                  => $request->get('date'),
                'documentSubtitle'      => ($customer->code && $customer->code != 'CFINAL' ? $customer->code . ' - ' : '') . $customer->name,
                'view'                  => 'admin.printer.customers.prices_table',

            ];


            if ($cover) {

                $mpdf->addPageByArray([
                    'format'        => 'A4',
                    'margin-left'   => 0,
                    'margin-right'  => 0,
                    'margin-top'    => 0,
                    'margin-bottom' => 0,
                    'margin-header' => 0,
                    'margin-footer' => 0,
                ]);


                $data['page'] = 1;
                $data['view'] = 'admin.printer.customers.prices_table';
                $mpdf->WriteHTML(view('admin.layouts.pdf_blank', $data)->render()); //write
            }


            if ($presentation) {
                $data['page'] = 2;
                $data['customPageHeader'] = true;
                //$mpdf->SetHTMLHeader(view('admin.layouts.pdf.header', $data)->render(), '', true);

                $mpdf->addPageByArray([
                    'format' => 'A4',
                    'margin-top'    => 20,
                    'margin-bottom' => 20,
                    'margin-header' => 0,
                    'margin-footer' => 0,
                ]);

                $mpdf->WriteHTML(view('admin.layouts.' . $layout, $data)->render()); //write
            }

            $data['page'] = 3;
            $data['customPageHeader'] = true;

            $mpdf->addPageByArray([
                'format' => 'A4',
                'margin-top'    => 20,
                'margin-bottom' => 20,
                'margin-header' => 0,
                'margin-footer' => 0,
            ]);
            //$mpdf->SetHTMLHeader(view('admin.layouts.pdf.header', $data)->render(), '', true);
            $mpdf->WriteHTML(view('admin.layouts.' . $layout, $data)->render()); //write

            if (Setting::get('prices_table_general_conditions') && $conditions) {
                $data['page'] = 4;
                $data['customPageHeader'] = true;

                $mpdf->addPageByArray([
                    'format' => 'A4',
                    'margin-top'    => 20,
                    'margin-bottom' => 20,
                    'margin-header' => 0,
                    'margin-footer' => 0,
                ]);
                $mpdf->WriteHTML(view('admin.layouts.' . $layout, $data)->render()); //write
            }

            if (Setting::get('open_print_dialog_docs')) {
                $mpdf->SetJS('this.print();');
            }

            //output pdf
            $mpdf->debug = true;

            //send email with password
            if ($sendEmail || $sendEmail == '1') {

                $attachment = $mpdf->Output('Tabela de Preços - ' . $customer->name . '.pdf', 'S');

                Mail::send('emails.prices_table', compact('title', 'customer'), function ($message) use ($title, $email, $attachment) {
                    $message->to($email);

                    $message = $message->subject($title);

                    //attach file
                    $filename = $title . '.pdf';
                    $message->attachData($attachment, $filename, ['mime' => 'application/pdf']);
                });
            }

            return $mpdf->Output('Tabela de Preços - ' . $customer->name . '.pdf', $outputFormat); //output to screen
            exit;
        } catch (\Exception $e) {
            dd($e->getMessage() . ' on file ' . $e->getFile() . ' line ' . $e->getLine());
            if (Auth::user() && Auth::user()->isAdmin()) {
                return Redirect::back()->with('error', $e->getMessage() . ' on file ' . $e->getFile() . ' line ' . $e->getLine());
            } else {
                return Redirect::back()->with('error', 'Erro interno ao gerar PDF.');
            }
        }
    }

    /**
     * Print debit direct authorization
     *
     * @param $proformaId
     * @param string $outputFormat
     * @return mixed
     */
    public function sepaAuthorization(Request $request, $customerId)
    {
        return Customer::printSepaAuthorization($customerId, 'I');
    }
}
