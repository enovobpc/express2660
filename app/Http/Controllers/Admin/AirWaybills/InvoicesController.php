<?php

namespace App\Http\Controllers\Admin\AirWaybills;


use App\Models\AirWaybill\Waybill;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceGateway\Base;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Html, Auth, Response, File, DB, Setting;

class InvoicesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'air-waybills-invoices';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',air-waybills-invoices']);
    }

    /**
     * Show modal to billing selected resources.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massBilling(Request $request) {

        $ids = $request->id;

        $waybills = Waybill::with('customer')
            ->filterSource()
            ->whereNull('invoice_id')
            ->whereIn('id', $ids)
            ->get();

        $uniqueCustomers = $waybills;
        $totalCustomers = $uniqueCustomers->groupBy('customer_id')->count();

        $customer = @$waybills->first()->customer;

        $docDate = date('Y-m-d');
        $docLimitDate = new Carbon('last day of next month');
        $docLimitDate = $docLimitDate->format('Y-m-d');

        $taxes = [
            '23' => '23%',
            '0' => '0%'
        ];

        $apiKeys = Invoice::getApiKeys();

        return view('admin.awb.air_waybills.edit_invoice', compact('waybills', 'customer', 'totalCustomers', 'apiKeys', 'docDate', 'docLimitDate', 'taxes'))->render();
    }

    /**
     * Create invoice from express services
     * GET /admin/express-services/invoices/create
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createInvoice(Request $request) {

        $input = $request->all();
        $input['draft'] = $request->get('draft', false);
        $input['send_email'] = $request->get('send_email', false);

        $ids = $input['ids'];

        $customer = Customer::findOrFail($input['customer_id']);

        try {
            $header = $this->prepareDocumentHeader($input, $customer);
            $lines  = $this->prepareDocumentLines($input);

            //store customer if country != pt
            if($customer->country != 'pt') {
                $class = Base::getNamespaceTo('Customer');
                $customerKeyinvoice = new $class();
                $customerKeyinvoice->insertOrUpdateCustomer(
                    $customer->vat,
                    $customer->code,
                    $customer->billing_name,
                    $customer->billing_address,
                    $customer->billing_zip_code,
                    $customer->billing_city,
                    $customer->billing_phone,
                    null,
                    $customer->billing_email,
                    $customer->obs,
                    $customer->billing_country,
                    $customer->payment_method,
                    $customer
                );
            }

            $invoice = new Invoice($input['api_key']);
            $documentId = $invoice->createDraft($input['type'], $header, $lines);

            $isDraft = 1;
            if(!$input['draft']) {
                $documentId = $invoice->convertDraftToDoc($documentId, $input['type']);
                $isDraft = 0;
            }

            Waybill::filterSource()
                ->whereIn('id', $ids)
                ->update([
                    'api_key'       => $input['api_key'],
                    'invoice_type'  => $input['type'],
                    'invoice_id'    => $documentId,
                    'invoice_draft' => $isDraft
                ]);

        } catch (\Exception $e) {
            return [
                'result'   => false,
                'feedback' => $e->getMessage()
            ];
        }

        /**
         * Send email
         */
        if($input['send_email']) {

            $data = [
                'invoice_id' => $documentId,
                'email'      => $input['email']
            ];

            $result = Waybill::sendBillingEmail($data);

            if(!$result) {
                return [
                    'result' => false,
                    'feedback' => 'Dados gravados com sucesso. Não foi possível enviar o e-mail ao cliente'
                ];
            }
        }

        return [
            'result'   => true,
            'feedback' => 'Dados gravados com sucesso.',
        ];
    }

    /**
     * Convert a Draft into Invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function convertFromDraft(Request $request, $id) {

        $waybill = Waybill::filterSource()->whereId($id)->first();

        if(empty($waybill->invoice_id) && empty($waybill->invoice_type) && $waybill->invoice_draft) {
            return Redirect::back()->with('error', 'Não foi emitido nenhum rascunho de fatura para esta carta de porte.');
        }

        $invoice = new Invoice($waybill->api_key);
        $invoiceId = $invoice->convertDraftToDoc($waybill->invoice_id, $waybill->invoice_type);

        if($invoiceId) {
            $waybill->invoice_id = $invoiceId;
            $waybill->invoice_draft = 0;
            $waybill->save();

            return Redirect::back()->with('success', 'Rascunho convertido com sucesso.');
        }

        return Redirect::back()->with('error', 'Ocorreu um erro ao tentar conveter o rascunho.');
    }

    /**
     * Download billing invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadInvoice(Request $request, $id) {

        $waybill = Waybill::filterSource()->whereId($id)->first();

        if(empty($waybill->invoice_id) && empty($waybill->invoice_type) && $waybill->invoice_draft) {
            return Redirect::back()->with('error', 'Não foi emitida nenhuma fatura para este serviço.');
        }

        $invoice = new Invoice($waybill->api_key);
        $doc = $invoice->getDocumentPdf($waybill->invoice_id, $waybill->invoice_type);

        $data = base64_decode($doc);
        header('Content-Type: application/pdf');
        echo $data;
    }

    /**
     * Destroy billing invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyInvoice(Request $request, $id) {

        $waybill = Waybill::filterSource()->whereId($id)->first();

        if(empty($waybill->invoice_id) && empty($waybill->invoice_type) && $waybill->invoice_draft) {
            return Redirect::back()->with('error', 'Não foi emitido nenhum rascunho de fatura para este serviço.');
        }

        $invoice = new Invoice($waybill->api_key);
        $documentId = $waybill->invoice_id;

        try {
            if ($waybill->invoice_draft) {
                $result = $invoice->destroyDraft($waybill->invoice_id, $waybill->invoice_type);
            } else {
                $data = [
                    'doc_serie'     => 4,
                    'credit_serie'  => 7,
                    'credit_date'   => date('Y-m-d'),
                    'credit_reason' => 'Erro na emissão da fatura.'
                ];

                $result = $invoice->destroyDocument($waybill->invoice_id, $waybill->invoice_type, $data);
            }
        } catch(\Exception $e) {
            $result = true;
        }

        if($result) {

            Waybill::where('invoice_id', $documentId)
                ->update([
                    'api_key'       => null,
                    'invoice_type'  => null,
                    'invoice_id'    => null,
                    'invoice_draft' => 0
                ]);

            return Redirect::back()->with('success', 'Documento de venda anulado com sucesso.');
        }

        return Redirect::back()->with('error', 'Ocorreu um erro ao anular o documento de venda.');
    }

    /**
     * Show modal to edit billing email
     * @param Request $request
     * @param $id
     */
    public function editBillingEmail($id) {

        $waybill = Waybill::filterSource()
            ->whereId($id)
            ->first();

        return view('admin.awb.air_waybills.modals.email', compact('waybill'))->render();
    }

    /**
     * Send billing info by e-mail
     * @param Request $request
     * @param $id
     */
    public function submitBillingEmail(Request $request, $id) {

        $emails = validateNotificationEmails($request->get('email'));

        $waybill = Waybill::filterSource()
            ->whereId($id)
            ->first();

        $data = [
            'invoice_id' => $waybill->invoice_id,
            'email'      => $emails['valid']
        ];

        $result = Waybill::sendEmail($data);

        if(!$result) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Não foi possível enviar o e-mail.'
            ]);
        }

        return Response::json([
            'result'   => true,
            'feedback' => 'E-mail enviado com sucesso.'
        ]);

    }


    /**
     * Prepare invoice header data array
     *
     * @return \Illuminate\Http\Response
     */
    public function prepareDocumentHeader($input, $customer) {

        return [
            'nif'      => $input['vat'],
            'obs'      => $input['obs'],
            'docdate'  => $input['docdate'],
            'duedate'  => $input['duedate'],
            'docref'   => $input['docref'],
            'code'     => $customer->code,
            'name'     => $input['name'],
            'address'  => $customer->address,
            'zip_code' => $customer->zip_code,
            'city'     => $customer->city,
            'printComment' => Setting::get('invoice_footer_obs')
        ];
    }

    /**
     * Prepare invoice lines data array
     *
     * @return \Illuminate\Http\Response
     */
    public function prepareDocumentLines($input) {

        $lines = [];

        foreach ($input['price'] as $key => $price) {

            $lines[] = [
                'ref'       => Setting::get('invoice_item_waybill_ref'),
                'qt'        => 1,
                'price'     => $price,
                'tax'       => $input['tax'][$key],
                'prodDesc'  => $input['description'][$key],
                'discount'  => 0
            ];

        }

        return $lines;
    }
}
