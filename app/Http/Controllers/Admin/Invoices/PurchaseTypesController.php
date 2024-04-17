<?php

namespace App\Http\Controllers\Admin\Invoices;

use App\Models\PurchaseInvoiceType;
use Html, Response, Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;

class PurchaseTypesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'purchase-invoices';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',purchase_invoices']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $purchaseType = new PurchaseInvoiceType();

        $formOptions = array('route' => array('admin.invoices.purchase.types.store'), 'method' => 'POST', 'class' => 'modal-ajax-form');

        return view('admin.invoices.purchases.types.index', compact('purchaseType', 'formOptions'))->render();
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        PurchaseInvoiceType::flushCache(PurchaseInvoiceType::CACHE_TAG);

        $input = $request->all();
        
        $purchaseType = PurchaseInvoiceType::filterSource()->findOrNew($id);

        if ($purchaseType->validate($input)) {
            $purchaseType->fill($input);
            $purchaseType->save();

            $row = $purchaseType;
            return Response::json([
                'result'   => true,
                'feedback' => 'Dados gravados com sucesso.',
                'html'     => view('admin.invoices.purchases.types.datatables.name', compact('row'))->render()
            ]);
        }

        return Response::json([
            'result'   => false,
            'feedback' => $purchaseType->errors()->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        PurchaseInvoiceType::flushCache(PurchaseInvoiceType::CACHE_TAG);

        $result = PurchaseInvoiceType::filterSource()
                                ->whereId($id)
                                ->delete();

        if (!$result) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Ocorreu um erro ao tentar remover o tipo de despesa.'
            ]);
        }

        return Response::json([
            'result'   => true,
            'feedback' => 'Tipo de despesa removido com sucesso.'
        ]);
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        PurchaseInvoiceType::flushCache(PurchaseInvoiceType::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = PurchaseInvoiceType::filterSource()
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

        $data = PurchaseInvoiceType::filterSource()->select();

        return Datatables::of($data)
            ->edit_column('name', function($row) {
                return view('admin.invoices.purchases.types.datatables.name', compact('row'))->render();
            })
            ->edit_column('target_type', function($row) {
                return view('admin.invoices.purchases.types.datatables.target', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.invoices.purchases.types.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }
}
