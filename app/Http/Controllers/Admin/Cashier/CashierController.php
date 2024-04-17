<?php

namespace App\Http\Controllers\Admin\Cashier;

use App\Models\Cashier\Movement;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Provider;
use App\Models\PurchaseInvoiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\User;
use Html, Auth, DB, Date, View, Setting;

class CashierController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'cashier';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',cashier']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $operators = User::filterAgencies()
            ->isActive()
            ->orderBy('name', 'asc')
            ->get()
            ->pluck('first_last_name', 'id')
            ->toArray();

        $purchasesTypes = PurchaseInvoiceType::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $paymentMethods = PaymentMethod::filterSource()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $data = compact(
            'operators',
            'purchasesTypes',
            'paymentMethods'
        );

        return $this->setContent('admin.cashier.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $movement = new Movement();

        $operators = User::filterAgencies()
            ->isActive()
            ->orderBy('name', 'asc')
            ->get()
            ->pluck('first_last_name', 'id')
            ->toArray();

        $purchasesTypes = PurchaseInvoiceType::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $paymentMethods = PaymentMethod::filterSource()
            ->isActive()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $action = 'Novo registo de valores';

        $formOptions = ['route' => ['admin.cashier.store'], 'method' => 'POST'];

        $data = compact(
            'movement',
            'action',
            'formOptions',
            'operators',
            'purchasesTypes',
            'paymentMethods'
        );
  
        return view('admin.cashier.edit', $data)->render();
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
//    public function show(Request $request, $id) {
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {

        $movement = Movement::filterSource()
            ->where('id', $id)
            ->first();

        $operators = User::filterAgencies()
            ->isActive()
            ->orderBy('name', 'asc')
            ->get()
            ->pluck('first_last_name', 'id')
            ->toArray();

        $purchasesTypes = PurchaseInvoiceType::filterSource()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();

        $paymentMethods = PaymentMethod::filterSource()
            ->isActive()
            ->ordered()
            ->pluck('name', 'code')
            ->toArray();

        $action = 'Editar registo de valores';

        $formOptions = ['route' => ['admin.cashier.update', $movement->id], 'method' => 'PUT'];

        $data = compact(
            'movement',
            'action',
            'formOptions',
            'operators',
            'purchasesTypes',
            'paymentMethods'
        );

        return view('admin.cashier.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $input = $request->all();

        $input['customer_id'] = $request->get('customer_id', null);
        $input['provider_id'] = $request->get('provider_id', null);
        $input['is_paid']     = $request->get('is_paid', false);
        $input['date']        = @$input['date'] ? $input['date'] : date('Y-m-d');

        $movement = Movement::filterSource()
                            ->findOrNew($id);

        if($movement->validate($input)) {
            $movement->fill($input);
            $movement->created_by = Auth::user()->id;
            $movement->source     = config('app.source');
            $movement->setMovementCode();

            return Redirect::back()->with('success', 'Movimento registado com sucesso.');
        }

        return Redirect::back()->with('error', 'Não foi possível registar o movimento.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = Movement::filterSource()
                    ->whereId($id)
                    ->delete($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o registo.');
        }

        return Redirect::route('admin.cashier.index')->with('success', 'Registo removido com sucesso.');
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

        $result = Movement::whereIn('id', $ids)->delete();
        
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

        $data = Movement::with('customer', 'provider', 'operator', 'type')
            ->with(['createdBy' => function($q){
                $q->withTrashed();
            }])
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

        //filter type
        $value = $request->get('type_id');
        if($request->has('type_id')) {
            $data = $data->where('type_id', $value);
        }

        //filter operator
        $value = $request->get('operator');
        if($request->has('operator')) {
            $data = $data->where('operator_id', $value);
        }

        //filter created by
        $value = $request->get('created_by');
        if($request->has('created_by')) {
            $data = $data->where('created_by', $value);
        }

        //filter customer
        $value = $request->get('customer');
        if($request->has('customer')) {
            $data = $data->where('customer_id', $value);
        }

        //filter provider
        $value = $request->get('provider');
        if($request->has('provider')) {
            $data = $data->where('provider_id', $value);
        }
        
        //filter sense
        $value = $request->get('sense');
        if($request->has('sense')) {
            $data = $data->where('sense', $value);
        }

        //filter payment method
        $value = $request->get('payment_method');
        if($request->has('payment_method')) {
            $data = $data->where('payment_method', $value);
        }

        //filter is paid
        $value = $request->get('paid');
        if($request->has('paid')) {
            $data = $data->where('is_paid', $value);
        }

        return Datatables::of($data)
            ->edit_column('code', function($row) {
                return view('admin.cashier.datatables.code', compact('row'))->render();
            })
            ->edit_column('date', function($row) {
                return $row->date->format('Y-m-d');
            })
            ->edit_column('type_id', function($row) {
                return view('admin.cashier.datatables.type', compact('row'))->render();
            })
            ->edit_column('customer', function($row) {
                return view('admin.cashier.datatables.customer', compact('row'))->render();
            })
            ->add_column('operator_id', function($row) {
                return view('admin.cashier.datatables.operator', compact('row'))->render();
            })
            ->edit_column('description', function($row) {
                return view('admin.cashier.datatables.description', compact('row'))->render();
            })
            ->edit_column('amount', function($row) {
                return view('admin.cashier.datatables.amount', compact('row'))->render();
            })
            ->edit_column('payment_method', function($row) {
                return view('admin.cashier.datatables.payment_method', compact('row'))->render();
            })
            ->edit_column('is_paid', function($row) {
                return view('admin.cashier.datatables.is_paid', compact('row'))->render();
            })
            ->edit_column('created_by', function($row) {
                return @$row->createdBy->name;
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.cashier.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Replicate movement
     *
     * @return type
     */
    public function replicate(Request $request, $id) {

        $originalMovement = Movement::filterSource()
            ->findOrFail($id);


        $movement = $originalMovement->replicate();
        $movement->code    = null;
        $movement->date    = date('Y-m-d');
        $movement->is_paid = false;
        $movement->setMovementCode();

        if ($movement) {
            return Redirect::route('admin.cashier.index', ['action' => 'edit', 'movement' => $movement->id])->with('success', 'Registo duplicado com sucesso.');
        }

        return Redirect::back()->with('error', 'Ocorreu um erro ao tentar duplicar o registo.');

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
            
            $customers = Customer::filterSource()
                ->filterAgencies()
                ->where(function($q) use($search){
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
                ->get(['name', 'code', 'id']);

            if($customers) {
                
                $results = array();
                foreach($customers as $customer) {
                    $results[] = [
                        'id'=> $customer->id,
                        'text' => $customer->code. ' - '.str_limit($customer->name, 40)
                    ];
                }
                
            } else {
                $results = [[
                    'id' => '',
                    'text' => 'Nenhum cliente encontrado.'
                ]];
            }
           
        } catch(\Exception $e) {
            $results = [[
                'id' => '',
                'text' => 'Erro interno ao processar o pedido.'
            ]];
        }

        return response()->json($results);
    }

    /**
     * Search customers on DB
     *
     * @return type
     */
    public function searchProvider(Request $request) {

        $search = $request->get('q');
        $search = '%' . str_replace(' ', '%', $search) . '%';

        try {

            $providers = Provider::filterSource()
                ->filterAgencies()
                ->where(function($q) use($search){
                    $q->where('code', 'LIKE', $search)
                        ->orWhere('name', 'LIKE', $search)
                        ->orWhere('vat', 'LIKE', $search)
                        ->orWhere('phone', 'LIKE', $search);
                })
                ->get(['name', 'code', 'id']);

            if($providers) {

                $results = array();
                foreach($providers as $provider) {
                    $results[] = [
                        'id'=> $provider->id,
                        'text' => $provider->code. ' - '.str_limit($provider->name, 40)
                    ];
                }

            } else {
                $results = [[
                    'id' => '',
                    'text' => 'Nenhum fornecedor encontrado.'
                ]];
            }

        } catch(\Exception $e) {
            $results = [[
                'id' => '',
                'text' => 'Erro interno ao processar o pedido.'
            ]];
        }

        return response()->json($results);
    }
}
