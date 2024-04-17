<?php

namespace App\Http\Controllers\Admin\Prospects;

use App\Http\Controllers\Admin\Customers\CustomersController;
use App\Models\Billing\Item;
use App\Models\BillingZone;
use App\Models\CustomerBusinessHistory;
use App\Models\CustomerService;
use App\Models\PaymentCondition;
use App\Models\PriceTable;
use App\Models\Route;
use App\Models\Service;
use App\Models\ServiceGroup;
use App\Models\ShippingExpense;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Auth;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\User;
use Croppa;
use Setting;

class ProspectsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'prospects';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',prospects']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        $status = array(
            '1' => 'Ativo',
            '0' => 'Bloqueado'
        );
        
        $types = CustomerType::filterSource()->pluck('name', 'id')->toArray();
        
        $agencies = Auth::user()->listsAgencies();
        
        $sellers = User::remember(5)
                        ->filterAgencies()
                        ->isSeller()
                        ->where('id', '>', 1)
                        ->pluck('name', 'id')
                        ->toArray();

        $pricesTables = PriceTable::filterAgencies()
            ->isActive()
            ->pluck('name', 'id')
            ->toArray();

        $routes = Route::listsWithCode(Route::filterSource()
            ->ordered()
            ->get());

        return $this->setContent('admin.prospects.index', compact('status', 'types', 'agencies', 'sellers', 'pricesTables', 'routes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $types = CustomerType::pluck('name', 'id')->toArray();

        $agencies = Auth::user()->listsAgencies();

        return view('admin.prospects.create', compact('types', 'agencies'))->render();
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
//        
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        $action = 'Editar Potencial Cliente';
        
        $prospect = Customer::filterAgencies()
                            ->isProspect()
                            ->findOrFail($id);

        $formOptions = array('route' => array('admin.prospects.update', $prospect->id), 'method' => 'PUT');

        $types = CustomerType::pluck('name', 'id')->toArray();
        
        $agencies = Auth::user()->listsAgencies();

        $sellers = User::remember(5)
                        ->filterAgencies()
                        ->isSeller()
                        ->where('id', '>', 1)
                        ->pluck('name', 'id')
                        ->toArray();

        $paymentConditions = PaymentCondition::filterSource()
            ->isPurchasesVisible()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $allServices = Service::filterAgencies()
            ->showOnPricesTable()
            ->ordered()
            ->get();

        $servicesList = $allServices->pluck('name', 'id')->toArray();

        $servicesGroups = ServiceGroup::filterSource()
            ->ordered()
            ->get();

        $servicesGroupsList = ServiceGroup::filterSource()
            ->pluck('name', 'code')
            ->toArray();

        $customerCollection = new CustomersController();
        $pricesTableData  = $customerCollection->getPricesTableData($allServices, $prospect, $servicesGroupsList);
        $rowsWeight       = @$pricesTableData['rows'];
        $pricesTableData  = $pricesTableData['prices'];

        $complementarServices = ShippingExpense::filterSource()
            ->isCustomerCustomization()
            ->get();

        $defaultWeights = explode(',',Setting::get('default_weights'));

        $pricesTables = PriceTable::filterAgencies()
            ->isActive()
            ->pluck('name', 'id')
            ->toArray();

        $routes = Route::listsWithCode(Route::filterSource()
            ->ordered()
            ->get());

        $billingZonesList = BillingZone::remember(config('cache.query_ttl'))
            ->cacheTags(BillingZone::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'code')
            ->toArray();

        $allBillingItems = Item::filterSource()
            ->isCustomerCustomizable()
            ->get();

        $data = compact(
            'prospect',
            'action',
            'formOptions',
            'types',
            'agencies',
            'sellers',
            'routes',
            'defaultWeights',
            'rowsWeight',
            'pricesTableData',
            'complementarServices',
            'servicesList',
            'servicesGroups',
            'servicesGroupsList',
            'pricesTables',
            'paymentConditions',
            'allServices',
            'billingZonesList',
            'allBillingItems'
        );

        return $this->setContent('admin.prospects.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null) {
        
        $user = Auth::user();
        
        $input = $request->all();
        $input['seller_id']    = $request->get('seller_id', null);
        $input['pickup_daily'] = $request->get('pickup_daily', false);
        $input['seller_id']    = ($user->hasRole(config('permissions.role.seller')) && !isset($input['seller_id'])) ? $user->id : $input['seller_id'];
        
        $prospect  = Customer::filterAgencies()
                            ->isProspect()
                            ->findOrNew($id);
        
        $exists = $prospect->exists ? true : false;

        if($exists) {
            if (isset($input['business_status']) && $prospect->business_status != $input['business_status']) {
                $history = new CustomerBusinessHistory();
                $history->customer_id = $id;
                $history->status      = $input['business_status'];
                $history->operator_id     = Auth::user()->id;
                $history->save();
            }
        }

        if ($prospect->validate($input)) {
            $prospect->fill($input);
            $prospect->is_prospect = 1;
            $prospect->save();

            if($exists) {
                return Redirect::back()->with('success', 'Dados gravados com sucesso.');
            } else {

                $history = new CustomerBusinessHistory();
                $history->customer_id = $prospect->id;
                $history->status      = 'pending';
                $history->operator_id = Auth::user()->id;
                $history->save();

                return Redirect::route('admin.prospects.edit', $prospect->id)->with('success', 'Dados gravados com sucesso.');
            }
        }
        
        return Redirect::back()->withInput()->with('error', $prospect->errors()->first());
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = Customer::filterSource()
                    ->filterAgencies()
                    ->isFinalConsumer(false)
                    ->isProspect()
                    ->whereId($id)
                    ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o cliente.');
        }

        return Redirect::route('admin.prospects.index')->with('success', 'Cliente removido com sucesso.');
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
        
        $result = Customer::filterSource()
                    ->whereIn('id', $ids)
                    ->isProspect()
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

        $data = Customer::with('seller')
            ->filterSource()
            ->filterAgencies()
            ->filterSeller()
            ->isProspect()
            ->select();

        //filter active
        $value = $request->active;
        if($request->has('active')) {
            $data = $data->where('active', $value);
        }
        
        //filter type
        $value = $request->type_id;
        if($request->has('type_id')) {
            $data = $data->where('type_id', $value);
        }
        
        //filter country
        $value = $request->country;
        if($request->has('country')) {
            $data = $data->where('country', $value);
        }
        
        //filter agency
        $value = $request->agency;
        if($request->has('agency')) {
            $data = $data->where('agency_id', $value);
        }
        
        //filter seller
        $value = $request->seller;
        if($request->has('seller')) {
            $data = $data->where('seller_id', $value);
        }

        //filter business status
        $value = $request->status;
        if($request->has('status')) {
            $data = $data->where('business_status', $value);
        }

        if(Auth::user()->isGuest()) {
            $data = $data->where('agency_id', '99999'); //hide data to gest agency role
        }

        return Datatables::of($data)
                        ->add_column('photo', function($row) {
                            return view('admin.partials.datatables.photo', compact('row'))->render();
                        })
                        ->edit_column('name', function($row) {
                            return view('admin.prospects.datatables.name', compact('row'))->render();
                        })
                        ->edit_column('seller', function($row) {
                            return @$row->seller->name;
                        })
                        ->edit_column('phone', function($row) {
                            return view('admin.prospects.datatables.contacts', compact('row'))->render();
                        })
                        ->edit_column('country', function($row) {
                            return view('admin.prospects.datatables.country', compact('row'))->render();
                        })
                        ->edit_column('business_status', function($row) {
                            return view('admin.prospects.datatables.business_status', compact('row'))->render();
                        })
                        ->edit_column('created_at', function($row) {
                            return view('admin.partials.datatables.created_at', compact('row'))->render();
                        })
                        ->add_column('select', function($row) {
                            return view('admin.partials.datatables.select', compact('row'))->render();
                        })
                        ->add_column('actions', function($row) {
                            return view('admin.prospects.datatables.actions', compact('row'))->render();
                        })
                        ->make(true);
    }
    
    /**
     * Convert prospect into contact
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function convert($id) {

        $prospect = Customer::filterSource()
                ->filterAgencies()
                ->isProspect()
                ->findOrNew($id);

        if(empty($prospect->agency_id)) {
            return Redirect::route('admin.customers.edit', $prospect->id)->with('error', 'Para converter em cliente tem de primeiro preencher o campo "Agencia".');
        }
        
        $prospect->is_prospect = 0;
        $prospect->setCode();

        $history = new CustomerBusinessHistory();
        $history->customer_id = $prospect->id;
        $history->status      = null;
        $history->message     = 'Convertido em ficha de cliente.';
        $history->operator_id = Auth::user()->id;
        $history->save();
        
        return Redirect::route('admin.customers.edit', $prospect->id)->with('success', 'Potencial cliente convertido em cliente com sucesso.');
    }

    /**
     * Activate all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massActivate(Request $request) {

        $ids = explode(',', $request->ids);

        $result = Customer::whereIn('id', $ids)->update(['is_prospect' => 0]);

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível ativar os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados ativados com sucesso.');
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableBusinessHistory(Request $request, $customerId) {

        $data = CustomerBusinessHistory::with('operator')
            ->where('customer_id', $customerId)
            ->select();

        return Datatables::of($data)
            ->edit_column('status', function($row) {
                return '<div class="text-center"><span class="label" style="background: '. trans('admin/prospects.status-label.' . $row->status) .'">' .  trans('admin/prospects.status.' . $row->status) . '</span></div>';
            })
            ->edit_column('operator_id', function($row) {
                return @$row->operator->name;
            })
            ->edit_column('created_at', function($row) {
                return $row->created_at->format('Y-m-d H:i');
            })
            ->make(true);
    }
}
