<?php

namespace App\Http\Controllers\Admin\Agencies;

use App\Models\Agency;
use App\Models\Bank;
use App\Models\Company;
use Html, Croppa, Auth, File, Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;

class CompaniesController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'agencies';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',companies']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*public function index() {
        return $this->setContent('admin.agencies.companies.index');
    }*/

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $company = new Company();

        $action = 'Adicionar Empresa';

        $formOptions = array('route' => array('admin.companies.store'), 'method' => 'POST', 'files' => true);

        $data = compact(
            'action',
            'formOptions',
            'company'
        );

        return view('admin.agencies.companies.edit', $data)->render();
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
        
        $action = 'Editar Empresa';
        
        $company = Company::findOrfail($id);

        $formOptions = array('route' => array('admin.companies.update', $company->id), 'method' => 'PUT', 'files' => true);

        $data = compact(
            'company',
            'action',
            'formOptions'
        );

        return view('admin.agencies.companies.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        Company::flushCache(Company::CACHE_TAG);

        $input = $request->all();
        //$input['is_active']  = $request->get('is_active', false);

        $company = Company::findOrNew($id);

        if ($company->validate($input)) {
            $company->fill($input);
            $company->source = config('app.source');
            $company->save();

            //delete image
            if ($request->delete_photo && !empty($company->filepath)) {
                Croppa::delete($company->filepath);
                $company->filepath = null;
                $company->filename = null;
            }

            //upload image black
            if($request->hasFile('image_black')) {

                if ($company->exists && !empty($company->filepath_black) && File::exists(public_path(). '/'.$company->filepath_black)) {
                    Croppa::delete($company->filepath_black);
                }

                $overrideColumns = [
                    'filename' => 'filename_black',
                    'filepath' => 'filepath_black'
                ];

                if (!$company->upload($request->file('image_black'), true, -1, $overrideColumns)) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem.');
                }
            }

            //upload image
            if($request->hasFile('image')) {

                if ($company->exists && !empty($company->filepath) && File::exists(public_path(). '/'.$company->filepath)) {
                    Croppa::delete($company->filepath);
                }

                if (!$company->upload($request->file('image'))) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem.');
                }
            }

            $company->filehost = env('APP_URL').'/';
            $company->save();


            //atualiza imagem das agências
            Agency::where('company_id', $company->id)
                ->update([
                    'filehost' => $company->filehost,
                    'filepath' => $company->filepath,
                    'filename' => $company->filename,
                    'filepath_black' => $company->filepath_black,
                    'filename_black' => $company->filename_black,
                ]);

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }

        return Redirect::back()->withInput()->with('error', $company->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Company::flushCache(Company::CACHE_TAG);

        $agencies = Agency::where('company_id', $id)->count();

        if($agencies){
            return Redirect::back()->with('error', 'Não é possível eliminar a empresa porque existem agências associadas.');
        }

        $result = Company::filterSource()
                        ->whereId($id)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover o registo.');
        }

        return Redirect::route('admin.agencies.index')->with('success', 'Registo removido com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        Company::flushCache(Company::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = Company::whereIn('id', $ids)
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


        $data = Company::filterAgencies()
                        ->select();

        return Datatables::of($data)
            ->add_column('photo', function($row) {
                return view('admin.agencies.companies.datatables.photo', compact('row'))->render();
            })
            ->edit_column('vat', function($row) {
                return view('admin.agencies.companies.datatables.vat', compact('row'))->render();
            })
            ->edit_column('name', function($row) {
                return view('admin.agencies.companies.datatables.company', compact('row'))->render();
            })
            ->edit_column('phone', function($row) {
                return view('admin.agencies.companies.datatables.phone', compact('row'))->render();
            })
            ->edit_column('charter', function($row) {
                return view('admin.agencies.companies.datatables.charter', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.agencies.companies.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }
}
