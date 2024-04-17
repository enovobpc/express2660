<?php

namespace App\Http\Controllers\Admin\Billing;

use App\Models\Billing\VatRate;
use App\Models\Company;
use Response, Setting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;

use App\Models\Invoice;

class VatRatesController extends \App\Http\Controllers\Admin\Controller
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
        $this->middleware(['ability:' . config('permissions.role.admin') . ',vat_rates']);
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
        $vatRate = new VatRate();
        $vatRate->is_active = true;
        $vatRate->is_sales  = true;

        $companies = Company::filterSource()
            ->pluck('display_name', 'id')
            ->toArray();

        $exemptionReasons = Invoice::getExemptionReasons();

        $valueBlocked = '';

        $action = 'Adicionar Taxa IVA';

        $formOptions = array('route' => array('admin.billing.vat-rates.store'), 'method' => 'POST', 'class' => 'form-vatrates');

        $data = compact(
            'vatRate',
            'companies',
            'action',
            'formOptions',
            'valueBlocked',
            'exemptionReasons'
        );

        return view('admin.billing.vat_rates.edit', $data)->render();
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
        $vatRate = VatRate::filterSource()
            ->findOrfail($id);

        $companies = Company::filterSource()
            ->pluck('display_name', 'id')
            ->toArray();

        $exemptionReasons = Invoice::getExemptionReasons();

        $valueBlocked = $vatRate->checkUsage() && !empty($vatRate->billing_code);
        $valueBlocked = $valueBlocked ? 'disabled' : '';

        $action = 'Editar Taxa IVA';

        $formOptions = array('route' => array('admin.billing.vat-rates.update', $vatRate->id), 'method' => 'PUT', 'class' => 'form-vatrates');

        $data = compact(
            'vatRate',
            'action',
            'formOptions',
            'companies',
            'valueBlocked',
            'exemptionReasons'
        );

        return view('admin.billing.vat_rates.edit', $data)->render();
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

        VatRate::flushCache(VatRate::CACHE_TAG);

        $input = $request->all();
        $input['is_active']    = $request->get('is_active', false);
        $input['is_default']   = $request->get('is_default', false);
        $input['is_sales']     = $request->get('is_sales', false);
        $input['is_purchases'] = $request->get('is_purchases', false);

        if($input['is_default']) {
            VatRate::where('id', '>=', 1)->update(['is_default' => 0]);
        }

        if(@$input['subclass'] == 'na') {
            $input['exemption_reason'] = 'M99';
            $input['value'] = '0.00';
        } elseif(@$input['subclass'] == 'ise') {
            $input['value'] = '0.00';
        }

        $vatRate = VatRate::filterSource()->findOrNew($id);

        if ($vatRate->validate($input)) {
            $vatRate->fill($input);
            $vatRate->source = config('app.source');
            $vatRate->save();

            if($vatRate->subclass == 'nor' && $vatRate->is_default) {
                Setting::set('vat_rate_normal', $vatRate->value);
                Setting::save();
            }

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $vatRate->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        VatRate::flushCache(VatRate::CACHE_TAG);

        $vatRate = VatRate::filterSource()
            ->find($id);

        if($vatRate->checkUsage()) {
            return Redirect::back()->with('error', 'Não pode eliminar a taxa porque está referênciada em documentos.');
        }

        $result = $vatRate->delete();
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

        VatRate::flushCache(VatRate::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $result = VatRate::filterSource()
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

        $data = VatRate::filterSource()
            ->select();

        //filter company
        $value = $request->company;
        if($request->has('company')) {
            $data = $data->where('company_id', $value);
        }

        return Datatables::of($data)
            ->edit_column('company_id', function ($row) {
                return view('admin.billing.vat_rates.datatables.company', compact('row'))->render();
            })
            ->edit_column('name', function ($row) {
                return view('admin.billing.vat_rates.datatables.name', compact('row'))->render();
            })
            ->edit_column('class', function ($row) {
                return view('admin.billing.vat_rates.datatables.class', compact('row'))->render();
            })
            ->edit_column('subclass', function ($row) {
                return view('admin.billing.vat_rates.datatables.subclass', compact('row'))->render();
            })
            ->edit_column('zone', function ($row) {
                return view('admin.billing.vat_rates.datatables.zone', compact('row'))->render();
            })
            ->edit_column('value', function ($row) {
                return view('admin.billing.vat_rates.datatables.value', compact('row'))->render();
            })
            ->edit_column('is_sales', function ($row) {
                return view('admin.billing.vat_rates.datatables.is_sales', compact('row'))->render();
            })
            ->edit_column('is_purchases', function ($row) {
                return view('admin.billing.vat_rates.datatables.is_purchases', compact('row'))->render();
            })
            ->edit_column('is_active', function ($row) {
                return view('admin.billing.vat_rates.datatables.is_active', compact('row'))->render();
            })
            ->edit_column('exemption_reason', function ($row) {
                return view('admin.billing.vat_rates.datatables.exemption_reason', compact('row'))->render();
            })
            ->edit_column('billing_code', function ($row) {
                return view('admin.billing.vat_rates.datatables.billing_code', compact('row'))->render();
            })
            ->add_column('select', function ($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function ($row) {
                return view('admin.billing.vat_rates.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }


    /**
     * Remove the specified resource from storage.
     * GET /admin/services/sort
     *
     * @return Response
     */
    public function sortEdit()
    {
        $items = VatRate::filterSource()
            ->orderBy('sort')
            ->get(['id', 'name']);

        $route = route('admin.billing.vat-rates.sort.update');

        return view('admin.partials.modals.sort', compact('items', 'route'))->render();
    }

    /**
     * Update the specified resource order in storage.
     * POST /admin/billing.items/sort
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sortUpdate(Request $request)
    {

        VatRate::flushCache(VatRate::CACHE_TAG);

        try {
            VatRate::setNewOrder($request->ids);

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
