<?php

namespace App\Http\Controllers\Admin\Products;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Setting;
use App\Models\Product;

class ProductsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'products';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',products']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.products.items.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Adicionar Produto';
        
        $product = new Product;
                
        $formOptions = array('route' => array('admin.products.items.store'), 'method' => 'POST');
        
        $vatRates = $this->getVatRates();

        $data = compact(
            'product',
            'action',
            'formOptions',
            'vatRates'
        );

        return view('admin.products.items.edit', $data)->render();
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
        
        $action = 'Editar Produto';
        
        $product = Product::filterSource()
                        ->findOrfail($id);

        $formOptions = array('route' => array('admin.products.items.update', $product->id), 'method' => 'PUT');

        $vatRates = $this->getVatRates();

        $data = compact(
            'product',
            'action',
            'formOptions',
            'vatRates'
        );
        
        return view('admin.products.items.edit', $data)->render();
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
        
        $product = Product::filterSource()
                        ->findOrNew($id);

        if ($product->validate($input)) {
            $product->fill($input);
            $product->save();

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $product->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = Product::filterSource()
                        ->whereId($id)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o produto.');
        }

        return Redirect::back()->with('success', 'Produto removido com sucesso.');
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
        
        $result = Product::filterSource()
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

        $data = Product::filterSource()
                        ->select();

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.products.items.datatables.name', compact('row'))->render();
                })
                ->edit_column('price', function($row) {
                    return view('admin.products.items.datatables.price', compact('row'))->render();
                })
                ->edit_column('cost_price', function($row) {
                    return money($row->cost_price, Setting::get('app_currency'));
                })
                ->edit_column('vat_rate', function($row) {
                    return view('admin.products.items.datatables.vat', compact('row'))->render();
                })
                ->edit_column('stock', function($row) {
                    return view('admin.products.items.datatables.stock', compact('row'))->render();
                })
                ->add_column('select', function($row) {
                    return view('admin.partials.datatables.select', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.products.items.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }
    
    /**
     * Return list of vat rates
     * @return type
     */
    public function getVatRates() {
        return [
            'normal'        => 'Normal ('.Setting::get('vat_rate_normal').'%)',
            'intermediate'  => 'Intermédia ('.Setting::get('vat_rate_intermediate').'%)',
            'reduced'       => 'Reduzida ('.Setting::get('vat_rate_reduced').'%)',
            'is'            => 'Isento (0%)',
        ];
    }
}
