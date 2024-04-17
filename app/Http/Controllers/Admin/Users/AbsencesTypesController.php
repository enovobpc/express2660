<?php

namespace App\Http\Controllers\Admin\Users;

use Html, Response, Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\UserAbsenceType;

class AbsencesTypesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'absences';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',users_absences']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $absenceType = new UserAbsenceType();

        $formOptions = array('route' => array('admin.users.absences-types.store'), 'method' => 'POST', 'class' => 'modal-ajax-form');

        return view('admin.users.users.absences_types.index', compact('absenceType', 'formOptions'))->render();
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

        UserAbsenceType::flushCache(UserAbsenceType::CACHE_TAG);

        $input = $request->all();
        
        $absenceType = UserAbsenceType::filterSource()->findOrNew($id);

        if ($absenceType->validate($input)) {
            $absenceType->fill($input);
            $absenceType->source = config('app.source');
            $absenceType->save();

            $row = $absenceType;
            return Response::json([
                'result'   => true,
                'feedback' => 'Dados gravados com sucesso.',
                'html'     => view('admin.users.users.absences_types.datatables.name', compact('row'))->render()
            ]);
        }

        return Response::json([
            'result'   => false,
            'feedback' => $absenceType->errors()->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        UserAbsenceType::flushCache(UserAbsenceType::CACHE_TAG);

        $result = UserAbsenceType::filterSource()
                                ->whereId($id)
                                ->delete();

        if (!$result) {
            return Response::json([
                'result'   => false,
                'feedback' => 'Ocorreu um erro ao tentar remover o tipo de ausência.'
            ]);
        }

        return Response::json([
            'result'   => true,
            'feedback' => 'Tipo de ausência removido com sucesso.'
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

        UserAbsenceType::flushCache(UserAbsenceType::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = UserAbsenceType::filterSource()
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

        $data = UserAbsenceType::where(function($q){
                $q->whereNull('source');
                $q->orWhere('source', config('app.source'));
            })
            ->select();

        return Datatables::of($data)
                ->edit_column('name', function($row) {
                    return view('admin.users.users.absences_types.datatables.name', compact('row'))->render();
                })
                ->add_column('actions', function($row) {
                    return view('admin.users.users.absences_types.datatables.actions', compact('row'))->render();
                })
                ->make(true);
    }
}
