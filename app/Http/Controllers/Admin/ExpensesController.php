<?php

namespace App\Http\Controllers\Admin;

use App\Models\Billing\Item;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShipmentExpense;
use App\Models\ShippingStatus;
use Html, Setting, Response, Cache;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;

use App\Models\ShippingExpense;
use App\Models\BillingZone;
use App\Models\ZipCode\ZipCodeZone;

class ExpensesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'shipping_expenses';

    /**
     * Forms types
     * @var array
     */
    protected $formTypes = [
        'checkbox'    => '[Checkbox] - Ativo/Inativo',
        'input'       => '[Texto] - Escrever Qtd',
        'money'       => '[Texto] - Escrever Preço',
        'percent'     => '[Texto] - Escrever Percentagem',
        'select-io'   => '[Lista] - Sim/Não',
        'select-time' => '[Lista] - Nº Minutos',
    ];

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',shipping_expenses']);
        validateModule('shipping_expenses');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.expenses.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function create(Request $request, ShippingExpense $shippingExpense = null) {

        if (!$shippingExpense) {
            $shippingExpense = new ShippingExpense;
        }

        if ($request->has('type')) {
            $shippingExpense->type = $request->get('type');
        }

        $billingZones = BillingZone::remember(config('cache.query_ttl'))
            ->cacheTags(BillingZone::CACHE_TAG)
            ->filterSource()
            ->orderBy('name')
            ->pluck('name', 'code')
            ->toArray();

        $formTypes  = $this->formTypes;
        $trigger    = trans('admin/expenses.triggers');
        $triggerVariables = trans('admin/expenses.trigger-fields');
        $triggerOperators = trans('admin/expenses.trigger-operators');
        $everyFields = trans('admin/expenses.every-fields');

        $formOptions = ['route' => ['admin.expenses.store'], 'method' => 'POST', 'class' => 'form-ajax'];

        if($shippingExpense->type == 'fuel') {
            $shippingExpense->name = 'Taxa Combustível';
            $action = 'Adicionar Taxa Combustível';
            $view   = 'admin.expenses.edit_fuel';
        } elseif($shippingExpense->type == 'dimensions') {
            $shippingExpense->name = 'Taxa Dimensões';
            $action = 'Adicionar Taxa Dimensões';
            $view   = 'admin.expenses.edit_dimensions';
        } else {
            $action = 'Adicionar Encargo Adicional';
            $view = 'admin.expenses.edit';
        }

        $servicesList = Service::filterSource()
            ->ordered()
            ->showOnPricesTable()
            ->pluck('name', 'id')
            ->toArray();

        $statusList = ShippingStatus::filterSources()
            ->isVisible()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $vatRates = ['' => 'Auto'] + Invoice::getVatTaxes(true, true);

        $remoteZones = ZipCodeZone::filterSource()
            ->where('type', 'remote')
            ->pluck('name', 'code')
            ->toArray() ?? [];

        $billingItems = Item::filterSource()
            ->where('has_stock', true)
            ->get()
            ->pluck('name', 'id')->toArray() ?? [];

        $data = compact(
            'shippingExpense',
            'action',
            'formOptions',
            'billingZones',
            'trigger',
            'formTypes',
            'servicesList',
            'statusList',
            'vatRates',
            'triggerVariables',
            'triggerOperators',
            'everyFields',
            'remoteZones',
            'billingItems'
        );

        return view($view, $data)->render();
    }

    public function duplicate(Request $request, ShippingExpense $shippingExpense) {
        return $this->create($request, $shippingExpense);
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $shippingExpense = ShippingExpense::filterSource()
                        ->findOrfail($id);


        $billingZones = BillingZone::remember(config('cache.query_ttl'))
            ->cacheTags(BillingZone::CACHE_TAG)
            ->filterSource()
            ->orderBy('name')
            ->pluck('name', 'code')
            ->toArray();

        $formTypes  = $this->formTypes;
        $trigger    = trans('admin/expenses.triggers');
        $triggerVariables = trans('admin/expenses.trigger-fields');
        $triggerOperators = trans('admin/expenses.trigger-operators');
        $everyFields = trans('admin/expenses.every-fields');

        $formOptions = ['route' => array('admin.expenses.update', $shippingExpense->id), 'method' => 'PUT', 'class' => 'form-ajax'];

        if($shippingExpense->type == 'fuel') {
            $shippingExpense->name = 'Taxa Combustível';
            $action = 'Editar Taxa Combustível';
            $view   = 'admin.expenses.edit_fuel';
        } elseif($shippingExpense->type == 'dimensions') {
            $shippingExpense->name = 'Taxa Dimensões';
            $action = 'Editar Taxa Dimensões';
            $view   = 'admin.expenses.edit_dimensions';
        } else {
            $action = 'Editar Taxa Adicional';
            $view = 'admin.expenses.edit';
        }

        $servicesList = Service::filterSource()
            ->ordered()
            ->showOnPricesTable()
            ->pluck('name', 'id')
            ->toArray();

        $statusList = ShippingStatus::filterSources()
            ->isVisible()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $vatRates = ['' => 'Auto'] + Invoice::getVatTaxes(true, true);

        $remoteZones = ZipCodeZone::filterSource()
            ->where('type', 'remote')
            ->pluck('name', 'code')
            ->toArray() ?? [];

        $billingItems = Item::filterSource()
            ->where('has_stock', true)
            ->get()
            ->pluck('name', 'id')->toArray() ?? [];

        $data = compact(
            'shippingExpense',
            'action',
            'formOptions',
            'billingZones',
            'trigger',
            'formTypes',
            'servicesList',
            'statusList',
            'vatRates',
            'triggerVariables',
            'triggerOperators',
            'everyFields',
            'remoteZones',
            'billingItems'
        );

        return view($view, $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id) {

        ShippingExpense::flushCache(ShippingExpense::CACHE_TAG);
        ShipmentExpense::flushCache(ShipmentExpense::CACHE_TAG);

        $input = $request->all();

        $input['complementar_service']            = $request->get('complementar_service',false);
        $input['collection_complementar_service'] = $request->get('collection_complementar_service', false);
        $input['account_complementar_service']    = $request->get('account_complementar_service', false);
        $input['customer_customization']          = $request->get('customer_customization',false);
        $input['trigger_services']                = $request->get('trigger_services',false);
        $input['has_range_prices']                = $request->get('has_range_prices',false);

        if(isset($input['zones_arr'])) {
            $totalRows = count($input['zones_arr']);
            $input['zones_arr'] = array_filter($input['zones_arr']);
            $validRows = count($input['zones_arr']);


            $input['uid_arr'] = [];
            foreach ($input['zones_arr'] as $key => $zoneCode) {
                $input['uid_arr'][$key] = @$input['services_arr'][$key].'#'.@$input['zones_arr'][$key];
            }

            for ($i = $validRows ; $i<$totalRows ; $i++) {
                unset($input['ranges_arr'][$i]);
                unset($input['values_arr'][$i]);
                unset($input['unity_arr'][$i]);
                unset($input['trigger_arr'][$i]);
                unset($input['base_price_arr'][$i]);
                unset($input['min_price_arr'][$i]);
                unset($input['max_price_arr'][$i]);
                unset($input['vat_rate_arr'][$i]);
            }
        }

        if(!$input['has_range_prices']) {
            $input['ranges_arr'][] = null;
        }

        if(isset($input['trigger_fields'])) {
            $totalRows = count($input['trigger_fields']);
            $input['trigger_fields'] = array_filter($input['trigger_fields']);
            $validRows = count($input['trigger_fields']);

            for ($i = $validRows ; $i<$totalRows ; $i++) {
                unset($input['trigger_joins'][$i]);
                unset($input['trigger_operators'][$i]);
                unset($input['trigger_values'][$i]);
            }
        }

        $shippingExpense = ShippingExpense::filterSource()->findOrNew($id);


        if ($shippingExpense->validate($input)) {
            $shippingExpense->fill($input);
            $shippingExpense->save();

            return Response::json([
                'result'   => true,
                'feedback' => 'Dados gravados com sucesso.'
            ]);
        }

        return Response::json([
            'result'   => false,
            'feedback' => $shippingExpense->errors()->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        ShippingExpense::flushCache(ShippingExpense::CACHE_TAG);
        ShipmentExpense::flushCache(ShipmentExpense::CACHE_TAG);

        $result = ShippingExpense::filterSource()
                        ->whereId($id)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a taxa adicional.');
        }

        return Redirect::back()->with('success', 'Taxa adicional removida com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        ShippingExpense::flushCache(ShippingExpense::CACHE_TAG);
        ShipmentExpense::flushCache(ShipmentExpense::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = ShippingExpense::filterSource()
                    ->whereIn('id', $ids)
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

        $servicesArr   = Service::filterSource()->pluck('name', 'id')->toArray();
        $servicesCodes = Service::filterSource()->pluck('display_code', 'id')->toArray();

        $data = ShippingExpense::filterSource()
            ->where('code', '<>', 'REC')
            ->select();

        if($request->type == 'fuel') {
            $data = $data->where('type', 'fuel');
        } else {

            $data = $data->where('type', '<>', 'fuel');

            //filter type
            $value = $request->type;
            if($request->has('type')) {
                $data = $data->where('type', $value);
            }
        }


        return Datatables::of($data)
            ->edit_column('code', function($row) {
                return view('admin.expenses.datatables.code', compact('row'))->render();
            })
            ->edit_column('type', function($row) {
                return view('admin.expenses.datatables.type', compact('row'))->render();
            })
            ->add_column('triggers', function($row) {
                return view('admin.expenses.datatables.triggers', compact('row'))->render();
            })
            ->edit_column('name', function($row) {
                return view('admin.expenses.datatables.name', compact('row'))->render();
            })
            ->edit_column('zones', function($row) use($servicesArr, $servicesCodes) {
                return view('admin.expenses.datatables.zones', compact('row', 'servicesArr', 'servicesCodes'))->render();
            })
            ->edit_column('min_price', function($row) {
                return view('admin.expenses.datatables.min_price', compact('row'))->render();
            })
            ->edit_column('settings', function($row) {
                return view('admin.expenses.datatables.settings', compact('row'))->render();
            })
            ->add_column('status', function($row) {
                return view('admin.expenses.datatables.status', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.expenses.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Remove the specified resource from storage.
     * GET /admin/services/sort
     *
     * @return Response
     */
    public function sortEdit() {

        $items = ShippingExpense::filterSource()
            ->orderBy('sort')
            ->get(['id', 'name']);

        $route = route('admin.expenses.sort.update');

        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/services/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request) {

        ShippingExpense::flushCache(ShippingExpense::CACHE_TAG);
        ShipmentExpense::flushCache(ShipmentExpense::CACHE_TAG);

        try {
            ShippingExpense::setNewOrder($request->ids);
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
}
