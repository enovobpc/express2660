<?php

namespace App\Http\Controllers\Admin\Agencies;

use App\Models\Company;
use Html, Croppa, Auth, File, Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use App\Models\Agency;

class AgenciesController extends \App\Http\Controllers\Admin\Controller {

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
        $this->middleware(['ability:' . config('permissions.role.admin') . ',agencies']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.agencies.agencies.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $agency = new Agency;

        $colors = trans('admin/global.colors');

        $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->get());

        $companies = $this->listCompanies(Company::filterSource()->get());

        $coreAgencies = \App\Models\Core\Agency::get();
        $coreAgenciesMaxId = $coreAgencies->max('id') + 1;
        $coreAgenciesIds = $coreAgencies->pluck('id')->toArray();
        $allPossibleIds = range(1, $coreAgenciesMaxId);
        $availableIds = array_values(array_diff($allPossibleIds, $coreAgenciesIds));

        $action = 'Novo Armazém/Centro Logístico';

        $formOptions = array('route' => array('admin.agencies.store'), 'method' => 'POST');

        $data = compact(
            'agencies',
            'agency',
            'action',
            'formOptions',
            'colors',
            'coreAgenciesMaxId',
            'availableIds',
            'companies'
        );

        return view('admin.agencies.agencies.edit', $data)->render();
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
        
        $user = Auth::user();

        $colors = trans('admin/global.colors');

        $agencies = Agency::listsGrouped(Agency::remember(config('cache.query_ttl'))
            ->cacheTags(Agency::CACHE_TAG)
            ->filterAgencies()
            ->orderBy('code', 'asc')
            ->get());

        $myAgencies = $user->agencies;
        
        $action = 'Editar Armazém/Centro Logístico';
        
        if(Auth::user()->hasRole([config('permissions.role.admin')])) {
            $agency = Agency::filterAgencies()->findOrfail($id);
        } else {
            $agency = Agency::filterAgencies()
                            ->whereIn('id', $myAgencies)
                            ->findOrfail($id);
        }

        $companies = $this->listCompanies(Company::filterSource()->get());

        $formOptions = array('route' => array('admin.agencies.update', $agency->id), 'method' => 'PUT');

        $data = compact(
            'agency',
            'agencies',
            'action',
            'formOptions',
            'colors',
            'companies'
        );

        return view('admin.agencies.agencies.edit', $data)->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        \App\Models\Agency::flushCache(Agency::CACHE_TAG);

        $input = $request->all();
        $input['name'] = '['.$input['code'].'] ' .$input['print_name'];

        $agency     = \App\Models\Agency::filterAgencies()->findOrNew($id);
        $coreAgency = \App\Models\Core\Agency::filterAgencies()->findOrNew($id);


        $exists = $agency->exists;
        if ($agency->validate($input)) {

            try {
                if(!$exists) {
                    //cria agencia no core
                    $coreAgency = new \App\Models\Core\Agency();
                    $coreAgency->fill($input);

                    if(@$input['agency_id']) {
                        $coreAgency->id = $input['agency_id'];
                    }

                    $coreAgency->save();
                }
            } catch (\Exception $e) {
                return Redirect::back()->withInput()->with('error', 'Não é possível criar a agência com o ID ' . $input['agency_id']);
            }

            //cria agencia
            $agency->fill($input);
            if(!$exists) {
                $agency->id = $coreAgency->id;
            }

            $agency->save();
            $fill = $agency->toArray();

            unset($fill['id']);
            $coreAgency->fill($fill);
            $coreAgency->save();

            //atualiza logotipo de acordo com a empresa
            $company = Company::find($input['company_id']);
            $updateArray = [
                'filehost' => $company->filehost,
                'filepath' => $company->filepath,
                'filename' => $company->filename,
                'filepath_black' => $company->filepath_black,
                'filename_black' => $company->filename_black,
            ];
            Agency::where('id', $agency->id)->update($updateArray);

            return Redirect::back()->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $agency->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        Agency::flushCache(Agency::CACHE_TAG);

        $result = Agency::filterAgencies()
                        ->whereId($id)
                        ->delete();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a agência.');
        }

        return Redirect::back()->with('success', 'Agência removida com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/users/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {

        Agency::flushCache(Agency::CACHE_TAG);

        $ids = explode(',', $request->ids);
        
        $result = Agency::filterAgencies()
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

        $user = Auth::user();
        $myAgencies = $user->agencies;

        $agencies = Agency::get(['id','code', 'name', 'color']);
        $agencies = $agencies->groupBy('id')->toArray();
        
        $data = Agency::with('company_details')
            ->filterAgencies()
            ->select();

        return Datatables::of($data)
            ->add_column('photo', function($row) {
                return view('admin.agencies.agencies.datatables.photo', compact('row'))->render();
            })
            ->edit_column('agencies', function($row) use ($agencies) {
                return view('admin.partials.datatables.agencies', compact('row', 'agencies'))->render();
            })
            ->edit_column('code', function($row) use($myAgencies) {
                return view('admin.agencies.agencies.datatables.code', compact('row', 'myAgencies'))->render();
            })
            ->edit_column('name', function($row) use($myAgencies) {
                return view('admin.agencies.agencies.datatables.name', compact('row', 'myAgencies'))->render();
            })
            ->edit_column('company', function($row) {
                return view('admin.agencies.agencies.datatables.company', compact('row'))->render();
            })
            ->edit_column('phone', function($row) {
                return view('admin.agencies.agencies.datatables.phone', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function($row) use($myAgencies) {
                return view('admin.agencies.agencies.datatables.actions', compact('row', 'myAgencies'))->render();
            })
            ->make(true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function replicate($id) {

        Agency::flushCache(Agency::CACHE_TAG);

        $agencyCopy = Agency::find($id);

        $newAgency = $agencyCopy->replicate();
        $newAgency->code = 'XXXXX';
        $newAgency->name = 'XXXXX';
        $newAgency->print_name = 'XXXXX';
        $result = $newAgency->save();

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar duplicar a agência.');
        }

        return Redirect::route('admin.agencies.edit', $newAgency->id)->with('success', 'Agência duplicada com sucesso.');
    }

    /**
     * List companies
     * @param $allServices
     * @return array
     */
    public function listCompanies($allCompanies)
    {

        if ($allCompanies->count() > 1) {
            $services[] = ['value' => '', 'display' => ''];
        } else {
            $services = [];
        }

        foreach ($allCompanies as $company) {
            $services[] = [
                'value'             => $company->id,
                'display'           => $company->display_name,
                'data-vat'          => $company->vat,
                'data-name'         => $company->name,
                'data-address'      => $company->address,
                'data-zip_code'     => $company->zip_code,
                'data-city'         => $company->city,
                'data-country'      => $company->country,
                'data-phone'        => $company->phone,
                'data-mobile'       => $company->mobile,
                'data-email'        => $company->email,
                'data-website'      => $company->website,
                'data-charter'      => $company->charter
            ];
        }
        return $services;
    }
}
