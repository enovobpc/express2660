<?php

namespace App\Http\Controllers\Admin\FleetGest;

use App\Models\FleetGest\Expense;
use App\Models\FleetGest\Vehicle;
use App\Models\FleetGest\Service;
use App\Models\Provider;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, File, Response, Setting;

class ExpensesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_expenses';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',fleet_expenses']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $vehicles = Vehicle::remember(config('cache.query_ttl'))
            ->cacheTags(Vehicle::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->orderBy('name', 'desc')
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterSource()
            ->ignoreAdmins()
            ->isActive()
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $allProviders = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->with('category')
            ->where(function ($q) {
                $q->where('source', config('app.source'));
                $q->orWhereNull('source');
            })
            ->get();

        $tollsProviders = $allProviders->filter(function($item) {
                return @$item->category->slug == 'tolls'; //->category_id == 5;
            })
            ->pluck('name', 'id')
            ->toArray();

        $allProviders = $allProviders->pluck('name', 'id')->toArray();

        $purchasesTypes = PurchaseInvoiceType::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'vehicles',
            'operators',
            'allProviders',
            'tollsProviders',
            'purchasesTypes'
        );

        return $this->setContent('admin.fleet.expenses.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $action = 'Registar Despesa';
        
        $expense = new Expense;
                
        $formOptions = array('route' => array('admin.fleet.expenses.store'), 'method' => 'POST', 'files' => true);

        $vehicles = Vehicle::remember(config('cache.query_ttl'))
            ->cacheTags(Vehicle::CACHE_TAG)
            ->filterSource()
            ->isActive()
            ->orderBy('name', 'desc')
            ->pluck('name', 'id')
            ->toArray();

        if($request->has('vehicle')) {
            $vehicles = [$request->vehicle => @$vehicles[$request->vehicle]];
        }

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterSource()
            ->whereType('others')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterSource()
            ->ignoreAdmins()
            ->isActive()
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $purchasesTypes = PurchaseInvoiceType::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'expense',
            'vehicles',
            'providers',
            'operators',
            'purchasesTypes',
            'action',
            'formOptions'
        );

        return view('admin.fleet.expenses.edit', $data)->render();
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
        
        $action = 'Editar Despesa';
        
        $expense = Expense::with('vehicle')
                ->filterSource()
                ->findOrfail($id);

        $formOptions = array('route' => array('admin.fleet.expenses.update', $expense->id), 'method' => 'PUT', 'files' => true);

        $providers = Provider::remember(config('cache.query_ttl'))
            ->cacheTags(Provider::CACHE_TAG)
            ->filterSource()
            ->whereType('others')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $operators = User::remember(config('cache.query_ttl'))
            ->cacheTags(User::CACHE_TAG)
            ->filterSource()
            ->ignoreAdmins()
            ->isActive()
            ->orderBy('source', 'asc')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $purchasesTypes = PurchaseInvoiceType::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $vehicles = [$expense->vehicle_id => $expense->vehicle->name];

        $data = compact(
            'expense',
            'operators',
            'providers',
            'purchasesTypes',
            'action',
            'formOptions',
            'vehicles'
        );

        return view('admin.fleet.expenses.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        Expense::flushCache(Expense::CACHE_TAG);

        $input = $request->all();

        $expense = Expense::filterSource()
                        ->findOrNew($id);

        if ($expense->validate($input)) {

            //create service if service_id is empty
            $service = Service::firstOrNew([
                'name' => trim($input['title']),
                'type' => 'expense'
            ]);

            if(empty($input['expense_id'])) {
                $service->source = config('app.source');
                $service->save();
            }

            $input['service_id'] = $service->id;

            //store expense
            $expense->fill($input);
            $expense->save();

            //delete file
            if ($request->delete_file && !empty($expense->filepath)) {
                File::delete(public_path().'/'.$expense->filepath);
                $expense->filepath = null;
                $expense->filename = null;
            }

            //upload file
            if($request->hasFile('file')) {

                if ($expense->exists && !empty($expense->filepath)) {
                    File::delete(storage_path().'/'.$expense->filepath);
                }

                if (!$expense->upload($request->file('file'), true, 20)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível carregar o ficheiro.');
                }

            } else {
                $expense->save();
            }

            //update vehicle counters
            Expense::updateVehicleCounters($expense->vehicle_id);

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $expense->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Expense::flushCache(Expense::CACHE_TAG);

        $result = Expense::filterSource()
                    ->find($id)
                    ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o registo.');
        }

        return Redirect::back()->with('success', 'Registo removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        Expense::flushCache(Expense::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $rows = Expense::filterSource()
                ->whereIn('id', $ids)
                ->get();

        foreach($rows as $row) {
            $result = $row->delete();
        }
        
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

        $data = Expense::with('vehicle', 'operator', 'provider', 'invoice')
            ->filterSource()
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

        //filter vehicle
        $value = $request->get('vehicle');
        if($request->has('vehicle')) {
            $data = $data->where('vehicle_id', $value);
        }

        //filter provider
        $value = $request->get('provider');
        if($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }

        //filter operator
        $value = $request->get('operator');
        if($request->has('operator')) {
            $data = $data->where('operator_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('date', function($row) {
                return $row->date->format('Y-m-d');
            })
            ->edit_column('vehicle_id', function($row) {
                return view('admin.fleet.expenses.datatables.vehicle', compact('row'))->render();
            })
            ->edit_column('provider_id', function($row) {
                return @$row->provider->name;
            })
            ->edit_column('operator_id', function($row) {
                return @$row->operator->name;
            })
            ->edit_column('assigned_invoice_id', function($row) {
                return view('admin.fleet.vehicles.datatables.invoice', compact('row'))->render();
            })
            ->edit_column('km', function($row) {
                return view('admin.fleet.vehicles.datatables.km', compact('row'))->render();
            })
            ->edit_column('total', function($row) {
                return view('admin.fleet.vehicles.datatables.total', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.fleet.expenses.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }


    /**
     * Search fleet maintenance services
     * GET /admin/fleet/maintenances/search/services
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchExpenses(Request $request) {

        $search = trim($request->get('query'));
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {

            $services = Service::where(function($q) use($search){
                    $q->where('name', 'LIKE', $search);
                })
                ->isExpense()
                ->get(['name', 'id']);

            if($services) {

                $results = array();
                foreach($services as $service) {
                    $results[] = ['data' => $service->id, 'value' => str_limit($service->name, 40)];
                }

            } else {
                $results = ['Nenhum serviço encontrado.'];
            }

        } catch(\Exception $e) {
            $results = ['Erro interno ao processar o pedido.'];
        }

        $results = [
            'suggestions' => $results
        ];

        return Response::json($results);
    }
}
