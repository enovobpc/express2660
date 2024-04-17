<?php

namespace App\Http\Controllers\Admin;

use App\Models\ServiceGroup;
use Auth, DB, Response, Setting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;

use App\Models\PriceTable;
use App\Models\PriceTableService;
use App\Models\CustomerService;
use App\Models\BillingZone;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Agency;

class PricesTablesController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'prices_tables';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',prices_tables|prices_tables_view']);
        validateModule('prices_tables');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->orderBy('code', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        return $this->setContent('admin.prices_tables.index', compact('agencies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterMyAgencies()
            ->orderBy('code')
            ->get();

        return view('admin.prices_tables.create', compact('agencies'))->render();
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

        $priceTable = PriceTable::with(['services' => function($q){
                $q->select(['services.id', 'code', 'unity', 'group', 'name', 'zones']);
            }])
            ->filterAgencies()
            ->findOrFail($id);

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterSource()
            ->orderBy('code')
            ->get();

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->ordered()
            ->get();

        $servicesGroups = ServiceGroup::filterSource()
            ->ordered()
            ->get();

        $servicesGroupsList = $servicesGroups->pluck('name', 'code')
            ->toArray();

        $pricesTableData  = $this->getPricesTableData($services, $priceTable, $servicesGroupsList, $request->get('origin_zone'));
        $rowsWeight       = @$pricesTableData['rows'];
        $rowsAdicional    = @$pricesTableData['adicional'];
        $pricesTableData  = $pricesTableData['prices'];

        $defaultWeights = explode(',', Setting::get('default_weights'));

        $pricesTables = PriceTable::filterSource()
            ->filterAgencies()
            ->where('id', '<>', $priceTable->id)
            ->pluck('name', 'id')
            ->toArray();

        $billingZones = BillingZone::remember(config('cache.query_ttl'))
            ->cacheTags(BillingZone::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'code')
            ->toArray();

        $billingZonesList = BillingZone::pluck('name', 'code')
            ->toArray();

        $formOptions = array('route' => array('admin.prices-tables.update', $priceTable->id), 'method' => 'PUT');

        $data = compact(
            'priceTable',
            'formOptions',
            'rowsWeight',
            'pricesTableData',
            'defaultWeights',
            'agencies',
            'pricesTables',
            'rowsAdicional',
            'billingZones',
            'servicesGroups',
            'servicesGroupsList',
            'billingZonesList'
        );

        return $this->setContent('admin.prices_tables.edit', $data);
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

        PriceTable::flushCache(PriceTable::CACHE_TAG);

        $input = $request->all();
        $input['active'] = $request->get('active', false);

        $priceTable = PriceTable::filterSource()
            ->filterAgencies()
            ->findOrNew($id);

        if ($priceTable->validate($input)) {
            $priceTable->fill($input);
            $priceTable->source = config('app.source');
            $priceTable->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $priceTable->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        PriceTable::flushCache(PriceTable::CACHE_TAG);

        $priceTable = PriceTable::with('customers')
            ->filterSource()
            ->filterAgencies()
            ->find($id);

        if ($priceTable->customers->count() > 0) {
            return Redirect::back()->with('error', 'Não é possível eliminar a tabela de preços porque existem clientes associados. Associe primeiro os clientes a outra tabela de preços antes de eliminar a tabela.');
        }

        $result = $priceTable->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a tabela de preços.');
        }

        return Redirect::route('admin.prices-tables.index')->with('success', 'Tabela de preços removida com sucesso.');
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

        PriceTable::flushCache(PriceTable::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $pricesTables = PriceTable::filterSource()
            ->filterAgencies()
            ->with('customers')
            ->whereIn('id', $ids)
            ->get();

        $result = true;
        foreach ($pricesTables as $pricesTable) {
            if ($pricesTable->customers->count()) {
                $result = false;
            } else {
                $pricesTable->delete();
            }
        }

        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover um ou mais registos porque porque existem clientes associados. Associe primeiro os clientes a outra tabela de preços antes de eliminar a tabela.');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }

    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request)
    {

        $data = PriceTable::filterSource()
            ->filterAgencies()
            ->select([
                '*',
                DB::raw('(select count(*) from customers where price_table_id = prices_tables.id and deleted_at is null) as customers')
            ]);

        //filter agency
        $values = $request->agency;
        if ($request->has('agency')) {
            $data = $data->where(function ($q) use ($values) {
                foreach ($values as $value) {
                    $q->orWhere('agencies', 'like', '%"' . $value . '"%');
                }
            });
        }

        $agencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->get(['id', 'code', 'name', 'color']);

        $agencies = $agencies->groupBy('id')->toArray();

        return Datatables::of($data)
            ->edit_column('agencies', function ($row) use ($agencies) {
                return view('admin.partials.datatables.agencies', compact('row', 'agencies'))->render();
            })
            ->edit_column('name', function ($row) {
                return view('admin.prices_tables.datatables.name', compact('row'))->render();
            })
            ->edit_column('customers', function ($row) {
                return view('admin.prices_tables.datatables.customers', compact('row'))->render();
            })
            ->edit_column('active', function ($row) {
                return view('admin.prices_tables.datatables.active', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function ($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.prices_tables.datatables.actions', compact('row'))->render();
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

        $input = $request->all();


        $originZone = $request->get('origin_zone', null);
        $originZone = empty($originZone) ? null : $originZone;


        if ($request->has('max')) {

            asort($input['max']);

            $unities          = @$input['max'];
            $services         = @$input['price'];
            $adicionalRows    = @$input['is_adicional'];
            $adicionalUnities = @$input['adicional_unity'];
        }

        $priceTable = PriceTable::filterSource()->filterAgencies()->findOrfail($id);

        $detachServiceIds = Service::where('group', $input['group'])
            ->pluck('id')
            ->toArray();

        //apaga todos os preços gravados na tabela para os serviços deste grupo
        $oldData = CustomerService::where('price_table_id', $id)
            ->whereIn('service_id', $detachServiceIds);
        if ($originZone) {
            $oldData = $oldData->where('origin_zone', $originZone);
        } else {
            $oldData = $oldData->whereNull('origin_zone');
        }
        $oldData->forceDelete();


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
                        $priceTable->services()->attach($serviceId, [
                            'origin_zone'     => $originZone,
                            'zone'            => $zone,
                            'min'             => $nextMinValue,
                            'max'             => $max,
                            'price'           => @$data[$rowNumber] ? @$data[$rowNumber] : 0,
                            'is_adicional'    => $isAdicional,
                            'adicional_unity' => $adicionalUnity
                        ]);
                    }
                }

                $nextMinValue = $max + 0.01;
            }
        }

        $priceTable->save();

        return Redirect::back()->with('success', 'Tabela de preços gravada com sucesso.');
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

        $input = $request->all();

        $pricesTable = PriceTable::filterSource()->filterAgencies()->findOrFail($id);

        $sourceTable = PriceTable::filterSource()->filterAgencies()->findOrFail($input['import_prices_table_id']);

        if (!empty($input['import_target'])) {

            $targetServices = Service::filterAgencies()
                ->where('group', $input['import_target'])
                ->ordered()
                ->pluck('id')
                ->toArray();

            $services = $sourceTable->services->filter(function ($item) use ($targetServices) {
                return in_array($item->id, $targetServices);
            });
        } else {
            $services = $sourceTable->services;
        }

        if (empty($services)) {
            return Redirect::back()->with('warning', 'A tabela que selecionou não tem definida nenhum preço.');
        }

        if (!empty($input['import_target'])) {
            $pricesTable->services()->wherePivotIn('service_id', $targetServices)->detach();
        } else {
            $pricesTable->services()->detach();
        }

        foreach ($services as $service) {
            $pricesTable->services()->attach($service->pivot->service_id, [
                'zone'  => $service->pivot->zone,
                'min'   => $service->pivot->min,
                'max'   => $service->pivot->max,
                'price' => $service->pivot->price
            ]);
        }

        return Redirect::back()->with('success', 'Tabela de preços importada com sucesso.');
    }


    /**
     * Return prices table data
     * @param $services
     * @return array
     */
    public function getPricesTableData($services, $priceTable, $servicesGroupsList, $originZone = null)
    {

        $pricesTables  = [];
        $rowsAdicional = [];
        $rows = [];

        foreach ($servicesGroupsList as $groupCode => $groupName) {

            $groupServices = $services->filter(function ($item) use ($groupCode) {
                return $item->group == $groupCode;
            });

            $groupRows = CustomerService::whereHas('service', function ($q) use ($groupCode) {
                $q->where('group', $groupCode);
            })
                ->where('price_table_id', $priceTable->id);

            if (empty($originZone)) {
                $groupRows = $groupRows->whereNull('origin_zone');
            } else {
                $groupRows = $groupRows->where('origin_zone', $originZone);
            }

            $groupRows = $groupRows->get();

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
            'prices' => $pricesTables,
            'adicional' => $rowsAdicional,
            'rows'   => $rows
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function massEditPrices()
    {

        $sourceAgencies = Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->whereSource(config('app.source'))
            ->pluck('id')
            ->toArray();

        $services = Service::remember(config('cache.query_ttl'))
            ->cacheTags(Service::CACHE_TAG)
            ->filterAgencies()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $pricesTables = PriceTable::remember(config('cache.query_ttl'))
            ->cacheTags(PriceTable::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->pluck('name', 'id')
            ->toArray();

        $billingZones   = BillingZone::remember(config('cache.query_ttl'))
            ->cacheTags(BillingZone::CACHE_TAG)
            ->filterSource()
            ->pluck('name', 'code')
            ->toArray();

        $customers      = Customer::filterAgencies()->pluck('name', 'id')->toArray();

        $data = compact(
            'services',
            'billingZones',
            'pricesTables',
            'customers'
        );

        return view('admin.prices_tables.modals.mass_edit', $data)->render();
    }

    /**
     * Copy services from another user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function massUpdatePrices(Request $request)
    {

        $input = $request->all();

        $value = $input['value'];

        if (empty($value)) {
            return Redirect::back()->with('error', 'Não indicou nenhuma acção.');
        }

        $services = CustomerService::whereIn('service_id', $input['update_services']);

        if (!empty($input['update_billing_zones'])) {
            $services = $services->whereIn('zone', $input['update_billing_zones']);
        }

        if (!empty($input['update_customer_id'])) {
            $services = $services->whereIn('customer_id', $input['update_customer_id']);
        }

        if (!empty($input['update_price_table_id'])) {
            if (in_array('-1', $input['update_price_table_id'])) {
                $services = $services->where(function ($q) use ($input) {
                    $q->whereIn('price_table_id', $input['update_price_table_id']);
                    $q->orWhereNull('price_table_id');
                });
            } else {
                $services = $services->whereIn('price_table_id', $input['update_price_table_id']);
            }
        }

        $services = $services->get();

        foreach ($services as $service) {

            $value = (float) $value;
            $price = (float) $service->price;

            try {
                if ($price > 0.00) {
                    if ($input['update_target'] == 'percent') {
                        if ($input['update_signal'] == 'add') {
                            $price = $price + ($price * ($value / 100));
                        } else {
                            $price = $price - ($price * ($value / 100));
                        }
                    } else {
                        if ($input['update_signal'] == 'add') {
                            $price = $price + $value;
                        } else {
                            $price = $price - $value;
                        }
                    }

                    $service->price = $price;
                    $service->save();
                }
            } catch (\Exception $e) {
                return Redirect::back()->with('error', $e->getMessage());
            }
        }

        return Redirect::back()->with('success', 'Tabela de preços importada com sucesso.');
    }
}
