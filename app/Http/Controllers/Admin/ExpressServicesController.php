<?php

namespace App\Http\Controllers\Admin;

use App\Models\Billing;
use App\Models\CustomerBilling;
use App\Models\ExpressService;
use App\Models\expressSarvice;
use App\Models\FleetGest\Vehicle;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Expr;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Customer;
use App\Models\User;
use Html, Auth, Croppa, Response, Setting, Carbon\Carbon;

class ExpressServicesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'express_services';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',express_services']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $agencies = Auth::user()->listsAgencies();

        $operators = User::remember(5)
                        ->filterAgencies()
                        ->where('id', '>', 1)
                        ->pluck('name', 'id')
                        ->toArray();

        return $this->setContent('admin.express_services.index', compact('agencies', 'operators'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        
        $action = 'Novo serviço expresso';

        $expressService = new ExpressService();

        $formOptions = array('route' => array('admin.express-services.store'), 'class' => 'form-expressService');
        
        $operators = User::remember(5)
                        ->filterAgencies()
                        ->where('id', '>', 1)
                        ->pluck('name', 'id')
                        ->toArray();

        $vehicles = null;
        if(hasModule('fleet')) {
            $vehicles = Vehicle::filterSource()->pluck('license_plate', 'license_plate')->toArray();
        }
        
        return view('admin.express_services.edit', compact('action', 'formOptions', 'expressService', 'operators', 'vehicles'))->render();
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function show($id) { 
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        $action = 'Editar serviço expresso';
        
        $expressService = ExpressService::with('customer')
                        ->whereHas('customer', function($q) {
                            $q->filterAgencies();
                        })
                        ->findOrFail($id);

        $formOptions = array('route' => array('admin.express-services.update', $expressService->id), 'method' => 'PUT', 'class' => 'form-expressService');

        $operators = User::remember(5)
            ->filterAgencies()
            ->where('id', '>', 1)
            ->pluck('name', 'id')
            ->toArray();

        $vehicles = null;
        if(hasModule('fleet')) {
            $vehicles = Vehicle::filterSource()->pluck('license_plate', 'license_plate')->toArray();
        }

        return view('admin.express_services.edit', compact('expressService', 'action', 'formOptions', 'operators', 'vehicles'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null) {

        $input = $request->all();
        $input['save_on_calendar'] = $request->get('save_on_calendar', false);
        $input['is_paid'] = $request->get('is_paid', false);

        $customer = Customer::filterAgencies()
                            ->findOrFail($input['customer_id']);

        $expressService = ExpressService::findOrNew($id);

        if ($expressService->validate($input)) {
            $expressService->agency_id = $customer->agency_id;
            $expressService->fill($input);
            $expressService->save();

            if($input['save_on_calendar']) {
                $expressService->setOnCalendar();
            }

            return [
                'result'   => true,
                'feedback' => 'Dados gravados com sucesso.'
            ];
        }

        return [
            'result'   => false,
            'feedback' => $expressService->errors()->first()
        ];
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $expressService = ExpressService::whereId($id)->first();

        $expressService->deleteFromCalendar();

        $result = $expressService->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o serviço expresso.');
        }

        return Redirect::route('admin.expressService.index')->with('success', 'Serviço expresso removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {
        
        $ids = explode(',', $request->ids);
        
        $result = ExpressService::whereIn('id', $ids)
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

        $data = ExpressService::with(['operator' => function($q){
                            $q->withTrashed();
                        }])
                        ->with(['customer' => function($q){
                            $q->filterAgencies();
                            $q->withTrashed();
                        }])
                        ->filterAgencies()
                        ->select();

        //filter date min
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {

            $dtMax = $dtMin;

            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }

            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }

        //filter status
        $value = $request->status;
        if($request->has('status')) {
            $data = $data->where('status', $value);
        }

        //filter agency
        $value = $request->agency;
        if($request->has('agency')) {
            $data = $data->whereHas('customer', function($q) use($value) {
                $q->where('agency_id', $value);
            });
        }

        //filter is paid
        $value = $request->paid;
        if($request->has('paid')) {
            $data = $data->where('is_paid', $value)
                         ->where('operator_price', '<>', 0.00);
        }
        
        //filter operator
        $value = $request->operator;
        if($request->has('operator')) {
            $data = $data->where('operator_id', $value);
        }

        //filter customer
        $value = $request->customer;
        if($request->has('customer')) {
            $data = $data->where('customer_id', $value);
        }

        //filter billed
        $value = $request->billed;
        if($request->has('billed')) {
            if($value == 1) {
                $data = $data->whereNotNull('invoice_id');
            } else {
                $data = $data->whereNull('invoice_id');
            }
        }
        
        return Datatables::of($data)
                    ->edit_column('date', function($row) {
                        return view('admin.express_services.datatables.date', compact('row'))->render();
                    })
                    ->edit_column('title', function($row) {
                        return view('admin.express_services.datatables.title', compact('row'))->render();
                    })
                    ->edit_column('operator.name', function($row) {
                        return view('admin.express_services.datatables.operator', compact('row'))->render();
                    })
                    ->edit_column('km', function($row) {
                        return view('admin.express_services.datatables.km', compact('row'))->render();
                    })
                    ->edit_column('invoice_id', function($row) {
                        return view('admin.express_services.datatables.invoice', compact('row'))->render();
                    })
                    ->edit_column('total_price', function($row) {
                        return view('admin.express_services.datatables.price', compact('row'))->render();
                    })
                    ->edit_column('is_paid', function($row) {
                        return view('admin.express_services.datatables.is_paid', compact('row'))->render();
                    })
                    ->edit_column('status', function($row) {
                        return view('admin.express_services.datatables.status', compact('row'))->render();
                    })
                    ->edit_column('status', function($row) {
                        return view('admin.express_services.datatables.status', compact('row'))->render();
                    })
                    ->add_column('select', function($row) {
                        return view('admin.partials.datatables.select', compact('row'))->render();
                    })
                    ->add_column('actions', function($row) {
                        return view('admin.express_services.datatables.actions', compact('row'))->render();
                    })
                    ->make(true);
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchCustomer(Request $request) {

        $search = $request->get('q');
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {

            $customers = Customer::filterAgencies()
                ->where(function($q) use($search){
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
                ->isDepartment(false)
                ->get(['name', 'code', 'id']);

            if($customers) {

                $results = array();
                foreach($customers as $customer) {

                    $text = $customer->code;
                    $text.= $customer->code ? ' - ' : '';
                    $text.= str_limit($customer->name, 40);

                    $results[]=array('id'=> $customer->id, 'text' => $text);
                }

            } else {
                $results = [['id' => '', 'text' => 'Nenhum cliente encontrado.']];
            }

        } catch(\Exception $e) {
            $results = [['id' => '', 'text' => 'Erro interno ao processar o pedido.']];
        }

        return Response::json($results);
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

        $expressServices = ExpressService::with('customer')
                                ->filterAgencies()
                                ->whereNull('invoice_id')
                                ->whereIn('id', $ids)
                                ->get();

        $uniqueCustomers = $expressServices;
        $totalCustomers = $uniqueCustomers->groupBy('customer_id')->count();

        $customer = @$expressServices->first()->customer;


        $docDate = date('Y-m-d');
        $docLimitDate = new Carbon('last day of next month');
        $docLimitDate = $docLimitDate->format('Y-m-d');

        $taxes = Invoice::getTaxes();

        $apiKeys = Invoice::getApiKeys();

        return view('admin.express_services.edit_invoice', compact('expressServices', 'customer', 'totalCustomers', 'apiKeys', 'docDate', 'docLimitDate', 'taxes'))->render();
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

                $class = InvoiceGateway\Base::getNamespaceTo('Customer');
                $customerKeyinvoice = new $class();
                $customerKeyinvoice->insertOrUpdateCustomer(
                    $customer->vat,
                    $customer->code,
                    $customer->name,
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

            ExpressService::filterAgencies()
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
                'feedback' => $e->getMessage() . ' on file ' . $e->getFile() .' Line '. $e->getLine()
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

            $result = ExpressService::sendEmail($data);

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

        $expressService = ExpressService::filterAgencies()->whereId($id)->first();

        if(empty($expressService->invoice_id) && empty($expressService->invoice_type) && $expressService->invoice_draft) {
            return Redirect::back()->with('error', 'Não foi emitido nenhum rascunho de fatura para este serviço.');
        }

        $invoice = new Invoice($expressService->api_key);
        $invoiceId = $invoice->convertDraftToDoc($expressService->invoice_id, $expressService->invoice_type);

        if($invoiceId) {
            $expressService->invoice_id = $invoiceId;
            $expressService->invoice_draft = 0;
            $expressService->save();

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

        $expressService = ExpressService::filterAgencies()->whereId($id)->first();

        if(empty($expressService->invoice_id) && empty($expressService->invoice_type) && $expressService->invoice_draft) {
            return Redirect::back()->with('error', 'Não foi emitido nenhum rascunho de fatura para este serviço.');
        }

        $invoice = new Invoice($expressService->api_key);
        $doc = $invoice->getDocumentPdf($expressService->invoice_id, $expressService->invoice_type);

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

        $expressService = ExpressService::filterAgencies()->whereId($id)->first();

        if(empty($expressService->invoice_id) && empty($expressService->invoice_type) && $expressService->invoice_draft) {
            return Redirect::back()->with('error', 'Não foi emitido nenhum rascunho de fatura para este serviço.');
        }

        $invoice = new Invoice($expressService->api_key);
        $documentId = $expressService->invoice_id;

        try {
            if ($expressService->invoice_draft) {
                $result = $invoice->destroyDraft($expressService->invoice_id, $expressService->invoice_type);
            } else {
                $data = [
                    'doc_serie'     => 4,
                    'credit_serie'  => 7,
                    'credit_date'   => date('Y-m-d'),
                    'credit_reason' => 'Erro na emissão da fatura.'
                ];

                $result = $invoice->destroyDocument($expressService->invoice_id, $expressService->invoice_type, $data);
            }
        } catch(\Exception $e) {
            $result = true;
        }

        if($result) {

            ExpressService::where('invoice_id', $documentId)
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

        $expressService = ExpressService::filterAgencies()
                                    ->whereId($id)
                                    ->first();

        return view('admin.express_services.modals.email', compact('expressService'))->render();
    }

    /**
     * Send billing info by e-mail
     * @param Request $request
     * @param $id
     */
    public function submitBillingEmail(Request $request, $id) {

        $emails = validateNotificationEmails($request->get('email'));

        $expressService = ExpressService::filterAgencies()
            ->whereId($id)
            ->first();

        $data = [
            'invoice_id' => $expressService->invoice_id,
            'email'      => $emails['valid']
        ];

        $result = ExpressService::sendEmail($data);

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
            'printComment' => Setting::get('invoice_footer_obs'),
            'customer_id' => $customer->id
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
                'ref'       => 4,//Setting::get('invoice_item_express_service_ref'),
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
