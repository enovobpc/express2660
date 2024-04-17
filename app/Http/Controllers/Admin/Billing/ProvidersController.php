<?php

namespace App\Http\Controllers\Admin\Billing;

use App\Models\PackType;
use App\Models\PaymentCondition;
use App\Models\User;
use Html, DB, Response, Excel, Auth, Setting;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Shipment;
use App\Models\Service;
use App\Models\Provider;
use App\Models\Agency;
use App\Models\ShippingStatus;
use Mpdf\Mpdf;

class ProvidersController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'billing-providers';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',billing_providers']);
        validateModule('billing_providers');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $years = yearsArr(2016, date('Y'), true);

        $agencies = Auth::user()->listsAgencies();

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $paymentConditions = PaymentCondition::filterSource()
            ->isSalesVisible()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $data =  compact(
            'paymentConditions',
            'years',
            'agencies',
            'providers'
        );

        return $this->setContent('admin.billing.providers.index', $data);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {

        $year  = $request->has('year') ? $request->year : date('Y');
        $month = $request->has('month') ? $request->month : date('m');

        $provider = Provider::filterAgencies()->firstOrNew(['id' => $id]);

        if(!$provider->exists) {
            $provider->id = $id;
            $provider->code = '';
            $provider->name = 'Envios sem fornecedor associado';
        }

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $status = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingStatus::CACHE_TAG)
            ->filterSources()
            ->isVisible()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::listOperators(User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->ignoreAdmins()
            ->orderBy('source', 'asc')
            ->orderBy('code', 'asc')
            ->orderBy('name', 'asc')
            ->get(['source', 'id', 'name', 'vehicle', 'provider_id']), true);

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'year',
            'month',
            'provider',
            'services',
            'status',
            'operators',
            'providers'
        );

        return $this->setContent('admin.billing.providers.show', $data);
    }

    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {

        $year  = $request->has('year') ? $request->year : date('Y');
        $month = $request->has('month') ? $request->month : date('m');

        $agencies = Auth::user()->agencies;

        $billing = [
            'shipments.id',
            'shipments.agency_id',
            'shipments.recipient_agency_id',
            'shipments.provider_id',
            'providers.name as name',
            'providers.color as color',
            DB::raw('count(total_price) as shipments'),
            DB::raw('sum(is_collection) as collections'),
            DB::raw('sum(cost_billing_subtotal) as cost'),
            DB::raw('sum(billing_subtotal) as total'),
            DB::raw('sum(charge_price) as charge_price'),
            DB::raw('MONTH(date) as month'),
            DB::raw('YEAR(date) as year')
        ];
        
        $data = Shipment::leftjoin('providers', 'shipments.provider_id', '=', 'providers.id')
                ->where('providers.source', config('app.source'))
                ->whereRaw('YEAR(date) = '.$year)
                ->whereRaw('MONTH(date) = '.$month)
                ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
                ->groupBy('provider_id')
                ->orderBy('provider_id')
                ->select($billing);

        if($agencies) {
            $data = $data->whereIn('shipments.agency_id', $agencies);
        }

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.billing.providers.datatables.provider', compact('row'))->render();
                })
                ->edit_column('month', function($row) {
                    return trans('datetime.month-tiny.'.$row->month).' '.$row->year;
                })
                 ->edit_column('shipments', function($row) {
                    return $row->shipments - $row->collections;
                })
                ->edit_column('charge_price', function($row) {
                    return money(empty($row->charge_price) ? 0 : $row->charge_price, Setting::get('app_currency'));
                })
                ->edit_column('cost', function($row) {
                    return '<b>' . money($row->cost, Setting::get('app_currency')) . '</b>';
                })
                ->edit_column('total', function($row) {
                    return money($row->total, Setting::get('app_currency'));
                })
                ->edit_column('profit', function($row) {
                    $profit = (($row->total_price + $row->total_expenses) - ($row->cost_price + $row->total_expenses_cost));
                    return view('admin.billing.providers.datatables.profit', compact('row', 'profit'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.billing.providers.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }

    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatableShipments(Request $request, $providerId) {

        $appMode = Setting::get('app_mode');
        $year  = $request->has('year')  ? $request->year : date('Y');
        $month = $request->has('month') ? $request->month : date('m');

        //status
        $statusList = ShippingStatus::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->get(['id', 'name', 'color', 'is_final']);
        $finalStatus = $statusList->filter(function($item) { return $item->is_final; })->pluck('id')->toArray();
        $statusList  = $statusList->groupBy('id')->toArray();

        //services
        $servicesList = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->get();
        $servicesList = $servicesList->groupBy('id')->toArray();

        //operator
        $operatorsList = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterAgencies()
            ->get(['source', 'id', 'code', 'code_abbrv', 'name', 'vehicle', 'provider_id']);
        $operatorsList = $operatorsList->groupBy('id')->toArray();

        //providers
        $providersList = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->get();
        $providersList = $providersList->groupBy('id')->toArray();

        //agencies
        $allAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->withTrashed()
            ->get(['name', 'code', 'id', 'color', 'source']);
        $agencies = $allAgencies->groupBy('id')->toArray();

        $packTypes = PackType::remember(config('cache.query_ttl'))
            ->cacheTags(PackType::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'code')
            ->toArray();

        $data = Shipment::filterMyAgencies()
                ->whereRaw('MONTH(date) = '.$month)
                ->whereRaw('YEAR(date) = '.$year)
                ->where('status_id', '<>', ShippingStatus::CANCELED_ID)
                ->select();
        
        if($providerId != '999999999') {
            $data = $data->whereProviderId($providerId);
        } else {
            $data = $data->whereNull('provider_id');
        }

        if(Auth::user()->isGuest()) {
            $data = $data->where('service_id', '99999'); //hide data to gest agency role
        }

        //filter date
        $dtMin = $request->get('date_min');
        if($request->has('date_min')) {
            $dtMax = $dtMin;
            if($request->has('date_max')) {
                $dtMax = $request->get('date_max');
            }

            $data = $data->whereBetween('date', [$dtMin, $dtMax]);
        }

        //filter status
        $value = $request->get('status');
        if($request->has('status')) {
            $data = $data->whereIn('status_id', $value);
        }

        //filter service
        $value = $request->get('service');
        if($request->has('service')) {
            $data = $data->whereIn('service_id', $value);
        }

        //filter provider
        $value = $request->get('provider');
        if($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        //filter operator
        $value = $request->operator;
        if($request->has('operator')) {
            $data = $data->whereIn('operator_id', $value);
        }

        //filter customer
        $value = $request->customer;
        if($request->has('customer')) {
            $data = $data->where('customer_id', $value);
        }

        //filter charge
        $value = $request->charge;
        if($request->has('charge')) {
            if($value == 0) {
                $data = $data->whereNull('charge_price');
            } elseif($value == 1) {
                $data = $data->whereNotNull('charge_price');
            }
        }

        //filter payment at recipient
        $value = $request->payment_recipient;
        if($request->has('payment_recipient')) {
            if($value == '0') {
                $data = $data->where('payment_at_recipient', 0);
            } elseif($value == '1') {
                $data = $data->where('payment_at_recipient', 1);
            }
        }

        //filter conferred
        $value = $request->conferred;
        if($request->has('conferred')) {
            if($value == '0') {
                $data = $data->where(function($q){
                    $q->whereNull('provider_conferred');
                    $q->orWhere('provider_conferred', 0);
                });
            } elseif($value == '1') {
                $data = $data->where('provider_conferred', 1);
            }
        }

        return Datatables::of($data)
            ->edit_column('tracking_code', function($row) use($agencies) {
                return view('admin.shipments.shipments.datatables.tracking', compact('row', 'agencies'))->render();
            })
            ->edit_column('sender_name', function($row) {
                return view('admin.shipments.shipments.datatables.sender', compact('row'))->render();
            })
            ->edit_column('recipient_name', function($row) {
                return view('admin.shipments.shipments.datatables.recipient', compact('row'))->render();
            })
            ->edit_column('service_id', function($row) use($agencies, $servicesList, $providersList) {
                return view('admin.shipments.shipments.datatables.service', compact('row', 'agencies', 'servicesList', 'providersList'))->render();
            })
            ->edit_column('status_id', function($row) use($statusList, $operatorsList) {
                return view('admin.shipments.shipments.datatables.status', compact('row', 'statusList', 'operatorsList'))->render();
            })
            ->edit_column('volumes', function($row) use($servicesList, $packTypes, $appMode) {
                return view('admin.shipments.shipments.datatables.volumes', compact('row', 'servicesList', 'packTypes', 'appMode'))->render();
            })
            ->edit_column('date', function($row) use($statusList) {
                return view('admin.shipments.shipments.datatables.date', compact('row', 'statusList'))->render();
            })
            ->edit_column('cost_price', function($row) {
                return view('admin.billing.providers.datatables.shipments.cost_price', compact('row'))->render();
            })
            ->edit_column('total_price', function($row) {
                return view('admin.billing.providers.datatables.shipments.total_price', compact('row'))->render();
            })
            ->add_column('profit', function($row) {
                $profit = (($row->shipping_price + $row->expenses_price) - ($row->cost_price + $row->cost_expenses_price));
                return view('admin.billing.providers.datatables.shipments.profit', compact('row', 'profit'))->render();
            })
            ->edit_column('provider_conferred', function($row) {
                return view('admin.billing.providers.datatables.shipments.conferred', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.billing.customers.datatables.shipments.actions', compact('row'))->render();
            })
            ->make(true);
    }


    /**
     * Confirm selected shipments
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function massConfirmShipments(Request $request) {

        $ids = $request->ids;
        $ids = explode(',', $ids);

        $shipments = Shipment::filterAgencies()
                        ->whereIn('id', $ids)
                        ->get();

        foreach ($shipments as $shipment) {
            if($request->has('confirm_status')) {
                $shipment->provider_conferred = $request->get('confirm_status', false);
            } else {
                $shipment->provider_conferred = !$shipment->provider_conferred;
            }
            $shipment->save();
        }

        $feedback = 'Envio confirmado com sucesso.';

        if(count($ids) > 1) {
            $feedback = 'Envios selecionados confirmados com sucesso.';
        }

        $row = $shipment;

        if($request->ajax()) {
            return Response::json([
                'result'   => true,
                'feedback' => $feedback,
                'html'     => view('admin.billing.providers.datatables.shipments.conferred', compact('row'))->render()
            ]);
        }

        return Redirect::back()->with('success', $feedback);
    }
}
