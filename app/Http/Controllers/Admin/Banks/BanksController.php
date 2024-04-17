<?php

namespace App\Http\Controllers\Admin\Banks;

use App\Models\Bank;
use App\Models\BankInstitution;
use App\Models\Company;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Redirect;
use Response;

class BanksController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'banks';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',banks']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        return $this->setContent('admin.banks.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Adicionar Banco';
        
        $bank = new Bank();

        $banks = BankInstitution::listBanks();

        $companies = Company::filterSource()
            ->pluck('display_name', 'id')
            ->toArray();
                
        $formOptions = array('route' => array('admin.banks.store'), 'method' => 'POST');
        
        $data = compact(
            'bank', 
            'banks',
            'companies',
            'action',
            'formOptions'
        );

        return view('admin.banks.edit', $data)->render();
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
        
        $action = 'Editar Banco';
        
        $bank = Bank::filterSource()->findOrfail($id);

        $banks = BankInstitution::listBanks();

        $companies = Company::filterSource()
            ->pluck('display_name', 'id')
            ->toArray();

        $formOptions = array('route' => array('admin.banks.update', $bank->id), 'method' => 'PUT');

        $data = compact(
            'bank', 
            'banks',
            'companies',
            'action',
            'formOptions'
        );

        return view('admin.banks.edit', $data)->render();
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
        $input['is_active']  = $request->get('is_active', false);

        $bankInstitution = BankInstitution::where('code', $input['bank_code'])->first();
        $input['bank_name']  = @$bankInstitution->bank_name;
        $input['bank_swift'] = @$bankInstitution->bank_swift;

        $bank = Bank::filterSource()->findOrNew($id);

        if ($bank->validate($input)) {
            $bank->fill($input);
            $bank->source = config('app.source');
            $bank->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $bank->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = Bank::whereId($id)->delete();

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
        
        $ids = explode(',', $request->ids);
        
        $result = Bank::whereIn('id', $ids)->delete();
        
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

        $data = Bank::select();

        return Datatables::of($data)
            ->editColumn('company_id', function($row) {
                return view('admin.banks.datatables.company', compact('row'));
            })
            ->editColumn('name', function($row) {
                return view('admin.banks.datatables.name', compact('row'));
            })
            ->editColumn('titular_name', function($row) {
                return view('admin.banks.datatables.titular', compact('row'));
            })
            ->editColumn('bank_iban', function($row) {
                return view('admin.banks.datatables.iban', compact('row'));
            })
            ->editColumn('active', function($row) {
                return view('admin.banks.datatables.active', compact('row'));
            })
            ->addColumn('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->addColumn('actions', function($row) {
                return view('admin.banks.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Remove the specified resource from storage.
     * GET /admin/features/sort
     *
     * @return Response
     */
    public function sortEdit() {

        $items = Bank::orderBy('sort')->get();

        $route = route('admin.banks.sort.update');

        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/features/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request) {

        $result = Bank::setNewOrder($request->ids);

        $response = [
            'message' => 'Ordenação gravada com sucesso.',
            'type'    => 'success'
        ];

        return Response::json($response);
    }
}
