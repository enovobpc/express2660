<?php

namespace App\Http\Controllers\Admin\Equipments;

use App\Models\Equipment\Category;
use App\Models\Equipment\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use Html, Response, Cache, Jenssegers\Date\Date;

class CategoriesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'equipments_categories';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',equipments_categories']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $category = new Category();

        $formOptions = array('route' => array('admin.equipments.categories.store'), 'method' => 'POST', 'class' => 'modal-ajax-form');

        return view('admin.equipments.categories.index', compact('category', 'formOptions'))->render();
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
        
        $input = $request->all();

        $category = Category::filterSource()->findOrNew($id);

        if ($category->validate($input)) {
            $category->fill($input);
            $category->source = config('app.source');
            $category->save();

            $row = $category;

            return Response::json([
                'result'   => true,
                'feedback' => 'Dados gravados com sucesso.',
                'html'     => view('admin.equipments.categories.datatables.name', compact('row'))->render()
            ]);
        }

        return Response::json([
            'result'   => false,
            'feedback' => $category->errors()->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        $result = Category::filterSource()
            ->whereId($id)
            ->delete();

        if (!$result) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Ocorreu um erro ao tentar remover a categoria.'
            ]);
        }

        return Response::json([
            'result'   => true,
            'feedback' => 'Categoria removida com sucesso.'
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

        $ids = explode(',', $request->ids);

        $result = Category::filterSource()
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

        $data = Category::filterSource()->select();

        return Datatables::of($data)
            ->edit_column('name', function($row) {
                return view('admin.equipments.categories.datatables.name', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.equipments.categories.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function history(Request $request, $id) {

        $minDate = $request->get('min_date');
        $maxDate = $request->get('max_date');

        if(!$minDate) {
            $minDate = new Date();
            $minDate = $minDate->subDays(30)->format('Y-m-d');
        }

        $maxDate = $maxDate ? $maxDate : date('Y-m-d');
        $minDate = $minDate . ' 00:00:00';
        $maxDate = $maxDate . ' 23:59:59';

        $histories = History::with('equipment')
            ->whereHas('equipment', function($q) use($id) {
                $q->filterSource();
                $q->where('category_id', $id);
            })
            ->whereBetween('created_at', [$minDate, $maxDate]);

        if($request->get('action')) {
            $histories = $histories->where('action', $request->get('action'));
        }

        $histories = $histories->get();

        $data = compact(
            'histories'
        );

        return view('admin.equipments.categories.history', $data)->render();
    }
}
