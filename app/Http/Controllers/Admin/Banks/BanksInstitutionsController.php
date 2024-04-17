<?php

namespace App\Http\Controllers\Admin\Banks;

use App\Models\BankInstitution;
use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Redirect;
use Auth;

class BanksInstitutionsController extends \App\Http\Controllers\Admin\Controller {

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
        
        $action = 'Adicionar Instituição Bancária';
        
        $bank = new BankInstitution();
                
        $formOptions = array('route' => array('admin.banks-institutions.store'), 'method' => 'POST');
        
        return view('admin.banks.edit_bank_institution', compact('bank', 'action', 'formOptions'))->render();
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
        
        $action = 'Editar Instituição Bancária';
        
        if(Auth::user()->isAdmin()) {
            $bank = BankInstitution::findOrfail($id);
        } else {
            $bank = BankInstitution::where('source', config('app.source'))->findOrfail($id);
        }
        
        $formOptions = array('route' => array('admin.banks-institutions.update', $bank->id), 'method' => 'PUT');

        return view('admin.banks.edit_bank_institution', compact('bank', 'action', 'formOptions'))->render();
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
        $input['is_active'] = $request->get('is_active', false);
        $input['bank_code'] = str_pad($request->get('bank_code'), 4, '0', STR_PAD_LEFT);
        $input['code']      = strtoupper($input['country'].$input['bank_code']);
        $input['bank_iban'] = strtoupper($input['country_code'].$input['bank_code']);

        $exists = BankInstitution::where('bank_iban', $input['bank_iban'])->first();
        if(!empty($exists)){
            return Redirect::back()->with('error', 'Banco já existente.');
        }

        if(Auth::user()->isAdmin()) {
            $bank = BankInstitution::findOrNew($id);
        } else {
            $bank = BankInstitution::where('source', config('app.source'))->findOrNew($id);
        }

        if ($bank->validate($input)) {
            $bank->fill($input);

            if(!Auth::user()->isAdmin()) {
                $bank->source     = config('app.source');
                $bank->created_by = Auth::user()->id;
            }
            
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
        
        $result = BankInstitution::filterSource()->whereId($id)->delete();

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
        
        $result = BankInstitution::whereIn('id', $ids)->delete();
        
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

        $data = BankInstitution::select();

        $value = $request->get('country');
        if($request->has('country')) {
            $data = $data->where('country', $value);
        }

        return Datatables::of($data)
            ->editColumn('id', function($row) {
                return view('admin.banks.datatables.banks_institutions.country', compact('row'));
            })
            ->editColumn('bank_name', function($row) {
                return view('admin.banks.datatables.banks_institutions.name', compact('row'));
            })
            ->editColumn('active', function($row) {
                return view('admin.banks.datatables.banks_institutions.active', compact('row'));
            })
            ->addColumn('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->addColumn('actions', function($row) {
                return view('admin.banks.datatables.banks_institutions.actions', compact('row'))->render();
            })
            ->make(true);
    }

}
