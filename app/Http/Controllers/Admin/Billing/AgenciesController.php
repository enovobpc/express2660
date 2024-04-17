<?php

namespace App\Http\Controllers\Admin\Billing;

use App\Models\AgencyBilling;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Html, DB, Excel, Auth, View, Setting, Redirect;
use Illuminate\Http\Request;
use App\Http\Requests;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Shipment;
use App\Models\Service;
use App\Models\Agency;
use App\Models\ShippingStatus;

class AgenciesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'billing-agencies';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',billing']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $years = yearsArr(2016, date('Y'), true);

        $agencies = Auth::user()->listsAgencies();

        $myAgencies = Auth::user()->agencies;
        if(!empty($myAgencies)) {
            $myAgencies = Agency::whereIn('id', $myAgencies)->get();
        } else {
            $myAgencies = Agency::get();
        }

        return $this->setContent('admin.billing.agencies.index', compact('years', 'agencies', 'myAgencies'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $sourceAgency, $partnerAgency) {

        $year   = $request->has('year') ? $request->year : date('Y');
        $month  = $request->has('month') ? intval($request->month) : date('n');
        $period = $request->has('period') ? $request->period : '30d';


        $sourceAgency = AgencyBilling::getBilling($sourceAgency, $partnerAgency, $month, $year, $period);

        $agencies = Agency::whereIn('id', [$sourceAgency, $partnerAgency])
                        ->get();


        $partnerAgency = $agencies->filter(function($item) use($partnerAgency) {
                return $item->id == $partnerAgency;
        })->first();


        $services = Service::remember(5)
            ->filterAgencies()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $status = ShippingStatus::remember(5)
            ->isVisible()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::remember(5)
            ->filterAgencies()
            ->where('id', '>', 1)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $billing = AgencyBilling::whereAgencyId($sourceAgency->id)
            ->where('partner_agency_id', $partnerAgency->id)
            ->where('month', $month)
            ->where('year', $year)
            ->where('period', $period)
            ->first();

        return $this->setContent('admin.billing.agencies.show', compact('year', 'month', 'period', 'billing', 'services', 'sourceAgency', 'partnerAgency', 'myAgencies', 'status', 'operators'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $sourceAgencyId) {

        $billingMonth = true;
        $year  = $request->has('year') ? $request->year : date('Y');
        $month = $request->has('month') ? $request->month : date('n');
        $period = $request->period ? $request->period : '30d';
        $partnerAgencyId = $request->partner;

        $curMonth = date('n');
        $total = $request->total;

        $agency = Agency::findOrFail($partnerAgencyId);

        $billing = AgencyBilling::whereAgencyId($sourceAgencyId)
            ->where('partner_agency_id', $partnerAgencyId)
            ->where('month', $month)
            ->where('year', $year)
            ->where('period', $period)
            ->first();

        if(!$billing) {
            $billing = AgencyBilling::getBilling($sourceAgencyId, $partnerAgencyId, $month, $year, $period);
        }

        if($curMonth == $month) {
            $docDate = date('Y-m-d');
            $docLimitDate = new Carbon('last day of next month');
            $docLimitDate = $docLimitDate->format('Y-m-d');
        } else {
            $docDate = new Carbon('last day of last month');
            $docDate = $docDate->format('Y-m-d');
            $docLimitDate = new Carbon($year.'-' . $month .'-01');
            $docLimitDate = $docLimitDate->addMonth()->endOfMonth()->format('Y-m-d');
        }

        $apiKeys = Invoice::getApiKeys();

        $formOptions = ['route' => ['admin.billing-agencies.update', $sourceAgencyId, 'partner' => $partnerAgencyId, 'month' => $month, 'year' => $year, 'period' => $period], 'method' => 'put', 'class' => 'form-billing'];

        $action = 'Emitir Fatura Mensal entre Agências';

        return view('admin.billing.agencies.edit_billing', compact('billing', 'billingMonth', 'action', 'formOptions', 'agency', 'year', 'month', 'total', 'docDate', 'docLimitDate', 'apiKeys', 'period'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $sourceAgencyId) {

        $input = $request->all();
        $partnerAgencyId = $request->partner;
        $input['month']  = empty($input['month']) ? date('n') : $input['month'];
        $input['year']   = empty($input['year']) ? date('Y') : $input['year'];
        $input['period'] = empty($input['period']) ? '30d' : $input['period'];
        $input['billed'] = $request->get('billed', false);
        $input['draft']  = $request->get('draft', false);
        $input['send_email']     = $request->get('send_email', false);

        $partnerAgency = Agency::findOrFail($partnerAgencyId);
        $sourceAgency  = AgencyBilling::getBilling($sourceAgencyId, $partnerAgencyId, $input['month'], $input['year'], $input['period']);

        $billing = AgencyBilling::firstOrNew(['agency_id' => $sourceAgencyId, 'partner_agency_id' => $partnerAgencyId, 'month' => $input['month'], 'year' => $input['year'], 'period' => $input['period']]);


        if ($billing->validate($input)) {
            $billing->agency_id = $sourceAgencyId;
            $billing->partner_agency_id = $partnerAgencyId;
            $billing->fill($input);
            $billing->save();

            //submit invoice
            if(isset($input['type']) && !empty($input['type'])) {

                try {
                    $header = $this->prepareDocumentHeader($input, $partnerAgency);
                    $lines  = $this->prepareDocumentLines($billing, $partnerAgency);

                 
                    //store customer if country != pt
                    if($input['country'] != 'pt') {
                        $class = InvoiceGateway\Base::getNamespaceTo('Customer');
                        $customerKeyinvoice = new $class();
                        $customerKeyinvoice->insertOrUpdateCustomer(
                            $input['vat'],
                            $partnerAgency->code,
                            $input['name'],
                            $input['address'],
                            $input['zip_code'],
                            $input['city'],
                            $partnerAgency->phone,
                            null,
                            $partnerAgency->email,
                            $partnerAgency->obs,
                            $input['country']);
                    }

                    $invoice = new Invoice($input['api_key']);
                    $documentId = $invoice->createDraft($input['type'], $header, $lines);

                    $isDraft = 1;
                    $billing->billed = 0;
                    if(!$input['draft']) {
                        $documentId = $invoice->convertDraftToDoc($documentId, $input['type']);
                        $isDraft = 0;
                        $billing->billed = 1;
                    }

                    $billing->invoice_doc_id = $documentId;
                    $billing->invoice_type   = $input['type'];
                    $billing->invoice_draft  = $isDraft;
                    $billing->api_key        = $billing->api_key;

                } catch (\Exception $e) {
                    return [
                        'result'   => false,
                        'feedback' => $e->getMessage()
                    ];
                }
            }

            $billing->save();

            /**
             * Send email
             */
            if($input['send_email']) {

                $data = [
                    'agency_id' => $sourceAgencyId,
                    'partner_agency_id' => $partnerAgency->id,
                    'email'  => $input['email'],
                    'month'  => $input['month'],
                    'year'   => $input['year'],
                    'period' => $input['period']
                ];

                if($input['email_options'] == 'all') {
                    $data['invoice'] = true;
                    $data['summary'] = true;
                } elseif($input['email_options'] == 'invoice') {
                    $data['invoice'] = true;
                } else {
                    $data['summary'] = true;
                }

                $result = AgencyBilling::sendEmail($data);

                if(!$result) {
                    return [
                        'result' => false,
                        'feedback' => 'Dados gravados com sucesso. Não foi possível enviar o e-mail ao cliente'
                    ];
                }
            }

            $month  = $input['month'];
            $year   = $input['year'];
            $period = $input['period'];

            return [
                'result'   => true,
                'feedback' => 'Dados gravados com sucesso.',
                'html'     => view('admin.billing.agencies.partials.header', compact('partnerAgency', 'sourceAgency','billing', 'month', 'year', 'period'))->render()
            ];
        }

        return json_encode([
            'result'   => false,
            'feedback' => $billing->errors()->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function destroy($id) {
//    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
//    public function massDestroy(Request $request) {
//    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {

        $sourceAgency = $request->has('source') ? $request->source : null;
        $year   = $request->has('year') ? $request->year : date('Y');
        $month  = $request->has('month') ? $request->month : date('m');
        $period = $request->has('period') ? $request->period : '30d';


        $agencies       = Agency::withTrashed()->pluck('print_name', 'id')->toArray();
        $agenciesColors = Agency::withTrashed()->pluck('color', 'id')->toArray();

        $billing = [
            'shipments.id',
            'shipments.agency_id as agency_id',
            'shipments.recipient_agency_id as recipient_agency_id',
            'shipments.provider_id',
            'agencies_billing.invoice_id',

            DB::raw('IF(shipments.agency_id ='.$sourceAgency.', shipments.recipient_agency_id, shipments.agency_id) as partner_agency'),

            DB::raw('sum(is_collection) as collections'),

            DB::raw('(select count(cost_price)
                        from shipments as temp_table 
                        where temp_table.deleted_at is null and MONTH(temp_table.date) = '.$month.' and YEAR(temp_table.date) = '.$year.' 
                        and temp_table.agency_id = '.$sourceAgency.'
                        and temp_table.recipient_agency_id = partner_agency)
                        as count_expeditions'),

            DB::raw('(select sum(cost_price)
                        from shipments as temp_table 
                        where temp_table.deleted_at is null and MONTH(temp_table.date) = '.$month.' and YEAR(temp_table.date) = '.$year.' 
                        and temp_table.agency_id = '.$sourceAgency.'
                        and temp_table.recipient_agency_id = partner_agency)
                        as total_expeditions_price'),

            DB::raw('(select count(delivery_price)
                        from shipments as temp_table 
                        where temp_table.deleted_at is null and MONTH(temp_table.date) = '.$month.' and YEAR(temp_table.date) = '.$year.' 
                        and temp_table.agency_id = partner_agency 
                        and (temp_table.recipient_agency_id='.$sourceAgency.' or temp_table.sender_agency_id='.$sourceAgency.'))
                        as count_deliveries'),

            DB::raw('(select sum(delivery_price)
                        from shipments as temp_table 
                        where temp_table.deleted_at is null and MONTH(temp_table.date) = '.$month.' and YEAR(temp_table.date) = '.$year.' 
                        and temp_table.agency_id = partner_agency 
                        and (temp_table.recipient_agency_id='.$sourceAgency.' or temp_table.sender_agency_id='.$sourceAgency.'))
                        as total_deliveries_price'),

            DB::raw('sum(charge_price) as charge_price'),
            DB::raw('MONTH(date) as month'),
            DB::raw('YEAR(date) as year')
        ];
        
        $data = Shipment::leftjoin('agencies_billing', function($join) use($month, $year, $period) {
                    $join->on('shipments.agency_id', '=', 'agencies_billing.agency_id')
                        ->where('agencies_billing.source', '=', config('app.source'))
                        ->where('agencies_billing.month', '=', $month)
                        ->where('agencies_billing.year', '=', $year)
                        ->where('agencies_billing.period', '=', $period);
                })
                ->whereRaw('YEAR(date) = '.$year)
                ->whereRaw('MONTH(date) = '.$month)
                ->where(function($q) use($sourceAgency) {
                    $q->where(function($q) use($sourceAgency) {
                        $q->where('shipments.agency_id', $sourceAgency)
                          ->where('shipments.recipient_agency_id', '<>',$sourceAgency);
                    })
                    ->orWhere('shipments.recipient_agency_id', $sourceAgency);
                })
                ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
                ->whereRaw('shipments.agency_id <> shipments.recipient_agency_id')
                ->groupBy('partner_agency')
                ->orderBy('source')
                ->select($billing);

        if(Auth::user()->isGuest()) {
            $data = $data->where('agency_id', '99999'); //hide data to gest agency role
        }

        return Datatables::of($data)
                ->add_column('partner_agency', function($row) use($sourceAgency, $agencies, $agenciesColors) {
                    return view('admin.billing.agencies.datatables.partner_agency', compact('row', 'sourceAgency', 'agencies', 'agenciesColors'))->render();
                })
                ->edit_column('month', function($row) {
                    return trans('datetime.month-tiny.'.$row->month).' '.$row->year;
                })
                 ->edit_column('shipments', function($row) {
                    return $row->shipments - $row->collections;
                })
                ->add_column('count_expeditions', function($row) {
                    return $row->count_expeditions ? $row->count_expeditions : '';
                })
                ->add_column('count_deliveries', function($row) {
                    return $row->count_deliveries ? $row->count_deliveries : '';
                })
                ->add_column('total_cost', function($row) use($period) {
                    return view('admin.billing.agencies.datatables.total_cost', compact('row', 'period'))->render();
                })
                ->add_column('total_delivery', function($row) use($period) {
                    return view('admin.billing.agencies.datatables.total_delivery', compact('row', 'period'))->render();
                })
                ->add_column('balance', function($row) use($period) {
                    $balance = $row->total_expeditions_price - $row->total_deliveries_price;
                    return view('admin.billing.agencies.datatables.balance', compact('row', 'balance', 'period'))->render();
                })
                ->add_column('invoice_id', function($row) use($period) {
                    return view('admin.billing.agencies.datatables.invoice', compact('row'))->render();
                })
                ->add_column('actions', function($row) use($sourceAgency) {
                    return view('admin.billing.agencies.datatables.actions', compact('row', 'sourceAgency'))->render();
                })
                ->make(true);
    }

    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatableShipments(Request $request, $sourceAgency, $partnerAgency) {

        $year   = $request->has('year')  ? $request->year : date('Y');
        $month  = $request->has('month') ? $request->month : date('m');
        $period = $request->has('period') ? $request->period : '30d';
        $type   = $request->has('type') ? $request->type : 'expeditions';


        $data = Shipment::with('service', 'provider', 'status', 'operator')
            ->filterAgencies()
            ->whereRaw('MONTH(date) = '.$month)
            ->whereRaw('YEAR(date) = '.$year)
            ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
            ->select();

        if($type == 'expeditions') {
            $data = $data->where(function($q) use($sourceAgency, $partnerAgency){
                        $q->where('agency_id', $sourceAgency)
                          ->where('recipient_agency_id', $partnerAgency);
                });
        } else {
            $data = $data->where(function($q) use($sourceAgency, $partnerAgency) {
                    $q->where(function($q) use($sourceAgency, $partnerAgency) {
                        $q->where('recipient_agency_id', $sourceAgency)
                          ->orWhere('sender_agency_id', $sourceAgency);
                    })
                     ->where('agency_id', $partnerAgency);
                });
        }

        
        $agencies = Agency::remember(5)->get(['name', 'code', 'id', 'color']);
        $agencies = $agencies->groupBy('id')->toArray();
        
        return Datatables::of($data)
                ->edit_column('tracking_code', function($row) use($agencies) {
                    return view('admin.shipments.datatables.tracking', compact('row', 'agencies'))->render();
                })
                ->edit_column('sender_name', function($row) {
                    return view('admin.shipments.datatables.sender', compact('row'))->render();
                })
                ->edit_column('recipient_name', function($row) {
                    return view('admin.shipments.datatables.recipient', compact('row'))->render();
                })
                ->edit_column('status_id', function($row) {
                    return view('admin.shipments.datatables.status', compact('row'))->render();
                })
                ->edit_column('volumes', function($row) {
                    return view('admin.shipments.datatables.volumes', compact('row'))->render();
                })
                ->edit_column('date', function($row) {
                    return view('admin.shipments.datatables.date', compact('row'))->render();
                })
                ->edit_column('service_id', function($row) use($agencies) {
                    return view('admin.shipments.datatables.service', compact('row', 'agencies'))->render();
                })
                ->edit_column('cost_price', function($row) {

                    $html = '';

                    if($row->ignore_billing) {
                        $html = '<strike class="text-muted">';
                    }

                    if($row->cost_price > 0.00) {
                        $html.= '<b>' . money($row->cost_price, Setting::get('app_currency')) . '</b>';
                    } else {
                        $html.= '<b class="text-red">' . money($row->cost_price, Setting::get('app_currency')) . '</b>';
                    }

                    if($row->ignore_billing) {
                        $html.= '</strike>';
                    }

                    return $html;
                })
                ->edit_column('delivery_price', function($row) {
                    if($row->delivery_price > 0.00) {
                        return '<b>' . money($row->delivery_price, Setting::get('app_currency')) . '</b>';
                    } else {
                        return '<b class="text-red">' . money($row->delivery_price, Setting::get('app_currency')) . '</b>';
                    }
                })
                ->edit_column('total_price', function($row) {
                    return money($row->total_price, Setting::get('app_currency'));
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->add_column('actions', function($row) use($type) {
                    if($type == 'expeditions') {
                        return view('admin.billing.agencies.datatables.expeditions.actions', compact('row', 'type'))->render();
                    } else {
                        return view('admin.billing.agencies.datatables.deliveries.actions', compact('row', 'type'))->render();
                    }

                })
                ->make(true);
    }

    /**
     * Print
     *
     * @param type $shipmentId
     * @return type
     */
    public function printShipments (Request $request, $sourceAgency, $partnerAgency){
        return AgencyBilling::printShipments($sourceAgency, $partnerAgency, $request->month, $request->year, 'I', null, $request->period);
    }


    /**
     * Show modal to edit billing emaill
     * @param Request $request
     * @param $id
     */
    public function editBillingEmail(Request $request, $sourceAgencyId, $partnerAgencyId) {

        $month = $request->get('month') ? $request->month : date('n');
        $year  = $request->get('year') ? $request->year : date('Y');
        $period = $request->has('period') ? $request->period : '30d';

        $agency = Agency::findOrFail($partnerAgencyId);

        return view('admin.billing.agencies.modals.email', compact('agency', 'month', 'year', 'period', 'sourceAgencyId'))->render();
    }

    /**
     * Send billing info by e-mail
     * @param Request $request
     * @param $id
     */
    public function submitBillingEmail(Request $request, $sourceAgencyId, $partnerAgencyId) {

        $data = [
            'agency_id' => $sourceAgencyId,
            'partner_agency_id' => $partnerAgencyId,
            'email' => $request->get('email'),
            'month' => $request->get('month'),
            'year'  => $request->get('year'),
            'period' => $request->get('period'),
            'invoice' => $request->get('invoice', false),
            'summary' => $request->get('summary', false),
        ];

        $result = AgencyBilling::sendEmail($data);

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
     * Download billing invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadInvoice(Request $request, $sourceAgencyId, $partnerAgencyId) {

        $year  = $request->has('year') ? $request->year : date('Y');
        $month = $request->has('month') ? $request->month : date('n');
        $period = $request->has('period') ? $request->period : '30d';

        $billing  = AgencyBilling::firstOrNew(['agency_id' => $sourceAgencyId, 'partner_agency_id' => $partnerAgencyId, 'month' => $month, 'year' => $year, 'period' => $period]);

        if(empty($billing->invoice_doc_id) && empty($billing->invoice_type)) {
            return Redirect::back()->with('error', 'Não foi emitida nenhuma fatura para este cliente no mês e ano indicados.');
        }

        $invoice = new Invoice($billing->api_key);
        $doc = $invoice->getDocumentPdf($billing->invoice_doc_id, $billing->invoice_type);

        $data = base64_decode($doc);
        header('Content-Type: application/pdf');
        echo $data;
    }

    /**
     * Destroy billing invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyInvoice(Request $request, $sourceAgencyId, $partnerAgencyId) {

        $year  = $request->has('year') ? $request->year : date('Y');
        $month = $request->has('month') ? $request->month : date('n');
        $period = $request->has('period') ? $request->period : '30d';

        $billing  = AgencyBilling::firstOrNew(['agency_id' => $sourceAgencyId, 'partner_agency_id' => $partnerAgencyId, 'month' => $month, 'year' => $year, 'period' => $period]);

        if(empty($billing->invoice_doc_id) && empty($billing->invoice_type)) {
            return Redirect::back()->with('error', 'Não foi emitida nenhuma fatura para esta agência no mês e ano indicados.');
        }

        $invoice = new Invoice($billing->api_key);

        try {
            if ($billing->invoice_draft) {
                $result = $invoice->destroyDraft($billing->invoice_doc_id, $billing->invoice_type);
            } else {
                $data = [
                    'doc_serie'     => 4,
                    'credit_serie'  => 7,
                    'credit_date'   => date('Y-m-d'),
                    'credit_reason' => 'Erro informático ao gerar a fatura.'
                ];

                $result = $invoice->destroyDocument($billing->invoice_doc_id, $billing->invoice_type, $data);
            }
        } catch(\Exception $e) {
            $result = true;
        }

        if($result) {
            $billing->forceDelete();
            return Redirect::back()->with('success', 'Documento de venda anulado com sucesso.');
        }

        return Redirect::back()->with('error', 'Ocorreu um erro ao anular o documento de venda.');
    }

    /**
     * Convert a Draft into Invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function convertFromDraft(Request $request, $sourceAgencyId, $partnerAgencyId) {
        $year   = $request->has('year') ? $request->year : date('Y');
        $month  = $request->has('month') ? $request->month : date('n');
        $period = $request->has('period') ? $request->period : '30d';

        $billing  = AgencyBilling::firstOrNew(['agency_id' => $sourceAgencyId, 'partner_agency_id' => $partnerAgencyId, 'month' => $month, 'year' => $year, 'period' => $period]);

        if(empty($billing->invoice_doc_id) && empty($billing->invoice_type) && $billing->invoice_draft) {
            return Redirect::back()->with('error', 'Não foi emitido nenhum rascunho de fatura para esta agência no mês e ano indicados.');
        }

        $invoice = new Invoice($billing->api_key);
        $invoiceId = $invoice->convertDraftToDoc($billing->invoice_doc_id, $billing->invoice_type);

        if($invoiceId) {
            $billing->invoice_doc_id = $invoiceId;
            $billing->invoice_draft = 0;
            $billing->billed = true;
            $billing->save();

            return Redirect::back()->with('success', 'Rascunho convertido com sucesso.');
        }

        return Redirect::back()->with('error', 'Ocorreu um erro ao tentar conveter o rascunho.');
    }

    /**
     * Prepare invoice header data array
     *
     * @return \Illuminate\Http\Response
     */
    public function prepareDocumentHeader($input, $partnerAgency) {

        return [
            'nif'      => $input['vat'],
            'obs'      => $input['obs'],
            'docdate'  => $input['docdate'],
            'duedate'  => $input['duedate'],
            'docref'   => $input['docref'],
            'code'     => $partnerAgency->code,
            'name'     => $input['name'],
            'address'  => $partnerAgency->address,
            'zip_code' => $partnerAgency->zip_code,
            'city'     => $partnerAgency->city,
            'printComment' => Setting::get('invoice_footer_obs')
        ];
    }

    /**
     * Prepare invoice lines data array
     *
     * @return \Illuminate\Http\Response
     */
    public function prepareDocumentLines($billing, $partnerAgency) {

        $lines = [];

        if(1) {//separar nacional e importações

            //Nacional Shipments
            if($billing->total_month_nacional > 0.00) {
                $partnerAgency->country == Setting::get('app_country') ? Setting::get('invoice_item_nacional_tax') : ($partnerAgency->final_consumer ? Setting::get('invoice_item_nacional_tax') : 0); //se for cliente estrangeiro, é sempre 0 de iva
                
                $lines[] = [
                    'ref'       => Setting::get('invoice_item_nacional_ref'),
                    'qt'        => 1,
                    'price'     => $billing->total_month_nacional,
                    'tax'       => $tax,
                    'tax_id'    => $tax,
                    'prodDesc'  => Setting::get('invoice_item_nacional_desc'),
                    'discount'  => 0
                ];
            }

            //Import Shipments
            if($billing->total_month_import > 0.00) {
                $lines[] = [
                    'ref'       => Setting::get('invoice_item_import_ref'),
                    'qt'        => 1,
                    'price'     => $billing->total_month_import,
                    'tax'       => $partnerAgency->country == Setting::get('app_country') ? Setting::get('invoice_item_nacional_tax') : ($partnerAgency->final_consumer ? Setting::get('invoice_item_nacional_tax') : 0),
                    'prodDesc'  => Setting::get('invoice_item_import_desc'),
                    'discount'  => 0
                ];
            }
        } else {

            //Envios nacionais e importações
            if($billing->total_month_nacional > 0.00) {
                $lines[] = [
                    'ref'       => Setting::get('invoice_item_nacional_ref'),
                    'qt'        => 1,
                    'price'     => $billing->total_month_nacional,
                    'tax'       => $partnerAgency->country == Setting::get('app_country') ? Setting::get('invoice_item_nacional_tax') : ($partnerAgency->final_consumer ? Setting::get('invoice_item_nacional_tax') : 0),
                    'prodDesc'  => Setting::get('invoice_item_nacional_desc'),
                    'discount'  => 0
                ];
            }
        }

        //Exportações Espanha
        if($billing->total_month_spain > 0.00) {
            $lines[] = [
                'ref'       => Setting::get('invoice_item_spain_ref'),
                'qt'        => 1,
                'price'     => $billing->total_month_spain,
                'tax'       => $partnerAgency->final_consumer ? Setting::get('invoice_item_nacional_tax') : Setting::get('invoice_item_spain_tax'),
                'prodDesc'  => Setting::get('invoice_item_spain_desc'),
                'discount'  => 0
            ];
        }

        //Exportações Internacionais
        if($billing->total_month_internacional > 0.00) {
            $lines[] = [
                'ref'       => Setting::get('invoice_item_internacional_ref'),
                'qt'        => 1,
                'price'     => $billing->total_month_internacional,
                'tax'       => $partnerAgency->final_consumer ? Setting::get('invoice_item_nacional_tax') : Setting::get('invoice_item_internacional_tax'),
                'prodDesc'  => Setting::get('invoice_item_internacional_desc'),
                'discount'  => '0'
            ];
        }

        return $lines;
    }
}
