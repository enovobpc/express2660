<?php

namespace App\Http\Controllers\Admin\Providers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\PaymentCondition;
use App\Models\ProviderCategory;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceType;
use App\Models\ServiceGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\ServiceVolumetricFactor;
use App\Models\WebserviceMethod;
use App\Models\ProviderService;
use App\Models\ShippingExpense;
use App\Models\ZipCode\AgencyZipCode;
use App\Models\PriceTable;
use App\Models\BillingZone;
use App\Models\Provider;
use App\Models\Service;
use App\Models\Agency;
use App\Models\InvoiceGateway;
use Html, Auth, DB, Response, Cache, Setting;

class ProvidersController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'providers';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',providers']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $agencies = Auth::user()->listsAgencies();

        $categories = ProviderCategory::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $paymentConditions = PaymentCondition::filterSource()
            ->isPurchasesVisible()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        return $this->setContent('admin.providers.index', compact('agencies', 'categories', 'paymentConditions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $type = $request->get('type', 'others');

        $provider = new Provider();
        $provider->type = $type;
        $provider->code = $provider->setCode(false);

        $colors = trans('admin/global.colors');

        $webserviceMethods = WebserviceMethod::remember(config('cache.query_ttl'))
            ->cacheTags(WebserviceMethod::CACHE_TAG)
            ->filterSources()
            ->ordered()
            ->pluck('name', 'method')
            ->toArray();

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterMyAgencies()
            ->orderBy('code')
            ->get();

        $agenciesList = $agencies->pluck('name', 'id')->toArray();

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            //->filterAgencies()
            ->showOnPricesTable()
            ->ordered()
            ->get();

        $servicesList = $services->pluck('name', 'id')->toArray();

        $providersList = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $categories = ProviderCategory::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $paymentConditions = PaymentCondition::filterSource()
            ->isPurchasesVisible()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $existingVats = Provider::filterSource()
            ->where(function ($q) {
                $q->where('vat', '<>', '999999990');
                $q->where('vat', '<>', '');
                $q->whereNotNull('vat');
            })
            ->pluck('vat')
            ->toArray();

        $country  = Setting::get('app_country');
        $district = null;

        $action = 'Criar Fornecedor';
        if ($type == 'carrier') {
            $action = 'Criar Transportadora Parceira';
        }

        $formOptions = array('route' => array('admin.providers.store'), 'method' => 'POST');

        $data = compact(
            'provider',
            'colors',
            'agencies',
            'country',
            'district',
            'webserviceMethods',
            'categories',
            'action',
            'formOptions',
            'paymentConditions',
            'existingVats',
            'servicesList',
            'providersList',
            'agenciesList'
        );

        return $this->setContent('admin.providers.edit', $data);
        //return view('admin.providers.create', $data)->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request, null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {

        $agencyId   = $request->get('agency');

        $provider = Provider::with('services')
            ->filterAgencies();


        if (!(Auth::user()->hasRole(config('permissions.role.admin')))) {
            $provider = $provider->whereNotNull('source');
        }

        $provider = $provider->findOrfail($id);

        $customer = null;
        $customerId = $request->get('customer');
        if (!empty($customerId) && $customerId != 'null') {
            $customer = Customer::find($customerId);
        }

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterMyAgencies()
            ->orderBy('code')
            ->get();

        $sourceAgencies = $agencies->filter(function ($item) {
                return $item->source == config('app.source');
            })
            ->pluck('id')
            ->toArray();

        if (empty($agencyId)) {
            $agencyId = @$sourceAgencies[0];
        }

        $agenciesList = $agencies->filter(function ($item) use ($provider) {
            return $provider->agencies ? in_array($item->id, $provider->agencies) : [];
        })->pluck('name', 'id')->toArray();


        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->where('agencies', 'like', '%"' . $agencyId . '"%')
            ->showOnPricesTable()
            ->ordered()
            ->get();

        $servicesList = $services->pluck('name', 'id')->toArray();

        $providersList = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterAgencies()
            ->isCarrier()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $billingZones = BillingZone::remember(config('cache.query_ttl'))
            ->cacheTags(BillingZone::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'code')
            ->toArray();

        $webserviceMethods = WebserviceMethod::remember(config('cache.query_ttl'))
            ->cacheTags(WebserviceMethod::CACHE_TAG)
            ->filterSources()
            ->ordered()
            ->pluck('name', 'method')
            ->toArray();

        $pricesTables = PriceTable::remember(config('cache.query_ttl'))
            ->cacheTags(PriceTable::CACHE_TAG)
            ->filterAgencies($sourceAgencies)
            ->isActive()
            ->pluck('name', 'id')
            ->toArray();

        $categories = ProviderCategory::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $purchasesTypes = PurchaseInvoiceType::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $paymentConditions = PaymentCondition::filterSource()
            ->isPurchasesVisible()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $servicesGroups = ServiceGroup::filterSource()
            ->ordered()
            ->get();

        $servicesGroupsList = ServiceGroup::filterSource()
            ->pluck('name', 'code')
            ->toArray();

        $existingVats = Provider::filterSource()
            ->where(function ($q) {
                $q->where('vat', '<>', '999999990');
                $q->where('vat', '<>', '');
                $q->whereNotNull('vat');
            })
            ->pluck('vat')
            ->toArray();

        $shippingExpenses = ShippingExpense::remember(config('cache.query_ttl'))
            ->cacheTags(ShippingExpense::CACHE_TAG)
            ->filterSource()
            ->get()
            ->sortBy(function ($expense) {
                // Show fuel expenses last
                return $expense->type == ShippingExpense::TYPE_FUEL;
            });

        $vatRates = Invoice::getVatTaxes(true, true);

        $country  = Setting::get('app_country');
        $district = null;


        $pricesTableData  = $this->getPricesTableData($services, $provider, $servicesGroupsList, $agencyId, $customerId, 'expedition', false);
        $rowsWeight       = @$pricesTableData['rows'];
        $rowsAdicional    = @$pricesTableData['adicional'];
        $pricesTableData  = @$pricesTableData['prices'];


        $totalExpired = PurchaseInvoice::filterSource()
            ->where('provider_id', $provider->id)
            ->where('is_settle', 0)
            ->whereIn('doc_type', ['provider-invoice', 'provider-credit-note'])
            ->where('due_date', '<', date('Y-m-d'))
            ->count();

        $colors = trans('admin/global.colors');

        $action = 'Editar Fornecedor';

        $formOptions = array('route' => array('admin.providers.update', $provider->id), 'method' => 'PUT');

        $data = compact(
            'provider',
            'customer',
            'action',
            'formOptions',
            'colors',
            'agencies',
            'agenciesList',
            'services',
            'servicesList',
            'providersList',
            'billingZones',
            'pricesTables',
            'webserviceMethods',
            'country',
            'district',
            'pricesTableData',
            'rowsAdicional',
            'rowsWeight',
            'categories',
            'purchasesTypes',
            'paymentConditions',
            'existingVats',
            'totalExpired',
            'servicesGroups',
            'servicesGroupsList',
            'shippingExpenses',
            'vatRates'
        );


        return $this->setContent('admin.providers.edit', $data);
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

        Provider::flushCache(Provider::CACHE_TAG);

        $input = $request->all();

        $category = ProviderCategory::filterSource()->find(@$input['category_id']);

        $input['customer_id']             = $request->get('assigned_customer_id', false);
        $input['daily_report']            = $request->get('daily_report', false);
        $input['autodetect_agencies']     = $request->get('autodetect_agencies', false);
        $input['allow_out_of_standard']   = $request->get('allow_out_of_standard', false);
        $input['update_billing_software'] = $request->get('update_billing_software', false);
        $input['type']                    = $request->get('type', 'others');
        $input['color']                   = $input['type'] == 'carrier' ? @$input['color'] : null;
        $input['category_slug']           = @$category->slug;

        $provider = Provider::filterAgencies()->findOrNew($id);

        if ($provider->validate($input)) {
            $provider->fill($input);
            $provider->billing_email = $input['billing_email'];
            $provider->save();

            try {
                if ($input['update_billing_software'] && !empty($provider->vat) && !in_array($provider->vat, ['999999990', '999999999'])) {

                    $class = InvoiceGateway\Base::getNamespaceTo('Provider');
                    $providerKeyinvoice = new $class();
                    $providerKeyinvoice->insertOrUpdateProvider(
                        $provider->vat,
                        $provider->code,
                        $provider->company,
                        $provider->address,
                        $provider->zip_code,
                        $provider->city,
                        $provider->phone,
                        null,
                        $provider->email,
                        $provider->obs,
                        $provider->country
                    );
                }
            } catch (\Exception $e) {
                return Redirect::back()->with('error', $e->getMessage());
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $provider->errors()->first());
    }

    public function updateCustomExpenses(Request $request, $id) {
        Provider::flushCache(Provider::CACHE_TAG);

        $input = $request->all();
        if (!empty($input['custom_expenses'])) {
            $customExpenses = [];
            foreach ($input['custom_expenses'] as $key => $zones) {
                foreach ($zones as $zone => $price) {
                    if (!empty($price)) {
                        $customExpenses[$key][$zone] = $price;
                    }
                }
            }

            $input['custom_expenses'] = $customExpenses;
        }

        $provider = Provider::filterAgencies()->findOrNew($id);
        $provider->fill($input);
        $provider->save();

        return Redirect::back()->with('success', 'Dados gravados com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        Provider::flushCache(Provider::CACHE_TAG);

        $result = Provider::filterAgencies()
            ->whereNotNull('source')
            ->whereId($id)
            ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o fornecedor.');
        }

        return Redirect::route('admin.providers.index')->with('success', 'Fornecedor removido com sucesso.');
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

        Provider::flushCache(Provider::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $result = Provider::whereIn('id', $ids)->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }

    /**
     * Inactivate customer and send it to prospects list
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inactivate($id)
    {

        Provider::flushCache(Provider::CACHE_TAG);

        $provider  = Provider::filterSource()
            ->filterAgencies()
            ->findOrNew($id);

        $provider->is_active = !$provider->is_active;
        $provider->save();

        $feedback = $provider->is_active ? 'Fornecedor ativo com sucesso' : 'Fornecedor inativo com sucesso.';

        return Redirect::back()->with('success', $feedback);
    }

    /**
     * Inactivate all selected resources from storage.
     * GET /admin/customers/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massInactivate(Request $request)
    {

        Provider::flushCache(Provider::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $result = Provider::whereIn('id', $ids)->update(['is_active' => 0]);

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível inativar os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados inativados com sucesso.');
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatable(Request $request)
    {

        $data = Provider::filterAgencies()
            ->with('category', 'paymentCondition')
            ->select();

        //filter type
        $value = $request->type;
        if ($request->has('type')) {
            $data = $data->where('type', $value);
        }

        //filter agency
        $value = $request->agency;
        if ($request->has('agency')) {
            $data = $data->where('agencies', 'like', '%"' . $value . '"%');
        }

        //filter code
        $value = $request->code;
        if ($request->has('code')) {
            $data = $data->where('code', $value);
        }

        //filter active
        $value = $request->active;
        if ($request->has('active')) {
            $data = $data->where('is_active', $value);
        }

        //filter country
        $value = $request->country;
        if ($request->has('country')) {
            $data = $data->where('country', $value);
        }

        //filter category
        $value = $request->category;
        if ($request->has('category')) {
            $data = $data->whereIn('category_id', $value);
        }

        //filter payment method
        $value = $request->payment_method;
        if ($request->has('payment_method')) {
            $data = $data->whereIn('payment_method', $value);
        }

        return Datatables::of($data)
            /*->edit_column('agencies', function($row) use ($agencies) {
                return view('admin.partials.datatables.agencies', compact('row', 'agencies'))->render();
            })*/
            ->edit_column('code', function ($row) {
                return view('admin.providers.datatables.code', compact('row'))->render();
            })
            ->edit_column('name', function ($row) {
                return view('admin.providers.datatables.name', compact('row'))->render();
            })
            ->edit_column('company', function ($row) {
                return view('admin.providers.datatables.company', compact('row'))->render();
            })
            ->edit_column('category_id', function ($row) {
                return @$row->category->name;
            })
            ->edit_column('contacts', function ($row) {
                return view('admin.providers.datatables.contacts', compact('row'))->render();
            })
            ->edit_column('country', function ($row) {
                return view('admin.providers.datatables.country', compact('row'))->render();
            })
            ->edit_column('balance_total_unpaid', function ($row) {
                return view('admin.providers.datatables.balance', compact('row'))->render();
            })
            ->edit_column('is_active', function ($row) {
                return view('admin.providers.datatables.is_active', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function ($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.providers.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }


    /**
     * Store provider services and prices.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeServices(Request $request, $id)
    {

        Provider::flushCache(Provider::CACHE_TAG);

        $input = $request->all();

        if ($request->has('max')) {
            asort($input['max']);

            $unities  = $input['max'];
            $services = $input['price'];
            $adicionalRows    = $input['is_adicional'];
            $adicionalUnities = $input['adicional_unity'];
        }

        $agencyId   = @$input['agency_id'];
        $customerId = strtolower(@$input['customer_id']);
        $customerId = $customerId == 'null' ? null : $customerId;
        $type       = $input['type'];
        $group      = $input['group'];

        if ($request->has('max')) {
            asort($input['max']);
            $unities  = $input['max'];
            $services = $input['price'];
        }

        $provider = Provider::findOrfail($id);

        $detachServiceIds = Service::where(function ($q) use ($agencyId) {
            $q->where('agencies', 'like', '%"' . $agencyId . '"%');
        })
            ->where('group', $group)
            ->pluck('id')
            ->toArray();


        $provider->services()
            ->wherePivot('type', $type);

        if (empty($customerId)) {
            $provider->services()
                ->wherePivot('type', $type)
                ->wherePivot('customer_id', '=', null)
                ->wherePivot('agency_id', $agencyId)
                ->detach($detachServiceIds);
        } else {
            $provider->services()
                ->wherePivot('type', $type)
                ->wherePivot('customer_id', $customerId)
                ->wherePivot('agency_id', $agencyId)
                ->detach($detachServiceIds);
        }

        if ($request->has('max')) {
            $nextMinValue = 0;

            foreach ($unities as $rowNumber => $max) {
                foreach ($services as $serviceId => $zones) {

                    if (empty($zones)) {
                        $zones = [Setting::get('app_country')];
                    }

                    $isAdicional    = 0;
                    $adicionalUnity = null;
                    if (@$adicionalRows[$rowNumber]) {
                        $isAdicional = 1;
                        $adicionalUnity = @$adicionalUnities[$rowNumber] ? @$adicionalUnities[$rowNumber] : 1;
                    }

                    foreach ($zones as $zone => $data) {

                        $provider->services()->attach($serviceId, [
                            'type'            => $type,
                            'agency_id'       => $agencyId,
                            'customer_id'     => empty($customerId) ? null : $customerId,
                            'zone'            => $zone,
                            'min'             => $nextMinValue,
                            'max'             => $max,
                            'price'           => $data[$rowNumber],
                            'is_adicional'    => $isAdicional,
                            'adicional_unity' => $adicionalUnity
                        ]);
                    }
                }
                $nextMinValue = $max + 0.01;
            }
        }

        return Redirect::back()->with('success', 'Tabela de preços gravada com sucesso.');
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableExpenses(Request $request, $providerId)
    {

        $input      = $request->all();
        $agencyId   = @$input['agency'];

        if (empty($agencyId)) {
            $agencyId = Agency::whereSource(config('app.source'))->first()->id;
        }

        $bindings = [
            'shipping_expenses.*',
        ];

        $data = ShippingExpense::filterSource()
            ->with('providers')
            //->whereIn('code', ['REEMB','REMB', 'RGUIA'])
            ->get($bindings);


        $provider = Provider::findOrFail($providerId);

        return Datatables::of($data)
            ->edit_column('cost_price', function ($row) use ($providerId, $provider, $agencyId) {
                return view('admin.providers.datatables.price_expedition', compact('row', 'providerId', 'provider', 'agencyId'))->render();
            })
            /*->edit_column('delivery_price', function ($row) use ($providerId, $provider, $agencyId) {
                return view('admin.providers.datatables.price_delivery', compact('row', 'providerId', 'provider', 'agencyId'))->render();
            })*/
            ->make(true);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeExpense(Request $request, $providerId)
    {

        $input = $request->all();
        $agencyId = $request->get('agency');

        if (empty($agencyId)) { //adiciona a despesa para todas as agencias
            $agencies = Agency::whereSource(config('app.source'))->pluck('id')->toArray();
        } else {
            $agencies = [$agencyId];
        }

        $provider = Provider::findOrFail($providerId);

        foreach ($agencies as $agencyId) { //grava para cada agência do source

            if (isset($input['expenses_expedition'])) {
                $allExpenses = $provider->expenses_expedition;

                $agencyExpenses = @$allExpenses[$agencyId] ? $allExpenses[$agencyId] : [];
                $agencyExpenses = array_merge($agencyExpenses, @$input['expenses_expedition'][$agencyId]);
                $agencyExpenses = @$agencyExpenses;

                $input['expenses_expedition'] = $allExpenses;
                $input['expenses_expedition'][$agencyId] = $agencyExpenses;
            }

            if (isset($input['expenses_delivery'])) {
                $allExpenses = $provider->expenses_delivery;

                $agencyExpenses = @$allExpenses[$agencyId] ? $allExpenses[$agencyId] : [];

                $agencyExpenses = array_merge($agencyExpenses, @$input['expenses_delivery'][$agencyId]);
                $agencyExpenses = @$agencyExpenses;

                $input['expenses_delivery'] = $allExpenses;
                $input['expenses_delivery'][$agencyId] = $agencyExpenses;
            }

            $provider->fill($input);
            $provider->save();
        }

        return Response::json([
            'type'      => 'success',
            'feedback'  => 'Preço gravado com sucesso.'
        ]);
    }

    /**
     * Loading table data
     *
     * @return Datatables
     */
    public function datatableServices(Request $request, $providerId)
    {

        $data = Service::filterAgencies()
            ->with(['volumetricFactors' => function ($q) use ($providerId) {
                $q->where('provider_id', $providerId);
            }])->select();

        return Datatables::of($data)
            ->edit_column('sort', function ($row) {
                return $row->sort;
            })
            ->edit_column('volume_min', function ($row) use ($providerId) {
                return view('admin.providers.datatables.services.volume_min', compact('row', 'providerId'))->render();
            })
            ->edit_column('factor', function ($row) use ($providerId) {
                return view('admin.providers.datatables.services.factor', compact('row', 'providerId'))->render();
            })
            ->edit_column('factor_provider', function ($row) use ($providerId) {
                return view('admin.providers.datatables.services.factor_provider', compact('row', 'providerId'))->render();
            })
            ->make(true);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeVolumetricFactor(Request $request, $providerId, $serviceId)
    {

        $input = $request->all();

        $service = ServiceVolumetricFactor::firstOrNew([
            'service_id'  => $serviceId,
            'provider_id' => $providerId,
            'zone'        => $input['zone']
        ]);

        $service->service_id = $serviceId;
        $service->provider_id = $providerId;
        $service->fill($input);
        $service->save();

        return Response::json([
            'type'      => 'success',
            'feedback'  => 'Preço gravado com sucesso.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * GET /admin/services/sort
     *
     * @return Response
     */
    public function sortEdit(Request $request)
    {

        $type = $request->get('type', 'other');

        $items = Provider::filterAgencies()
            ->where('type', $type)
            ->orderBy('sort')
            ->get(['id', 'name']);

        $route = route('admin.providers.sort.update');

        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/services/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request)
    {

        Provider::flushCache(Provider::CACHE_TAG);

        try {
            Provider::setNewOrder($request->ids);
            $response = [
                'result'  => true,
                'message' => 'Ordenação gravada com sucesso.',
            ];
        } catch (\Exception $e) {
            $response = [
                'result'  => false,
                'message' => 'Erro ao gravar ordenação. ' . $e->getMessage(),
            ];
        }

        return Response::json($response);
    }

    /**
     * Render price table
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $providerId
     * @param int $groupId
     * @return string
     */
    public function priceTable(Request $request, int $providerId, int $groupId) {
        $agencyId   = $request->get('agency');

        $provider = Provider::with('services')
            ->filterAgencies();


        if (!(Auth::user()->hasRole(config('permissions.role.admin')))) {
            $provider = $provider->whereNotNull('source');
        }

        $provider = $provider->findOrfail($providerId);

        $customer = null;
        $customerId = $request->get('customer');
        if (!empty($customerId) && $customerId != 'null') {
            $customer = Customer::find($customerId);
        }

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterMyAgencies()
            ->orderBy('code')
            ->get();

        $sourceAgencies = $agencies->filter(function ($item) {
                return $item->source == config('app.source');
            })
            ->pluck('id')
            ->toArray();

        if (empty($agencyId)) {
            $agencyId = @$sourceAgencies[0];
        }

        $agenciesList = $agencies->filter(function ($item) use ($provider) {
            return $provider->agencies ? in_array($item->id, $provider->agencies) : [];
        })->pluck('name', 'id')->toArray();

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->where('agencies', 'like', '%"' . $agencyId . '"%')
            ->showOnPricesTable()
            ->ordered()
            ->get();

        $servicesGroups = ServiceGroup::filterSource()
            ->where('id', $groupId)
            ->ordered()
            ->get();

        $servicesGroupsList = ServiceGroup::filterSource()
            ->where('id', $groupId)
            ->pluck('name', 'code')
            ->toArray();

        $pricesTableData  = $this->getPricesTableData($services, $provider, $servicesGroupsList, $agencyId, $customerId, 'expedition');
        $rowsWeight       = @$pricesTableData['rows'];
        $rowsAdicional    = @$pricesTableData['adicional'];
        $pricesTableData  = @$pricesTableData['prices'];

        $data = compact(
            'provider',
            'customer',
            'agencies',
            'agenciesList',
            'services',
            'pricesTableData',
            'rowsAdicional',
            'rowsWeight',
            'servicesGroups',
            'servicesGroupsList'
        );

        return view('admin.providers.partials.prices.price_table_data', $data)->render();
    }

    /**
     * Return prices table data
     * @param $services
     * @return array
     */
    public function getPricesTableData($services, $provider, $servicesGroupsList, $agencyId, $customerId, $type, $withData = true)
    {
        $pricesTables  = [];
        $rowsAdicional = [];
        $rows = [];

        $allGroupRows = ProviderService::with(['service' => function ($q) {
                $q->remember(config('cache.query_ttl'));
                $q->cacheTags(Service::CACHE_TAG);
            }])
            ->whereHas('service', function ($q) use($servicesGroupsList) {
                $q->whereIn('group', array_keys($servicesGroupsList));
            })
            ->where('type', $type)
            ->where('provider_id', $provider->id)
            ->where('agency_id', $agencyId)
            ->where('provider_id', $provider->id);

        $customerId = $customerId == 'null' ? null : $customerId;
        if (empty($customerId)) {
            $allGroupRows = $allGroupRows->whereNull('customer_id');
        } else {
            $allGroupRows = $allGroupRows->where('customer_id', $customerId);
        }
        $allGroupRows = $allGroupRows->get();

        foreach ($servicesGroupsList as $groupCode => $groupName) {
            $groupServices = $services->filter(function ($item) use ($groupCode) {
                return $item->group == $groupCode;
            });

            /**
             * Only load group service services 
             * and don't load the table information
             */
            if (!$withData) {
                $pricesTables[$groupCode]  = $groupServices;
                $rows[$groupCode]          = [];
                $rowsAdicional[$groupCode] = [];
                continue;
            }

            $groupRows = $allGroupRows->filter(function ($item) use ($groupCode) {
                return $item->service->group == $groupCode;
            });

            $groupRows = $groupRows->sortBy('min')->groupBy('max');

            $arr = [];
            $adicionalRows = [];
            foreach ($groupRows as $weight => $row) {
                $service = $row->groupBy('service_id');
                $rowServices = [];
                foreach ($service as $serviceId => $zone) {
                    $zones = $zone->groupBy('zone')->toArray();
                    $rowServices[$serviceId] = $zones;
                }
                $arr[$weight] = $rowServices;

                $adicionalRows[$weight] = [
                    'is_adicional'    => @$row[0]['is_adicional'],
                    'adicional_unity' => @$row[0]['adicional_unity'],
                ];
            }

            $groupRows = $arr;

            if (!$groupServices->isEmpty()) {
                $pricesTables[$groupCode] = $groupServices;
                $rows[$groupCode] = $groupRows;
                $rowsAdicional[$groupCode] = $adicionalRows;
            }
        }

        return [
            'prices'    => $pricesTables,
            'adicional' => $rowsAdicional,
            'rows'      => $rows
        ];
    }

    /**
     * Copy services from another user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function importServices(Request $request, $id)
    {

        Provider::flushCache(Provider::CACHE_TAG);

        $input = $request->all();
        $agencyId = @$input['agency'];
        $type     = @$input['type'];

        $provider = Provider::filterSource()->findOrfail($id);

        if (@$input['source'] == 'prices-table') {
            $sourceProvider = PriceTable::filterAgencies()->findOrFail($input['import_global_prices_id']);
        } else {
            $sourceProvider = Provider::filterSource()->findOrFail($id);
        }

        if (!empty($input['import_target'])) {

            $targetServices = Service::filterAgencies()
                ->where('group', $input['import_target'])
                ->ordered()
                ->pluck('id')
                ->toArray();

            $services = $sourceProvider->services->filter(function ($item) use ($targetServices) {
                return in_array($item->id, $targetServices);
            });
        } else {

            if (@$input['source'] == 'prices-table') {
                $services = $sourceProvider->services;
            } else {

                $services = $sourceProvider->services->filter(function ($item) use ($input) {
                    return $item->pivot->agency_id == $input['import_agency_id'] && $item->pivot->type == $input['type'];
                });
            }
        }

        if (empty($services)) {
            return Redirect::back()->with('warning', 'O fornecedor que selecionou não tem definida nenhuma tabela de preços.');
        }

        if (!empty($input['import_target'])) {
            $provider->services()
                ->wherePivot('type', $type)
                ->wherePivotIn('service_id', $targetServices)
                ->wherePivot('agency_id', $agencyId)
                ->detach();
        } else {
            $provider->services()
                ->wherePivot('type', $type)
                ->wherePivot('agency_id', $agencyId)
                ->detach();
        }

        foreach ($services as $service) {
            $provider->services()->attach($service->pivot->service_id, [
                'zone'  => $service->pivot->zone,
                'min'   => $service->pivot->min,
                'max'   => $service->pivot->max,
                'price' => $service->pivot->price,
                'type'  => $type,
                'agency_id' => $agencyId
            ]);
        }

        return Redirect::back()->with('success', 'Tabela de preços importada com sucesso.');
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchCustomer(Request $request)
    {

        $search = $request->get('q');
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {

            $customers = Customer::filterSource()
                ->where(function ($q) use ($search) {
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
                ->get(['name', 'code', 'id']);

            if ($customers) {

                $results = array();
                foreach ($customers as $customer) {
                    $results[] = array('id' => $customer->id, 'text' => $customer->code . ' - ' . $customer->name);
                }
            } else {
                $results = [['id' => '', 'text' => 'Nenhum cliente encontrado.']];
            }
        } catch (\Exception $e) {
            $results = [['id' => '', 'text' => 'Erro interno ao processar o pedido.']];
        }

        return Response::json($results);
    }
}
