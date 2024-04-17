<?php

namespace App\Http\Controllers\Admin\FleetGest;

use App\Models\Billing\Item;
use App\Models\Brand;
use App\Models\BrandModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Auth, Setting;

class PartsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'fleet_parts';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',fleet_parts']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index() {

        $categories = trans('admin/fleet.parts.categories');

        $brands = Brand::filterSource()
            ->pluck('name', 'id')
            ->toArray();

        $models = BrandModel::whereHas('brand', function ($q) {
                $q->filterSource();
            })
            ->pluck('name', 'id')
            ->toArray();

        $data = compact(
            'categories',
            'brands',
            'models',
            'categories'
        );

        return $this->setContent('admin.fleet.parts.index', $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Item::flushCache(Item::CACHE_TAG);

        $part = Item::isFleetPart();
        if(!Auth::user()->isAdmin()) {
            $part = $part->filterSource();
        }

        $part = $part->findOrfail($id);

        $result = $part->delete();

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

        Item::flushCache(Item::CACHE_TAG);

        $ids = explode(',', $request->ids);

        $part = Item::isFleetPart()
            ->whereIn('id', $ids);

        if(!Auth::user()->isAdmin()) {
            $part = $part->filterSource();
        }

        $result = $part->delete();
        
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

        $data = Item::filterSource()
            ->filterRequest($request)
            ->isFleetPart()
            ->with(['brand', 'brandModel'])
            ->select();

        $currency = Setting::get('app_currency');

        return Datatables::of($data)
            ->edit_column('name', function($row) {
                return view('admin.billing.items.datatables.name', compact('row'))->render();
            })
            ->edit_column('category', function($row) {
                return view('admin.fleet.parts.datatables.parts.category', compact('row'))->render();
            })
            ->edit_column('provider_id', function($row) {
                return view('admin.billing.items.datatables.provider', compact('row'))->render();
            })
            ->edit_column('stock_total', function($row) {
                return view('admin.billing.items.datatables.stock_total', compact('row'))->render();
            })
            ->edit_column('price', function($row) use($currency) {
                return view('admin.billing.items.datatables.price', compact('row'))->render();
            })
            ->edit_column('is_active', function($row) use($currency) {
                return view('admin.billing.items.datatables.is_active', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.fleet.parts.datatables.parts.actions', compact('row'))->render();
            })
            ->make(true);
    }

}
