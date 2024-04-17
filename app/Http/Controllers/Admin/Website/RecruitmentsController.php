<?php

namespace App\Http\Controllers\Admin\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Website\Recruitment;

class RecruitmentsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'recruitments';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',recruitments']);
        validateModule('website_recruitments');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        $status = array(
            '1' => 'Lido',
            '0' => 'Não Lido'
        );
        
        $cities = Recruitment::groupBy('city')->pluck('city', 'city')->toArray();
        
        return $this->setContent('admin.website.recruitments.index', compact('status', 'cities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function create() {
//    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
//    public function store(Request $request) {
//    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        
        $recruitment  = Recruitment::filterSource()
                                ->whereHash($id)
                                ->orWhere('id', $id)
                                ->firstOrFail();
        
        return $this->setContent('admin.website.recruitments.show', compact('recruitment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function edit($id) {
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function update(Request $request, $id) {
//    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = Recruitment::filterSource()
                              ->whereHash($id)
                              ->delete();

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
        
        $result = Recruitment::filterSource()->whereIn('id', $ids)->delete();
        
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

        $data = Recruitment::filterSource()
                           ->select();
        
        //filter area
        $value = $request->get('area');
        if($request->has('area')) {
            $data = $data->where('area', $value);
        }
        
        //filter gender
        $value = $request->get('gender');
        if($request->has('gender')) {
            $data = $data->where('gender', $value);
        }
        
        //filter city
        $value = $request->get('city');
        if($request->has('city')) {
            $data = $data->where('city', $value);
        }
        
        //filter experience
        $value = $request->get('experience');
        if($request->has('experience')) {
            $data = $data->where('experience', $value);
        }
        
        //filter availability
        $value = $request->get('availability');
        if($request->has('availability')) {
            $data = $data->where('availability', $value);
        }
        
        //filter has experience
        $value = $request->get('has_experience');
        if($request->has('has_experience')) {
            $data = $data->where('has_experience', $value);
        }
        
        //filter professional situation
        $value = $request->get('professional_situation');
        if($request->has('professional_situation')) {
            $data = $data->where('professional_situation', $value);
        }
        
        //filter qualifications
        $value = $request->get('qualifications');
        if($request->has('qualifications')) {
            $data = $data->where('qualifications', $value);
        }
        
        //filter driving license
        $value = $request->get('driving_licence');
        if($request->has('driving_licence')) {
            $data = $data->where('driving_licence', $value);
        }
        
        
        return Datatables::of($data)
                        ->add_column('select', function($row) {
                            return view('admin.partials.datatables.select', compact('row'))->render();
                        })
                        ->edit_column('area', function($row) {
                            return view('admin.website.recruitments.datatables.area', compact('row'))->render();
                        })
                        ->edit_column('name', function($row) {
                            return view('admin.website.recruitments.datatables.name', compact('row'))->render();
                        })
                        ->edit_column('experience', function($row) {
                            return view('admin.website.recruitments.datatables.experience', compact('row'))->render();
                        })
                        ->edit_column('created_at', function($row) {
                            return view('admin.partials.datatables.created_at', compact('row'))->render();
                        })
                        ->add_column('actions', function($row) {
                            return view('admin.website.recruitments.datatables.actions', compact('row'))->render();
                        })
                        ->make(true);
    }

}
