<?php

namespace App\Http\Controllers\Admin\Billing;

use App\Models\Billing\ApiKey;
use App\Models\Company;
use Response, Setting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;

class ApiKeysController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'billing-items';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',billing_api_keys']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //public function index(){}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $apiKey = new ApiKey();
        $apiKey->start_date = date('Y-m-d');
        $apiKey->is_active  = true;

        $companies = Company::filterSource()
            ->pluck('display_name', 'id')
            ->toArray();

        $action = 'Ligar a API de Faturação';

        $formOptions = array('route' => array('admin.billing.api-keys.store'), 'method' => 'POST', 'class' => 'form-apikey');

        $data = compact(
            'apiKey',
            'companies',
            'action',
            'formOptions'
        );

        return view('admin.billing.api_keys.edit', $data)->render();
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
    public function edit($id)
    {
        $apiKey = ApiKey::filterSource()
            ->findOrfail($id);

        $companies = Company::filterSource()
            ->pluck('display_name', 'id')
            ->toArray();

        $action = 'Ligar a API de Faturação';

        $formOptions = array('route' => array('admin.billing.api-keys.update', $apiKey->id), 'method' => 'PUT', 'class' => 'form-vatrates');

        $data = compact(
            'apiKey',
            'action',
            'formOptions',
            'companies'
        );

        return view('admin.billing.api_keys.edit', $data)->render();
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

        ApiKey::flushCache(ApiKey::CACHE_TAG);

        $input = $request->all();
        $input['is_active']    = $request->get('is_active', false);
        $input['is_default']   = $request->get('is_default', false);

        if($input['is_default']) {
            ApiKey::where('id', '>=', 1)->update(['is_default' => 0]);
        }

        $apiKey = ApiKey::filterSource()->findOrNew($id);

        if ($apiKey->validate($input)) {
            $apiKey->fill($input);
            $apiKey->source = config('app.source');
            $apiKey->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $apiKey->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ApiKey::flushCache(ApiKey::CACHE_TAG);

        $result = ApiKey::filterSource()
            ->destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o registo');
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
    public function massDestroy(Request $request)
    {

        ApiKey::flushCache(ApiKey::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $result = ApiKey::filterSource()
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
    public function datatable(Request $request)
    {
        $data = ApiKey::filterSource()
            ->select();

        //filter company
        $value = $request->active;
        if($request->has('active')) {
            $data = $data->where('is_active', $value);
        }

        return Datatables::of($data)
            ->edit_column('company_id', function ($row) {
                return view('admin.billing.api_keys.datatables.company', compact('row'))->render();
            })
            ->edit_column('name', function ($row) {
                return view('admin.billing.api_keys.datatables.name', compact('row'))->render();
            })
            ->edit_column('start_date', function ($row) {
                return view('admin.billing.api_keys.datatables.start_date', compact('row'))->render();
            })
            ->edit_column('is_active', function ($row) {
                return view('admin.billing.api_keys.datatables.is_active', compact('row'))->render();
            })
            ->edit_column('created_at', function ($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.billing.api_keys.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }
}
