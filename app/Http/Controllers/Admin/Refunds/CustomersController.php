<?php

namespace App\Http\Controllers\Admin\Refunds;

use App\Models\CacheSetting;
use App\Models\Route;
use Auth, File, Setting, Response, DB, Excel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Jenssegers\Date\Date;
use Yajra\Datatables\Facades\Datatables;

use App\Models\ShippingStatus;
use App\Models\RefundControl;
use App\Models\Shipment;
use App\Models\Provider;
use App\Models\Customer;
use App\Models\Agency;
use App\Models\User;

class CustomersController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'refunds_customers';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',refunds_customers']);
        validateModule('refunds_customers');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->get());

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $routes = Route::listsWithCode(Route::remember(config('cache.query_ttl'))
            ->cacheTags(Route::CACHE_TAG)
            ->filterSource()
            ->ordered()
            ->get());

        $allOperators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->isActive()
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id', 'is_operator', 'login_app']);

        $operators = User::listOperators($allOperators->filter(function ($item) {
            return $item->is_operator || $item->login_app;
        }));
        $users     = User::listOperators($allOperators->filter(function ($item) {
            return !$item->is_operator;
        }));

        $data = compact(
            'agencies',
            'providers',
            'status',
            'operators',
            'users',
            'routes'
        );

        return $this->setContent('admin.refunds.customers.index', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $customerId)
    {

        $customer = Customer::filterSource()->find($customerId);

        $shipments = Shipment::where(function ($q) use ($customerId) {
            $q->where('customer_id', $customerId);
            $q->orWhere('requested_by', $customerId);
        })
            ->with(['status' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(ShippingStatus::CACHE_TAG);
                $q->select(['id', 'name', 'color', 'is_final']);
            }])
            ->with('last_history')
            ->with('refund_control')
            ->where('is_collection', 0)
            ->whereNotNull('charge_price')
            ->whereNotIn('status_id', [ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID])
            ->whereHas('refund_control', function ($q) {
                $q->whereNull('payment_method');
                $q->whereNull('payment_date');
                $q->whereNotNull('received_method');
                $q->whereNotNull('received_date');
                $q->where('received_method', '<>', 'claimed');
                $q->where('canceled', 0);
            })
            ->get();

        return view('admin.refunds.customers.show', compact('customer', 'shipments'))->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {

        if ($request->has('grouped')) {
            $ids   = RefundControl::getCustomerAvailableRefunds($id);
            $count = count($ids);
            $total = Shipment::whereIn('id', $ids)->sum('charge_price');
            $ids   = implode(',', $ids);

            $customer = Customer::filterSource()->find($id);

            return view('admin.refunds.customers.modals.mass_edit_form', compact('ids', 'customer', 'total', 'count'))->render();
        }


        $shipment = Shipment::with(['requested_customer' => function ($q) {
            $q->withTrashed();
            $q->remember(config('cache.query_ttl'));
            $q->cacheTags(Customer::CACHE_TAG);
            $q->select(['id', 'code', 'name', 'contact_email', 'refunds_email', 'iban_refunds']);
        }])
            ->with(['customer' => function ($q) {
                $q->withTrashed();
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Customer::CACHE_TAG);
                $q->select(['id', 'code', 'name', 'contact_email', 'refunds_email', 'iban_refunds']);
            }])
            ->filterAgencies()
            ->with('refund_control')
            ->findOrFail($id);

        if (empty($shipment->refund_control)) {
            $refund = new RefundControl;
        } else {
            $refund = $shipment->refund_control;
        }

        $refund->shipment_id = $shipment->id;
        $refund->iban        = $shipment->requested_by ? @$shipment->requested_customer->iban_refunds : @$shipment->customer->iban_refunds;
        $refund->email       = $shipment->requested_by ? @$shipment->requested_customer->refunds_email : @$shipment->customer->refunds_email;

        $compact = ['refund', 'shipment'];

        if (config('app.source') === 'invictacargo') {
            $refund->load('shipment');
            $operators = User::listOperators(User::remember(config('cache.query_ttl'))
                ->cacheTags(User::CACHE_TAG)
                ->filterAgencies()
                ->isOperator()
                ->ignoreAdmins()
                ->orderBy('source', 'asc')
                ->orderBy('name', 'asc')
                ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), Auth::user()->isAdmin() ? true : false);

            $compact[] = 'operators';
        }

        return view('admin.refunds.customers.edit', compact($compact))->render();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $input = $request->all();
        $input['send_email']    = $request->get('send_email', false);
        $input['print_proof']   = $request->get('print_proof', false);
        $input['print_summary'] = $request->get('print_summary', false);

        if (!empty($input['send_email']) && !empty($input['email'])) {
            $emails = validateNotificationEmails($input['email']);
            if (!empty($emails['error'])) {
                return Response::json([
                    'result'   => false,
                    'feedback' => 'Não é possível gravar as alterações porque um ou mais e-mails introduzidos são inválidos.'
                ]);
            }
        }

        $refund = RefundControl::firstOrNew(['shipment_id' => $id]);

        if ($refund->validate($input)) {
            $refund->fill($input);
            $refund->canceled = 0;
            $refund->save();

            if (config('app.source') === 'invictacargo') {
                $operatorId = $request->get('operator');

                // Verificar se o operador foi alterado e atualizar o estado da encomenda
                if ($operatorId && $operatorId != $refund->shipment->operator_id && !in_array($refund->shipment->status_id, [ShippingStatus::DELIVERED_ID, ShippingStatus::INCIDENCE_ID])) {
                    $refund->shipment->history()->create([
                        'status_id'     => ShippingStatus::IN_DISTRIBUTION_ID,
                        'operator_id'   => $operatorId,
                    ]);

                    $refund->shipment->status_id    = ShippingStatus::IN_DISTRIBUTION_ID;
                    $refund->shipment->operator_id  = $operatorId;
                    $refund->shipment->save();
                }
            }

            if ($input['save_iban']) {
                if ($refund->shipment->requested_by) {
                    $refund->shipment->requested_customer->iban_refunds = $input['iban'];
                    $refund->shipment->requested_customer->save();
                } else {
                    $refund->shipment->customer->iban_refunds = $input['iban'];
                    $refund->shipment->customer->save();
                }

                Customer::flushCache(Customer::CACHE_TAG);
            }

            if ($request->hasFile('attachment')) {

                if (!empty($refund->filepath)) {
                    File::delete(public_path() . '/' . $refund->filepath);
                }

                if (!$refund->upload($input['attachment'], true, 20)) {
                    return Redirect::back()->withInput()->with('error', 'Erro ao carregar o anexo.');
                }
            } else {
                $refund->save();
            }


            if (empty($refund->received_date) && empty($refund->received_method) && empty($refund->payment_date) && empty($refund->payment_method)) {
                $refund->forceDelete();
            }


            if (!empty($input['send_email']) && !empty($input['email'])) {

                if (empty($refund->payment_date) && empty($refund->payment_method)) {
                    return Response::json([
                        'result'   => false,
                        'feedback' => 'Reembolso gravado com sucesso. Não foi enviado o e-mail porque nenhum dos reembolsos selecionados foi marcado como pago.'
                    ]);
                }

                $shipments = Shipment::with('customer')->whereId($id)->get();
                RefundControl::sendEmail($emails['valid'], $shipments);
            }

            $printProof = $printSummary = null;
            if ($input['print_proof']) {
                $printProof = route('admin.printer.refunds.customers.proof', $refund->shipment_id);
            }

            if ($input['print_summary']) {
                $printSummary = route('admin.printer.refunds.customers.summary', ['id[]' => $refund->shipment_id]);
            }

            $result = [
                'result'        => true,
                'feedback'      => 'Estado do reembolso gravado com sucesso.',
                'printProof'    => $printProof,
                'printSummary'  => $printSummary,
                'html'          => view('admin.shipments.shipments.modals.popup_denied')->render()
            ];
        } else {
            $result = [
                'result'   => false,
                'feedback' => $refund->errors()->first()
            ];
        }

        return Response::json($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        RefundControl::flushCache(RefundControl::CACHE_TAG);

        $shipment = Shipment::filterAgencies()
            ->whereId($id)
            ->firstOrFail();

        $refundCountrol = @$shipment->refund_control;

        if ($refundCountrol) {
            $refundCountrol->canceled = 1;
            $result = $refundCountrol->save();
        } else {
            $refundCountrol = new RefundControl();
            $refundCountrol->shipment_id     = $shipment->id;
            $refundCountrol->received_method = 'canceled';
            $refundCountrol->received_at     = date('Y-m-d H:i:s');
            $refundCountrol->save();
        }

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar anular o reembolso.');
        }

        return Redirect::back()->with('success', 'Reembolso anulado com sucesso.');
    }

    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {

        RefundControl::flushCache(RefundControl::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $obs = $request->obs;

        $shipments = Shipment::with('refund_control')
            ->filterAgencies()
            ->whereIn('id', $ids)
            ->get();

        $errors = [];
        foreach ($shipments as $shipment) {

            if (!$shipment->refund_control) {
                $shipment->refund_control = new RefundControl();
            }

            $shipment->refund_control->shipment_id  = $shipment->id;
            $shipment->refund_control->canceled     = 1;
            $shipment->refund_control->obs          = $shipment->refund_control->obs . ' ' . $obs;
            $result = $shipment->refund_control->save();

            if ($result) {
                $errors[] = $shipment->id;
            }
        }


        if (empty($errors)) {
            return Redirect::back()->with('success', 'Reembolsos selecionados anulados com sucesso.');
        }

        return Redirect::back()->with('error', 'Não foi possível anular um ou mais reembolsos');
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request)
    {
        $grouped = $request->get('grouped');

        $bindings = [
            'shipments.id',
            'tracking_code',
            'shipments.provider_tracking_code',
            'shipments.reference',
            'shipments.reference2',
            'shipments.reference3',
            'shipments.sender_name',
            'shipments.sender_address',
            'shipments.sender_zip_code',
            'shipments.sender_city',
            'shipments.recipient_name',
            'shipments.recipient_address',
            'shipments.recipient_zip_code',
            'shipments.recipient_city',
            'shipments.date',
            'shipments.agency_id',
            'shipments.sender_agency_id',
            'shipments.recipient_agency_id',
            'shipments.charge_price',
            'shipments.customer_id',
            'shipments.requested_by',
            'shipments.provider_id',
            'shipments.status_id',
            'shipments.operator_id',
            'shipments.route_id',
            'shipments.refund_method'
        ];

        if ($grouped) {
            $bindings[] = DB::raw('count(sender_name) as count');
            $bindings[] = DB::raw('sum(charge_price) as total');
            $bindings[] = DB::raw('min(date) as oldest_date');
            $bindings[] = DB::raw('(IFNULL(requested_by, shipments.customer_id)) as customerID');
        }

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->get(['name', 'code', 'id', 'color', 'source']);

        $sourceAgencies = $agencies->filter(function ($item) {
            return $item->source == config('app.source');
        })->pluck('id')->toArray();

        $agencies = $agencies->groupBy('id')->toArray();

        $data = Shipment::where('is_collection', 0)
            ->whereNotNull('charge_price')
            ->whereIn('shipments.agency_id', $sourceAgencies)
            ->with(['status' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(ShippingStatus::CACHE_TAG);
                $q->select(['id', 'name', 'color', 'is_final']);
            }])
            ->with(['customer' => function ($q) {
                $q->withTrashed();
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Customer::CACHE_TAG);
                $q->select(['id', 'code', 'name', 'contact_email', 'refunds_email', 'iban_refunds', 'agency_id']);
            }])
            ->with(['requested_customer' => function ($q) {
                $q->withTrashed();
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Customer::CACHE_TAG);
                $q->select(['id', 'code', 'name', 'contact_email', 'refunds_email', 'iban_refunds', 'agency_id']);
            }])
            ->with(['operator' => function ($q) {
                $q->withTrashed();
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(User::CACHE_TAG);
                $q->select(['id', 'name', 'code', 'code_abbrv']);
            }])
            ->with('last_history')
            ->with('refund_control')
            ->whereNotIn('status_id', [ShippingStatus::DEVOLVED_ID, ShippingStatus::CANCELED_ID])
            ->applyRefundsRequestFilters($request)
            ->select($bindings);

        if ($grouped) {
            $data = $data->groupBy('customerID');
        }

        return Datatables::of($data)
            ->edit_column('shipments.id', function ($row) use ($agencies) {
                return view('admin.shipments.shipments.datatables.tracking', compact('row', 'agencies'))->render();
            })
            ->add_column('customer', function ($row) use ($grouped) {
                if ($grouped) {
                    return view('admin.refunds.customers.datatables.grouped_customer', compact('row'))->render();
                }
            })
            ->add_column('code', function ($row) use ($grouped, $agencies) {
                if ($grouped) {
                    return view('admin.refunds.customers.datatables.grouped_code', compact('row', 'agencies'))->render();
                }
            })
            ->edit_column('reference', function ($row) {
                return view('admin.shipments.shipments.datatables.reference', compact('row'))->render();
            })
            ->edit_column('sender_name', function ($row) {
                return view('admin.shipments.shipments.datatables.sender', compact('row'))->render();
            })
            ->edit_column('recipient_name', function ($row) {
                return view('admin.shipments.shipments.datatables.recipient', compact('row'))->render();
            })
            ->edit_column('date', function ($row) {
                return view('admin.refunds.customers.datatables.date', compact('row'))->render();
            })
            ->edit_column('delivery_date', function ($row) {
                return view('admin.refunds.customers.datatables.delivery_date', compact('row'))->render();
            })
            ->edit_column('charge_price', function ($row) {
                return view('admin.refunds.customers.datatables.price', compact('row'))->render();
            })
            ->edit_column('refund_control.received_date', function ($row) {
                return view('admin.refunds.customers.datatables.received_method', compact('row'))->render();
            })
            ->edit_column('refund_control.payment_date', function ($row) {
                return view('admin.refunds.customers.datatables.payment_method', compact('row'))->render();
            })
            ->edit_column('confirmed', function ($row) {
                return view('admin.refunds.customers.datatables.confirmed', compact('row'))->render();
            })
            ->edit_column('refund_control.obs', function ($row) {
                return view('admin.refunds.customers.datatables.obs', compact('row'))->render();
            })
            ->edit_column('refund_control.customer_obs', function ($row) {
                return @$row->refund_control->customer_obs;
            })
            ->add_column('oldest', function ($row) {
                return view('admin.refunds.customers.datatables.grouped_oldest', compact('row'))->render();
            })
            ->edit_column('count', function ($row) {
                return @$row->count;
            })
            ->edit_column('total', function ($row) {
                return view('admin.refunds.customers.datatables.grouped_total', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) use ($grouped) {
                return view('admin.refunds.customers.datatables.actions', compact('row', 'grouped'))->render();
            })
            ->make(true);
    }

    /**
     * Mass update
     *
     * @param type $shipmentId
     * @return type
     */
    public function massUpdate(Request $request)
    {

        $input = $request->all();
        $ids   = explode(',', $request->ids);
        $input['send_email']    = $request->get('send_email', false);
        $input['print_proof']   = $request->get('print_proof', false);
        $input['print_summary'] = $request->get('print_summary', false);
        $hasFile                = $request->hasFile('attachment');

        if (!empty($input['send_email']) && !empty($input['email'])) {
            $emails = validateNotificationEmails($input['email']);
            if (!empty($emails['error'])) {
                return Redirect::back()->with('success', 'Não é possível gravar as alterações porque um ou mais e-mails introduzidos são inválidos.');
            }
        }

        if (empty($input['received_method'])) {
            unset($input['received_method'], $input['received_date']);
        }

        if (empty($input['payment_method'])) {
            unset($input['payment_method'], $input['payment_date']);
        }


        try {

            $shipments = Shipment::with('refund_control')
                ->filterAgencies()
                ->whereIn('id', $ids)
                ->get();

            $errors = [];
            $filepath = null;
            $filename = null;

            foreach ($shipments as $key => $shipment) {

                $refund = $shipment->refund_control;

                if (empty($refund)) {
                    $refund = new RefundControl();
                }

                $refund->fill($input);
                $refund->shipment_id = $shipment->id;
                $refund->canceled    = 0;
                $refund->save();

                if ($input['save_iban']) {
                    if ($refund->shipment->requested_by) {
                        $refund->shipment->requested_customer->iban_refunds = $input['iban'];
                        $refund->shipment->requested_customer->save();
                    } else {
                        $refund->shipment->customer->iban_refunds = $input['iban'];
                        $refund->shipment->customer->save();
                    }

                    Customer::flushCache(Customer::CACHE_TAG);
                }

                if ($hasFile && !$filepath) {

                    if (!empty($refund->filepath)) {
                        File::delete(public_path() . '/' . $refund->filepath);
                    }

                    if ($refund->upload($input['attachment'], true, 20)) {
                        $filepath = $refund->filepath;
                        $filename = $refund->filename;
                        $hasFile = true;
                    }
                } else {
                    if ($filepath) {
                        $refund->filepath = $filepath;
                        $refund->filename = $filename;
                    }
                    $refund->save();
                }

                //atualiza o envio para em distribuição pelo motorista indicado na ficha
                if (config('app.source') === 'invictacargo' || 1) {
                    $operatorId = $request->get('operator');

                    // Verificar se o operador foi alterado e atualizar o estado da encomenda
                    if ($operatorId && $operatorId != $refund->shipment->operator_id && !in_array($refund->shipment->status_id, [ShippingStatus::DELIVERED_ID, ShippingStatus::INCIDENCE_ID])) {
                        $refund->shipment->history()->create([
                            'status_id'     => ShippingStatus::IN_DISTRIBUTION_ID,
                            'operator_id'   => $operatorId,
                        ]);

                        $refund->shipment->status_id    = ShippingStatus::IN_DISTRIBUTION_ID;
                        $refund->shipment->operator_id  = $operatorId;
                        $refund->shipment->save();
                    }
                }

                if (empty($refund->received_date) && empty($refund->received_method) && empty($refund->payment_date) && empty($refund->payment_method)) {
                    $refund->forceDelete();
                }

                if (empty($refund->payment_method) || empty($refund->payment_date)) {
                    $errors[] = $shipment->id;
                    unset($shipments[$key]);
                }
            }

            //send email
            if (!empty($input['send_email']) && !empty($input['email'])) {

                if ($shipments->isEmpty()) {
                    return Redirect::back()->with('warning', 'Reembolso gravado com sucesso. Não foi enviado o e-mail porque nenhum dos reembolsos selecionados foi marcado como pago.');
                }

                RefundControl::sendEmail($emails['valid'], $shipments);
            }


            $redirect = Redirect::back();

            if ($input['print_proof']) {
                $queryStr = implode('&id[]=', $ids);
                $printProof = route('admin.printer.refunds.customers.proof') . '?id[]=' . $queryStr;
                $redirect = $redirect->with('printProof', $printProof);
            }

            if ($input['print_summary']) {
                $queryStr = implode('&id[]=', $ids);
                $printSummary = route('admin.printer.refunds.customers.summary') . '?id[]=' . $queryStr;
                $redirect = $redirect->with('printSummary', $printSummary);
            }

            return $redirect->with('success', 'Registos selecionados alterados com sucesso.');
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage() . ' on file ' . $e->getFile() . ' line ' . $e->getLine());
        }
    }


    /**
     * Import refunds file
     *
     * @param Request $request
     * @return mixed
     */
    public function import(Request $request)
    {
        switch ($request->get('refund_file_type')) {
            case 'envialia':
                $result = $this->importEnvialia($request);
                break;
            case 'gls':
                $result = $this->importGLS($request);
                break;
            case 'ctt':
                $result = $this->importCtt($request);
                break;
            case 'ctt_spain':
                $result = $this->importCttSpain($request);
                break;
            case 'tipsa':
                $result = $this->importTipsa($request);
                break;
            case 'via_directa':
                $result = $this->importViaDirecta($request);
                break;
            default:
                $result = Response::json([
                    'success'   => false,
                    'html'      => null,
                    'feedback'  => 'O ficheiro não é suportado.',
                    'totalErrors' => 0
                ]);
                break;
        }

        return $result;
    }

    /**
     * Import refunds file
     *
     * @param Request $request
     * @return mixed
     */
    public function importEnvialia(Request $request)
    {

        $filepath = $request->file('file');

        $provider    = 'Enviália';
        $rows        = [];
        $totalErrors = 0;
        Excel::load($filepath, function ($reader) use ($request, &$rows, &$totalErrors) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $reader->each(function ($row) use (&$rows, &$totalErrors) {
                $row = json_decode(json_encode($row), true);


                $date = trim(@$row['fecha_pago']['date']);

                if (!empty($date)) { //só se a data de pago está preenchida

                    $date = new Date($date);
                    $date = $date->format('Y-m-d');

                    $trk  = trim($row['referencia']);
                    $trk  = str_pad(substr($trk, 0, 15), 15, '0', STR_PAD_LEFT);
                    $trk  = str_replace('TRK', '', $trk);

                    $trkProvider = trim($row['albaran']);

                    $numDoc = trim($row['numero_de_documento']);
                    $banco  = trim($row['nombre_banco_ing']);

                    $message = $numDoc ? $numDoc : '';
                    $message .= ($numDoc ? ' - ' : '') . $banco;

                    $numDoc = removeAccents($numDoc);
                    $banco  = removeAccents($banco);

                    $method = 'transfer';
                    if (
                        str_contains($numDoc, 'TALON') || str_contains($numDoc, 'CHEQUE')
                        || str_contains($banco, 'TALON') || str_contains($banco, 'CHEQUE')
                    ) {
                        $method = 'check';
                    }

                    $shipment = Shipment::filterAgencies()
                        ->where('tracking_code', $trk)
                        ->first();

                    $success = true;
                    if (!$shipment) {
                        $success = false;
                        $totalErrors++;
                    }

                    $rows[] = [
                        'success'       => $success,
                        'trk'           => @$shipment->tracking_code,
                        'provider_trk'  => $trkProvider,
                        'message'       => $success ? $message : 'Envio não encontrado',
                        'method'        => $method,
                        'date'          => $date,
                        'shipment'      => @$shipment->id,
                        'value'         => @$shipment->charge_price
                    ];
                }
            });
        });

        $totalSuccess = count($rows) - $totalErrors;

        $result = [
            'success'     => true,
            'feedback'    => 'Reembolsos importados',
            'html'        => view('admin.refunds.customers.partials.import_refunds_result', compact('rows', 'totalSuccess', 'totalErrors', 'provider'))->render(),
            'totalErrors' => $totalErrors,
            'close'       => false,
        ];

        return Response::json($result);
    }

    /**
     * Import refunds file
     *
     * @param Request $request
     * @return mixed
     */
    public function importTipsa(Request $request)
    {

        $filepath = $request->file('file');

        $provider    = 'Tipsa';
        $rows        = [];
        $totalErrors = 0;
        Excel::load($filepath, function ($reader) use ($request, &$rows, &$totalErrors) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $reader->each(function ($row) use (&$rows, &$totalErrors) {
                $row = json_decode(json_encode($row), true);

                try {
                    $date = trim(@$row['fecha_pago']['date']);
                } catch (\Exception $e) {
                    dd($row);
                }

                if (!empty($date)) { //só se a data de pago está preenchida

                    $date = new Date($date);
                    $date = $date->format('Y-m-d');

                    $trk  = trim($row['referencia']);
                    $trk  = str_pad(substr($trk, 0, 15), 15, '0', STR_PAD_LEFT);
                    $trk  = str_replace('TRK', '', $trk);

                    $trkProvider = trim($row['albaran']);

                    $numDoc = trim($row['numero_de_documento']);
                    $banco  = trim($row['nombre_banco_ing']);

                    $message = $numDoc ? 'Doc: ' . $numDoc : '';
                    $message .= ($numDoc ? ' / ' : '') . 'Banco ' . $banco;

                    $numDoc = removeAccents($numDoc);
                    $banco  = removeAccents($banco);

                    $method = 'transfer';
                    if (
                        str_contains($numDoc, 'TALON') || str_contains($numDoc, 'CHEQUE')
                        || str_contains($banco, 'TALON') || str_contains($banco, 'CHEQUE')
                    ) {
                        $method = 'check';
                    }

                    $shipment = Shipment::filterAgencies()
                        ->where('tracking_code', $trk)
                        ->first();

                    $success = true;
                    if (!$shipment) {
                        $success = false;
                        $totalErrors++;
                    }

                    $rows[] = [
                        'success'       => $success,
                        'trk'           => @$shipment->tracking_code,
                        'provider_trk'  => $trkProvider,
                        'message'       => $success ? $message : 'Envio não encontrado',
                        'method'        => $method,
                        'date'          => $date,
                        'shipment'      => @$shipment->id,
                        'value'         => @$shipment->charge_price
                    ];
                }
            });
        });

        $totalSuccess = count($rows) - $totalErrors;

        $result = [
            'success'     => true,
            'feedback'    => 'Reembolsos importados',
            'html'        => view('admin.refunds.customers.partials.import_refunds_result', compact('rows', 'totalSuccess', 'totalErrors', 'provider'))->render(),
            'totalErrors' => $totalErrors,
            'close'       => false,
        ];

        return Response::json($result);
    }

    /**
     * Import refunds file
     *
     * @param Request $request
     * @return mixed
     */
    public function importCtt(Request $request)
    {
        $filepath = $request->file('file');
        $totalErrors = 0;
        $provider    = 'CTT';
        $rows        = [];

        Excel::load($filepath, function ($reader) use ($request, &$rows, &$totalErrors) {
            // Skip 6 rows
            $reader->setHeaderRow(6);

            $reader->each(function ($row) use (&$rows, &$totalErrors) {

                $row = json_decode(json_encode($row), true);

                if (!empty($row)) { //só se a data de pago está preenchida
                    // Gets the TRK
                    $trk  = trim($row['referencia_de_cliente'] ?? '');
                    $trk  = str_pad(substr($trk, 0, 15), 15, '0', STR_PAD_LEFT);
                    $trk  = str_replace('TRK', '', $trk);

                    $trkProvider = trim($row['objeto'] ?? 'N/A');
                    if (strlen($trkProvider) < 13) { // Removes the possibility to find the shipment but still shows in the end
                        $trkProvider .= '.';
                    }

                    $date = new Date($row['data_de_processamento'] ?? '');
                    $date = $date->format('Y-m-d');

                    // $paymentMethod = trim($row['forma_de_pagamento']);
                    switch (trim($row['forma_de_pagamento'] ?? '')) {
                            // Enum 'money','check','transfer','mb'
                        case 'D':
                            $method = 'money';
                            break;
                        case 'M':
                            $method = 'transfer';
                            break;
                        case 'C':
                            $method = 'check';
                            break;
                        default:
                            $method = 'transfer';
                            break;
                    }

                    $shipment = Shipment::select('id', 'tracking_code', 'charge_price')
                        ->filterAgencies()
                        ->where(function ($q) use ($trk, $trkProvider) {
                            $q->where('provider_tracking_code', 'like', '%' . $trkProvider . '%')
                                ->orWhere('tracking_code', $trk);
                        })->first();

                    $success = true;
                    if (empty($shipment)) {
                        $success = false;
                        $totalErrors++;
                    }

                    $rows[] = [
                        'success'       => $success,
                        'trk'           => $shipment->tracking_code ?? $trk ?? null,
                        'provider_trk'  => $trkProvider,
                        'message'       => $success ? 'Encontrado em sistema' : 'Envio não encontrado',
                        'method'        => $method,
                        'date'          => $date,
                        'shipment'      => @$shipment->id,
                        'value'         => $shipment->charge_price ?? '', // $row['montante'] ??
                    ];
                }
            });
        });
        $totalSuccess = count($rows) - $totalErrors;

        $result = [
            'success'     => true,
            'feedback'    => 'Reembolsos importados',
            'html'        => view('admin.refunds.customers.partials.import_refunds_result', compact('rows', 'totalSuccess', 'totalErrors', 'provider'))->render(),
            'totalErrors' => $totalErrors,
            'close'       => false,
        ];

        return Response::json($result);
    }

    /**
     * Import refunds file
     *
     * @param Request $request
     * @return mixed
     */
    public function importCttSpain(Request $request)
    {
        $filepath = $request->file('file');
        $totalErrors = 0;
        $provider    = 'CTT';
        $rows        = [];

        Excel::load($filepath, function ($reader) use ($request, &$rows, &$totalErrors) {
            $reader->each(function ($row) use (&$rows, &$totalErrors) {

                $row = json_decode(json_encode($row), true);

                if (!empty($row)) { //só se a data de pago está preenchida
                    // Gets the TRK
                    $trk  = trim($row['destino'] ?? '');
                    $trk  = str_replace('Refª TRK', '', $trk);
                    $trk  = explode(' -', $trk);
                    $trk  = $trk[0];

                    $trkProvider = trim($row['no_objeto'] ?? 'N/A');
                    if (strlen($trkProvider) < 13) { // Removes the possibility to find the shipment but still shows in the end
                        $trkProvider .= '.';
                    }

                    $date = new Date();
                    $date = $date->format('Y-m-d');

                    $shipment = Shipment::select('id', 'tracking_code', 'charge_price')
                        ->filterAgencies()
                        ->where(function ($q) use ($trk, $trkProvider) {
                            $q->where('provider_tracking_code', 'like', '%' . $trkProvider . '%')
                                ->orWhere('tracking_code', $trk);
                        })->first();

                    $success = true;
                    if (empty($shipment)) {
                        $success = false;
                        $totalErrors++;
                    }

                    $rows[] = [
                        'success'       => $success,
                        'trk'           => $shipment->tracking_code ?? $trk ?? null,
                        'provider_trk'  => $trkProvider,
                        'message'       => $success ? 'Encontrado em sistema' : 'Envio não encontrado',
                        'method'        => 'transfer',
                        'date'          => $date,
                        'shipment'      => @$shipment->id,
                        'value'         => $shipment->charge_price ?? '', // $row['montante'] ??
                    ];
                }
            });
        });

        $totalSuccess = count($rows) - $totalErrors;

        $result = [
            'success'     => true,
            'feedback'    => 'Reembolsos importados',
            'html'        => view('admin.refunds.customers.partials.import_refunds_result', compact('rows', 'totalSuccess', 'totalErrors', 'provider'))->render(),
            'totalErrors' => $totalErrors,
            'close'       => false,
        ];

        return Response::json($result);
    }

    /**
     * Import refunds file in txt, old function not used
     *
     * @param Request $request
     * @return mixed
     */
    public function importCttTxt(Request $request)
    {

        $filepath = $request->file('file');

        $it = 0;
        $provider    = 'CTT';
        $rows        = [];
        $totalErrors = 0;
        foreach (file($filepath) as $line) {
            if ($it > 0 && !str_contains($line, 'Dia:') && !str_contains($line, 'Valor Total') && !str_contains($line, 'Quantidade de Objectos')) {

                $line   = explode(';', $line);

                $method = 'transfer';
                $trkProvider = @$line['1'];
                $date        = @$line['2'];

                $trk  = substr(@$line['9'], 0, 15);
                $trk  = str_replace('TRK', '', $trk);

                //find shipment
                $shipment = Shipment::filterAgencies()
                    //->where('tracking_code', $trk)
                    ->where('provider_tracking_code', 'like', '%' . $trkProvider . '%')
                    ->first();

                $success = true;
                if (!$shipment) {
                    $success = false;
                    $totalErrors++;
                }

                $rows[] = [
                    'success'       => $success,
                    'trk'           => @$shipment->tracking_code,
                    'provider_trk'  => $trkProvider,
                    'message'       => $success ? 'Encontrado em sistema' : 'Envio não encontrado',
                    'method'        => $method,
                    'date'          => $date,
                    'shipment'      => @$shipment->id,
                    'value'         => @$shipment->charge_price
                ];
            }

            $it++;
        }

        $totalSuccess = count($rows) - $totalErrors;

        $result = [
            'success'     => true,
            'feedback'    => 'Reembolsos importados',
            'html'        => view('admin.refunds.customers.partials.import_refunds_result', compact('rows', 'totalSuccess', 'totalErrors', 'provider'))->render(),
            'totalErrors' => $totalErrors,
            'close'       => false,
        ];

        return Response::json($result);
    }

    /**
     * Import refunds file
     *
     * @param Request $request
     * @return mixed
     */
    public function importGLS(Request $request)
    {


        $filepath = $request->file('file');

        $provider    = 'GLS';
        $rows        = [];
        $totalErrors = 0;
        Excel::load($filepath, function ($reader) use ($request, &$rows, &$totalErrors) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $reader->each(function ($row) use (&$rows, &$totalErrors) {
                $row = json_decode(json_encode($row), true);

                if (!empty(@$row['data_transferencia']) && !empty(@$row['referencia_cliente'])) {
                    $method = 'transfer';

                    $date = isset($row['data_transferencia']['date']) ? $row['data_transferencia']['date'] : trim($row['data_transferencia']);
                    $date = new Date($date);
                    $date = $date->format('Y-m-d');

                    $trk  = $row['referencia_cliente'];
                    if (is_array($trk)) {
                        $trk = implode('_', $trk);
                    }

                    $trk  = str_pad(substr(trim($trk), 0, 12), 12, '0', STR_PAD_LEFT);
                    $trkProvider = is_array($row['n_volume']) ? '' : $row['n_volume'];

                    //find shipment
                    $shipment = Shipment::filterAgencies()
                        ->where('tracking_code', $trk)
                        ->first();

                    $success = true;
                    if (!$shipment) {
                        $success = false;
                        $totalErrors++;
                    }

                    $rows[] = [
                        'success'       => $success,
                        'trk'           => @$shipment->tracking_code,
                        'provider_trk'  => $trkProvider,
                        'message'       => $success ? 'Encontrado em sistema' : 'Envio não encontrado',
                        'method'        => $method,
                        'date'          => $date,
                        'shipment'      => @$shipment->id,
                        'value'         => @$shipment->charge_price
                    ];
                }
            });
        });

        $totalSuccess = count($rows) - $totalErrors;

        $result = [
            'success'     => true,
            'feedback'    => 'Reembolsos importados',
            'html'        => view('admin.refunds.customers.partials.import_refunds_result', compact('rows', 'totalSuccess', 'totalErrors', 'provider'))->render(),
            'totalErrors' => $totalErrors,
            'close'       => false,
        ];

        return Response::json($result);
    }

    /**
     * Import refunds file
     *
     * @param Request $request
     * @return mixed
     */
    public function importViaDirecta(Request $request)
    {

        $filepath = $request->file('file');

        $provider    = 'ViaDirecta';
        $rows        = [];
        $totalErrors = 0;
        Excel::load($filepath, function ($reader) use ($request, &$rows, &$totalErrors) {

            $test = $reader->toArray();
            if (is_array(@$test[0][0])) { //multiple sheets
                $reader = $reader->first();
            }

            $reader->each(function ($row) use (&$rows, &$totalErrors) {
                $row = json_decode(json_encode($row), true);

                if (!empty(@$row['objecto']) && !empty(@$row['valor_pago'])) {

                    $method = 'transfer';

                    if (str_contains(@$row['tipo_pagamento'], 'POS')) {
                        $method = 'mb';
                    }

                    $date = new Date();
                    $date = $date->format('Y-m-d');

                    $trk  = trim($row['objecto']);
                    $trk  = str_replace('VD', '', $trk); //str_pad(substr(trim($trk), 0, 12), 12, '0', STR_PAD_LEFT);

                    //find shipment
                    $shipment = Shipment::filterAgencies()
                        ->where('tracking_code', $trk)
                        ->first();

                    $success = true;
                    if (!$shipment) {
                        $success = false;
                        $totalErrors++;
                    }

                    $rows[] = [
                        'success'       => $success,
                        'trk'           => @$shipment->tracking_code,
                        'provider_trk'  => @$shipment->provider_tracking_code,
                        'message'       => $success ? 'Encontrado em sistema' : 'Envio não encontrado',
                        'method'        => $method,
                        'date'          => $date,
                        'shipment'      => @$shipment->id,
                        'value'         => @$shipment->charge_price
                    ];
                }
            });
        });

        $totalSuccess = count($rows) - $totalErrors;

        $result = [
            'success'     => true,
            'feedback'    => 'Reembolsos importados',
            'html'        => view('admin.refunds.customers.partials.import_refunds_result', compact('rows', 'totalSuccess', 'totalErrors', 'provider'))->render(),
            'totalErrors' => $totalErrors,
            'close'       => false,
        ];

        return Response::json($result);
    }

    /**
     * Import refunds file
     *
     * @param Request $request
     * @return mixed
     */
    public function confirmImport(Request $request)
    {

        $input = $request->all();

        if (empty($input['method'])) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Nada para importar',
                'close'    => true,
            ]);
        }

        foreach ($input['method'] as $shipmentId => $method) {

            $date = @$input['date'][$shipmentId];
            $date = empty($date) ? date('Y-m-d') : $date;

            $refundControl = RefundControl::firstOrNew([
                'shipment_id' => $shipmentId
            ]);

            $refundControl->received_date   = $date;
            $refundControl->received_method = $method;
            $refundControl->save();
        }

        return Response::json([
            'result'   => true,
            'feedback' => 'Registos importados com sucesso',
            'close'    => true,
        ]);
    }
}
